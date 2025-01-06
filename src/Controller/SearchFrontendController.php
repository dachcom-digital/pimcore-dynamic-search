<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Form\Type\SearchFormType;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;
use DynamicSearchBundle\Processor\OutputChannelProcessorInterface;
use Pimcore\Controller\FrontendController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SearchFrontendController extends FrontendController
{
    public function __construct(
        protected FormFactoryInterface $formFactory,
        protected ConfigurationInterface $configuration,
        protected OutputChannelProcessorInterface $outputChannelWorkflowProcessor
    ) {
    }

    /**
     * @throws NotFoundHttpException
     */
    public function searchAction(Request $request, string $contextName, string $outputChannelName): Response
    {
        $outputChannelName = str_replace('-', '_', $outputChannelName);

        if (!$this->outputChannelExists($contextName, $outputChannelName)) {
            throw $this->createNotFoundException(sprintf('invalid, internal or no frontend output channel "%s".', $outputChannelName));
        }

        return $this->renderFrontendSearch($request, $outputChannelName, $contextName, $this->getOutputChannelView($contextName, $outputChannelName, 'list'));
    }

    /**
     * @throws NotFoundHttpException
     */
    public function multiSearchAction(Request $request, string $contextName, string $outputChannelName): Response
    {
        $outputChannelName = str_replace('-', '_', $outputChannelName);

        if (!$this->outputChannelExists($contextName, $outputChannelName, true)) {
            throw $this->createNotFoundException(sprintf('invalid, internal or no frontend output channel "%s".', $outputChannelName));
        }

        return $this->renderFrontendSearch($request, $outputChannelName, $contextName, $this->getOutputChannelView($contextName, $outputChannelName, 'multi-list'));
    }

    protected function renderFrontendSearch(Request $request, string $outputChannelName, string $contextName, string $viewName): Response
    {
        $hasError = false;
        $errorMessage = null;
        $outputChannelResult = null;
        $searchActive = false;

        $form = $this->formFactory->createNamed('', SearchFormType::class, null, ['method' => 'GET']);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $searchActive = true;
                $outputChannelResult = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, $outputChannelName);
            } catch (\Throwable $e) {
                $hasError = true;
                $errorMessage = sprintf('Error while loading search output channel "%s" for "%s" context. Error was: %s', $outputChannelName, $contextName,
                    $e->getMessage());
            }
        }

        $viewName = sprintf('@DynamicSearch/output-channel/%s/list.html.twig', $viewName);

        if ($hasError === true) {
            return $this->renderTemplate($viewName, [
                'has_error'     => $hasError,
                'error_message' => $errorMessage
            ]);
        }

        if ($searchActive === false) {
            return $this->renderTemplate($viewName, [
                'has_error'     => $hasError,
                'error_message' => $errorMessage,
                'search_active' => $searchActive,
                'form'          => $form->createView(),
            ]);
        }

        $runtimeQueryProvider = null;
        $routeName = null;
        if ($outputChannelResult instanceof MultiOutputChannelResultInterface) {
            $routeName = 'dynamic_search_frontend_multi_search_list';
            $runtimeQueryProvider = $outputChannelResult->getRuntimeQueryProvider();
        } elseif ($outputChannelResult instanceof OutputChannelResultInterface) {
            $routeName = 'dynamic_search_frontend_search_list';
            $runtimeQueryProvider = $outputChannelResult->getRuntimeQueryProvider();
        }

        if ($runtimeQueryProvider === null) {
            return $this->renderTemplate($viewName, [
                'has_error'     => true,
                'error_message' => sprintf(
                    'output channel result "%s" needs to be instance of "%s" or "%s".',
                    $outputChannelName,
                    MultiOutputChannelResultInterface::class,
                    OutputChannelResultInterface::class
                )
            ]);
        }

        $params = [
            'has_error'         => false,
            'error_message'     => null,
            'search_active'     => $searchActive,
            'form'              => $form->createView(),
            'user_query'        => $runtimeQueryProvider->getUserQuery(),
            'query_identifier'  => $runtimeQueryProvider->getQueryIdentifier(),
            'search_route_name' => $routeName,
            'context_name'      => $contextName
        ];

        if ($outputChannelResult instanceof OutputChannelResultInterface) {
            return $this->renderTemplate($viewName, array_merge($params, $this->prepareQueryVars($outputChannelResult)));
        }

        $blocks = [];
        if ($outputChannelResult instanceof MultiOutputChannelResultInterface) {
            foreach ($outputChannelResult->getResults() as $resultBlockIdentifier => $resultBlock) {
                $blocks[$resultBlockIdentifier] = $this->prepareQueryVars($resultBlock);
            }
        }

        return $this->renderTemplate($viewName, array_merge($params, ['blocks' => $blocks]));
    }

    protected function prepareQueryVars(OutputChannelResultInterface $outputChannelResult): array
    {
        $data = null;
        $paginator = null;

        if ($outputChannelResult instanceof OutputChannelPaginatorResultInterface) {
            $paginator = $outputChannelResult->getPaginator();
        } elseif ($outputChannelResult instanceof OutputChannelArrayResultInterface) {
            $data = $outputChannelResult->getResult();
        }

        $runtimeOptions = $outputChannelResult->getRuntimeOptions();

        return [
            'data'            => $data,
            'paginator'       => $paginator,
            'current_page'    => $runtimeOptions['current_page'],
            'page_identifier' => $runtimeOptions['page_identifier'],
            'total_count'     => $outputChannelResult->getHitCount(),
            'filter'          => $outputChannelResult->getFilter(),
            'oc_allocator'    => $outputChannelResult->getOutputChannelAllocator(),
        ];
    }

    protected function outputChannelExists(string $contextName, string $outputChannelName, bool $multiSearchOnly = false) :bool
    {
        $channelConfig = $this->getOutputChannelConfig($contextName, $outputChannelName);

        if (!is_array($channelConfig)) {
            return false;
        }

        if ($channelConfig['internal'] === true) {
            return false;
        }

        if ($multiSearchOnly === true && $channelConfig['multiple'] !== true) {
            return false;
        }

        if ($multiSearchOnly === false && $channelConfig['multiple'] === true) {
            return false;
        }

        return $channelConfig['use_frontend_controller'] === true;
    }

    protected function getOutputChannelView(string $contextName, string $outputChannelName, string $default) :string
    {
        $channelConfig = $this->getOutputChannelConfig($contextName, $outputChannelName);

        if (!is_array($channelConfig)) {
            return $default;
        }

        return isset($channelConfig['view_name']) && is_string($channelConfig['view_name']) ? $channelConfig['view_name'] : $default;
    }

    protected function getOutputChannelConfig(string $contextName, string $outputChannelName): ?array
    {
        $contextConfig = $this->getParameter('dynamic_search.context.full_configuration');

        if (!isset($contextConfig[$contextName])) {
            return null;
        }

        if (!array_key_exists($outputChannelName, $contextConfig[$contextName]['output_channels'])) {
            return null;
        }

        return $contextConfig[$contextName]['output_channels'][$outputChannelName];
    }
}

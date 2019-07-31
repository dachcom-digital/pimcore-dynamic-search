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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SearchFrontendController extends FrontendController
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OutputChannelProcessorInterface
     */
    protected $outputChannelWorkflowProcessor;

    /**
     * @param ConfigurationInterface          $configuration
     * @param OutputChannelProcessorInterface $outputChannelWorkflowProcessor
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelProcessorInterface $outputChannelWorkflowProcessor
    ) {
        $this->configuration = $configuration;
        $this->outputChannelWorkflowProcessor = $outputChannelWorkflowProcessor;
    }

    /**
     * @param Request $request
     * @param string  $contextName
     * @param string  $outputChannelName
     *
     * @return Response|NotFoundHttpException
     */
    public function searchAction(Request $request, string $contextName, string $outputChannelName)
    {
        $outputChannelName = str_replace('-', '_', $outputChannelName);

        if (!$this->outputChannelExists($contextName, $outputChannelName)) {
            throw $this->createNotFoundException(sprintf('invalid or internal output channel "%s".', $outputChannelName));
        }

        return $this->renderFrontendSearch($request, $outputChannelName, $contextName, 'List');
    }

    /**
     * @param Request $request
     * @param string  $contextName
     * @param string  $outputChannelName
     *
     * @return Response|NotFoundHttpException
     */
    public function multiSearchAction(Request $request, string $contextName, string $outputChannelName)
    {
        $outputChannelName = str_replace('-', '_', $outputChannelName);

        if (!$this->outputChannelExists($contextName, $outputChannelName, true)) {
            throw $this->createNotFoundException(sprintf('invalid or internal output channel "%s".', $outputChannelName));
        }

        return $this->renderFrontendSearch($request, $outputChannelName, $contextName, 'MultiList');
    }

    /**
     * @param Request $request
     * @param string  $contextName
     * @param string  $outputChannelName
     * @param string  $viewName
     *
     * @return Response
     */
    protected function renderFrontendSearch(Request $request, string $outputChannelName, string $contextName, string $viewName)
    {
        $hasError = false;
        $errorMessage = null;
        $outputChannelResult = null;
        $searchActive = false;

        $form = $this->get('form.factory')->createNamed('', SearchFormType::class, null, ['method' => 'GET']);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $searchActive = true;
                $outputChannelResult = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, $outputChannelName);
            } catch (\Throwable $e) {
                $hasError = true;
                $errorMessage = sprintf('Error while loading search output channel for "%s" context. Error was: %s', $contextName, $e->getMessage());
            }
        }

        $viewName = sprintf('@DynamicSearch/OutputChannel/%s/list.html.twig', $viewName);

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
                'paginator'     => null,
                'search_active' => $searchActive,
                'form'          => $form->createView(),
            ]);
        }

        $runtimeOptionsProvider = null;
        if ($outputChannelResult instanceof MultiOutputChannelResultInterface) {
            $runtimeOptionsProvider = $outputChannelResult->getRuntimeOptionsProvider();
        } elseif ($outputChannelResult instanceof OutputChannelResultInterface) {
            $runtimeOptionsProvider = $outputChannelResult->getRuntimeOptionsProvider();
        }

        if ($runtimeOptionsProvider === null) {
            return $this->renderTemplate($viewName, [
                'has_error'     => true,
                'error_message' => sprintf('output channel result "%s" needs to be instance of "%s" or "%s".',
                    $outputChannelName,
                    MultiOutputChannelResultInterface::class,
                    OutputChannelResultInterface::class
                )
            ]);
        }
        $params = [
            'has_error'        => false,
            'error_message'    => null,
            'search_active'    => $searchActive,
            'current_page'     => $runtimeOptionsProvider->getCurrentPage(),
            'user_query'       => $runtimeOptionsProvider->getUserQuery(),
            'query_identifier' => $runtimeOptionsProvider->getQueryIdentifier(),
            'form'             => $form->createView(),
        ];

        if ($outputChannelResult instanceof OutputChannelResultInterface) {
            return $this->renderTemplate($viewName, array_merge($params, $this->prepareQueryVars($outputChannelResult)));
        }

        $blocks = [];

        foreach ($outputChannelResult->getResults() as $resultBlockIdentifier => $resultBlock) {

            if (!$resultBlock instanceof OutputChannelResultInterface) {
                return $this->renderTemplate($viewName, [
                    'has_error'     => true,
                    'error_message' => sprintf('output channel "%s" for context "%s" should return OutputChannelResultInterface.', $outputChannelName, $contextName)
                ]);
            }

            $blocks[$resultBlockIdentifier] = $this->prepareQueryVars($resultBlock);

        }

        return $this->renderTemplate($viewName, array_merge($params, ['blocks' => $blocks]));

    }

    /**
     * @param OutputChannelResultInterface $outputChannelResult
     *
     * @return array
     */
    protected function prepareQueryVars(OutputChannelResultInterface $outputChannelResult)
    {
        $data = null;
        $paginator = null;

        if ($outputChannelResult instanceof OutputChannelPaginatorResultInterface) {
            $paginator = $outputChannelResult->getPaginator();
        } elseif ($outputChannelResult instanceof OutputChannelArrayResultInterface) {
            $data = $outputChannelResult->getResult();
        }

        return [
            'paginator' => $paginator,
            'data'      => $data,
            'filter'    => $outputChannelResult->getFilter()
        ];
    }

    /**
     * @param string $contextName
     * @param string $outputChannelName
     * @param bool   $multiSearchOnly
     *
     * @return bool
     */
    protected function outputChannelExists(string $contextName, string $outputChannelName, $multiSearchOnly = false)
    {
        $contextConfig = $this->configuration->get('context');

        if (!isset($contextConfig[$contextName])) {
            return false;
        }

        if (!array_key_exists($outputChannelName, $contextConfig[$contextName]['output_channels'])) {
            return false;
        }

        $channelConfig = $contextConfig[$contextName]['output_channels'][$outputChannelName];

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
}

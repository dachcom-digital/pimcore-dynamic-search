<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Form\Type\SearchFormType;
use DynamicSearchBundle\OutputChannel\OutputChannelResultInterface;
use DynamicSearchBundle\Paginator\PaginatorInterface;
use DynamicSearchBundle\Processor\OutputChannelWorkflowProcessor;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchFrontendController extends FrontendController
{
    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var OutputChannelWorkflowProcessor
     */
    protected $outputChannelWorkflowProcessor;

    /**
     * @param ConfigurationInterface         $configuration
     * @param OutputChannelWorkflowProcessor $outputChannelWorkflowProcessor
     */
    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelWorkflowProcessor $outputChannelWorkflowProcessor
    ) {
        $this->configuration = $configuration;
        $this->outputChannelWorkflowProcessor = $outputChannelWorkflowProcessor;
    }

    /**
     * @param Request $request
     * @param string  $contextName
     *
     * @return Response
     */
    public function searchAction(Request $request, string $contextName)
    {
        $hasError = false;
        $errorMessage = null;
        $paginator = null;
        $outputChannelResult = null;
        $searchActive = false;

        $form = $this->get('form.factory')->createNamed('', SearchFormType::class, null, ['method' => 'GET']);

        $form = $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $searchActive = true;
                $outputChannelResult = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, 'search', ['request' => $request]);
            } catch (\Throwable $e) {
                $hasError = true;
                $errorMessage = sprintf('Error while loading search output channel for "%s" context. Error was: %s', $contextName, $e->getMessage());
            }
        }

        if ($hasError === true) {
            return $this->renderTemplate('@DynamicSearch/OutputChannel/Search/list.html.twig', [
                'has_error'     => $hasError,
                'error_message' => $errorMessage
            ]);
        }

        if ($searchActive === false) {
            return $this->renderTemplate('@DynamicSearch/OutputChannel/Search/list.html.twig', [
                'has_error'     => $hasError,
                'error_message' => $errorMessage,
                'paginator'     => $paginator,
                'search_active' => $searchActive,
                'form'          => $form->createView(),
            ]);
        }

        if (!$outputChannelResult instanceof OutputChannelResultInterface) {
            return $this->renderTemplate('@DynamicSearch/OutputChannel/Search/list.html.twig', [
                'has_error'     => true,
                'error_message' => sprintf('output channel "search" for context "%s" should return OutputChannelResultInterface.', $contextName)
            ]);
        }

        $data = $outputChannelResult->getResult();
        $runtimeOptions = $outputChannelResult->getRuntimeOptionsProvider();

        if ($data instanceof PaginatorInterface) {
            $paginator = $data;
        }

        return $this->renderTemplate('@DynamicSearch/OutputChannel/Search/list.html.twig', [
            'has_error'        => $hasError,
            'error_message'    => $errorMessage,
            'paginator'        => $paginator,
            'search_active'    => $searchActive,
            'current_page'     => $runtimeOptions->getCurrentPage(),
            'user_query'       => $runtimeOptions->getUserQuery(),
            'query_identifier' => $runtimeOptions->getQueryIdentifier(),
            'form'             => $form->createView(),
        ]);
    }
}
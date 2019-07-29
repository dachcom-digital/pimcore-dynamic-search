<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Form\Type\SearchFormType;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;
use DynamicSearchBundle\Processor\OutputChannelProcessorInterface;
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
     *
     * @return Response
     */
    public function searchAction(Request $request, string $contextName)
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
                $outputChannelResult = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, 'search');
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
                'paginator'     => null,
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

        $runtimeOptions = $outputChannelResult->getRuntimeOptionsProvider();

        $data = null;
        if ($outputChannelResult instanceof OutputChannelPaginatorResultInterface) {
            $data = $outputChannelResult->getPaginator();
        }

        return $this->renderTemplate('@DynamicSearch/OutputChannel/Search/list.html.twig', [
            'has_error'        => $hasError,
            'error_message'    => $errorMessage,
            'paginator'        => $data,
            'search_active'    => $searchActive,
            'filter'           => $outputChannelResult->getFilter(),
            'current_page'     => $runtimeOptions->getCurrentPage(),
            'user_query'       => $runtimeOptions->getUserQuery(),
            'query_identifier' => $runtimeOptions->getQueryIdentifier(),
            'form'             => $form->createView(),
        ]);
    }
}

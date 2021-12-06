<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;
use DynamicSearchBundle\Processor\OutputChannelProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends AbstractController
{
    protected ConfigurationInterface $configuration;
    protected OutputChannelProcessorInterface $outputChannelWorkflowProcessor;

    public function __construct(
        ConfigurationInterface $configuration,
        OutputChannelProcessorInterface $outputChannelWorkflowProcessor
    ) {
        $this->configuration = $configuration;
        $this->outputChannelWorkflowProcessor = $outputChannelWorkflowProcessor;
    }

    public function jsonSearchAction(Request $request, string $contextName, string $outputChannelName): JsonResponse
    {
        $outputChannelName = str_replace('-', '_', $outputChannelName);

        if (!$this->outputChannelExists($contextName, $outputChannelName)) {
            return $this->json(['error' => sprintf('invalid or internal output channel "%s".', $outputChannelName)], 500);
        }

        try {
            $outputChannelResult = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, $outputChannelName);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => sprintf('Error while loading json based output channel for "%s" context. Error was: %s', $contextName, $e->getMessage())],
                500,
                [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
        }

        if ($outputChannelResult instanceof OutputChannelResultInterface) {
            return $this->json($this->getOutputParameters($outputChannelResult));
        }

        if ($outputChannelResult instanceof MultiOutputChannelResultInterface) {
            $params = [];
            foreach ($outputChannelResult->getResults() as $resultBlockIdentifier => $resultBlock) {
                $params[] = $this->getOutputParameters($outputChannelResult);
            }

            return $this->json($params);
        }

        return $this->json(['result' => []]);
    }

    protected function getOutputParameters(OutputChannelResultInterface $outputChannelResult): array
    {
        $data = null;

        if ($outputChannelResult instanceof OutputChannelPaginatorResultInterface) {
            $data = $outputChannelResult->getPaginator();
        } elseif ($outputChannelResult instanceof OutputChannelArrayResultInterface) {
            $data = $outputChannelResult->getResult();
        }

        return [
            'result'       => $data,
            'total_count'  => $outputChannelResult->getHitCount(),
            'filter'       => $outputChannelResult->getFilter(),
            'oc_allocator' => $outputChannelResult->getOutputChannelAllocator()
        ];
    }

    protected function outputChannelExists(string $contextName, string $outputChannelName): bool
    {
        $contextConfig = $this->getParameter('dynamic_search.context.full_configuration');

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

        return $channelConfig['use_frontend_controller'] === false;
    }
}

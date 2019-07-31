<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\OutputChannel\Result\MultiOutputChannelResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelArrayResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelPaginatorResultInterface;
use DynamicSearchBundle\OutputChannel\Result\OutputChannelResultInterface;
use DynamicSearchBundle\Processor\OutputChannelProcessorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
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
     * @return JsonResponse
     */
    public function jsonSearchAction(Request $request, string $contextName, string $outputChannelName)
    {
        $outputChannelName = str_replace('-', '_', $outputChannelName);

        if (!$this->outputChannelExists($contextName, $outputChannelName)) {
            return $this->json(['error' => sprintf('invalid or internal output channel "%s".', $outputChannelName)], 500);
        }

        try {
            $outputChannelResult = $this->outputChannelWorkflowProcessor->dispatchOutputChannelQuery($contextName, $outputChannelName);
        } catch (\Throwable $e) {
            return $this->json(
                ['error' => sprintf('Error while loading auto complete output channel for "%s" context. Error was: %s', $contextName, $e->getMessage())],
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

    /**
     * @param OutputChannelResultInterface $outputChannelResult
     *
     * @return array
     */
    protected function getOutputParameters(OutputChannelResultInterface $outputChannelResult)
    {
        $data = null;

        if ($outputChannelResult instanceof OutputChannelPaginatorResultInterface) {
            $data = $outputChannelResult->getPaginator();
        } elseif ($outputChannelResult instanceof OutputChannelArrayResultInterface) {
            $data = $outputChannelResult->getResult();
        }

        return [
            'result' => $data,
            'filter' => $outputChannelResult->getFilter()
        ];
    }

    /**
     * @param string $contextName
     * @param string $outputChannelName
     *
     * @return bool
     */
    protected function outputChannelExists(string $contextName, string $outputChannelName)
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

        return $channelConfig['use_frontend_controller'] === false;
    }
}

<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
use DynamicSearchBundle\Manager\IndexManagerInterface;
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
     * @var IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @param ConfigurationInterface $configuration
     * @param IndexManagerInterface  $indexManager
     */
    public function __construct(ConfigurationInterface $configuration, IndexManagerInterface $indexManager)
    {
        $this->configuration = $configuration;
        $this->indexManager = $indexManager;
    }

    /**
     * @param Request $request
     * @param string  $contextName
     *
     * @return JsonResponse
     */
    public function autoCompleteAction(Request $request, string $contextName)
    {
        $contextDefinition = $this->configuration->getContextDefinition($contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            return $this->json(['error' => sprintf('Context configuration "%s" does not exist', $contextName)], 500);
        }

        $autoCompleteService = $this->indexManager->getIndexProviderOutputChannel($contextDefinition, 'autocomplete');

        $data = $autoCompleteService->execute($contextDefinition, ['query' => $request->get('q')]);

        return $this->json($data);

    }

    /**
     * @param Request $request
     * @param string  $contextName
     *
     * @return JsonResponse
     */
    public function searchAction(Request $request, string $contextName)
    {
         $contextDefinition = $this->configuration->getContextDefinition($contextName);

        if (!$contextDefinition instanceof ContextDataInterface) {
            return $this->json(['error' => sprintf('Context configuration "%s" does not exist', $contextName)], 500);
        }

        $autoCompleteService = $this->indexManager->getIndexProviderOutputChannel($contextDefinition, 'search');

        $data = $autoCompleteService->execute($contextDefinition, ['query' => $request->get('q')]);

        return $this->json($data);
    }
}
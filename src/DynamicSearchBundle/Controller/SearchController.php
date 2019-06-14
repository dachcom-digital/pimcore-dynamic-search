<?php

namespace DynamicSearchBundle\Controller;

use DynamicSearchBundle\Configuration\ConfigurationInterface;
use DynamicSearchBundle\Context\ContextDataInterface;
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
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param Request $request
     * @param string  $contextName
     *
     * @return JsonResponse
     */
    public function autoCompleteAction(Request $request, string $contextName)
    {
        $context = $this->configuration->getContextDefinition($contextName);

        if (!$context instanceof ContextDataInterface) {
            return $this->json(['error' => sprintf('Context configuration "%s" does not exist', $contextName)], 500);
        }

        return $this->json([]);
    }

    public function searchAction(Request $request, string $context)
    {
        return $this->json([]);
    }
}
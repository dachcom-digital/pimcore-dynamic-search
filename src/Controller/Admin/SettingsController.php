<?php

namespace DynamicSearchBundle\Controller\Admin;

use DynamicSearchBundle\Manager\QueueManagerInterface;
use DynamicSearchBundle\Provider\Extension\ProviderBundleLocator;
use DynamicSearchBundle\Registry\HealthStateRegistryInterface;
use DynamicSearchBundle\Runner\ContextRunnerInterface;
use DynamicSearchBundle\State\HealthStateInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends AdminAbstractController
{
    public function __construct(
        private array $contextFullConfiguration
    )
    {
    }

    public function healthStateAction(HealthStateRegistryInterface $healthStateRegistry): JsonResponse
    {
        $stateLines = [];
        foreach ($healthStateRegistry->all() as $healthStateService) {

            $stateIcon = 'pimcore_icon_save';
            if ($healthStateService->getState() === HealthStateInterface::STATE_WARNING) {
                $stateIcon = 'pimcore_icon_info';
            } elseif ($healthStateService->getState() === HealthStateInterface::STATE_ERROR) {
                $stateIcon = 'pimcore_icon_cancel';
            } elseif ($healthStateService->getState() === HealthStateInterface::STATE_SILENT) {
                $stateIcon = null;
            }

            $stateLines[] = [
                'module'  => $healthStateService->getModuleName(),
                'title'   => $healthStateService->getTitle(),
                'comment' => $healthStateService->getComment(),
                'icon'    => $stateIcon,
            ];

        }

        return $this->json([
            'lines' => $stateLines
        ]);
    }

    public function providerAction(ProviderBundleLocator $providerBundleLocator): JsonResponse
    {
        return $this->json([
            'provider' => $providerBundleLocator->findProviderBundles()
        ]);
    }

    public function indexQueueInfoAction(QueueManagerInterface $queueManager): JsonResponse
    {
        return $this->json([
            'tableName' => $queueManager->getQueueTableName(),
            'count' => $queueManager->getTotalQueuedItems()
        ]);
    }

    public function indexQueueAllDataAction(Request $request, ContextRunnerInterface $contextRunner): Response
    {
        $contextName = $request->get('context');

        if (empty($contextName)) {
            return new Response('no context given', 400);
        }

        try {
            $contextRunner->runSingleContextCreation($contextName);
        } catch (\Throwable $e) {
            return new Response($e->getMessage(), 500);
        }

        return new Response();
    }

    public function clearIndexQueueAction(QueueManagerInterface $queueManager): Response
    {
        $queueManager->clearQueue();
        return new Response();
    }

    public function contextFullConfigurationAction(): JsonResponse
    {
        return $this->json($this->contextFullConfiguration);
    }
}

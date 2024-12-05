<?php

namespace DynamicSearchBundle\Controller\Admin;

use DynamicSearchBundle\Provider\Extension\ProviderBundleLocator;
use DynamicSearchBundle\Registry\HealthStateRegistryInterface;
use DynamicSearchBundle\State\HealthStateInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function contextFullConfigurationAction(ProviderBundleLocator $providerBundleLocator): JsonResponse
    {
        return $this->json($this->contextFullConfiguration);
    }
}

<?php

namespace DynamicSearchBundle\Controller\Admin;

use DynamicSearchBundle\Provider\Extension\ProviderBundleLocator;
use DynamicSearchBundle\Registry\HealthStateRegistryInterface;
use DynamicSearchBundle\State\HealthStateInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SettingsController extends AdminController
{
    public function healStateAction(HealthStateRegistryInterface $healthStateRegistry): JsonResponse
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
        $providerBundles = $providerBundleLocator->findProviderBundles();

        return $this->json([
            'provider' => $providerBundles
        ]);
    }
}

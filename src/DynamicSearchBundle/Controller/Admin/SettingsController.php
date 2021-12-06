<?php

namespace DynamicSearchBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SettingsController extends AdminController
{
    public function logAction(): JsonResponse
    {
        return $this->json([]);
    }

    public function stateAction(): JsonResponse
    {
        return $this->json([]);
    }
}

<?php

namespace Ig0rbm\Memo\Controller\Health;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/health")
 * @package Ig0rbm\Memo\Controller\Health
 */
class CheckController
{
    /**
     * @Route("/check", name="health_check", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function indexAction(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'okay'
        ]);
    }
}
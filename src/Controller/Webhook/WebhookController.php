<?php

namespace Ig0rbm\Memo\Controller\Webhook;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/webhook")
 * @package Ig0rbm\Memo\Controller\Webhook
 */
class WebhookController
{
    /**
     * @Route("/bot/memo", name="webhook_bot", methods={"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function indexAction(): JsonResponse
    {
        return new JsonResponse(['ok' => true]);
    }
}
<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\Webhook;

use Ig0rbm\Memo\Service\Telegram\BotService;
use Ig0rbm\Memo\Service\Telegram\TokenChecker;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

/**
 * @Route("/webhook")
 * @package Ig0rbm\Memo\Controller\Webhook
 */
class WebhookController
{
    private BotService $bot;

    private TokenChecker $tokenChecker;

    private LoggerInterface $logger;

    public function __construct(
        BotService $bot,
        TokenChecker $tokenChecker,
        LoggerInterface $logger
    ) {
        $this->bot          = $bot;
        $this->tokenChecker = $tokenChecker;
        $this->logger       = $logger;
    }

    /**
     * @Route("/bot/memo/{token}", name="webhook_bot", methods={"GET", "POST"})
     *
     * @param string $token
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(string $token, Request $request): JsonResponse
    {
        if (false === $this->tokenChecker->isValidToken($token)) {
            return new JsonResponse(
                ['ok' => false, 'message' => 'Wrong token'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }

        $this->logger->debug('Message: ', ['message' => $request->getContent()]);

        try {
            $this->bot->handle($request->getContent());
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage(), ['trace' => $e->getTrace()]);

            return new JsonResponse(['ok' => false]);
        }

        return new JsonResponse(['ok' => true]);
    }
}

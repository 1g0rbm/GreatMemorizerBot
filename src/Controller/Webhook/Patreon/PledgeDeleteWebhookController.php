<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\Webhook\Patreon;

use Doctrine\ORM\EntityManagerInterface;
use Ig0rbm\Memo\Entity\Billing\Patreon\Pledge;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/webhook/patreon")
 */
class PledgeDeleteWebhookController
{
    private LoggerInterface $logger;

    /**
     * @Route("/delete", name="webhook_patreon_celete", methods={"POST"})
     */
    public function indexAction(Request $request): JsonResponse
    {

    }

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function handleError(Request $request, string $error, int $httpStatus)
    {
        $this->logger->error(
            $error,
            [
                'request_data'    => $request->getContent(),
                'request_headers' => $request->headers->all(),
            ]
        );

        return new JsonResponse(['ok' => false], $httpStatus);
    }

    private function isVerifiedRequest(Request $request): bool
    {
        $receivedSignature = $request->headers->get('x-patreon-signature');
        $createdSignature  = hash_hmac('md5', $request->getContent(), $this->secret);

        return $receivedSignature === $createdSignature;
    }
}
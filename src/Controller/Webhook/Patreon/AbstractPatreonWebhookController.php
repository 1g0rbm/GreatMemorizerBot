<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\Webhook\Patreon;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use function hash_hmac;

class AbstractPatreonWebhookController
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function handleError(Request $request, string $error, int $httpStatus)
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

    protected function isVerifiedRequest(Request $request, string $secret): bool
    {
        $receivedSignature = $request->headers->get('x-patreon-signature');
        $createdSignature  = hash_hmac('md5', $request->getContent(), $secret);

        return $receivedSignature === $createdSignature;
    }
}
<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\Webhook\Patreon;

use Doctrine\ORM\EntityManagerInterface;
use Ig0rbm\Memo\Entity\Billing\Patreon\Pledge;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use function hash_hmac;

class PledgeCreateWebhookController
{
    private EntityManagerInterface $em;

    private LoggerInterface $logger;

    private string $secret;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, string $secret)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->secret = $secret;
    }

    /**
     * @Route("/webhook/patreon/create", name="pledge_create", methods={"POST"})
     */
    public function indexAction(Request $request): JsonResponse
    {
        if (! $this->isVerifiedRequest($request)) {
            return $this->handleError($request, 'http_forbiden', JsonResponse::HTTP_FORBIDDEN);
        }

        $content = json_decode($request->getContent(), true);
        $email   = $content['data']['attributes']['email'] ?? null;

        if ($email === null) {
            return $this->handleError(
                $request,
                'create_pledge_email_not_found',
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $pledge = new Pledge();
        $pledge->setEmail($email);

        $this->em->persist($pledge);
        $this->em->flush();

        return new JsonResponse(['ok' => true]);
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

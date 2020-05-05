<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\Webhook\Patreon;

use Doctrine\ORM\EntityManagerInterface;
use Ig0rbm\Memo\Entity\Billing\Patreon\Pledge;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PledgeCreateWebhookController extends AbstractPatreonWebhookController
{
    private EntityManagerInterface $em;

    private string $secret;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, string $secret)
    {
        parent::__construct($logger);
        $this->em     = $em;
        $this->secret = $secret;
    }

    /**
     * @Route("/webhook/patreon/create", name="pledge_create", methods={"POST"})
     */
    public function indexAction(Request $request): JsonResponse
    {
        if (! $this->isVerifiedRequest($request, $this->secret)) {
            return $this->handleError($request, 'http_forbidden', JsonResponse::HTTP_FORBIDDEN);
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
}

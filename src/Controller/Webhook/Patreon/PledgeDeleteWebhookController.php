<?php

declare(strict_types=1);

namespace Ig0rbm\Memo\Controller\Webhook\Patreon;

use Ig0rbm\Memo\Service\Billing\PatreonLicenseDeactivator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\NonUniqueResultException;

/**
 * @Route("/webhook/patreon")
 */
class PledgeDeleteWebhookController extends AbstractPatreonWebhookController
{
    private PatreonLicenseDeactivator $deactivator;

    private string $secret;

    public function __construct(PatreonLicenseDeactivator $deactivator, LoggerInterface $logger, string $secret)
    {
        parent::__construct($logger);
        $this->deactivator = $deactivator;
        $this->secret      = $secret;
    }

    /**
     * @Route("/delete", name="webhook_patreon_delete", methods={"POST"})
     *
     * @throws NonUniqueResultException
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

        $this->deactivator->deactivate($email);

        return new JsonResponse(['ok' => true]);
    }
}
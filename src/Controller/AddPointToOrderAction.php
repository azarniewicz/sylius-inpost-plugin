<?php

declare(strict_types=1);

namespace Azarniewicz\SyliusInPostPlugin\Controller;

use Azarniewicz\SyliusInPostPlugin\Client\InPostApiClient;
use Azarniewicz\SyliusInPostPlugin\Entity\InPostPointInterface;
use Azarniewicz\SyliusInPostPlugin\Model\InPostPointsAwareInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final readonly class AddPointToOrderAction
{
    private const CSRF_TOKEN_ID = 'azarniewicz_sylius_inpost.add_point';
    private const POINT_NAME_PATTERN = '/^[A-Za-z0-9_-]+$/';

    public function __construct(
        private FactoryInterface $inPostPointFactory,
        private EntityManagerInterface $entityManager,
        private InPostApiClient $client,
        private CartContextInterface $cartContext,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var InPostPointsAwareInterface $cart */
        $cart = $this->cartContext->getCart();
        Assert::isInstanceOf($cart, OrderInterface::class);

        if (BaseOrderInterface::STATE_CART !== $cart->getState()) {
            throw new BadRequestHttpException();
        }

        return $this->addPoint($request, $cart);
    }

    private function addPoint(Request $request, InPostPointsAwareInterface $order): Response
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken(self::CSRF_TOKEN_ID, (string) $request->request->get('_token', '')))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], Response::HTTP_FORBIDDEN);
        }

        $name = trim((string) $request->request->get('name', ''));

        if ($name === '') {
            return new JsonResponse(['error' => 'Paczkomat code is required'], Response::HTTP_BAD_REQUEST);
        }

        if (preg_match(self::POINT_NAME_PATTERN, $name) !== 1) {
            return new JsonResponse(['error' => 'Invalid Paczkomat code'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $pointData = $this->client->getPointByName($name);
        } catch (\RuntimeException $exception) {
            return new JsonResponse(['error' => 'Unable to fetch Paczkomat details'], Response::HTTP_BAD_REQUEST);
        }

        $point = $order->getPoint();

        if ($point === null) {
            /** @var InPostPointInterface $point */
            $point = $this->inPostPointFactory->createNew();
        }

        $point->setName($name);
        $order->setPoint($point);

        $this->entityManager->flush();

        return new JsonResponse($pointData);
    }
}

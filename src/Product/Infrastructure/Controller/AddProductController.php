<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\ProductFacade;
use Gacela\Framework\DocBlockResolverAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method ProductFacade getFacade()
 */
final class AddProductController extends AbstractController
{
    use DocBlockResolverAwareTrait;

    public function __invoke(Request $request): Response
    {
        $name = $request->get('name');
        $price = $request->get('price');

        $this->getFacade()->createNewProduct($name, $this->validatePriceInput($price));

        $this->addFlash('success', "The product {$name} has been created.");

        return $this->redirectToRoute('product_list');
    }

    private function validatePriceInput(?string $price): ?int
    {
        if ($price === null) {
            return null;
        }

        if (filter_var($price, FILTER_VALIDATE_INT) === 0 || !filter_var($price, FILTER_VALIDATE_INT) === false) {
            return (int) $price;
        }

        throw new \RuntimeException('Second parameter [price] must be of type integer');
    }
}

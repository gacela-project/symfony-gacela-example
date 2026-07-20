<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\Infrastructure\PriceInput;
use App\Product\ProductFacade;
use Gacela\Framework\ServiceResolverAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method ProductFacade getFacade()
 */
final class AddProductController extends AbstractController
{
    use ServiceResolverAwareTrait;

    public function __invoke(Request $request): Response
    {
        $name = (string) $request->get('name');
        $priceInput = $request->get('price');
        $price = is_string($priceInput) ? $priceInput : null;

        $this->getFacade()->createNewProduct($name, PriceInput::parse($price));

        $this->addFlash('success', "The product {$name} has been created.");

        return $this->redirectToRoute('product_list');
    }
}

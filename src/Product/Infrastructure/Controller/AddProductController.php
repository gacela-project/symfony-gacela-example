<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\ProductFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddProductController
{
    private ProductFacade $facade;

    public function __construct(ProductFacade $facade)
    {
        $this->facade = $facade;
    }

    public function __invoke(Request $request): Response
    {
//        $facade = new ProductFacade();
        $products = $this->facade->getAllProducts();

        $result = '';
        foreach ($products as $product) {
            $result .= sprintf(
                'Product name: %s, price: %s',
                $product->getName(),
                $product->getPrice(),
            );
        }

        return new Response($result);
    }
}

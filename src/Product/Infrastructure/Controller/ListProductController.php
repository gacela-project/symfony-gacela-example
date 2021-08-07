<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\ProductFacade;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ListProductController
{
//    private ProductFacade $facade;
    private Environment $twig;

    public function __construct(/*ProductFacade $facade,*/ Environment $twig)
    {
/*        $this->facade = $facade;*/
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $facade = new ProductFacade();
        $products = $facade->getAllProducts();
//        $products = $this->facade->getAllProducts();

        return new Response(
            $this->twig->render(
                './../Templates/list-product/index.twig',
                ['products' => $products]
            )
        );
    }
}

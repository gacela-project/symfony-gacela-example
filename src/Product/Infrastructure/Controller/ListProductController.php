<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\ProductFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ListProductController extends AbstractController
{
    private ProductFacade $facade;

    public function __construct(ProductFacade $facade)
    {
        $this->facade = $facade;
    }

    public function __invoke(Request $request): Response
    {
        $products = $this->facade->getAllProducts();

        return $this->render(
            '/list-product/index.twig',
            ['products' => $products]
        );
    }
}

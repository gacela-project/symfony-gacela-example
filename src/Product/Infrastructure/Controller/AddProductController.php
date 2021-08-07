<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Controller;

use App\Product\ProductFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddProductController extends AbstractController
{
    private ProductFacade $facade;

    public function __construct(ProductFacade $facade)
    {
        $this->facade = $facade;
    }

    public function __invoke(Request $request): Response
    {
        $name = $request->get('name');
        $price = 123456;
        $this->facade->createNewProduct($name, $price);

        $this->addFlash("success", "The product {$name} has been created.");
        return $this->redirectToRoute('product_list');
    }
}

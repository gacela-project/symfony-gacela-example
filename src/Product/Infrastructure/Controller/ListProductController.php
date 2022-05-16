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
final class ListProductController extends AbstractController
{
    use DocBlockResolverAwareTrait;

    public function __invoke(Request $request): Response
    {
        $products = $this->getFacade()->getAllProducts();

        return $this->render(
            '/list-product/index.twig',
            ['products' => $products]
        );
    }
}

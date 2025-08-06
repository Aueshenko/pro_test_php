<?php

namespace App\Controller;

use App\Service\ProductService;

class SitemapController extends BaseController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $baseUrl = $scheme . '://' . $host;

        $products = $this->productService->findAll();

        $isXml = true;

        $this->render('sitemap', [
            'products' => $products,
            'baseUrl' => $baseUrl
        ], $isXml);
    }
}
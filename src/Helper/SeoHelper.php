<?php

namespace App\Helper;

class SeoHelper
{
    public static function buildProductSeo(array $product): array
    {
        $desc = strip_tags($product['description'] ?? '');
        $desc = trim(preg_replace('/\s+/', ' ', $desc));
        $shortDesc = mb_substr($desc, 0, 150);

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        $imagePath = !empty($product['image_url'])
            ? ltrim($product['image_url'], '/')
            : 'assets/placeholder.png';

        $imageUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $imagePath;
        $pageUrl = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return [
            'title' => $product['name'] . ' - ' . $product['category_name'] . ' | ProShop',
            'description' => $shortDesc,
            'image' => $imageUrl,
            'url' => $pageUrl
        ];
    }

    public static function buildCatalogSeo(): array
    {
        return [
            'title' => 'Наш Каталог Продуктов - ProShop',
            'description' => 'Управление продуктами'
        ];
    }
}
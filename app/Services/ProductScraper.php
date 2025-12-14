<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ProductScraper
{
    public function scrape($url)
    {
        $html = Http::get($url)->body();
        $crawler = new Crawler($html);

        // Safe title extraction
        $title = $this->safeText($crawler, [
            'h1',
            'h1.product-title',
            'h1.product__title',
            '.product-title',
            '.product_title',
        ]);

        // Safe price extraction
        $price = $this->safeText($crawler, [
            '.price',
            '.product__price',
            '.money',
            '.price-item',
            '.woocommerce-Price-amount',
            '.price-value',
        ]);

        // Safe image extraction
        $image = $this->safeAttr($crawler, [
            '.product__media img',
            '.wp-post-image',
            '.product-gallery img',
            '.woocommerce-product-gallery__image img',
            'img',
        ], 'src');

        return [
            'title' => trim($title),
            'price' => trim($price),
            'image' => $image,
            'url' => $url
        ];
    }

    private function safeText(Crawler $crawler, array $selectors)
    {
        foreach ($selectors as $selector) {
            try {
                $node = $crawler->filter($selector)->first();
                if ($node->count()) {
                    return $node->text('');
                }
            } catch (\Exception $e) {}
        }
        return ''; // fallback
    }

    private function safeAttr(Crawler $crawler, array $selectors, $attribute)
    {
        foreach ($selectors as $selector) {
            try {
                $node = $crawler->filter($selector)->first();
                if ($node->count()) {
                    return $node->attr($attribute);
                }
            } catch (\Exception $e) {}
        }
        return null; // fallback
    }
}

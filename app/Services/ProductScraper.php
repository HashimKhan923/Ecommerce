<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class ProductScraper
{
    public function scrape($url)
    {
        try {
            $html = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'en-US,en;q=0.5'
            ])->timeout(15)->retry(3, 200)->get($url)->body();
        } catch (\Exception $e) {
            return [
                'title' => '',
                'price' => '',
                'image' => null,
                'url' => $url,
                'error' => $e->getMessage()
            ];
        }

        $crawler = new Crawler($html);

        // TITLE DETECTION (advanced)
        $title = $this->safeText($crawler, [
            'h1',
            '.product-title',
            '.product__title',
            '.page-title',
            '.product_title'
        ]);

        // PRICE DETECTION (advanced)
        $price = $this->safeText($crawler, [
            '.price',
            '.price-item',
            '.money',
            '.product-price',
            '.product__price',
            '.woocommerce-Price-amount'
        ]);

        // IMAGE DETECTION (safe)
        $image = $this->safeAttr($crawler, [
            '.product-gallery img',
            '.woocommerce-product-gallery__image img',
            '.product__media img',
            'img'
        ], 'src');

        return [
            'title' => trim($title),
            'price' => trim($price),
            'image' => $image,
            'url' => $url
        ];
    }

    // Safe extraction helper: NEVER crashes
    private function safeText(Crawler $crawler, array $selectors)
    {
        foreach ($selectors as $selector) {
            try {
                $node = $crawler->filter($selector)->first();
                if ($node->count()) return $node->text('');
            } catch (\Exception $e) {}
        }
        return '';
    }

    private function safeAttr(Crawler $crawler, array $selectors, $attr)
    {
        foreach ($selectors as $selector) {
            try {
                $node = $crawler->filter($selector)->first();
                if ($node->count()) return $node->attr($attr);
            } catch (\Exception $e) {}
        }
        return null;
    }
}

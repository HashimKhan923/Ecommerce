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
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5'
            ])
            ->timeout(15)
            ->retry(3, 300)
            ->get($url)
            ->body();

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

        // 1. Try JSON-LD extraction for accurate price
        $price = '';
        $jsonLd = $crawler->filter('script[type="application/ld+json"]')->each(fn($n) => $n->text());

        foreach ($jsonLd as $json) {
            $data = json_decode($json, true);
            if (isset($data['offers']['price'])) {
                $price = $data['offers']['price'];
            }
        }

        // 2. Fallback HTML selectors
        $title = $this->safeText($crawler, ['h1', '.product-title', '.product__title']);
        $price = $price ?: $this->safeText($crawler, ['.money', '.price', '.price-item']);
        $image = $this->safeAttr($crawler, ['img', '.product__media img', '.woocommerce-product-gallery__image img'], 'src');

        return [
            'title' => trim($title),
            'price' => trim($price),
            'image' => $image,
            'url' => $url
        ];
    }

    private function safeText($crawler, $selectors)
    {
        foreach ($selectors as $selector) {
            try {
                $node = $crawler->filter($selector)->first();
                if ($node->count()) return $node->text('');
            } catch (\Exception $e) {}
        }
        return '';
    }

    private function safeAttr($crawler, $selectors, $attr)
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

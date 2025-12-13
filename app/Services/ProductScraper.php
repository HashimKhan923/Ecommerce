<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Http;

class ProductScraper
{
    public function scrape($url)
    {
        $html = Http::get($url)->body();
        $crawler = new Crawler($html);

        $title = $crawler->filter('h1')->first()->text('');
        $price = $crawler->filter('.price, .product__price, .money')->first()->text('');
        $image = $crawler->filter('img')->first()->attr('src') ?? null;

        return [
            'title' => trim($title),
            'price' => trim($price),
            'image' => $image,
            'url' => $url,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserSearchingKeyword;
use Illuminate\Support\Facades\Http;


class ProductSearchService
{
    // public function search(string $searchValue, int $length = 0)
    // {
    //     $Keyword = UserSearchingKeyword::firstOrNew(['keyword' => $searchValue]);
    //     $Keyword->count++;
    //     $Keyword->save();

    //     $stopWords = ['for', 'the', 'a', 'and', 'of', 'to', 'on', 'in'];
    //     $searchWords = explode(' ', strtolower($searchValue));
    //     $keywords = array_diff($searchWords, $stopWords);

    //     $data = $this->searchProducts($keywords, $length);

    //     while ($data->isEmpty() && count($keywords) > 1) {
    //         $data = $this->searchProducts($keywords, $length);

    //         if ($data->isEmpty()) {
    //             $keywords = $this->removeNonMatchingKeyword($keywords, $length);
    //             if (empty($keywords)) break;
    //         }
    //     }

    //     return $data;
    // }





public function search(string $searchValue, int $length = 0)
{
    $keywordEntry = UserSearchingKeyword::firstOrNew(['keyword' => $searchValue]);
    $keywordEntry->count++;
    $keywordEntry->save();

    $stopWords = ['for', 'the', 'a', 'and', 'of', 'to', 'on', 'in'];
    $searchWords = explode(' ', strtolower($searchValue));
    $keywords = array_diff($searchWords, $stopWords);


    $products = $this->searchProducts($keywords, $length);

    if ($products->isEmpty() && !empty($aiKeywords)) {
        $products = $this->searchProducts($aiKeywords, $length);
    }

    if ($products->isEmpty()) {
        foreach ($keywords as $keyword) {
            $products = $this->searchProducts([$keyword], $length);
            if ($products->isNotEmpty()) {
                break;
            }
        }
    }

    return $products;
}


private function searchProducts(array $keywords, int $length = 0)
{
    return Product::with([
            'user', 'category', 'brand', 'shop.shop_policy', 'model', 'stock',
            'product_gallery' => function ($query) {
                $query->orderBy('order', 'asc');
            },
            'product_varient', 'discount', 'tax', 'shipping'
        ])
        ->where('published', 1)
        ->whereHas('shop', fn($q) => $q->where('status', 1))
        ->where(function ($query) use ($keywords) {
            // Add full phrase match first
            $fullPhrase = implode(' ', $keywords);
            $soundexFull = soundex($fullPhrase);

            $query->where(function ($q) use ($fullPhrase, $soundexFull) {
                $q->where('name', 'LIKE', "%{$fullPhrase}%")
                    ->orWhere('sku', 'LIKE', "%{$fullPhrase}%")
                    ->orWhereRaw("SOUNDEX(name) = ?", [$soundexFull])
                    ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$fullPhrase])
                    ->orWhereHas('shop', fn($q) => $q->where('name', 'LIKE', "%{$fullPhrase}%"))
                    ->orWhereHas('brand', fn($q) => $q->where('name', 'LIKE', "%{$fullPhrase}%"))
                    ->orWhereHas('model', fn($q) => $q->where('name', 'LIKE', "%{$fullPhrase}%"))
                    ->orWhereHas('category', fn($q) => $q->where('name', 'LIKE', "%{$fullPhrase}%"))
                    ->orWhereHas('sub_category', fn($q) => $q->where('name', 'LIKE', "%{$fullPhrase}%"));
            });

            // Then search each individual keyword
            foreach ($keywords as $keyword) {
                $soundexKeyword = soundex($keyword);

                $query->orWhere(function ($q) use ($keyword, $soundexKeyword) {
                    $q->where('sku', 'LIKE', "%{$keyword}%")
                        ->orWhere('name', 'LIKE', "%{$keyword}%")
                        ->orWhereRaw("SOUNDEX(name) = ?", [$soundexKeyword])
                        ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$keyword])
                        ->orWhereRaw("JSON_SEARCH(start_year, 'one', ?) IS NOT NULL", [$keyword])
                        ->orWhereHas('shop', fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('brand', fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('model', fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('category', fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('sub_category', fn($q) => $q->where('name', 'LIKE', "%{$keyword}%"));
                });
            }
        })
        ->distinct()
        ->orderBy('featured', 'DESC')
        ->orderBy('id', 'ASC')
        ->skip($length)
        ->take(12)
        ->get();
}




}

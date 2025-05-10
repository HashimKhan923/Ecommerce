<?php

namespace App\Services;

use App\Models\Product;
use App\Models\UserSearchingKeyword;

class ProductSearchService
{
    public function search(string $searchValue, int $length = 0)
    {
        $Keyword = UserSearchingKeyword::firstOrNew(['keyword' => $searchValue]);
        $Keyword->count++;
        $Keyword->save();

        $stopWords = ['for', 'the', 'a', 'and', 'of', 'to', 'on', 'in'];
        $searchWords = explode(' ', strtolower($searchValue));
        $keywords = array_diff($searchWords, $stopWords);

        $data = $this->searchProducts($keywords, $length);

        while ($data->isEmpty() && count($keywords) > 1) {
            $data = $this->searchProducts($keywords, $length);

            if ($data->isEmpty()) {
                $keywords = $this->removeNonMatchingKeyword($keywords, $length);
                if (empty($keywords)) break;
            }
        }

        return $data;
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
            foreach ($keywords as $keyword) {
                $soundexKeyword = soundex($keyword);

                $query->where(function ($query) use ($keyword, $soundexKeyword) {
                    $query->where('sku', 'LIKE', "%{$keyword}%")
                        ->orWhere('name', 'LIKE', "%{$keyword}%")
                        ->orWhereRaw("SOUNDEX(name) = ?", [$soundexKeyword])
                        ->orWhereJsonContains('tags', $keyword)
                        ->orWhereJsonContains('start_year', $keyword)
                        ->orWhereHas('shop', function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('brand', function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('model', function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('category', function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        })
                        ->orWhereHas('sub_category', function ($query) use ($keyword) {
                            $query->where('name', 'LIKE', "%{$keyword}%");
                        });
                });
            }
        })
        ->orderBy('featured', 'DESC')
        ->orderBy('id', 'ASC')
        ->skip($length)->take(12)->get();
    }

    private function removeNonMatchingKeyword(array $keywords, int $length = 0): array
    {
        foreach ($keywords as $index => $keyword) {
            $tempKeywords = $keywords;
            unset($tempKeywords[$index]);

            $data = $this->searchProducts($tempKeywords, $length);
            if (!$data->isEmpty()) {
                return array_values($tempKeywords);
            }
        }

        return [];
    }
}

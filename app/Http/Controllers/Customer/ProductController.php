<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\ProductComments;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Http;


class ProductController extends Controller
{
    // Common method to fetch products
    private function getProductsWithRelationships($length = null, $searchValue = null)
    {

        $query = Product::with([
            'user','wishlistProduct', 'category','sub_category','brand', 'model', 'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'discount', 'tax', 'shipping', 'deal',
            'wholesale', 'shop', 'reviews.user', 'product_varient'
        ])->where('published', 1)
        ->orderByRaw('featured DESC')
        // ->whereHas('stock', function ($query) {
        //     $query->where('stock', '>', 0);
        // })
        ->whereHas('shop', function ($query) {
            $query->where('status', 1);
        });

        // Apply search logic if a search value is provided
        if ($searchValue && !empty($searchValue)) {
            $keywords = explode(' ', $searchValue); // Split the searchValue into keywords

            $query->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('sku', 'LIKE', "%{$keyword}%")
                            ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($keyword) . '%'])
                            ->orWhereJsonContains('tags', $keyword); // Assuming 'tags' is stored as JSON
                    });
                }
            });
        }
    
        if ($length !== null) {
            $query->skip($length)->take(12);
        } else {
            $query->take(12);
        }
    
        return $query->get();
    }

    // Index method to load initial products
    public function index()
    {
        // Fetch the first 24 products
        $Products = $this->getProductsWithRelationships();

        return response()->json(['Products' => $Products]);
    }

    // Load more products method
    public function load_more($length, $searchValue = null)
    {
        
        $data = $this->getProductsWithRelationships($length, $searchValue);
        return response()->json(['data' => $data]);
    }


    public function detail($id)
    {
        $data = Product::with([
            'wishlistProduct',
            'category',
            'sub_category',
            'brand',
            'model',
            'stock',
            'product_gallery' => function($query) {
                $query->orderBy('order', 'asc');
            },
            'discount',
            'tax',
            'shipping',
            'deal',
            'wholesale',
            'shop' => function($query) {
                $query->withCount('product')
                      ->with('shop_policy');
            },
            'reviews.user',
            'product_varient',
            'user.productReviews' => function ($query) {
                $query->with([
                    'user', // This loads the user who wrote the review
                    'product' => function ($q) {
                        $q->select('id', 'name')
                          ->with('product_single_gallery');
                    }
                ]);
            }
        ])->where('id', $id)->first();
    
        return response()->json(['data' => $data]);
    }
    

    public function comment(Request $request)
    {
        $new = new ProductComments();
        $new->product_id = $request->product_id;
        $new->person_name = $request->person_name;
        $new->comment = $request->comment;
        $new->save();
    }

    public function rating(Request $request)
    {
        $new = new ProductRating();
        $new->product_id = $request->product_id;
        $new->user_id = $request->user_id;
        $new->rating = $request->rating;
        $new->save();
    }

    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $chatHistory = $request->input('chat_history', []);
        $storedFilters = $request->input('filters', []);

        

        // Append the user's message to history
        $chatHistory[] = ['role' => 'user', 'content' => $userMessage];

        // System prompt
            $systemMessage = [
                'role' => 'system',
                'content' => 'You are AutoGenie, an auto parts shopping assistant for a marketplace website. only answers auto part-related questions. Always reply in English.
                if someone ask question which is not related to auto parts just reply I am here to assist only with auto part-related questions. Please ask something related to car parts.
        Respond naturally to users. Not every time you have to return product‑related data—if the message doesn’t contain any auto parts keyword or request, just answer the question.
        At the end of each reply, include a JSON object with available filters like: {"make":"Honda","model":"Civic","year":2016,"part":"tail light","max_price":100}. All fields are optional.'
            ];

        $messages = array_merge([$systemMessage], $chatHistory);

        // Send to OpenAI
        $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
        ]);

        $assistantReply = $response['choices'][0]['message']['content'] ?? '';
        $chatHistory[] = ['role' => 'assistant', 'content' => $assistantReply];

        // Extract JSON filters from assistant's reply
        preg_match('/\{(?:[^{}]|(?R))*\}/', $assistantReply, $jsonMatch);
        $newFilters = [];
        if (!empty($jsonMatch)) {
            try {
                $decoded = json_decode($jsonMatch[0], true);
                if (is_array($decoded)) {
                    $newFilters = $decoded;
                }
            } catch (\Exception $e) {
                $newFilters = [];
            }
        }

        $filters = [];
        $products = collect();
        $keywords = [];

        // Check for intent to view products
        $productIntents = [
            'do you have', 'i need', 'i want', 'can i get', 'show me', 'looking for',
            'search for', 'find me', 'need to buy', 'i wanna buy', 'where can i get'
        ];

        $userMessageLower = strtolower($userMessage);
        $hasProductIntent = false;
        foreach ($productIntents as $intent) {
            if (strpos($userMessageLower, $intent) !== false) {
                $hasProductIntent = true;
                break;
            }
        }

        // Only search products if it's an actual product intent
        if ($hasProductIntent && !empty($newFilters) && (
            !empty($newFilters['make']) ||
            !empty($newFilters['model']) ||
            !empty($newFilters['part']) ||
            !empty($newFilters['year']) ||
            !empty($newFilters['max_price'])
        )) {
            $filters = array_merge($storedFilters, array_filter($newFilters));

            $searchQuery = implode(' ', array_filter([
                $filters['make'] ?? '',
                $filters['model'] ?? '',
                $filters['part'] ?? '',
                $filters['year'] ?? ''
            ]));

            $keywords = $searchQuery ? explode(' ', $searchQuery) : [];

            $products = Product::with(['product_single_gallery' => function ($q) {
                $q->orderBy('order', 'asc')->select('id', 'product_id', 'image');
            }])
            ->select('id', 'name', 'featured', 'price', 'published')
            ->where('published', 1)
            ->whereHas('shop', fn($q) => $q->where('status', 1))
            ->where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $soundex = soundex($keyword);
                    $query->where(function ($q2) use ($keyword, $soundex) {
                        $q2->where('sku', 'LIKE', "%{$keyword}%")
                        ->orWhere('name', 'LIKE', "%{$keyword}%")
                        ->orWhereRaw("SOUNDEX(name) = ?", [$soundex])
                        ->orWhereJsonContains('tags', $keyword)
                        ->orWhereJsonContains('start_year', $keyword)
                        ->orWhereHas('shop', fn($q3) => $q3->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('brand', fn($q3) => $q3->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('model', fn($q3) => $q3->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('category', fn($q3) => $q3->where('name', 'LIKE', "%{$keyword}%"))
                        ->orWhereHas('sub_category', fn($q3) => $q3->where('name', 'LIKE', "%{$keyword}%"));
                    });
                }
            })
            ->when(!empty($filters['max_price']), fn($q) => $q->where('price', '<=', $filters['max_price']))
            ->distinct()
            ->orderBy('featured', 'DESC')
            ->orderBy('id', 'ASC')
            ->take(20)
            ->get();

            if ($products->count() && str_contains($assistantReply, "don't have")) {
                $assistantReply = "Yes, we have products matching your query. Let me show you the available options.";
            }
        }

        return response()->json([
            'reply' => $assistantReply,
            'products' => $products,
            'chat_history' => $chatHistory,
            'filters' => $filters,
            'keywords' => $keywords,
        ]);
    }


}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Services\ProductFilterService;
use Illuminate\Http\JsonResponse; 
use Illuminate\Support\Facades\Log;
use App\Models\search_histories;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Display a paginated list of products with their random variants(in color , size & etc).
     *
     * This endpoint returns a limited number of products, with an optional
     * `offset` parameter to paginate the results. Each product includes
     * its related `randomVariant` data.
     *
     * @param Request $request
     *     The HTTP request containing optional 'offset' query parameter.
     *
     * @return JsonResponse
     *     JSON response containing either a list of products or an error message.
     *
     * @throws Exception
     *     If an unexpected error occurs during product retrieval.
     */
    public function index(Request $request): JsonResponse
    {
        // Validate 'offset' input (optional, integer, min value 0)
        $validator = Validator::make($request->only('offset'), [
            'offset' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $limit = 4;
            $offset = (int) $request->get('offset', 0);

            // Fetch products with the 'randomVariant' relationship
            $products = Product::with(['randomVariant' => function ($query) {
                    $query->select('id', 'stock','img','product_id'); 
                }])
                ->skip($offset)
                ->take($limit)
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Products retrieved successfully',
                'products' => $products,
            ]);
        } catch (\Throwable $e) { 
            // Log the error for debugging
            Log::error('Product retrieval error.', [
                'error' => $e->getMessage(),
                'context' => [
                    'offset' => $request->input('offset'),
                    'user_id' => optional($request->user())->id,
                ],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
            ], 500);
        }
    }

    /**
     * Search for products by name or description.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // Validate the query parameter
            $validated = $request->validate([
                'query' => 'required|string|min:1|max:255',
            ]);

            $query = $validated['query'];

            // Ensure the query is sanitized and safe for use
            $query = trim(strip_tags($query));
            if (empty($query)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query cannot be empty.'
                ], 400);
            }

            // Perform the product search from history
            if( auth::check() ) { 
                $searchHistory = search_histories::where('term', 'LIKE', "%{$query}%")
                    ->select('term', 'category_name','brand','type')
                    ->where('user_id', Auth::user()->uuid)
                    ->orderByDesc('updated_at')
                    ->limit(5)
                    ->get();
            }

            // Perform the product search
            $excludedTerms = $searchHistory?->pluck('term')?? [];
            $search_new = Product::query()
                ->select('id', 'name', 'category','brand','type')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                })
                ->whereNotIn('name', $excludedTerms)
                ->limit(5)
                ->get();

            // Check if any products were found
            if ($search_new->isEmpty() && empty($searchHistory)) {
                return response()->json([
                    'success' => false,
                    'message' => "No products found for '{$query}'."
                ], 404);
            }

            // Return the search results
            return response()->json([
                'success' => true,
                'message' => 'Search results retrieved successfully.',
                'search_new' => $search_new,
                'search_history' => $searchHistory ?? [],
            ]);

        } catch (ValidationException $e) {
            // Handle validation errors gracefully
            return response()->json([
                'success' => false,
                'message' => 'Invalid search query.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) { 
            // log the error and return a generic error message
            Log::error('Product search error.', [
                'error' => $e->getMessage(),
                'context' => [
                    'query' => $request->input('query'),
                    'user_id' => optional($request->user())->id,
                ],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for products. Please try again later.',
            ], 500);
        }
    }

    /**
     * Filter products based on request parameters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Services\ProductFilterService  $filterService
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request, ProductFilterService $filterService): JsonResponse
    { 
         // Validate inputs
         $validator = Validator::make($request->all(), [
            'offset' => 'sometimes|integer|min:0',
            'sort' => 'sometimes|in:price-asc,price-desc,newest,oldest',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string',
            'type' => 'sometimes|string',
            'product' => 'sometimes|string',
            'brand' => 'sometimes|string',
            'free_shipping' => 'sometimes',
            'color' => 'sometimes|string',
            'size' => 'sometimes|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input parameters.',
                'errors' => $validator->errors(),
            ], 422);
        }
 

        try {
            $products = $filterService->filter($request);

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No products found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Products filtered successfully.',
                'products' => $products
            ]);
        } catch (\Throwable $e) { 
            // Log the error for debugging
            Log::error('Product filtering error.', [
                'error' => $e->getMessage(),
                'context' => [
                    'filters' => $request->all(),
                    'user_id' => optional($request->user())->id,
                ],
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while filtering products.'
            ], 500);
        }
    }
    
}


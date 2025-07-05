<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Models\search_histories;
use Illuminate\Support\Facades\Auth;

class ProductFilterService
{
    /**
     * Filter products based on request parameters.
     *
     * @param Request $request
     * @return Collection
     *
     * @throws \Exception
     */
    public function filter(Request $request): Collection
    {
        $limit = 4;
        $offset = (int) $request->get('offset', 0);

        $query = Product::query()->where('quantity', '>', 0);

        // Sort
        $sortClause = match ($request->input('sort')) {
            'price-asc'  => 'COALESCE(discount_price, original_price) ASC',
            'price-desc' => 'COALESCE(discount_price, original_price) DESC',
            'newest'     => 'created_at DESC',
            'oldest'     => 'created_at ASC',
            default      => null,
        };

        if ($sortClause) {
            if (str_contains($sortClause, 'COALESCE')) {
                $query->orderByRaw($sortClause);
            } else {
                [$column, $direction] = explode(' ', $sortClause);
                $query->orderBy($column, $direction);
            }
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where(DB::raw('COALESCE(discount_price, original_price)'), '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where(DB::raw('COALESCE(discount_price, original_price)'), '<=', $request->input('max_price'));
        }

        // Category filter 
        $category = $request->input('category');
        if (!empty($category)) {
            $query->where('category', $category);

            // create or update search history
            $product_name = trim(strip_tags($request->input('product', $category)));
            $brand = trim(strip_tags($request->input('brand', '')));
            $type = trim(strip_tags($request->input('type', '')));
            if(Auth::check() && !empty($product_name)) {
                search_histories::updateOrCreate(
                    [
                        'user_id' => $request->user()->uuid,
                        'term' =>  $product_name,
                        'brand' =>  $brand,
                        'type' =>  $type,
                        'category_name' => $category,
                    ]
                );
            }

        }

        // Brand filter
        if ($request->filled('brand')) {
            $brands = array_filter(explode(',', $request->input('brand')));
            if (!empty($brands)) {
                $query->whereIn('brand', $brands);
            }
        }

        if ($request->filled('type')) {
            $type = array_filter(explode(',', $request->input('type')));
            if (!empty($type)) {
                $query->whereIn('type', $type);
            }
        }

        // Free shipping filter
        if ($request->filled('free_shipping')) {
            $query->where('shipping_price', 0);
        }

        // Variant filters
        $query->whereHas('variants', function (Builder $q) use ($request) {
            if ($request->filled('size')) {
                $sizes = array_filter(explode(',', $request->input('size')));
                if (!empty($sizes)) {
                    $q->whereIn('size', $sizes);
                }
            }

            if ($request->filled('color')) {
                $colors = array_filter(explode(',', $request->input('color')));
                if (!empty($colors)) {
                    $q->whereIn('color', $colors);
                }
            }

            $q->where('stock', '>', 0);
        });

        // Eager load matching variants
        $query->with(['variants' => function ($q) use ($request) {
            if ($request->filled('size')) {
                $sizes = array_filter(explode(',', $request->input('size')));
                if (!empty($sizes)) {
                    $q->whereIn('size', $sizes);
                }
            }

            if ($request->filled('color')) {
                $colors = array_filter(explode(',', $request->input('color')));
                if (!empty($colors)) {
                    $q->whereIn('color', $colors);
                }
            }

            $q->where('stock', '>', 0);
        }]);

        return $query->skip($offset)->take($limit)->get();
    }
}

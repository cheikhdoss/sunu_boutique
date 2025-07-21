<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        Log::info('Fetching all products');
        $products = Product::with('category')->get();
        Log::info('Products fetched:', ['count' => $products->count()]);
        
        return response()->json($products);
    }

    public function show(Product $product)
    {
        Log::info('Fetching product', ['id' => $product->id]);
        return response()->json($product->load('category'));
    }

    public function getByCategory(Category $category)
    {
        Log::info('Fetching products by category', ['category_id' => $category->id]);
        $products = $category->products()->with('category')->get();
        return response()->json($products);
    }

    public function search($query)
    {
        Log::info('Searching products', ['query' => $query]);
        $products = Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
        return response()->json($products);
    }
}

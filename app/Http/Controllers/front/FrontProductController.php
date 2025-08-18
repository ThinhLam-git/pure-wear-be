<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class FrontProductController extends Controller
{
    //
    public function getProducts(Request $request)
    {
        $products = Product::orderBy('created_at', 'desc')
            ->where('status', 1);

        if(!empty($request->category_id)){
            $catArray = explode(',', $request->category_id);
            $products->whereIn('category', $catArray);
        }

        if(!empty($request->brand_id)){
            $brandArray = explode(',', $request->brand_id);
            $products->whereIn('brand', $brandArray);
        }

        $products = $products->get();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Products successfully',
            'data' => $products,
        ]);
    }

    public function latestProducts()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->where('status', 1)
            ->limit(8)
            ->get();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Latest Products successfully',
            'data' => $products,
        ]);
    }

    public function featuredProducts()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->where('status', 1)
            ->where('is_featured', 'yes')
            ->limit(4)
            ->get();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Featured Products successfully',
            'data' => $products,
        ]);
    }

    public function getCategories()
    {
        $categories = Category::orderBy('created_at', 'desc')->where('status', 1)->get();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Categories successfully',
            'data' => $categories,
        ]);
    }

    public function getBrands()
    {
        $brands = Brand::orderBy('created_at', 'desc')->where('status', 1)->get();
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Brands successfully',
            'data' => $brands,
        ]);
    }

    public function getProduct($id){
        $product = Product::with(['product_images', 'product_sizes'])->find($id);
        
        if(!$product){
            return response()->json([
                'status' => 404,
                'message' => 'Product not found',
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Retrieved Product successfully',
            'data' => $product,
        ]);
    }
}

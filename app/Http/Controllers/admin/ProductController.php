<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ProductController extends Controller
{
    //
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')
            ->with(['product_images', 'product_sizes'])
            ->get();
        return response()->json([
            'status' => 200,
            'message' => 'Products retrieved successfully',
            'data' => $products
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required|numeric',
            'category' => 'required',
            'sku' => 'required|unique:products,sku',
            'is_featured' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        // Store the product
        $product = new Product();
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->image = $request->image;
        $product->category = $request->category;
        $product->brand = $request->brand;
        $product->qty = $request->qty;
        $product->sku = $request->sku;
        $product->status = $request->status;
        $product->barcode = $request->barcode;
        $product->is_featured = $request->is_featured;

        $product->save();

        if (!empty($request->sizes)) {
            foreach ($request->sizes as $sizeId) {
                $productSize = new ProductSize();
                $productSize->product_id = $product->id;
                $productSize->size_id = $sizeId;  // Assuming you pass size_id in the request
                $productSize->save();
            }
        }

        // Save the product image if provided
        if ($request->gallery) {
            foreach ($request->gallery as $key => $tempImageId) {
                // Assuming you have a method to handle image saving
                // $this->saveProductImage($product, $image);
                $tempImage = TempImage::find($tempImageId);

                // Large Thumbnail
                $extArray = explode('.', $tempImage->name);
                $ext = end($extArray);
                $rand = rand(1000, 10000);

                $imageName = $product->id . '-' . $rand . time() . '.' . $ext;  // {$product->id}-{}.jpg
                $manager = new ImageManager(Driver::class);
                $img = $manager->read(public_path('uploads/temp/' . $tempImage->name));
                $img->scaleDown(1200);
                $img->save(public_path('uploads/products/large/' . $imageName));

                // Small Thumbnail
                $manager = new ImageManager(Driver::class);
                $img = $manager->read(public_path('uploads/temp/' . $tempImage->name));
                $img->coverDown(400, 450);
                $img->save(public_path('uploads/products/small/' . $imageName));

                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = $imageName;
                $productImage->save();

                if ($key == 0) {
                    $product->image = $imageName;
                    $product->save();
                }
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product created successfully',
            'data' => $product
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|integer',
            'sku' => 'required|unique:products,sku,' . $id . ',id',
            'is_featured' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        // Update the product
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->category = $request->category;
        $product->brand = $request->brand;
        $product->qty = $request->qty;
        $product->sku = $request->sku;
        $product->status = $request->status;
        $product->barcode = $request->barcode;
        $product->is_featured = $request->is_featured;

        $product->save();

        if (!empty($request->sizes)) {
            ProductSize::where('product_id', $product->id)->delete();
            foreach ($request->sizes as $sizeId) {
                $productSize = new ProductSize();
                $productSize->product_id = $product->id;
                $productSize->size_id = $sizeId;  // Assuming you pass size_id in the request
                $productSize->save();
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product updated successfully',
            'data' => $product
        ], 200);
    }

    public function show($id)
    {
        $product = Product::with(['product_images', 'product_sizes'])->find($id);

        $productSizes = $product->product_sizes()->pluck(column: 'size_id');
        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Product retrieved successfully',
            'data' => $product,
            'productSizes' => $productSizes
        ], 200);
    }

    public function destroy($id)
    {
        $product = Product::with('product_images')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 404,
                'message' => 'Product not found'
            ], 404);
        }

        if ($product->product_images()) {
            foreach ($product->product_images as $image) {
                File::delete(public_path('uploads/products/large/' . $image->image));
                File::delete(public_path('uploads/products/small/' . $image->image));
                $image->delete();
            }
        }

        $product->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Product deleted successfully'
        ], 200);
    }

    public function saveProductImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        // Store the image

        $image = $request->file('image');
        $imageName = $request->product_id . '-' . time() . '.' . $image->extension();

        // Large Thumbnail
        $manager = new ImageManager(Driver::class);
        $img = $manager->read($image->getPathName());
        $img->scaleDown(1200);
        $img->save(public_path('uploads/products/large/' . $imageName));

        // Small Thumbnail
        $manager = new ImageManager(Driver::class);
        $img = $manager->read($image->getPathName());
        $img->coverDown(400, 450);
        $img->save(public_path('uploads/products/small/' . $imageName));

        // Save a record in the product image table
        $productImage = new ProductImage();
        $productImage->image = $imageName;
        $productImage->product_id = $request->product_id;  // Assuming you pass product_id in the request
        $productImage->save();

        return response()->json([
            'status' => 200,
            'message' => 'Image uploaded successfully',
            'data' => $productImage
        ], 200);
    }

    public function updateDefaultImage(Request $request)
    {
        $product = Product::find($request->product_id);
        $product->image = $request->image;  // Assuming you pass the new image name in the request
        $product->save();

        return response()->json([
            'status' => 200,
            'message' => 'Default image updated successfully',
            'data' => (object) [
                'image' => $product->image,
                'product_id' => $product->id
            ]
        ], 200);
    }

    public function deleteProductImage($id)
    {
        $productImage = ProductImage::find($id);
        if (!$productImage) {
            return response()->json([
                'status' => 404,
                'message' => 'Image not found'
            ], 404);
        }

        File::delete(public_path('uploads/products/large/' . $productImage->image));
        File::delete(public_path('uploads/products/small/' . $productImage->image));
        $productImage->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Image deleted successfully'
        ], 200);
    }
}

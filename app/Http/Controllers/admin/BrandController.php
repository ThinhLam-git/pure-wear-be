<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    //

    public function index()
    {
        $brands = Brand::orderBy('created_at', 'desc')->get();

        if (!$brands) {
            return response()->json([
                'status' => 404,
                'message' => 'No brands found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Brands retrieved successfully',
            'data' => $brands
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => $validator->errors()
            ], 400);
        }

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->save();
        return response()->json([
            'status' => 200,
            'message' => 'Brand created successfully',
            'data' => $brand
        ], 200);
    }

    public function show($id) {
        $brand = Brand::find($id);
        
        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Brand retrieved successfully',
            'data' => $brand
        ], 200);
    }

    public function update($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'data' => $validator->errors()
            ], 400);
        }

        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found'
            ], 404);
        }

        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->save();

        return response()->json([
            'status' => 200,
            'message' => 'Brand updated successfully',
            'data' => $brand
        ], 200);
    }

    public function destroy($id) {
        $brand = Brand::find($id);
        
        if (!$brand) {
            return response()->json([
                'status' => 404,
                'message' => 'Brand not found'
            ], 404);
        }

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Brand deleted successfully'
        ], 200);
    }
}

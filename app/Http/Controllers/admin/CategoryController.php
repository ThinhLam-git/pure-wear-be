<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //This method will return all the categories
    public function index()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    //This method will store a new category
    public function store(Request $request ) {
        $validator = Validator::make($request->all(), [
            'name'=> 'required', 
        ]);

        if($validator->fails()){
            return response()->json([
                'status'=> 400,
                'data'=> $validator->errors()
            ], 400);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

        return response()->json([
            'status'=>200,
            'message'=> 'Category created successfully',
            'data' => $category
        ], 200);
    }

    //This method will return a specific category
    public function show($id) {
        $category = Category::find($id);

        if(!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $category
        ]);
    }


    //This method will update a specific category
    public function update($id, Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status'=> 400,
                'data'=> $validator->errors()
            ], 400);
        }
        $category = Category::find($id);
        if(!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'Category not found'
            ], 404);
        }

        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();
        return response()->json([
            'status' => 200,
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);

    }

    //This method will delete a specific category
    public function destroy($id) {
        $category = Category::find($id);
        if(!$category) {
            return response()->json([
                'status' => 404,
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}

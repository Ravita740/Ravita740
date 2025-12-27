<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // TESTIG from developer branch            
            $categories = Category::orderBy('id', 'desc')->get();
            if ($categories->isEmpty()) {
                return response()->json(['message' => 'No categories found'], 404);
            }
            return response()->json(['categories' => $categories], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Server Error ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50',
                'description' => 'string|required|max:500',
            ])->validate();

            // if ($validator->stopOnFirstFailure()->fails()) {
            //     return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
            // }

            $category = new Category();
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();
            return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $request) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:50',
                'description' => 'sometimes|string|required|max:500',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
            }

            $category->update($validator->validated());
            return response()->json(['message' => 'Category updated successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return response()->json(['message' => 'Server Error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}

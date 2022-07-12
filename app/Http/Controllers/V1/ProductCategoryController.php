<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $productCategories = ProductCategory::latest();

        return response()->json([
            'product categories' => $productCategories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'name'     => 'required|min:2|max:10',
            'images'   => 'required|array',
            'images.*' => 'required|file|image',
        ]);
        $productCategory = ProductCategory::create($validation);

        // add  images to productCategory using media library
        if ($request->hasFile('images')) {
            $fileAdders = $productCategory->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('images');
            });
        }

        // checking the creation
        if ($productCategory){
            return response()->json([
                'message' => "The product's category created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        if ($productCategory){
            return response()->json([
                'product Category' => $productCategory,
            ], 200);
        }

        return response()->json([
            'message' => 'Error',
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validation = $request->validate([
            'name'     => 'required|min:2|max:10',
            'images'   => 'required|array',
            'images.*' => 'required|file|image',
        ]);

        $productCategory->name = $validation['name'];
        // get all images
        $mediaItems = $productCategory->getMedia('images');

        // change the images (delete the previous collection and add new one)
        if ($request->hasFile('images')) {
            $productCategory->clearMediaCollection('images');
            $fileAdders = $productCategory->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('images');
            });
        }

        $productCategory->save();

        if ($productCategory){
            return response()->json([
                'message' => "The product's category edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductCategory $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductCategory $productCategory)
    {
        $productCategory->delete();

        if($productCategory){
            return response()->json([
                'message' => 'Error',
            ], 400);
        }

        return response()->json([
            'message' => "The product's category deleted successfully",
        ], 200);
    }
}

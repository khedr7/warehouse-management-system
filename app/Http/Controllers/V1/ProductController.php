<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest();

        return response()->json([
            'Products' => $products,
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
            'name'                => 'required|min:2|max:10',
            'description'         => 'required|min:10|max:100',
            'product_category_id' => 'required|numeric|exists:productCategories,id',
            'images'   => 'required|array',
            'images.*' => 'required|file|image',
        ]);

        $product = Product::create($validation);

        // add  images to product using media library
        if ($request->hasFile('images')) {
            $fileAdders = $product->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('images');
            });
        }

        // checking the creation
        if ($product){
            return response()->json([
                'message' => "Product created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $mediaItems = $product->getMedia('images');

        if ($product){
            return response()->json([
                'product'     => $product,
                'Media Items' => $mediaItems
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
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validation = $request->validate([
            'name'                => 'required|min:2|max:10',
            'description'         => 'required|min:10|max:100',
            'product_category_id' => 'required|numeric|exists:productCategories,id',
            'images'   => 'required|array',
            'images.*' => 'required|file|image',
        ]);

        $product->name = $validation['name'];
        $product->description = $validation['description'];
        $product->product_category_id = $validation['product_category_id'];

        // get all images
        $mediaItems = $product->getMedia('images');

        // change the images (delete the previous collection and add new one)
        if ($request->hasFile('images')) {
            $product->clearMediaCollection('images');
            $fileAdders = $product->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('images');
            });
        }

        $product->save();

        if ($product){
            return response()->json([
                'message' => "Product edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        if($product){
            return response()->json([
                'message' => 'Error',
            ], 400);
        }

        return response()->json([
            'message' => "The product's category deleted successfully",
        ], 200);
    }
}

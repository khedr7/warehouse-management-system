<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        foreach ($products as $product) {
            $product->getMedia('images');
        }

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
            'name'                => 'required|min:2|max:20',
            'description'         => 'max:100',
            'product_category_id' => 'required|numeric|exists:product_Categories,id',
            'images'              => 'array',
            'images.*'            => 'file|image',
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
        if ($product){

            $fillOrderItem = DB::table('fill_order_items')->where('product_id', $product->id)->latest()->first();
            $id  = $fillOrderItem->id;
            $fillBillItem  = DB::table('fill_bill_items')->where('fill_order_item_id', $id)->latest()->first();

            $product->getMedia('images');

            $product->stores;

            foreach ($product->stores as $store) {
                $storeProduct = DB::table('store_product')->select('store_product.*')->where([
                    ['store_id'  , '=', $store->id],
                    ['product_id', '=', $product->id]
            ])->first();
                $store->quantity = $storeProduct->quantity;
            }

            $product->price = $fillBillItem->price;
            return response()->json([
                'product'     => $product,
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
            'name'                => 'required|min:2|max:20',
            'description'         => 'max:100',
            'product_category_id' => 'required|numeric|exists:product_Categories,id',
            'images'              => 'array',
            'images.*'            => 'file|image',
        ]);

        $product->name = $validation['name'];
        $product->description = $validation['description'];
        $product->product_category_id = $validation['product_category_id'];

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

        if($product){
            $product->delete();
            return response()->json([
                'message' => "The product's category deleted successfully",
            ], 200);
        }

        return response()->json([
            'message' => "Error",
        ], 400);
    }
}

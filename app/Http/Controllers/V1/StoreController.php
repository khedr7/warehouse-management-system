<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::all();

        foreach ($stores as $store) {
            $store->getMedia('images');

        }

        return response()->json([
            'Stores' => $stores
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
            'name'              => 'required|min:2|max:20',
            'capacity'          => 'required|numeric',
            'status'            => 'required|boolean',
            'location_id'       => 'required|numeric|exists:locations,id',
            'store_category_id' => 'required|numeric|exists:store_Categories,id',
            'images'            => 'array',
            'images.*'          => 'file|image',
        ]);

        $validation['current_capacity'] = 0;
        $store = Store::create($validation);

        // add  images to store using media library
        if ($request->hasFile('images')) {
            $fileAdders = $store->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('images');
            });
        }

        // checking the creation
        if ($store){
            return response()->json([
                'message' => "Store created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store $store
     * @return \Illuminate\Http\Response
     */
    public function show(Store $store)
    {
        if ($store){


            $store->getMedia('images');
            $store->products;
            foreach ($store->products as $product) {
                $storeProduct = DB::table('store_product')->select('store_product.*')->where([
                    ['store_id'  , '=', $store->id],
                    ['product_id', '=', $product->id]
            ])->first();
                $product->quantity = $storeProduct->quantity;
            }
            return response()->json([
                'Store'       => $store,
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
     * @param  \App\Models\Store $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Store $store)
    {
        $validation = $request->validate([
            'name'              => 'required|min:2|max:20',
            'capacity'          => 'required|numeric',
            'status'            => 'required|boolean',
            'store_category_id' => 'required|numeric|exists:store_Categories,id',
            'images'            => 'array',
            'images.*'          => 'file|image',
        ]);

        $store->name = $validation['name'];
        $store->capacity = $validation['capacity'];
        $store->status = $validation['status'];
        $store->store_category_id = $validation['store_category_id'];

        // change the images (delete the previous collection and add new one)
        if ($request->hasFile('images')) {
            $store->clearMediaCollection('images');
            $fileAdders = $store->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('images');
            });
        }

        $store->save();

        if ($store){
            return response()->json([
                'message' => "Store edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store $store
     * @return \Illuminate\Http\Response
     */
    public function destroy(Store $store)
    {
        if($store){
            $store->delete();
            return response()->json([
                'message' => 'Store deleted successfully',
            ], 200);
        }

        return response()->json([
            'message' => "Error",
        ], 400);
    }
}

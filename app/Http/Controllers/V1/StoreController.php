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


    public function move(Request $request)
    {
        $validation = $request->validate([
            'store_id_1'  => 'required|numeric|exists:stores,id',
            'store_id_2'  => 'required|numeric|exists:stores,id',
            'product_id'  => 'required|numeric|exists:products,id',
            'quantity'    => 'required|numeric',
        ]);

        $store_1 = DB::table('store_product')->select('store_product.*')->where([
            ['store_id'  , '=', $validation['store_id_1']],
            ['product_id', '=', $validation['product_id']]
        ])->first();

        if ($store_1->quantity >= $validation['quantity']) {
            DB::table('store_product')->select('store_product.*')->where([
                ['store_id'  , '=', $validation['store_id_1']],
                ['product_id', '=', $validation['product_id']]
            ])->limit(1)
            ->update(array('quantity' => DB::raw('quantity -'. $validation['quantity'])));

            $store = Store::findOrFail($validation['store_id_1']);
            $store->current_capacity = $store->current_capacity - $validation['quantity'];
            $store->save();

            $store_2 = DB::table('store_product')->select('store_product.*')->where([
                ['store_id'  , '=', $validation['store_id_2']],
                ['product_id', '=', $validation['product_id']]
            ])->first();

            if ($store_2) {
                DB::table('store_product')->select('store_product.*')->where([
                    ['store_id'  , '=', $validation['store_id_2']],
                    ['product_id', '=', $validation['product_id']]
                ])->limit(1)
                ->update(array('quantity' => DB::raw('quantity +'. $validation['quantity'])));

                $store = Store::findOrFail($validation['store_id_2']);
                $store->current_capacity = $store->current_capacity + $validation['quantity'];
                $store->save();
            }
            else {
                $store = Store::findOrFail($validation['store_id_2']);
                $store->products()->attach($$validation['product_id'], ['quantity' => $validation['quantity']]);

                $store->current_capacity = $store->current_capacity + $validation['quantity'];
                $store->save();
            }

            return response()->json([
                'message' => "done",
            ], 200);
        }

        else {
            return response()->json([
                'message' => "The first store does not has enough products",
            ], 400);
        }

    }
}

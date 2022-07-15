<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stores = Store::latest();

        $data = [];
        $i=0;
        foreach ($stores as $Store) {
            $data[$i] = [
                'Store'       => $Store,
                'Media Items' => $Store->getMedia('images')
            ];
            $i++;
        }

        return response()->json([
            'Store categories' => $data,
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
            'name'              => 'required|min:2|max:10',
            'capacity'          => 'required|numeric',
            'status'            => 'required|boolean',
            'location_id'       => 'required|numeric|exists:locations,id',
            'store_category_id' => 'required|numeric|exists:storeCategories,id',
            'images'            => 'required|array',
            'images.*'          => 'required|file|image',
        ]);

        $store = Store::create($validation);
        $store->current_capacity = $store->capacity;
        $store->save();

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
            return response()->json([
                'Store'       => $store,
                'Media Items' => $store->getMedia('images')
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
            'name'              => 'required|min:2|max:10',
            'capacity'          => 'required|numeric',
            'status'            => 'required|boolean',
            'store_category_id' => 'required|numeric|exists:storeCategories,id',
            'images'            => 'required|array',
            'images.*'          => 'required|file|image',
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
        $store->delete();

        if($store){
            return response()->json([
                'message' => 'Error',
            ], 400);
        }

        return response()->json([
            'message' => "Store deleted successfully",
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\StoreCategory;
use Illuminate\Http\Request;

class StoreCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $storeCategories = StoreCategory::all();

        foreach ($storeCategories as $storeCategory) {
            $storeCategory->getMedia('images');
        }

        return response()->json([
            'Store categories' => $storeCategories,
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
            'name'     => 'required|min:2|max:15',
            'images'   => 'array',
            'images.*' => 'file|image',
        ]);
        $storeCategory = StoreCategory::create($validation);

        // add  images to storeCategory using media library
        if ($request->hasFile('images')) {
            $fileAdders = $storeCategory->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('images');
            });
        }

        // checking the creation
        if ($storeCategory){
            return response()->json([
                'message' => "The store's category created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StoreCategory $storeCategory
     * @return \Illuminate\Http\Response
     */
    public function show(StoreCategory $storeCategory)
    {
        if ($storeCategory){
            $storeCategory->getMedia('images');
            return response()->json([
                'Store Category' => $storeCategory,
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
     * @param  \App\Models\StoreCategory $storeCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StoreCategory $storeCategory)
    {
        $validation = $request->validate([
            'name'     => 'required|min:2|max:15',
            'images'   => 'array',
            'images.*' => 'file|image',
        ]);

        $storeCategory->name = $validation['name'];

        // change the images (delete the previous collection and add new one)
        if ($request->hasFile('images')) {
            $storeCategory->clearMediaCollection('images');
            $fileAdders = $storeCategory->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('images');
            });
        }

        $storeCategory->save();

        if ($storeCategory){
            return response()->json([
                'message' => "The store's category edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreCategory $storeCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(StoreCategory $storeCategory)
    {
        if($storeCategory){
            $storeCategory->delete();
            return response()->json([
                'message' => "The store's category deleted successfully",
            ], 200);
        }

        return response()->json([
            'message' => 'Error',
        ], 400);

    }
}

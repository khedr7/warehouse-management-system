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
        $storeCategories = StoreCategory::latest();

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
            'name'     => 'required|min:2|max:10',
            'images'   => 'required|array',
            'images.*' => 'required|file|image',
        ]);
        $storeCategories = StoreCategory::create($validation);

        // add  images to storeCategories using media library
        if ($request->hasFile('images')) {
            $fileAdders = $storeCategories->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('images');
            });
        }

        // checking the creation
        if ($storeCategories){
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
            'name'     => 'required|min:2|max:10',
            'images'   => 'required|array',
            'images.*' => 'required|file|image',
        ]);

        $storeCategory->name = $validation['name'];
        // get all images
        $mediaItems = $storeCategory->getMedia('images');

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
        $storeCategory->delete();

        if($storeCategory){
            return response()->json([
                'message' => 'Error',
            ], 400);
        }

        return response()->json([
            'message' => "The store's category deleted successfully",
        ], 200);
    }
}

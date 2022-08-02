<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\DistributionCenter;
use Illuminate\Http\Request;

class DistributionCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $distributionCenters = DistributionCenter::all();


        foreach ($distributionCenters as $distributionCenter) {
            $distributionCenter->getMedia('images');
        }

        return response()->json([
            'Distribution Centers' => $distributionCenters,
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
            'name'          => 'required|min:2|max:20',
            'location_id'   => 'required|numeric|exists:locations,id',
            'user_id'       => 'required|numeric|exists:users,id',
            'images'        => 'array',
            'images.*'      => 'file|image',
        ]);

        $distributionCenter = DistributionCenter::create($validation);

        // add  images to distributionCenter using media library
        if ($request->hasFile('images')) {
            $fileAdders = $distributionCenter->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('images');
            });
        }

        // checking the creation
        if ($distributionCenter){
            return response()->json([
                'message' => "Distribution Center created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DistributionCenter $distributionCenter
     * @return \Illuminate\Http\Response
     */
    public function show(DistributionCenter $distributionCenter)
    {
        if ($distributionCenter){
            $distributionCenter->getMedia('images');
            return response()->json([
                'Distribution Center' => $distributionCenter,
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
     * @param  \App\Models\DistributionCenter $distributionCenter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DistributionCenter $distributionCenter)
    {
        $validation = $request->validate([
            'name'          => 'required|min:2|max:20',
            'location_id'   => 'required|numeric|exists:locations,id',
            'user_id'       => 'required|numeric|exists:users,id',
            'images'        => 'array',
            'images.*'      => 'file|image',
        ]);

        $distributionCenter->name = $validation['name'];
        $distributionCenter->location_id = $validation['location_id'];
        $distributionCenter->user_id = $validation['user_id'];

        // change the images (delete the previous collection and add new one)
        if ($request->hasFile('images')) {
            $distributionCenter->clearMediaCollection('images');
            $fileAdders = $distributionCenter->addMultipleMediaFromRequest(['images'])
            ->each(function ($fileAdder) {
                $fileAdder->toMediaCollection('images');
            });
        }

        $distributionCenter->save();

        if ($distributionCenter){
            return response()->json([
                'message' => "Distribution Center edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DistributionCenter $distributionCenter
     * @return \Illuminate\Http\Response
     */
    public function destroy(DistributionCenter $distributionCenter)
    {

        if($distributionCenter){
            $distributionCenter->delete();
            return response()->json([
                'message' => 'Distribution Center deleted successfully',
            ], 400);
        }

        return response()->json([
            'message' => "Error",
        ], 200);
    }
}

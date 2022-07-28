<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $locations = Location::all();

        return response()->json([
            'Locations' => $locations,
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
            'name'       => 'required|min:2|max:10',
            'state_id'   => 'required|numeric|exists:states,id',
        ]);

        $location = Location::create($validation);

        // checking the creation
        if ($location){
            return response()->json([
                'message' => "Location created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function show(Location $location)
    {
        if ($location){
            return response()->json([
                'Location' => $location,
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
     * @param  \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Location $location)
    {
        $validation = $request->validate([
            'name'       => 'required|min:2|max:10',
            'state_id'   => 'required|numeric|exists:states,id',
        ]);

        $location->name = $validation['name'];
        $location->state_id = $validation['state_id'];
        $location->save();

        if ($location){
            return response()->json([
                'message' => "Location edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Location $location
     * @return \Illuminate\Http\Response
     */
    public function destroy(Location $location)
    {

        if($location){
            $location->delete();
            return response()->json([
                'message' => 'Location deleted successfully',
            ], 400);
        }

        return response()->json([
            'message' => "Error",
        ], 200);
    }
}

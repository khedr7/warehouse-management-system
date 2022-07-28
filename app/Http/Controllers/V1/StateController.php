<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states = State::all();

        return response()->json([
            'States' => $states,
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
        ]);
        $state = State::create($validation);

        // checking the creation
        if ($state){
            return response()->json([
                'message' => "State created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\State $state
     * @return \Illuminate\Http\Response
     */
    public function show(State $state)
    {
        if ($state){
            return response()->json([
                'State' => $state,
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
     * @param  \App\Models\State $state
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, State $state)
    {
        $validation = $request->validate([
            'name'     => 'required|min:2|max:10',
        ]);

        $state->name = $validation['name'];
        $state->save();

        if ($state){
            return response()->json([
                'message' => "State edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\State $state
     * @return \Illuminate\Http\Response
     */
    public function destroy(State $state)
    {

        if($state){
            $state->delete();
            return response()->json([
                'message' => 'State deleted successfully',
            ], 400);
        }

        return response()->json([
            'message' => "Error",
        ], 200);
    }
}

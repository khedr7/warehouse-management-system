<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FillOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FillOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fillOrders = FillOrder::latest();

        $data = [];
        $i=0;
        foreach ($fillOrders as $fillOrder) {
            $data[$i] = [
                'Fill Order'       => $fillOrder,
                'Fill Order Items' => $fillOrder->fillOrderItems
            ];
            $i++;
        }

        return response()->json([
            'Fill orders' => $data,
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
            'description'    => 'max:50',
            'fillOrderItems' => 'required|array'
        ]);
        $validation['user_id'] = Auth::id();

        $fillOrder = FillOrder::create($validation);

        foreach ($validation['fillOrderItems'] as $fillOrderItem) {
            $fillOrder->fillOrderItems()->create([
                'fill_order_id' => $fillOrder->id,
                'product_id'    => $fillOrderItem['product_id'],
                'quantity'      => $fillOrderItem['quantity'],
            ]);
        }

        // checking the creation
        if ($fillOrder){
            return response()->json([
                'message' => "Fill Order created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FillOrder  $fillOrder
     * @return \Illuminate\Http\Response
     */
    public function show(FillOrder $fillOrder)
    {
        if ($fillOrder){
            return response()->json([
                'Fill order'       => $fillOrder,
                'Fill Order Items' => $fillOrder->fillOrderItems
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
     * @param  \App\Models\FillOrder  $fillOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FillOrder $fillOrder)
    {
        $date = Carbon::parse($fillOrder->created_at)->addHours(3);
        if (count($fillOrder->fillBills) == 0 && $date > now() && $fillOrder->user_id == Auth::id()) {

            $validation = $request->validate([
                'description'    => 'max:50',
                'fillOrderItems' => 'required|array'
            ]);

            $fillOrder->description = $validation['description'];

            $fillOrder->fillOrderItems()->delete();

            foreach ($validation['fillOrderItems'] as $fillOrderItem) {
                $fillOrder->fillOrderItems()->create([
                    'fill_order_id' => $fillOrder->id,
                    'product_id'    => $fillOrderItem['product_id'],
                    'quantity'      => $fillOrderItem['quantity'],
                ]);
            }
            $fillOrder->save();

            if ($fillOrder){
                return response()->json([
                    'message' => "fill Order edited successfully",
                ], 200);
            }
            return response()->json([
                'message' => 'Error',
            ], 400);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FillOrder  $fillOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(FillOrder $fillOrder)
    {
        $date = Carbon::parse($fillOrder->created_at)->addHours(3);
        if (count($fillOrder->fillBills) == 0 && $date > now() && $fillOrder->user_id == Auth::id()) {

            $fillOrder->fillOrderItems()->delete();
            $fillOrder->delete();

            if($fillOrder){
                return response()->json([
                    'message' => 'Error',
                ], 400);
            }

            return response()->json([
                'message' => "Fill Order deleted successfully",
            ], 200);
        }
    }
}

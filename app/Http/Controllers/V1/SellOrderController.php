<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\DistributionCenter;
use App\Models\SellOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellOrders = SellOrder::latest();

        $data = [];
        $i=0;
        foreach ($sellOrders as $sellOrder) {
            $data[$i] = [
                'sell Order'       => $sellOrder,
                'sell Order Items' => $sellOrder->sellOrderItems
            ];
            $i++;
        }

        return response()->json([
            'sell orders' => $data,
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
            'sellOrderItems' => 'required|array'
        ]);
        $user_id = Auth::id();
        $distributionCenters = DistributionCenter::all();
        $distributionCenter = $distributionCenters->where('user_id', 'like', $user_id)/*->first()*/;
        $validation['distribution_center_id'] = $distributionCenter->id;

        $sellOrder = SellOrder::create($validation);

        foreach ($validation['sellOrderItems'] as $sellOrderItem) {
            $sellOrder->sellOrderItems()->create([
                'sell_order_id' => $sellOrder->id,
                'product_id'    => $sellOrderItem['product_id'],
                'quantity'      => $sellOrderItem['quantity'],
            ]);
        }

        // checking the creation
        if ($sellOrder){
            return response()->json([
                'message' => "sell Order created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SellOrder  $sellOrder
     * @return \Illuminate\Http\Response
     */
    public function show(SellOrder $sellOrder)
    {
        if ($sellOrder){
            return response()->json([
                'Sell order'       => $sellOrder,
                'Sell Order Items' => $sellOrder->sellOrderItems
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
     * @param  \App\Models\SellOrder  $sellOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellOrder $sellOrder)
    {
        $user_id = Auth::id();
        $distributionCenters = DistributionCenter::all();
        $distributionCenter = $distributionCenters->where('user_id', 'like', $user_id)/*->first()*/;

        $date = Carbon::parse($sellOrder->created_at)->addHours(3);
        if (count($sellOrder->sellBills) == 0 && $date > now() && $sellOrder->distribution_center_id == $distributionCenter->id) {

            $validation = $request->validate([
                'description'    => 'max:50',
                'sellOrderItems' => 'required|array'
            ]);

            $sellOrder->description = $validation['description'];

            $sellOrder->sellOrderItems()->delete();

            foreach ($validation['sellOrderItems'] as $sellOrderItem) {
                $sellOrder->sellOrderItems()->create([
                    'sell_order_id' => $sellOrder->id,
                    'product_id'    => $sellOrderItem['product_id'],
                    'quantity'      => $sellOrderItem['quantity'],
                ]);
            }
            $sellOrder->save();

            if ($sellOrder){
                return response()->json([
                    'message' => "Sell Order edited successfully",
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
     * @param  \App\Models\SellOrder  $sellOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellOrder $sellOrder)
    {
        $user_id = Auth::id();
        $distributionCenters = DistributionCenter::all();
        $distributionCenter = $distributionCenters->where('user_id', 'like', $user_id)/*->first()*/;

        $date = Carbon::parse($sellOrder->created_at)->addHours(3);
        if (count($sellOrder->sellBills) == 0 && $date > now() && $sellOrder->distribution_center_id == $distributionCenter->id) {

            $sellOrder->sellOrderItems()->delete();
            $sellOrder->delete();

            if($sellOrder){
                return response()->json([
                    'message' => 'Error',
                ], 400);
            }

            return response()->json([
                'message' => "sell Order deleted successfully",
            ], 200);
        }
    }
}

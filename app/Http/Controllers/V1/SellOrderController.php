<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\DistributionCenter;
use App\Models\SellBill;
use App\Models\SellOrder;
use App\Models\User;
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
        $sellOrders = SellOrder::all();

        foreach ($sellOrders as $sellOrder) {
            $sellOrder->sellOrderItems;
        }

        return response()->json([
            'sell orders' => $sellOrders,
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
            'description'    => 'max:100',
            'sellOrderItems' => 'required|array'
        ]);
        $user_id = Auth::id();
        $distributionCenters = DistributionCenter::all();
        $distributionCenter = $distributionCenters->where('user_id', 'like', $user_id)->first();
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
            $sellOrder->sellOrderItems;

            return response()->json([
                'Sell order' => $sellOrder,
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
        $distributionCenter = $distributionCenters->where('user_id', 'like', $user_id)->first();

        $date = Carbon::parse($sellOrder->created_at)->addHours(3);
        if (count($sellOrder->sellBills) == 0 && $date > now() && $sellOrder->distribution_center_id == $distributionCenter->id) {

            $validation = $request->validate([
                'description'    => 'max:100',
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
        $distributionCenter = $distributionCenters->where('user_id', 'like', $user_id)->first();

        $date = Carbon::parse($sellOrder->created_at)->addHours(3);
        if (count($sellOrder->sellBills) == 0 && $date > now() && $sellOrder->distribution_center_id == $distributionCenter->id) {

            if($sellOrder){

                $sellOrder->sellOrderItems()->delete();
                $sellOrder->delete();

                return response()->json([
                    'message' => 'sell Order deleted successfully',
                ], 200);
            }

            return response()->json([
                'message' => "Error",
            ], 400);
        }
    }

    public function sellOrderWithNoBill()
    {
        $sellOrders = SellOrder::all();

        $a = [];
        foreach ($sellOrders as $sellOrder) {
            $s = 0;
            $sellOrder->sellOrderItems;

            foreach ($sellOrder->sellOrderItems as $sellOrderItem) {
                $sum = 0;
                foreach ($sellOrderItem->sellBillItems as $sellBillItem) {
                    $sum += $sellBillItem->quantity;
                }
                if ($sum !== $sellOrderItem->quantity){
                    $s++;
                }
            }
            if ($s !== 0) {
                array_push($a, $sellOrder);
            }
        }

        return response()->json([
            'Sell orders with no bills' => $a,
        ], 200);
    }


    public function myOrders()
    {
        $sellOrders = SellOrder::all();
        $a =[];
        foreach ($sellOrders as $sellOrder) {
            $sellOrder->sellOrderItems;
            $distCenter = $sellOrder->distributionCenter;
            $user1 = $distCenter->user;
            if($user1->id == Auth::id()) {
                array_push($a, $sellOrder);
            }
        }

        return response()->json([
            'My Sell Orders' => $a,
        ], 200);
    }

    public function orderBills(Request $request)
    {
        $orders = SellOrder::all();
        $a = [];
        foreach ($orders as $order) {
            if ($order) {
                $bills = $order->sellBills;
                if ($bills) {
                    foreach ($bills as $bill) {
                        $bill->sellBillItems;
                    }
                }
                if ($order->id == $request->id ) {
                    array_push($a, $bills);
                }
            }
        }
        return response()->json([
            'Order bills' => $bills,
        ], 200);

    }
}

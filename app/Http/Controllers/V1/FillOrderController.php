<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FillOrder;
use App\Models\User;
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
        $fillOrders = FillOrder::all();

        foreach ($fillOrders as $fillOrder) {
            $fillOrder->fillOrderItems;
        }

        return response()->json([
            'Fill orders' => $fillOrders,
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
            $fillOrder->fillOrderItems;

            return response()->json([
                'Fill order' => $fillOrder,
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
                'description'    => 'max:100',
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

            if($fillOrder){

                $fillOrder->fillOrderItems()->delete();
                $fillOrder->delete();

                return response()->json([
                    'message' => 'Fill Order deleted successfully',
                ], 200);
            }

            return response()->json([
                'message' => "Error",
            ], 400);
        }
    }

    public function fillOrderWithNoBill()
    {
        $fillOrders = FillOrder::all();

        $a = [];
        foreach ($fillOrders as $fillOrder) {
            $s = 0;
            $fillOrder->fillOrderItems;


            foreach ($fillOrder->fillOrderItems as $fillOrderItem) {
                $sum = 0;
                foreach ($fillOrderItem->fillBillItems as $fillBillItem) {
                    $sum += $fillBillItem->quantity;
                }
                if ($sum !== $fillOrderItem->quantity){
                    $s++;
                }
            }
            if ($s !== 0) {
                array_push($a, $fillOrder);
            }
        }

        return response()->json([
            'Fill orders with no bills' => $a,
        ], 200);
    }

    public function myOrders()
    {
        $fillOrders = FillOrder::all();
        $a =[];
        foreach ($fillOrders as $fillOrder) {
            $fillOrder->fillOrderItems;
            $user1 = $fillOrder->user_id;
            // $users = User::all();
            // $user1 = $users->where('id', 'like', Auth::id());
            if($user1 == Auth::id()) {
                array_push($a, $fillOrder);
            }
        }
            return response()->json([
                'My Fill Orders' => $a,
            ], 200);
    }

    public function orderBills(Request $request)
    {

        $orders = FillOrder::all();
        $a = [];
        foreach ($orders as $order) {
        if ($order) {
            $bills = $order->fillBills;
            if ($bills) {
                foreach ($bills as $bill) {
                    $bill->fillBillItems;
                }
            }
            if ($order->id == $request->id ) {
                array_push($a, $bills);
            }
        }
        return response()->json([
            'Order bills' => $bills,
        ], 200);
    }

    }
}

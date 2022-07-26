<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\SellBill;
use App\Models\SellOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellBills = SellBill::latest();

        $data = [];
        $i=0;
        foreach ($sellBills as $sellBill) {
            $data[$i] = [
                'Sell Bill'       => $sellBill,
                'Sell Bill Items' => $sellBill->sellBillItems
            ];
            $i++;
        }

        return response()->json([
            'Sell Bills' => $data,
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
            'number'   => 'required|numeric',
            'description'   => 'max:50',
            'Sell_order_id' => 'required|numeric|exists:sellOrders,id',
            'sellBillItems' => 'required|array'
        ]);
        $sellBill = SellBill::create($validation);

        foreach ($validation['sellBillItems'] as $sellBillItem) {

            $sellOrderItem = SellOrderItem::findOrFail($sellBillItem['sell_order_item_id']);
            $sbItems = $sellOrderItem->sellBillItems;
            $sum = 0;
            foreach ($sbItems as $sbItem) {
                $sum += $sbItem->quantity;
            }

            $fillOrderItem = DB::table('sell_order_items')->where('product_id', $sellOrderItem->product_id)->latest()->first();
            $fillBillItem  = $fillOrderItem->fillBillItems()->latest()->first();

            if ( ($sellBillItem['quantity'] + $sum) <= $sellOrderItem->quantity ){

                $sellBill->sellBillItems()->create([
                    'sell_bill_id'       => $sellBill->id,
                    'sell_order_item_id' => $sellBillItem['sell_order_item_id'],
                    'price'              => $fillBillItem->price,
                    'quantity'           => $sellBillItem['quantity'],
                ]);
            }
        }

        // checking the creation
        if ($sellBill){
            return response()->json([
                'message' => "sell Bill created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SellBill  $sellBill
     * @return \Illuminate\Http\Response
     */
    public function show(SellBill $sellBill)
    {
        if ($sellBill){
            return response()->json([
                'Sell Bill'       => $sellBill,
                'Sell Bill Items' => $sellBill->sellBillItems
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
     * @param  \App\Models\SellBill  $sellBill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellBill $sellBill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SellBill  $sellBill
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellBill $sellBill)
    {
        //
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FillOrderItem;
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
        $sellBills = SellBill::all();

        foreach ($sellBills as $sellBill) {
            $sellBill->sellBillItems;
        }

        return response()->json([
            'Sell Bills' => $sellBills,
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
            'description'   => 'max:100',
            'sell_order_id' => 'required|numeric|exists:sell_Orders,id',
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

            $fillOrderItem = DB::table('fill_order_items')->where('product_id', $sellOrderItem->product_id)->latest()->first();
            $id  = $fillOrderItem->id;
            $fillBillItem  = DB::table('fill_bill_items')->where('fill_order_item_id', $id)->latest()->first();

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
        if (count($sellBill->sellBillItems) == 0) {
            $sellBill->delete();
            return response()->json([
                'message' => "Error: No Items ",
            ], 400);
        }

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
            $sellBill->sellBillItems;
            return response()->json([
                'Sell Bill'       => $sellBill,
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

    public function sellBillWithNoFullBookOut()
    {
        $sellBills = SellBill::all();

        $a = [];
        foreach ($sellBills as $sellBill) {
            $s = 0;
            $sellBill->sellBillItems;
            foreach ($sellBill->sellBillItems as $sellBillItem) {
                $sum = 0;
                foreach ($sellBillItem->bookOuts as $bookOut) {
                    $sum += $bookOut->quantity;
                }
                if ($sum !== $sellBillItem->quantity){
                    $s++;
                }
            }
            if ($s !== 0) {
                array_push($a, $sellBill);
            }
        }

        return response()->json([
            'Sell Bills' => $a,
        ], 200);
    }
}

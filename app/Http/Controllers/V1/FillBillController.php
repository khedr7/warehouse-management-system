<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\FillBill;
use App\Models\FillOrderItem;
use Illuminate\Http\Request;

class FillBillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fillBills = FillBill::latest();

        $data = [];
        $i=0;
        foreach ($fillBills as $fillBill) {
            $data[$i] = [
                'Fill Bill'       => $fillBill,
                'Fill Bill Items' => $fillBill->fillBillItems
            ];
            $i++;
        }

        return response()->json([
            'Fill Bills' => $data,
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
            'description'   => 'max:50',
            'fill_order_id' => 'required|numeric|exists:fillOrders,id',
            'fillBillItems' => 'required|array'
        ]);
        $fillBill = FillBill::create($validation);

        foreach ($validation['fillBillItems'] as $fillBillItem) {

            $fillOrderItem = FillOrderItem::findOrFail($fillBillItem['fill_order_item_id']);
            $fbItems = $fillOrderItem->fillBillItems;
            $sum = 0;
            foreach ($fbItems as $fbItem) {
                $sum += $fbItem->quantity;
            }

            if ( ($fillBillItem['quantity'] + $sum) <= $fillOrderItem->quantity ){
                $fillBill->fillBillItems()->create([
                    'fill_bill_id'       => $fillBill->id,
                    'fill_order_item_id' => $fillBillItem['fill_order_item_id'],
                    'price'              => $fillBillItem['price'],
                    'quantity'           => $fillBillItem['quantity'],
                ]);
            }
        }

        // checking the creation
        if ($fillBill){
            return response()->json([
                'message' => "Fill Bill created successfully",
            ], 201);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FillBill  $fillBill
     * @return \Illuminate\Http\Response
     */
    public function show(FillBill $fillBill)
    {
        if ($fillBill){
            return response()->json([
                'Fill Bill'       => $fillBill,
                'Fill Bill Items' => $fillBill->fillBillItems
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
     * @param  \App\Models\FillBill  $fillBill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FillBill $fillBill)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FillBill  $fillBill
     * @return \Illuminate\Http\Response
     */
    public function destroy(FillBill $fillBill)
    {
        //
    }
}

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
        $fillBills = FillBill::all();


        foreach ($fillBills as $fillBill) {
            $fillBill->fillBillItems;
        }

        return response()->json([
            'Fill Bills' => $fillBills,
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
            'number'        => 'required|numeric',
            'description'   => 'max:100',
            'fill_order_id' => 'required|numeric|exists:fill_Orders,id',
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
        if (count($fillBill->fillBillItems) == 0) {
            $fillBill->delete();
            return response()->json([
                'message' => "Error: No Items ",
            ], 400);
        }

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
            $fillBill->fillBillItems;
            return response()->json([
                'Fill Bill'       => $fillBill,
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
        $validation = $request->validate([
            'number'                             => 'required|numeric',
            'description'                        => 'max:100',
            'fillBillItems'                      => 'required|array',
            'fillBillItems.*.fill_order_item_id' => 'required|numeric|exists:fill_order_items,id',
            'fillBillItems.*.quantity'           => 'required|numeric'
        ]);

        $fillBill->number = $validation['number'];
        $fillBill->description = $validation['description'];
        $fillBill->save();

        foreach ($fillBill->fillBillItems as $fillBillItem) {

            if (count($fillBillItem->bookIns) == 0) {

                $s=0;
                foreach ($validation['fillBillItems'] as $validatItem) {
                    if ($fillBillItem->fill_order_item_id == $validatItem['fill_order_item_id']) {
                        $s++;
                        $val = $validatItem;
                    }
                }
                if ($s !== 0) {

                    $fillOrderItem = FillOrderItem::findOrFail($fillBillItem->fill_order_item_id);
                    $fbItems = $fillOrderItem->fillBillItems;
                    $sum = 0;
                    foreach ($fbItems as $fbItem) {
                        $sum += $fbItem->quantity;
                    }
                    $sum -= $fillBillItem->quantity;

                    if ( ($val['quantity'] + $sum) <= $fillOrderItem->quantity ){
                        $fillBillItem->quantity = $val['quantity'];
                        $fillBillItem->price = $val['price'];
                        $fillBillItem->save();
                    }
                    else {
                        $fillBillItem->price = $val['price'];
                        $fillBillItem->save();
                    }
                }
                else {
                    $fillBillItem->delete();
                }
            }
            else {
                $s=0;
                foreach ($validation['fillBillItems'] as $validatItem) {
                    if ($fillBillItem->fill_order_item_id == $validatItem['fill_order_item_id']) {
                        $s++;
                        $val = $validatItem;
                    }
                }
                if ($s !== 0) {
                    $fillBillItem->price = $val['price'];
                    $fillBillItem->save();
                }
            }
        }
        $a=0;
        foreach ($validation['fillBillItems'] as $validatItem) {
            foreach ($fillBill->fillBillItems as $fillBillItem) {
                if ($validatItem['fill_order_item_id'] == $fillBillItem->fill_order_item_id) {
                    $a++;
                }
            }
            if ($a == 0) {
                $fillOrderItem = FillOrderItem::findOrFail($validatItem['fill_order_item_id']);
                $fbItems = $fillOrderItem->fillBillItems;
                $sum = 0;
                foreach ($fbItems as $fbItem) {
                    $sum += $fbItem->quantity;
                }

                if ( ($validatItem['quantity'] + $sum) <= $fillOrderItem->quantity ){
                    $fillBill->fillBillItems()->create([
                        'fill_bill_id'       => $fillBill->id,
                        'fill_order_item_id' => $validatItem['fill_order_item_id'],
                        'price'              => $validatItem['price'],
                        'quantity'           => $validatItem['quantity'],
                    ]);
                }
            }
        }

        if ($fillBill){
            return response()->json([
                'message' => "fill Bill edited successfully",
            ], 200);
        }
        return response()->json([
            'message' => 'Error',
        ], 400);

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

    public function fillBillWithNoFullBookIn()
    {
        $fillBills = FillBill::all();

        $a = [];
        foreach ($fillBills as $fillBill) {
            $s = 0;
            $fillBill->fillBillItems;
            foreach ($fillBill->fillBillItems as $fillBillItem) {
                $sum = 0;
                foreach ($fillBillItem->bookIns as $bookIn) {
                    $sum += $bookIn->quantity;
                }
                if ($sum !== $fillBillItem->quantity){
                    $s++;
                }
            }
            if ($s !== 0) {
                array_push($a, $fillBill);
            }
        }

        return response()->json([
            'Fill Bills' => $a,
        ], 200);
    }
}

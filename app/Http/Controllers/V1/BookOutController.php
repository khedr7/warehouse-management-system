<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BookOut;
use App\Models\SellBillItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookOutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookOuts = BookOut::latest();

        $data = [];
        $i=0;
        foreach ($bookOuts as $bookOut) {
            $sellBillItem  = $bookOut->sellBillItem;
            $sellOrderItem = $sellBillItem->fillOrderItem;

            $data[$i] = [
                'BookOut'    => $bookOut,
                'product_id' => $sellOrderItem->product_id
            ];
            $i++;
        }

        return response()->json([
            'BookOuts' => $data,
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
            'store_id'          => 'required|numeric|exists:users,id',
            'sell_bill_item_id' => 'required|numeric|exists:sellBillItems,id',
            'date'              => 'required|date',
            'quantity'          => 'required|numeric'
        ]);

        $sellBillItem = SellBillItem::findOrFail($validation['sell_bill_item_id']);
        $bookOuts = $sellBillItem->bookOuts;
        $sum = 0;
            foreach ($bookOuts as $bookOut) {
                $sum += $bookOut->quantity;
            }

        if ( ($validation['quantity'] + $sum) <= $sellBillItem->quantity ) {

            $bookOut = BookOut::create($validation);

            $storeProducts = DB::table('store_product')->where('store_id', $bookOut->store_id);

            $sellBillItem = $bookOut->sellBillItem;
            $sellOrderItem = $sellBillItem->sellOrderItem;

            $storeProducts = $storeProducts->where('product_id', 'like', $sellOrderItem->product_id);

            if ($storeProducts && $storeProducts->quantity >= $bookOut->quantity) {
                $storeProducts->quantity = $storeProducts->quantity - $bookOut->quantity;
                $storeProducts->save();

                if ($bookOut){
                    return response()->json([
                        'message' => "BookOut created successfully",
                    ], 201);
                }
            }
            else {
                return response()->json([
                    'message' => 'Error',
                ], 400);

            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookOut  $bookOut
     * @return \Illuminate\Http\Response
     */
    public function show(BookOut $bookOut)
    {
        if ($bookOut){
            $sellBillItem  = $bookOut->sellBillItem;
            $sellOrderItem = $sellBillItem->fillOrderItem;

            return response()->json([
                'Bookin'     => $bookOut,
                'product_id' => $sellOrderItem->product_id
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
     * @param  \App\Models\BookOut  $bookOut
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookOut $bookOut)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookOut  $bookOut
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookOut $bookOut)
    {
        //
    }
}

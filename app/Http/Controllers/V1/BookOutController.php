<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BookOut;
use App\Models\SellBillItem;
use App\Models\Store;
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
        $bookOuts = BookOut::all();

        foreach ($bookOuts as $bookOut) {
            $sellBillItem  = $bookOut->sellBillItem;
            $sellOrderItem = $sellBillItem->fillOrderItem;
        }

        return response()->json([
            'BookOuts' => $bookOuts,
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
            'store_id'          => 'required|numeric|exists:stores,id',
            'sell_bill_item_id' => 'required|numeric|exists:sell_Bill_Items,id',
            'date'              => 'required|date',
            'quantity'          => 'required|numeric'
        ]);

        $sellBillItem = SellBillItem::findOrFail($validation['sell_bill_item_id']);
        $bookOuts = $sellBillItem->bookOuts;
        $sum = 0;
            foreach ($bookOuts as $bOut) {
                $sum += $bOut->quantity;
            }

        if ( ($validation['quantity'] + $sum) <= $sellBillItem->quantity ) {

            $bookOut = BookOut::create($validation);

            $sellBillItem = $bookOut->sellBillItem;
            $sellOrderItem = $sellBillItem->sellOrderItem;

            $storeProducts = DB::table('store_product')->select('store_product.*')->where([
                                                                                        ['store_id'  , '=', $bookOut->store_id],
                                                                                        ['product_id', '=', $sellOrderItem->product_id]
                                                                                ])->first();


            if ($storeProducts && $storeProducts->quantity >= $bookOut->quantity) {
                DB::table('store_product')->select('store_product.*')->where([
                    ['store_id'  , '=', $bookOut->store_id],
                    ['product_id', '=', $sellOrderItem->product_id]
               ])->limit(1)
                ->update(array('quantity' => DB::raw('quantity -'. $bookOut->quantity)));

                $store = Store::findOrFail($bookOut->store_id);
                $store->current_capacity = $store->current_capacity - $bookOut->quantity;
                $store->save();

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
            $sellOrderItem = $sellBillItem->sellOrderItem;

            return response()->json([
                'Bookin'     => $bookOut,
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

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
            'book_outs'         => 'required|array',
        ]);

        $number = 0;
        foreach ($validation['book_outs'] as $book_out) {


        $sellBillItem = SellBillItem::findOrFail($book_out['sell_bill_item_id']);
        $bookOuts = $sellBillItem->bookOuts;
        $sum = 0;
            foreach ($bookOuts as $bOut) {
                $sum += $bOut->quantity;
            }

        if ( ($book_out['quantity'] + $sum) <= $sellBillItem->quantity && $sellBillItem->quantity !=0) {

            $bookOut = BookOut::create([
                'store_id'          => $book_out['store_id'],
                'sell_bill_item_id' => $book_out['sell_bill_item_id'],
                'date'              => $book_out['date'],
                'quantity'          => $book_out['quantity'],
            ]);

            $number++;

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

            }
            else {
            $bookOut->delete();
            $number--;
            }
        }

        if ($number == count($validation['book_outs'])) {
            return response()->json([
                'message' => "done",
            ], 200);
        }
        elseif ($number == 0) {
            return response()->json([
                'message' => "Error: No Items ",
            ], 400);
        }
        else {
            return response()->json([
                'message' => "uncomplited",
            ], 200);
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

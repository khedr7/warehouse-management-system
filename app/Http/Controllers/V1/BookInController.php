<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BookIn;
use App\Models\FillBillItem;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookInController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bookIns = BookIn::all();

        foreach ($bookIns as $bookIn) {
            $fillBillItem  = $bookIn->fillBillItem;
            $fillBillItem->fillOrderItem;

        }

        return response()->json([
            'BookIns' => $bookIns,
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
            'fill_bill_item_id' => 'required|numeric|exists:fill_Bill_Items,id',
            'date'              => 'required|date',
            'quantity'          => 'required|numeric'
        ]);

        $fillBillItem = FillBillItem::findOrFail($validation['fill_bill_item_id']);
        $bookIns = $fillBillItem->bookIns;
        $sum = 0;
            foreach ($bookIns as $bIn) {
                $sum += $bIn->quantity;
            }

        $store = Store::findOrFail($validation['store_id']);


        if ( ($validation['quantity'] + $sum) <= $fillBillItem->quantity && ($store->current_capacity + $validation['quantity']) <= $store->capacity) {

            $bookIn = BookIn::create($validation);

            $fillBillItem = $bookIn->fillBillItem;
            $fillOrderItem = $fillBillItem->fillOrderItem;

            $storeProducts = DB::table('store_product')->select('store_product.*')->where([
                                                                                            ['store_id'  , '=', $bookIn->store_id],
                                                                                            ['product_id', '=', $fillOrderItem->product_id]
                                                                                ])->first();



            if ($storeProducts) {
                DB::table('store_product')->select('store_product.*')->where([
                    ['store_id'  , '=', $bookIn->store_id],
                    ['product_id', '=', $fillOrderItem->product_id]
               ])->limit(1)
                ->update(array('quantity' => DB::raw('quantity +'. $bookIn->quantity)));

                $store = $bookIn->store;
                $store->current_capacity = $store->current_capacity + $bookIn->quantity;
                $store->save();



            }
            else {
                $store = $bookIn->store;
                $store->products()->attach($fillOrderItem->product_id, ['quantity' => $bookIn->quantity]);

                $store->current_capacity = $store->current_capacity + $bookIn->quantity;
                $store->save();

            }

            if ($bookIn){
                return response()->json([
                    'message' => 'book in created successfully',
                ], 201);
            }
        }

        return response()->json([
            'message' => 'Error',
        ], 400);
    }

        /**
         * Display the specified resource.
     *
     * @param  \App\Models\BookIn  $bookIn
     * @return \Illuminate\Http\Response
     */
    public function show(BookIn $bookIn)
    {
        if ($bookIn){
            $fillBillItem = $bookIn->fillBillItem;
            $fillOrderItem = $fillBillItem->fillOrderItem;

            return response()->json([
                'Bookin'     => $bookIn,
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
     * @param  \App\Models\BookIn  $bookIn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BookIn $bookIn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookIn  $bookIn
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookIn $bookIn)
    {
        //
    }
}

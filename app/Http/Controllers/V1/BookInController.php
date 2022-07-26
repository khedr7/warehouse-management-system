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
        $bookIns = BookIn::latest();

        $data = [];
        $i=0;
        foreach ($bookIns as $bookIn) {
            $fillBillItem  = $bookIn->fillBillItem;
            $fillOrderItem = $fillBillItem->fillOrderItem;

            $data[$i] = [
                'BookIn'     => $bookIn,
                'product_id' => $fillOrderItem->product_id
            ];
            $i++;
        }

        return response()->json([
            'BookIns' => $data,
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
            'store_id' => 'required|numeric|exists:users,id',
            'fill_bill_item_id' => 'required|numeric|exists:fillBillItems,id',
            'date' => 'required|date',
            'quantity' => 'required|numeric'
        ]);

        $fillBillItem = FillBillItem::findOrFail($validation['fill_bill_item_id']);
        $bookIns = $fillBillItem->bookIns;
        $sum = 0;
            foreach ($bookIns as $bookIn) {
                $sum += $bookIn->quantity;
            }

        if ( ($validation['quantity'] + $sum) <= $fillBillItem->quantity ) {

            $bookIn = BookIn::create($validation);

            $storeProducts = DB::table('store_product')->where('store_id', $bookIn->store_id);

            $fillBillItem = $bookIn->fillBillItem;
            $fillOrderItem = $fillBillItem->fillOrderItem;

            $storeProducts = $storeProducts->where('product_id', 'like', $fillOrderItem->product_id);

            if ($storeProducts) {
                $storeProducts->quantity = $storeProducts->quantity + $bookIn->quantity;
                $storeProducts->save();
            }
            else {
                $store = $bookIn->store;
                $store->products()->attach($fillOrderItem->product_id);

                $storeProducts = DB::table('store_product')->where('store_id', $bookIn->store_id);
                $storeProducts = $storeProducts->where('product_id', 'like', $fillOrderItem->product_id);

                $storeProducts->quantity = $bookIn->quantity;
                $storeProducts->save();
            }
        }

        if ($bookIn){
            return response()->json([
                'message' => "BookIn created successfully",
            ], 201);
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
                'product_id' => $fillOrderItem->product_id
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellBillItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sell_bill_id')->constrained('sell_bills')->onDelete('cascade');
            $table->foreignId('sell_order_item_id')->constrained('sell_order_items')->onDelete('cascade');
            $table->float('price');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sell_bill_items');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFillBillItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fill_bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fill_bill_id')->constrained('fill_bills')->onDelete('cascade');
            $table->foreignId('fill_order_item_id')->constrained('fill_order_items')->onDelete('cascade');
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
        Schema::dropIfExists('fill_bill_items');
    }
}

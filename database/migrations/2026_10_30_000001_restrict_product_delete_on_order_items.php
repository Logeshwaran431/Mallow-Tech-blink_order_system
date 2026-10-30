<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestrictProductDeleteOnOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * Deleting a product must not silently remove historical order lines,
     * so the cascade is replaced with a restrict.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingPurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('NroOrden');
            $table->string('ProveedorNit');
            $table->string('ProveedorName');
            $table->date('FechaOrden');
            $table->datetime('FechaEnvio');
            $table->datetime('FechaEnvioaGerencia');
            $table->datetime('FechaDevoGerencia');
            $table->text('Comments')->nullable();
            $table->boolean('Status')->default(1);
            $table->integer('user_id');
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
        Schema::dropIfExists('shipping_purchase_orders');
    }
}

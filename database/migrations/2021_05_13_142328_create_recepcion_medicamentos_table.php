<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecepcionMedicamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recepcion_medicamentos', function (Blueprint $table) {
            $table->id();
            $table->string('SumCod');
            $table->integer('NroOrden');
            $table->integer('NroEntrada');
            $table->dateTime('FechaEntrada');
            $table->string('ProveedorNit');
            $table->string('TipoProveedor');
            $table->string('NroFactura')->nullable();
            $table->string('NroRemision')->nullable();
            $table->string('Concentracion')->nullable();
            $table->integer('ForFarmaceutica')->nullable();
            $table->integer('Cantidad');
            $table->integer('TamMuestra');
            $table->string('Lote')->nullable();
            $table->date('FechaVencimiento')->nullable();
            $table->string('RegSanitario');
            $table->string('TitularRegSanitario')->nullable();
            $table->string('PresComercial')->nullable();
            $table->string('EstadoRegSan')->nullable();
            $table->string('MarcaDispositivo')->nullable();
            $table->string('ClasiRiesgo')->nullable();
            $table->string('Embalaje')->nullable();
            $table->string('InspDefecto')->nullable();
            $table->string('TipoProducto')->nullable();
            $table->string('Desviacion')->nullable();
            $table->text('ObsDesviacion')->nullable();
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
        Schema::dropIfExists('recepcion_medicamentos');
    }
}

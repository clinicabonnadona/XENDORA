<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecepcionMedicamentos extends Model
{
    //
    protected $table = 'recepcion_medicamentos';
    protected $fillable = [
        'SumCod',
        'NroOrden',
        'NroEntrada',
        'FechaEntrada',
        'ProveedorNit',
        'TipoProveedor',
        'NroFactura',
        'NroRemision',
        'Concentracion',
        'ForFarmaceutica',
        'Cantidad',
        'TamMuestra',
        'Lote',
        'FechaVencimiento',
        'RegSanitario',
        'TitularRegSanitario',
        'PresComercial',
        'EstadoRegSan',
        'MarcaDispositivo',
        'ClasiRiesgo',
        'Embalaje',
        'InspDefecto',
        'TipoProducto',
        'Desviacion',
        'ObsDesviacion',
        'user_id',
    ];

}

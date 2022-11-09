<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingPurchaseOrders extends Model
{
    //
    protected $table = 'shipping_purchase_orders';

    protected $fillable = [
        'NroOrden',
        'ProveedorNit',
        'ProveedorName',
        'FechaOrden',
        'FechaEnvio',
        'Comments',
        'Status',
        'user_id',
        'created_at',
        'updated_at',
        'FechaEnvioaGerencia',
        'FechaDevoGerencia'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class RecepcionFarmaciaExport implements FromQuery
{

    use Exportable;
    protected $initDate;
    protected $endDate;

    public function __construct(string $initDate, string $endDate)
    {
        $this->initDate = $initDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {

        $consulta = DB::connection('sqlsrv_xendora_prod')
            ->table('recepcion_medicamentos')
            ->select('recepcion_medicamentos.SumCod as CODIGO_MED')
            ->join('suministros', 'suministros.SumCod', '=', 'recepcion_medicamentos.SumCod')
            ->leftJoin('TERCEROS', 'TERCEROS.TerCod', '=', 'recepcion_medicamentos.ProveedorNit')
            ->join('users', 'users.id', '=', 'recepcion_medicamentos.user_id')
            ->whereBetween('recepcion_medicamentos.FechaEntrada', [$this->initDate, $this->endDate])->get();

        return collect($consulta);
    }
}

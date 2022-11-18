<?php

namespace App\Exports;

use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class AuditsExport implements FromQuery
{
    /**
     * @return \Illuminate\Support\Collection
     */

    use Exportable;

    public function __construct(string $initDate, string $endDate)
    {
        $this->initDate = $initDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return
            DB::connection('pgsql')
            ->table('audits')
            ->select(
                'control.id as AUDITORIA_ID',
                'control.startDate as FECHA_EJECUCION',
                'control.endDate as FECHA_FINALIZACION',
                'entity.name as ENTIDAD',
                'area.name as PROCESO',
                'audits.quantitative_fulfilment as CALIFICACION_CUANTITATIVA',
                DB::raw(
                    "
                        trim(control.description) as DESCRIPCION
                        , CASE  WHEN control.state = 0 THEN 'PROGRAMADA'
                                WHEN control.state = 1 THEN 'REALIZADA'
                                WHEN control.state = 2 THEN 'CANCELADA POR ENTIDAD'
                                WHEN control.state = 3 THEN 'CANCELADA POR OCBP'
                        END as ESTADO
                        , CASE WHEN control.state IN(2, 3) then control.reason
                            else ''
                        END as MOTIVO_CANCELACION
                        , CASE  WHEN audits.external = true THEN 'EXTERNA'
                                WHEN audits.external = false THEN 'INTERNA'
                        END as TIPO_AUDITORIA
                    "
                )
            )
            ->selectRaw('count(noncompliance.activity) as hallazgos')
            ->leftJoin('control', 'audits.controlId', '=', 'control.id')
            ->leftJoin('noncompliance', 'noncompliance.auditId', '=', 'control.id')
            ->leftJoin('entity', 'entity.id', '=', 'audits.entityId')
            ->leftJoin('area', 'area.id', '=', 'audits.areaId')
            ->whereBetween('control.startDate', [$this->initDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->groupBy('control.id', 'control.startDate', 'control.endDate', 'entity.name', 'area.name', 'audits.quantitative_fulfilment', 'control.description', 'control.state', 'audits.external')
            ->orderBy('control.startDate', 'desc')
            ->get();
    }
}

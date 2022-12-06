<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DespachosController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // ========================================================================================
    // FUNCTION TO RETURN 0 IN SPACE WITHOUT DISPATCH
    // ========================================================================================
    public function getSumDispatchSuperAcTwo(Request $request, $sumCod = '')
    {

        if ($request->ajax()) {

            if (!$sumCod) {
                return response()
                    ->json([
                        'msg' => 'Parameter Cannot Be Empty',
                        'status' => 400,
                    ], 400);
            } else {

                try {

                    $despachosAC = DB::connection('sqlsrv_hosvital')
                        ->select(
                            DB::raw(
                                "SELECT * FROM XENDORA_DESPACHOS_SUPER_ALTO_COSTO_3('$sumCod') ORDER BY PACIENTE"
                            )
                        );

                    if (count($despachosAC) > 0) {

                        $records = [];

                        foreach (json_decode(json_encode($despachosAC), true) as $item) {

                            if (!isset($records[$item['DOCUMENTO_PACIENTE']])) {

                                $records[$item['DOCUMENTO_PACIENTE']] = array(
                                    'patientName' => trim($item['PACIENTE']),
                                    'patientDocument' => trim($item['DOCUMENTO_PACIENTE']),
                                    'patientProductCode' => trim($item['COD_PROD']),
                                    'patientProduct' => trim($item['PRODUCTO']),
                                    'patientFirstDispatch' => Carbon::parse($item['FECHA_PRIMER_DESPACHO'])->format('d-m-Y'),
                                    'patientContract' => trim($item['CONTRATO']),
                                    'patientAliveStatus' => trim($item['ESTADO_PACIENTE']),
                                );

                                unset(
                                    $records[$item['DOCUMENTO_PACIENTE']]['TIPO_DOCUMENTO'],
                                    $records[$item['DOCUMENTO_PACIENTE']]['MES'],
                                    $records[$item['DOCUMENTO_PACIENTE']]['ANIO'],
                                    $records[$item['DOCUMENTO_PACIENTE']]['TOTAL_DESPACHOS'],
                                    $records[$item['DOCUMENTO_PACIENTE']]['CONTRATO'],
                                    $records[$item['DOCUMENTO_PACIENTE']]['TIPO_CONTRATO'],
                                );

                                $records[$item['DOCUMENTO_PACIENTE']]['dispatch'] = [];
                            }

                            $records[$item['DOCUMENTO_PACIENTE']]['dispatch'][] = array(
                                'month' => (int) $item['MES'],
                                'year' => (int) $item['ANIO'],
                                'quantity' => (int) $item['TOTAL_DESPACHOS'],
                                'period' => $item['ANIO'] . '-' . $item['MES'],
                                '__variant' => (Carbon::parse($item['FECHA_PRIMER_DESPACHO'])->format('Y') . "-" . Carbon::parse($item['FECHA_PRIMER_DESPACHO'])->format('m')) === ($item['ANIO'] . '-' . $item['MES']) ? 'success' : ''
                            );
                        }

                        $objectDispatch = json_decode(json_encode($records));
                        $objectMonths = json_decode(json_encode($this->monthsReturns(), true));

                        foreach ($objectMonths->months as $arrayMonth) {
                            foreach ($objectDispatch as $record) {

                                $exist = array_filter($record->dispatch, fn ($mon) => $mon->period === $arrayMonth->period);

                                if (!count($exist)) {

                                    $month = date('m', strtotime($arrayMonth->period));
                                    $record->dispatch[] = (object)[
                                        'month' => (int) $month,
                                        'year' => $arrayMonth->year,
                                        'quantity' => 0,
                                        'period' => $arrayMonth->period,
                                        '__variant' => ''
                                    ];
                                    usort($record->dispatch, fn ($a, $b) => $a->period === $b->period ? 0 : ($a->period < $b->period ? -1 : 1));
                                }
                            }
                        }

                        $objectDispatch = array_values(json_decode(json_encode($objectDispatch), true));

                        if (count($objectDispatch) > 0) {

                            return response()
                                ->json([
                                    'msg' => 'Ok',
                                    'status' => 200,
                                    'count' => count($objectDispatch),
                                    'dataPerMonth' => $this->countingByGroupArryData($sumCod),
                                    'dataPerMonthWithNewDispatch' => $this->countingByGroupArryDataWithNewDispatchs($sumCod),
                                    'data' => $objectDispatch,
                                ]);
                        } else {

                            return response()
                                ->json([
                                    'msg' => 'Empty Records Array',
                                    'status' => 204,
                                    'data' => [],
                                ]);
                        }
                    } else {

                        return response()
                            ->json([
                                'msg' => 'Empty AC Query Result',
                                'status' => 204,
                                'data' => [],
                            ]);
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        } else {

            return response()
                ->json([
                    'msg' => 'Bad Request',
                    'status' => 400,
                ], 400);
        }
    }

    // ========================================================================================
    // FUNCTION TO COUNT BY GROUPING ARRAY DATA
    // ========================================================================================
    function countingByGroupArryData($sumCod = '')
    {
        $inputArray = DB::connection('sqlsrv_hosvital')
            ->select("SELECT * FROM XENDORA_DESPACHOS_SUPER_ALTO_COSTO_3('$sumCod')");

        $objectDispatch = json_decode(json_encode($inputArray));
        $objectMonths = json_decode(json_encode($this->monthsReturns(), true));
        $interArray = [];
        $outputArray = [];

        foreach ($objectDispatch as $item) $interArray[] = [
            'year' => $item->ANIO,
            'month' => $item->MES,
            'period' => $item->ANIO . '-' . $item->MES
        ];

        foreach ($interArray as $row) {
            if (!isset($outputArray[$row['period']])) {
                $outputArray[$row['period']] = array(
                    'period' => $row['period'],
                    'month' => $row['month'],
                    'year' => $row['year'],
                    'quantity' => 0,
                );
            }
            $outputArray[$row['period']]['quantity']++;
        }
        usort($outputArray, fn ($a, $b) => $a['period'] === $b['period'] ? 0 : ($a['period'] < $b['period'] ? -1 : 1));

        return array_values($outputArray);
    }

    // ========================================================================================
    // FUNCTION TO COUNT BY GROUPING ARRAY DATA WITH NEW DISPATCHS
    // ========================================================================================
    function countingByGroupArryDataWithNewDispatchs($sumCod = '')
    {
        $inputArray = DB::connection('sqlsrv_hosvital')
            ->select("SELECT * FROM XENDORA_DESPACHOS_SUPER_ALTO_COSTO_3('$sumCod')");

        $objectDispatch = json_decode(json_encode($inputArray));
        $objectMonths = json_decode(json_encode($this->monthsReturns(), true));
        $interArray = [];
        $outputArray = [];

        foreach ($objectDispatch as $item) {
            if (Carbon::parse($item->FECHA_PRIMER_DESPACHO)->format('Y'). "-" .Carbon::parse($item->FECHA_PRIMER_DESPACHO)->format('m') === ($item->ANIO .'-'.$item->MES)) {
                $interArray[] = [
                    'year' => $item->ANIO,
                    'month' => $item->MES,
                    'period' => $item->ANIO . '-' . $item->MES
                ];
            }
        }

        foreach ($interArray as $row) {

            if (!isset($outputArray[$row['period']])) {
                $outputArray[$row['period']] = array(
                    'period' => $row['period'],
                    'month' => $row['month'],
                    'year' => $row['year'],
                    'quantity' => 0,
                );
            }
            $outputArray[$row['period']]['quantity']++;
        }
        usort($outputArray, fn ($a, $b) => $a['period'] === $b['period'] ? 0 : ($a['period'] < $b['period'] ? -1 : 1));

        return array_values($outputArray);
    }


    // ========================================================================================
    // FUNCTION TO RETURN LAST 12 MONTHS
    // ========================================================================================
    function monthsReturns()
    {
        $period = now()->startOfMonth()->subMonths(12)->monthsUntil(now()->startOfMonth());

        $data = [];
        foreach ($period as $date) {

            $data[] = [
                'month' => Carbon::parse($date)->monthName,
                'year' => Carbon::parse($date)->year,
                'position' => Carbon::parse($date)->month,
                'period' => Carbon::parse($date)->format('Y') . "-" . Carbon::parse($date)->format('m')
            ];
        }

        return array_reverse(['months' => $data]);
    }

    //
}

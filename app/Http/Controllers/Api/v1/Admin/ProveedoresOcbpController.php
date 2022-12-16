<?php

namespace App\Http\Controllers\Api\v1\admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProveedoresOcbpController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    // ========================================================================================
    // FUNCTION TO RETURN ALL DATA FROM DB
    // ========================================================================================

    public function providersOcbpQuery(Request $request, String $providerType)
    {

        /* if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ], 400); */

        try {

            $queryProviders = DB::connection('sqlsrv_hosvital')
                ->select("SELECT NIT, RAZON_SOCIAL, MES, ANIO, VALOR, TIPO_PROVEEDOR FROM XENDORA_PROVEEDORES_OCBP_V1('$providerType')");
                //->select("SELECT NIT, RAZON_SOCIAL, MES, ANIO, VALOR, TIPO_PROVEEDOR FROM XENDORA_PROVEEDORES_OCBP_V1('$providerType') WHERE NIT = '64564542'");
            //->select("SELECT NIT, RAZON_SOCIAL, FECHA_CAUSACION, MES, ANIO, VALOR, TIPO_PROVEEDOR FROM PROVEEDORES_OCBP_V1() WHERE TIPO_PROVEEDOR = 'HONORARIOS PAGOS FIJOS' AND nit = '72192832'");

            if (sizeof($queryProviders) < 0) return response()->json([
                'msg' => 'Empty Providers Query',
                'status' => 204
            ], 204);

            return $this->providersOcbpQueryGrouped($queryProviders);

            $queryProvidersResponse = $this->providersOcbpQueryGrouped($queryProviders);

            if (!$queryProvidersResponse) return response()->json([
                'msg' => 'Empty Providers Response',
                'status' => 500
            ], 500);

            return $this->providersOcbpQueryGrouped($queryProviders);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // ========================================================================================
    // FUNCTION TO RETURN ALL DATA FROM DB GROUPED BY KEY
    // ========================================================================================
    function providersOcbpQueryGrouped($providers = [])
    {
        try {

            if (!$providers) return response()->json([
                'msg' => 'Empty providers in Group Function',
                'status' => 500
            ]);

            $records = [];

            foreach (json_decode(json_encode($providers), true) as $item) {

                if (!isset($records[$item['NIT']])) {
                    $records[$item['NIT']] = [
                        'providerName' => $this->removeWhiteSpaces($item['RAZON_SOCIAL']),
                        'providerNit' => $item['NIT'],
                        'providerType' => $item['TIPO_PROVEEDOR'],
                    ];

                    unset(
                        $records[$item['NIT']]['MES'],
                        $records[$item['NIT']]['ANIO'],
                        $records[$item['NIT']]['VALOR'],
                    );

                    $records[$item['NIT']]['movement'] = [];
                }

                $records[$item['NIT']]['movement'][] = [
                    'month' => (int) $item['MES'],
                    'year' => (int) $item['ANIO'],
                    'period' => $item['ANIO'] . "-" . $item['MES'],
                    'mount' => $item['VALOR'],
                    '__variant' => ''
                ];
            }

            $objectMovement = json_decode(json_encode($records));
            $objectMonths = json_decode(json_encode($this->monthsReturns(), true));

            foreach ($objectMonths->months as $arrayMonth) {
                foreach ($objectMovement as $record) {

                    $exist = array_filter($record->movement, fn ($mon) => $mon->period === $arrayMonth->period);

                    if (!count($exist)) {
                        $month = date('m', strtotime($arrayMonth->period));

                        $record->movement[] = (object)[
                            'month' => (int) $month,
                            'year' => $arrayMonth->year,
                            'mount' => 0,
                            'period' => $arrayMonth->period,
                            '__variant' => ''
                        ];
                        usort($record->movement, fn ($a, $b) => $a->period === $b->period ? 0 : ($a->period < $b->period ? -1 : 1));
                    }
                }
            }


           $records = array_values(json_decode(json_encode($objectMovement), true));           

            if (count($records) < 0) return response()->json([
                'msg' => 'Empty Records Array',
                'status' => 204,
                'data' => [],
            ]);

            return response()
                ->json([
                    'msg' => 'Ok',
                    'status' => 200,
                    'count' => count($records),
                    'data' => $records,
                ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // ========================================================================================
    // FUNCTION TO RETURN ALL PROVIDERS TYPE
    // ========================================================================================
    public function getProvidersType()
    {

        try {

            $typeQuery = DB::connection('sqlsrv_hosvital')
                ->select("SELECT DISTINCT TIPO_PROVEEDOR, CUENTA FROM PROVEEDORES_OCBP_V1()");

            if (sizeof($typeQuery) < 0) return response()
                ->json([
                    'msg' => 'Provider Type',
                    'status' => 204
                ], 204);

            $providersType = [];
            foreach ($typeQuery as $item) $providersType[] = ['typeName' => $item->TIPO_PROVEEDOR, 'typeNum' => $item->CUENTA];

            if (sizeof($providersType) < 0) return response()
                ->json([
                    'msg' => 'Empty Provider Type Array',
                    'status' => 204
                ]);

            return response()->json([
                'msg' => 'Providers Type',
                'status' => 200,
                'data' => $providersType
            ]);

            //
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    // ========================================================================================
    // FUNCTION TO RETURN LAST 12 MONTHS
    // ========================================================================================
    function monthsReturns()
    {
        $period = now()->startOfMonth()->subMonths(11)->monthsUntil(now()->startOfMonth());

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

    // ========================================================================================
    // FUNCTION TO RETURN LAST 12 MONTHS
    // ========================================================================================
    function removeWhiteSpaces($phrase)
    {
        if (!$phrase) return;

        $phraseArray = explode(" ", $phrase);

        $removingWhiteSpace = str_replace(array_pop($phraseArray), "", $phrase);

        return $removingWhiteSpace;
    }
}

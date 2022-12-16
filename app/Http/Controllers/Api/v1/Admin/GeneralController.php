<?php

namespace App\Http\Controllers\Api\v1\Admin;

use PDF;


use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;

class GeneralController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware(['auth']);
    }


    // ========================================================================================
    // FUNCTION TO GET ALL SUM WITH BALANCE IN MAIN SUM ROTATION COMPONENT
    // ========================================================================================
    public function getSumToRotacion()
    {

        try {

            $sums = DB::connection('sqlsrv_xendora_prod')
                ->select('EXEC getSumForRotacion');

            if (sizeof($sums) < 0) return response()->json([
                'msg' => 'Empty Query Result',
                'status' => 204
            ]);

            $supplies = [];

            foreach ($sums as $item) {

                $balance = $this->getBalanceForSum($item->MSRESO);

                if ($balance === null) return response()
                    ->json([
                        'msg' => 'Cannot Get the Current Balance',
                        'status' => 204
                    ]);

                $supplies[] = [
                    'sumCod' => $item->MSRESO,
                    'sumDescription' => $item->SUMNOMC,
                    'sumCommercialDescription' => $item->SUMNOMG,
                    'sumBalance' => $balance,
                ];
            }

            if (sizeof($supplies) < 0) return response()->json([
                'msg' => 'Error Processing Supplies',
                'status' => 204
            ]);

            return response()->json([
                'msg' => 'Supplies',
                'status' => 200,
                'data' => $supplies
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getRotacion($SumCod)
    {
        $rotacion = DB::table('movimientos')
            ->where(rtrim('movimientos.MSRESO'), '=', $SumCod)
            /*->groupBy('movimientos.MSRESO', 'movimientos.SALDO', 'movimientos.ROTACION',
                                'movimientos.mes', 'movimientos.nomMES', 'movimientos.Anio', 'movimientos.fechAct')*/
            ->get();
        return response()->json($rotacion);
    }

    public function getRotacionMeses($SumCod)
    {
        $rotacion = DB::table('movimientos')
            ->where(rtrim('movimientos.MSRESO'), '=', $SumCod)
            ->groupBy(
                'movimientos.MSRESO',
                'movimientos.SALDO',
                'movimientos.ROTACION',
                'movimientos.mes',
                'movimientos.nomMES',
                'movimientos.Anio',
                'movimientos.anioMes',
                'movimientos.ultValMes',
                'movimientos.fechAct'
            )
            ->get();
        //$rotacion = DB::select('EXEC movimientosGeneralAnio "'.$SumCod.'"');
        return response()->json(json_encode($rotacion));
    }

    public function getRotacionInfo($id)
    {

        if ($id != null) {
            $rotacion = DB::table('movimientos as m')
                ->select('m.rotacion as rotacion', 'm.mes as nummes', 'm.nomMes as nommes')
                ->join('suministros as s', 'm.MSRESO', '=', 's.SumCod')
                ->where('s.id', '=', $id)
                ->get();

            if (sizeOf($rotacion) > 0) {

                $otherRotacion = DB::table('movimientos as m')
                    ->join('suministros as S', 'm.MSRESO', '=', 'S.SumCod')
                    ->where('S.id', '=', $id)
                    ->get();
                return response()->json(['rotacion' => $rotacion, 'otherRotacion' => $otherRotacion], 200);
            } else {
                return response()->json(['msg' => 'Sin Rotacion'], 204);
            }
        } else {
            return response()->json(['msg' => 'Hubo Un error'], 500);
        }
    }

    // ========================================================================================
    // FUNCTION TO GET ALL ROTATIONS DETAILS
    // ========================================================================================
    public function getRotacionMesesComplete($sumCod = null)
    {

        try {

            if (!$sumCod) return response()->json([
                'msg' => 'Parameter Cannot be Empty!',
                'status' => 500
            ], 500);

            $rotacion = DB::connection('sqlsrv_xendora_prod')
                ->table('movimientos')
                ->select('*')
                ->where(trim('movimientos.MSRESO'), $sumCod)
                ->orderBy('Anio', 'asc')
                ->orderBy('mes', 'asc')
                ->get();

            if (sizeof($rotacion) < 0) return response()->json([
                'msg' => 'Error Processing Rotations',
                'status' => 204
            ]);

            $rotations = [];
            $realBalance = $this->getBalanceForSum($sumCod);
            $realBalanceCentralM = $this->getBalanceForSumCentralM($sumCod);
            $realBalanceCrashCart = $this->getBalanceForSumCrashCarts($sumCod);
            $averageValuesSum = $this->getAverageResultForRotation($rotacion);
            $minValue = 0;
            $maxValue = 0;
            $inventoryDays = $this->getInventoryDay($rotacion);
            $stockAlert = 3;

            foreach ($rotacion as $key => $value) {


                $minValue = ($averageValuesSum / 30) * 15;
                $maxValue = ($averageValuesSum / 30) * 90;

                /** Stock Variables  */
                if ($value->saldo > $maxValue) $stockAlert = 1;
                if ($value->saldo < $minValue) $stockAlert = 0;

                $rotations[$value->MSRESO]['sumBalance'] = $realBalance;
                $rotations[$value->MSRESO]['sumBalanceCentralM'] = $realBalanceCentralM;
                $rotations[$value->MSRESO]['sumBalanceCrashCart'] = $realBalanceCrashCart;
                $rotations[$value->MSRESO]['sumLastPrice'] = $value->ultValMes;
                $rotations[$value->MSRESO]['infoUpdateDate'] = $value->fechAct;
                $rotations[$value->MSRESO]['averageValue'] = round($averageValuesSum);
                //$rotations[$value->MSRESO]['averageValueall'] = $averageValuesSum;
                $rotations[$value->MSRESO]['minValue'] = round($minValue);
                $rotations[$value->MSRESO]['maxValue'] = round($maxValue);
                $rotations[$value->MSRESO]['stockAlert'] = $stockAlert;
                $rotations[$value->MSRESO]['inventoryDays'] = $inventoryDays;
                $rotations[$value->MSRESO]['rotations'][] = [
                    'month' => (int) str_pad($value->mes, 2, '0', STR_PAD_LEFT),
                    'year' => (int) $value->Anio,
                    'quantity' => (int) $value->rotacion,
                    'period' => $value->Anio . "-" . str_pad($value->mes, 2, '0', STR_PAD_LEFT)
                ];
            }

            $objectRotations = json_decode(json_encode($rotations));
            $objectMonths = json_decode(json_encode($this->monthsReturns(), true));

            foreach ($objectMonths->months as $arrayMonth) {
                foreach ($objectRotations as $record) {

                    $exist = array_filter($record->rotations, fn ($mon) => $mon->period === $arrayMonth->period);

                    if (!count($exist)) {

                        $month = date('m', strtotime($arrayMonth->period));
                        $record->rotations[] = (object)[
                            'month' => (int) $month,
                            'year' => $arrayMonth->year,
                            'quantity' => 0,
                            'period' => $arrayMonth->period,
                        ];
                        usort($record->rotations, fn ($a, $b) => $a->period === $b->period ? 0 : ($a->period < $b->period ? -1 : 1));
                    }
                }
            }


            $objectRotations = array_values(json_decode(json_encode($objectRotations), true));

            return response()->json([
                'msg' => 'Ok',
                'status' => 200,
                'data' => $objectRotations,
                //'x' => $this->getInventoryDay($rotacion)
                //'xx' => $this->getAverageResultForRotation($rotacion)
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function divnum($numerator, $denominator)
    {
        return $denominator == 0 ? 0 : ($numerator / $denominator);
    }

    function getAverageResultForRotation($rotations)
    {

        $objectMonths = json_decode(json_encode($this->monthsReturns(), true));

        $firstMonth = ($objectMonths->months)[0];
        $lastMonth = end($objectMonths->months);

        //$arrayAveragesValues = [];
        $arrayAveragesValue = [];
        $averageValuesSumS = 0;
        $averageTotal = 0;

        foreach ($rotations as $key => $value) {
            if (($value->Anio . '-' . (int)$value->mes) != $firstMonth->period && ($value->Anio . '-' . (int) $value->mes) != $lastMonth->period) {
                if ((int) $value->rotacion > 0) {
                    $arrayAveragesValue[] = (int) $value->rotacion;
                }
            }
            //$averageValuesSum = array_sum($arrayAveragesValues);
            $averageValuesSumS = array_sum($arrayAveragesValue);
            $countMonth = count($arrayAveragesValue);
            $averageTotal = $this->divnum($averageValuesSumS, ($countMonth));
        }

        return $averageTotal;
        //return 10;

        /* return [
            'averageValuesSum' => $averageValuesSum,
            'averageValuesSumValue' => $averageValuesSumS,
            'averageMonthCount' => count($arrayAveragesValues),
            'averageTotal' => $averageTotal,
            'array_average' => $arrayAveragesValues,
            'first_month' => $firstMonth
        ]; */
    }

    function getInventoryDay($rotations)
    {

        $lastThreeMonths = null;

        if (sizeof($rotations) < 3) $lastThreeMonths = json_decode(json_encode($rotations));

        $lastThreeMonths = array_slice(json_decode(json_encode($rotations)), -2, 2, true);

        $arrayMonths = [];
        $arrayAverageValue = 0;
        $sumBalance = 0;
        $inventoryDay = 0;

        foreach ($lastThreeMonths as $key => $value) {
            $arrayMonths[] = $value->rotacion;
            $sumBalance = $value->saldo;
        }

        $arrayAverageValue = array_sum($arrayMonths);
        $inventoryDay = $this->divnum($sumBalance, $arrayAverageValue) * 30;

        return round($inventoryDay);
    }

    public function getSumEntradas($sumcod)
    {
        $entradas = DB::table('movCompras')
            ->where('COD_SUM', '=', $sumcod)
            ->where('TIPO', '=', 'C')
            ->get();

        if (sizeof($entradas) > 0) {
            return response(json_encode($entradas, true), 200, ["Content-Type" => "application/json"]);
        }
    }

    public function getSumDespachos($sumcod)
    {
        $despachos = DB::table('movCompras')
            ->where('COD_SUM', '=', $sumcod)
            ->where('TIPO', '=', 'S')
            ->get();

        if (sizeof($despachos) > 0) {
            return response(json_encode($despachos, true), 200, ["Content-Type" => "application/json"]);
        }
    }

    public function getInvoiceDetails($sumcod, $mes, $anio)
    {

        $errors = [];

        if (!$sumcod) array_push($errors, "No se obtuvo el código del Producto");
        if (!$mes) array_push($errors, "No se obtuvo el mes");
        if (!$anio) array_push($errors, "No se obtuvo el año");

        if (sizeof($errors) > 0) {
            return response()->json([
                'status' => 200,
                'message' => 'Ok',
                'data' => $errors
            ]);
        } else {

            $temp = [];

            $query = DB::connection('sqlsrv_hosvital')
                ->select(
                    DB::raw("SELECT * FROM DETALLADO_FACTURA_COMPRA('$mes', '$anio', '$sumcod')")
                );

            if (sizeof($query) > 0) {

                $records = [];

                foreach ($query as $row) {
                    $temp = array(
                        'factura' => $row->FACTURA,
                        'compras' => $row->COMPRAS,
                        'proveedor' => $row->TERCERO,
                        'fec_factura' => $row->FECHA_FACTURA,
                        'valor_factura' => $row->VALOR_EN_FACTURA,
                        'causacion' => $row->ENTNROCAU
                    );

                    $records[] = $temp;
                }

                return response()->json([
                    'status' => 200,
                    'message' => 'Ok',
                    'data' => $records
                ]);
            } else {

                return response()->json([
                    'status' => 500,
                    'message' => 'error'
                ]);
            }
        }
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

    // ========================================================================================
    // FUNCTION TO FIND THE BALANCE
    // ========================================================================================
    function getBalanceForSum($sumCod)
    {

        try {

            $balance = 0;

            if (!$sumCod) return response()
                ->json([
                    'msg' => 'Cannot Find the MSRESO Value',
                    'status' => 500
                ], 500);

            $queryBalance = DB::connection('sqlsrv_hosvital')
                ->select("SELECT SALDO FROM XENDORA_SALDO_ACTUAL_SUMINISTROS('$sumCod')");

            if (sizeof($queryBalance) < 0) return null;

            foreach ($queryBalance as $item) $balance = $item->SALDO;

            return $balance;
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    // ========================================================================================
    // FUNCTION TO FIND THE BALANCE CENTRAL MEZCLAS
    // ========================================================================================
    function getBalanceForSumCentralM($sumCod)
    {

        try {

            $balance = 0;

            if (!$sumCod) return response()
                ->json([
                    'msg' => 'Cannot Find the MSRESO Value',
                    'status' => 500
                ], 500);

            $queryBalance = DB::connection('sqlsrv_hosvital')
                ->table('KARDEX')
                ->join('MAESUM1', 'MAESUM1.MSRESO', '=', 'KARDEX.MSRESO')
                ->join('BODEGAS', 'BODEGAS.BODEGA', '=', 'KARDEX.BODEGA')
                ->select('MAESUM1.MSRESO', 'MAESUM1.MSNomG')
                ->selectRaw('CAST(sum(KARDEX.MovSUni) AS NUMERIC) AS SALDO')
                ->where('KARDEX.BODEGA', '=', 'CTME')
                ->where('MAESUM1.MSRESO', '=', $sumCod)
                ->groupBy('MAESUM1.MSRESO', 'MAESUM1.MSNomG')
                ->get();

            if (sizeof($queryBalance) < 0) return null;

            foreach ($queryBalance as $item) $balance = $item->SALDO;

            return $balance;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // ========================================================================================
    // FUNCTION TO FIND THE BALANCE CARROS DE PARO
    // ========================================================================================
    function getBalanceForSumCrashCarts($sumCod)
    {

        try {

            $balance = 0;

            if (!$sumCod) return response()
                ->json([
                    'msg' => 'Cannot Find the MSRESO Value',
                    'status' => 500
                ], 500);

            $queryBalance = DB::connection('sqlsrv_hosvital')
                ->table('KARDEX')
                ->join('MAESUM1', 'MAESUM1.MSRESO', '=', 'KARDEX.MSRESO')
                ->join('BODEGAS', 'BODEGAS.BODEGA', '=', 'KARDEX.BODEGA')
                ->select('MAESUM1.MSRESO', 'MAESUM1.MSNomG')
                ->selectRaw('CAST(sum(KARDEX.MovSUni) AS NUMERIC) AS SALDO')
                ->where('KARDEX.BODEGA', '=', 'CP18')
                ->where('MAESUM1.MSRESO', '=', $sumCod)
                ->groupBy('MAESUM1.MSRESO', 'MAESUM1.MSNomG')
                ->get();

            if (sizeof($queryBalance) < 0) return null;

            foreach ($queryBalance as $item) $balance = $item->SALDO;

            return $balance;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

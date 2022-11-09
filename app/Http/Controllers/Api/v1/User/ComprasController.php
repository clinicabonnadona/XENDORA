<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComprasController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware([
            'auth'
        ]);
    }

    // ==============================================================================
    // Function To return View
    // ==============================================================================
    public function index()
    {
        return view('shared.Compras.ComprasSeguimiento');
    }

    public function getComprasSeguimientos(Request $request, $initDate = '', $endDate = '')
    {

        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ]);

        try {

            if (!$initDate || !$endDate) return response()->json([
                'msg' => 'Parameters Cannot Be Empty',
                'status' => 400
            ]);

            //$init = $initDate . ' 00:00:00';
            $init = '2022/06/01 00:00:00';
            $end = $endDate . ' 23:59:00';

            $queryInventory = DB::connection('sqlsrv_hosvital')
                ->select("SELECT * FROM XENDORA_INFORME_ENTRADAS_GALMACEN_GRAL('$init', '$end')");

            //return $queryInventory;
            //->select("SELECT * FROM XENDORA_INFORME_ENTRADAS_GALMACEN_GRAL('2022/06/01 00:00:00', '01/07/2022 23:59:00')");

            if (sizeof($queryInventory) > 0) {
                $inventory = [];

                foreach (json_decode(json_encode($queryInventory), true) as $item) {

                    $inventory[] = [
                        'sumCode' => $item['CODIGO'],
                        'sumDescription' => $item['NOMBRE_PROD'],
                        'sumGroupCode' => (int) $item['CODIGO_GRUPO'],
                        'sumGroupDescription' => $item['DESCRIPCION_GRUPO'],
                        'sumQuantity' => (int) $item['SALDO'],
                        'sumTotalValue' => (int) $item['VALOR_TOTAL'],
                    ];
                }

                if (sizeof($inventory) > 0) {

                    return response()
                        ->json([
                            'msg' => 'Ok',
                            'status' => 200,
                            'data' => $inventory
                        ]);
                } else {

                    return response()
                        ->json([
                            'msg' => 'Inventory Array is Empty!',
                            'status' => 204,
                            'data' => []
                        ]);
                }
            } else {

                return response()->json([
                    'msg' => 'Inventory Query is Empty!',
                    'status' => 204,
                    'data' => []
                ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}

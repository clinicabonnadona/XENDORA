<?php

namespace App\Http\Controllers\Api\v1\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DevolucionesController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware([
            'auth'
        ]);
    }

    public function index()
    {
        return view('shared.reportes.devolucionesreporte');
    }

    public function totalReturns()
    {
        return view('shared.reportes.devoluciontotal');
    }

    /* -----------------------------------------------------------------------------------------------------------------
    */

    public function getReturns($initDate)
    {
        if ($initDate) {

            $query = DB::connection('sqlsrv_hosvital')
                ->select(
                    DB::raw(
                        "SELECT * FROM Devoluciones_OXIGENO() WHERE CAST(FECHA_SOLICITUD AS DATE) = '$initDate'"
                        //"SELECT * FROM Devoluciones_OXIGENO()"
                    )
                );

            if (count($query) > 0)
            {
                $records = [];

                foreach ($query as $item)
                {
                    $temp = array(
                        'reqNum' => $item->NRO_SOLICITUD,
                        'reqDate' => $item->FECHA_SOLICITUD,
                        'bodega' => $item->BODEGA_SOLICITA,
                        'sumCod' => $item->CODIGO_PRODUCTO,
                        'sumNomG' => $item->NOMBRE_PRODUCTO,
                        'quantity' => $item->CANT,
                        'docType' => $item->TP_DOCUMENTO,
                        'docNro' => $item->NRO_DOCUMENTO,
                        'pacNam' => $item->PACIENTE,
                        'invoice' => $item->FOLIO,
                        'pav' => $item->PABELLON,
                        'reqStatus' => $item->EST_SOLICITUD,
                        'approved' => $item->NIEGA_AUTORIZA
                    );

                    $records[] = $temp;
                }

                if (sizeof($records) > 0)
                {

                    return response()
                        ->json([
                            'status' => 200,
                            'msg' => 'Ok',
                            'data' => $records
                        ], 200);

                } else {

                    return response()
                        ->json([
                            'status' => 200,
                            'msg' => 'No hay datos en la respuesta',
                            'data' => []
                        ]);

                }

            } else {

                return response()
                    ->json([
                        'status' => 400,
                        'msg' => 'No hay datos en la respuesta',
                        'data' => []
                    ]);

            }

        } else {

            return response()
                ->json([
                    'status' => 400,
                    'msg' => 'Campo Fecha Vacio',
                ], 400);

        }
    }


    public function getTotalReturns($docPacNum = '11111111')
    {
        if (!$docPacNum)
        {
            return response()
                ->json([
                    'msg' => 'El parametro docPacNum no puede estar vacio',
                    'status' => 400,
                ]);
        } else {

            $query = DB::connection('sqlsrv_hosvital')
                ->select(
                    DB::raw(
                        "SELECT * FROM Devoluciones_Total('$docPacNum') WHERE FECHA_SOLICITUD > '2021-01-01' ORDER BY FECHA_SOLICITUD DESC"
                    )
                );

            if (count($query) > 0)
            {

                return response()
                    ->json([
                        'msg' => 'Ok',
                        'data' => $query,
                        'status' => 200
                    ]);

            } else {

                return response()
                    ->json([
                        'msg' => 'Ok',
                        'data' => [],
                        'status' => 200
                    ]);

            }

        }
    }

}

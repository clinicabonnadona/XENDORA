<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlucometriasController extends Controller
{
    //
    public function index() {
        return view('shared.reportes.glucometriareporte');
    }

    public function getActivoGlucometries($initDate) {
//        return $initDate.' '.$endDate;
        $query = DB::Connection('sqlsrv_hosvital')
            ->select(DB::raw("select * from procedimientos_ordenados('903883') WHERE FEC_ORDEN='$initDate'"));

        if (count($query) > 0)
        {
            $records = [];

            foreach ($query as $item){
                $temp = array(
                    'docPac' => $item->DOC,
                    'docTypePac' => $item->T_DOC,
                    'admCon' => $item->INGRESO,
                    'pacName' => $item->NOMBRE_PACIENTE,
                    'atteType' => $item->TIPO_ATENCION,
                    'pav' => $item->PABELLON,
                    'hab' => $item->CAMA,
                    'eps' => $item->EPS,
                    //'codPro' => $item->CODIGO,
                    'proName' => $item->PROCEDIMIENTO,
                    'quantity' => $item->CANTIDAD,
                    'orderDate' => $item->FEC_ORDEN
                );

                if(!empty($temp))
                {
                    $records[] = $temp;
                }
            }

            return response()
                    ->json([
                        'msg' => 'Ok',
                        'status' => 200,
                        'data' => $records
                    ], 200);

        } else {

            return response()
                    ->json([
                        'msg' => 'La Petición No Ha Devuelto Ningún Dato',
                        'status' => 200,
                        'data' => []
                    ], 200);
        }
    }

}

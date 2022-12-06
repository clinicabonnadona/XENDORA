<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Exports\RecepcionFarmaciaExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\RecepcionMedicamentos;
use Maatwebsite\Excel\Facades\Excel;

class RecepcionFarmaciaController extends Controller
{


    public function getActiveOrder(Request $request, $initDate, $endDate)
    {
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ]);

        if ($initDate && $endDate) {

            $initDate = Carbon::parse($initDate)->format('Ymd');
            $endDate = Carbon::parse($endDate)->format('Ymd');

            $query = DB::connection('sqlsrv_hosvital')
                ->select(
                    "SELECT * FROM XENDORA_ANALISIS_ENTRA_ALMACEN_V2('$initDate', '$endDate')"
                );

            if (sizeof($query) > 0) {

                $records = [];

                foreach ($query as $item) {

                    $tamMuestra = $item->CANTIDAD == 1 ? 1 : ($item->CANTIDAD >= 2 && $item->CANTIDAD <= 8 ? 2 : ($item->CANTIDAD >= 9 && $item->CANTIDAD <= 15 ? 3 : ($item->CANTIDAD >= 16 && $item->CANTIDAD <= 26 ? 5 : ($item->CANTIDAD >= 27 && $item->CANTIDAD <= 50 ? 8 : ($item->CANTIDAD >= 51 && $item->CANTIDAD <= 90 ? 13 : ($item->CANTIDAD >= 91 && $item->CANTIDAD <= 150 ? 20 : ($item->CANTIDAD >= 151 && $item->CANTIDAD <= 200 ? 13 : ($item->CANTIDAD >= 201 && $item->CANTIDAD <= 500 ? 14 : ($item->CANTIDAD >= 501 && $item->CANTIDAD <= 1200 ? 18 : ($item->CANTIDAD >= 1201 && $item->CANTIDAD <= 3200 ? 125 : ($item->CANTIDAD >= 3201 && $item->CANTIDAD <= 10000 ? 200 : ($item->CANTIDAD >= 10001 && $item->CANTIDAD <= 25000 ? 315 : ($item->CANTIDAD >= 25001 && $item->CANTIDAD <= 150000 ? 500 : ($item->CANTIDAD >= 150001 && $item->CANTIDAD <= 500000 ? 800 : ($item->CANTIDAD >= 500001 ? 1250 : '')))))))))))))));

                    $temp = array(
                        'nroOrden' => $item->NUM_ORDEN_COMPRA,
                        'nroEntrada' => $item->NUMERO_ENTRADA,
                        'fecEntrada' => $item->FECHA_ENTRADA,
                        'nitProvider' => $item->NIT_PROVEEDOR,
                        'provider' => $item->NOMBRE_PROVEEDOR,
                        'invoice' => $item->NRO_FACTURA,
                        'remission' => $item->NRO_REMISION,
                        'sum_cod' => $item->COD_PRODUCTO,
                        'sum_desc' => $item->DESC_PRODUCTO,
                        'concentration' => $item->CONCENTRACION,
                        'codPForm' => $item->COD_FORMA,
                        'pForm' => $item->FORMA_FARMACEUTICA,
                        'quantity' => (int) $item->CANTIDAD,
                        'sampleSize' => $tamMuestra,
                        'lot' => $item->LOTE,
                        'dueDate' => $item->FECHA_VENCE,
                        'riskClasi' => $item->CLASE_RIESGO,
                        'temperatura' => $item->TEMPERATURA
                    );

                    //$records[] = $temp;

                    if (!empty($temp)) {

                        $sumcod = $temp['sum_cod'];
                        $nroorden = $temp['nroOrden'];
                        $nroentrada = $temp['nroEntrada'];
                        $nrolot = $temp['lot'];
                        $fechaVence = $temp['dueDate'];
                        $cantidad = $temp['quantity'];
                        $tamMuestra = $temp['sampleSize'];

                        $query2 = DB::connection('sqlsrv')->select(
                            DB::raw(
                                "SELECT *, recepcion_medicamentos.id as receptionId, users.username as username, users.name as nombre, users.lastName as apellido
                                    FROM recepcion_medicamentos INNER JOIN users ON recepcion_medicamentos.user_id = users.id
                                    WHERE SumCod = '$sumcod' AND NroOrden = '$nroorden' AND (NroEntrada = '$nroentrada' OR Lote = '$nrolot' OR
                                            FechaVencimiento = '$fechaVence') AND Cantidad = '$cantidad' AND TamMuestra = '$tamMuestra'"
                            )
                        );

                        if (sizeof($query2) > 0) {


                            foreach ($query2 as $row) {

                                $temp2 = array(
                                    'receptionId' => $row->receptionId,
                                    'providerType' => $row->TipoProveedor,
                                    'healthRegister' => $row->RegSanitario,
                                    'healthRegisterState' => $row->EstadoRegSan,
                                    'pcomercial' => $row->PresComercial,
                                    'deviceBrand' => $row->MarcaDispositivo,
                                    'sanitaryRegHolder' => $row->TitularRegSanitario,
                                    'riskClassification' => $row->ClasiRiesgo,
                                    'packaging' => $row->Embalaje,
                                    'defectInspection' => $row->InspDefecto,
                                    'kindProduct' => $row->TipoProducto,
                                    'deviation' => $row->Desviacion,
                                    'deviationObs' => $row->ObsDesviacion,
                                    'username' => $row->username,
                                    'nameLastName' => $row->nombre . ' ' . $row->apellido,
                                    'temperatura' => $row->Temperatura,
                                    'serieDispositivo' => $row->SerieDispositivo,
                                    'vidaUtil' => $row->VidaUtil,
                                    '_rowVariant' => 'success'
                                );
                            }
                        } else {
                            $temp2 = array(
                                '_rowVariant' => ''
                            );
                        }
                    }

                    $records[] = array_merge($temp, $temp2);
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

    public function saveOrder(Request $request)
    {
        /* return response()
            ->json([
                'msg' => 'ok',
                'status' => 201,
                'data' => $request->SumCod
            ]); */
        if (!$request->ajax()) return response()->json([
            'msg' => 'Bad Request',
            'status' => 400
        ]);

        try {
            $this->validate(
                $request,
                [
                    'SumCod' => 'required',
                    'NroOrden' => 'required',
                    'NroEntrada' => 'required',
                    'FechaEntrada' => 'required',
                    'ProveedorNit' => 'required',
                    'Cantidad' => 'required',
                    'TamMuestra' => 'required',
                    'RegSanitario' => 'required',
                    'PresComercial' => 'max:200',
                    'TipoProveedor' => 'required',
                    //'EstadoRegSan' => 'required',
                    'Embalaje' => 'required',
                    'InspDefecto' => 'required',
                    'TipoProducto' => 'required',
                    'user_id' => 'required',
                ],
                [
                    'RegSanitario.required' => "Por favor registre este campo",
                    'PresComercial.required' => "Por favor registre este campo",
                    'TipoProveedor.required' => "Por favor registre este campo",
                    'Embalaje.required' => "Por favor registre este campo",
                    'InspDefecto.required' => "Por favor registre este campo",
                    'TipoProducto.required' => "Por favor registre este campo",
                ]
            );

            $entry = new RecepcionMedicamentos;
            $entry->SumCod = $request->SumCod;
            $entry->NroOrden = $request->NroOrden;
            $entry->NroEntrada = $request->NroEntrada;
            $entry->FechaEntrada = $request->FechaEntrada;
            $entry->ProveedorNit = $request->ProveedorNit;
            $entry->TipoProveedor = $request->TipoProveedor;
            $entry->NroFactura = $request->NroFactura;
            $entry->NroRemision = $request->NroRemision;
            $entry->Concentracion = $request->Concentracion;
            $entry->ForFarmaceutica = $request->CodPForm;
            $entry->Cantidad = $request->Cantidad;
            $entry->TamMuestra = $request->TamMuestra;
            $entry->Lote = $request->Lote;
            $entry->FechaVencimiento = $request->FechaVencimiento;
            $entry->RegSanitario = $request->RegSanitario;
            $entry->TitularRegSanitario = $request->ProveedorDesc;
            $entry->PresComercial = $request->PresComercial;
            $entry->EstadoRegSan = $request->EstadoRegSan;
            $entry->MarcaDispositivo = $request->MarcaDispositivo;
            $entry->ClasiRiesgo = $request->ClasiRiesgo;
            $entry->Embalaje = $request->Embalaje;
            $entry->InspDefecto = $request->InspDefecto;
            $entry->TipoProducto = $request->TipoProducto;
            $entry->Desviacion = $request->Desviacion;
            $entry->ObsDesviacion = $request->ObsDesviacion;
            $entry->Temperatura = $request->Temperatura;
            $entry->SerieDispositivo = $request->SerieDispositivo;
            $entry->VidaUtil = $request->VidaUtil;
            $entry->user_id = $request->user_id;

            if ($entry->save()) {
                return response()
                    ->json([
                        'msg' => 'ok',
                        'status' => 201,
                    ]);
            } else {
                return response()
                    ->json([
                        'msg' => 'Error Al Guardar',
                        'status' => 500,
                    ]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getReceivedOrdersReport(Request $request, $initDate, $endDate)
    {

        if (!$request->ajax()) {

            return response()
                ->json([
                    'msg' => 'Petición no Valida',
                    'status' => 400,
                ]);
        } else {

            if ($initDate && $endDate) {

                $query = DB::select(
                    DB::raw(
                        "SELECT * FROM getReceivedOrderReport('$initDate', '$endDate')"
                    )
                );

                array_chunk($query, 5000);

                if (sizeof($query) > 0) {

                    $records = [];

                    foreach ($query as $item) {

                        $temp = array(
                            'NRO_ORDEN' => $item->NRO_ORDEN,
                            'NRO_ENTRADA' => $item->NRO_ENTRADA,
                            'FECHA_ENTRADA' => $item->FECHA_ENTRADA,
                            'PROVEEDOR_RAZSOC' => $item->PROVEEDOR_RAZSOC,
                            'NIT_PROVEEDOR' => $item->NIT_PROVEEDOR,
                            'NRO_FACTURA' => $item->NRO_FACTURA,
                            'NRO_REMISION' => $item->NRO_REMISION,
                            'CODIGO_MED' => $item->CODIGO_MED,
                            'NOMBRE_GENERICO' => $item->NOMBRE_GENERICO,
                            'CANTIDAD' => $item->CANTIDAD,
                            'FECHA_VENCIMIENTO' => $item->FECHA_VENCIMIENTO,
                            'DESVIACION' => $item->DESVIACION,
                            'OBSERVACION_DESVIACION' => $item->OBSERVACION_DESVIACION,
                            'REGISTRO_SANITARIO' => $item->REGISTRO_SANITARIO,
                            'USUARIO' => $item->USUARIO,
                            'NOMBRE_USUARIO' => $item->NOMBRE_USUARIO,
                            'ESTADO_REG_SANITARIO' => $item->ESTADO_REG_SANITARIO,
                            'TITULAR_REG_SANITARIO' => $item->TITULAR_REG_SANITARIO,
                            'MARCA_DISPOSITIVO' => $item->MARCA_DISPOSITIVO,
                            'CLASIFICACION_RIESGO' => $item->CLASIFICACION_RIESGO,
                            'EMABALAJE' => $item->EMABALAJE,
                            'INSP_DEFECTO' => $item->INSP_DEFECTO,
                            'TIPO_PRODUCTO' => $item->TIPO_PRODUCTO,
                            'CONCENTRACION' => $item->CONCENTRACION,
                            'FORMA_FARMACEUTICA_COD' => $item->FORMA_FARMACEUTICA_COD,
                            'TAMAÑO_MUESTRA' => $item->TAMAÑO_MUESTRA,
                            'LOTE' => $item->LOTE,
                            'PRESENTACION_COMERCIAL' => $item->PRESENTACION_COMERCIAL,
                            'TIPO_PROVEEDOR' => $item->TIPO_PROVEEDOR,
                            'VIDA_UTIL' => $item->VIDA_UTIL,
                            'TEMPERATURA' => $item->TEMPERATURA,
                            'SERIE_DISPOSITIVO' => $item->SERIE_DISPOSITIVO,
                        );

                        $records[] = $temp;
                    }

                    return response()
                        ->json([
                            'msg' => 'Ok',
                            'status' => 200,
                            'data' => $records
                        ]);
                } else {

                    return response()
                        ->json([
                            'msg' => 'La Petición No Ha Devuelto Ningún Dato',
                            'status' => 200,
                            'data' => []
                        ], 200);
                }
            } else {

                return response()
                    ->json([
                        'msg' => 'Parametros vacios, verifique',
                        'status' => 400,
                    ]);
            }
        }
    }


    public function editingActiveOrder(Request $request, $id)
    {

        if ($request->ajax()) {


            if (!$request->receptionId) {
                return response()
                    ->json([
                        'msg' => 'Parameters RId cannot be Empty',
                        'status' => 400
                    ]);
            } else {
                try {
                    $query = RecepcionMedicamentos::findOrFail($request->receptionId);

                    if (!$query) {
                        return response()
                            ->json([
                                'msg' => 'Canno Find Any Reception With' . $request->receptionId . 'ID',
                                'status' => 204
                            ]);
                    }
                    $query->RegSanitario = $request->RegSanitario;
                    $query->TitularRegSanitario = $request->ProveedorDesc;
                    $query->PresComercial = $request->PresComercial;
                    $query->EstadoRegSan = $request->EstadoRegSan;
                    $query->MarcaDispositivo = $request->MarcaDispositivo;
                    $query->ClasiRiesgo = $request->ClasiRiesgo;
                    $query->Embalaje = $request->Embalaje;
                    $query->InspDefecto = $request->InspDefecto;
                    $query->TipoProducto = $request->TipoProducto;
                    $query->Desviacion = $request->Desviacion;
                    $query->ObsDesviacion = $request->ObsDesviacion;
                    $query->Temperatura = $request->Temperatura;
                    $query->SerieDispositivo = $request->SerieDispositivo;
                    $query->VidaUtil = $request->VidaUtil;

                    if ($query->save()) return response()->json([
                        'msg' => 'Ok',
                        'status' => 200,
                    ]);
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        } else {
            return response()
                ->json([
                    'msg' => 'Bad Request',
                    'status' => 400
                ]);
        }
    }

    public function exportReport(Request $request, $initDate = '', $endDate = '')
    {
        /* if (!$request->ajax()) return response()
            ->json([
                'msg' => 'Petición no Valida',
                'status' => 400,
            ]); */

        try {
            return Excel::download(new RecepcionFarmaciaExport($initDate, $endDate), 'reporteRecepción.xlsx');

            //
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}


/*carrera 27 # 36 - 14,
edificio empresarial suramericano, ofi. 322.*/

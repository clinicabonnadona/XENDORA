<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    //
    public function reportOne()
    {
        return view('shared.reportes.nomina1');
    }

    public function reportTwo()
    {
        return view('shared.reportes.nomina2');
    }

    public function reportThree()
    {
        return view('shared.reportes.rrhh1');
    }

    public function reportFourth()
    {
        return view('shared.reportes.rrhh2');
    }

    public function reportFifth()
    {
        return view('shared.reportes.nomina3');
    }

    public function reportPrealta()
    {
        return view('shared.reportes.prealta');
    }

    public function reportAudMedAmb()
    {
        return view('shared.reportes.audmedamb');
    }

    public function reportMarcacionesView()
    {
        return view('shared.reportes.marcacionesegresos');
    }


    public function getPrealtaData()
    {
        $prealta = DB::connection('sqlsrv_fenix')->select(
            DB::raw(
                "SELECT  DISTINCT	eg_censo.codigo CODIGO,
                        eg_censo.fecha FECHA,
                        eg_censo.ingreso INGRESO,
                        CAMA,
                        pabellon_nombre PABELLON,
                        eg_torres.piso PISO,
                        eg_torres.nombre TORRE,
                        disp PROCESO,
                        eg_estados.observacion MARCACION,
                        eg_censo.usuario MEDICO
                   FROM eg_censo
                            inner join eg_torres on eg_torres.pabellon_id=eg_censo.pabellon
                            INNER JOIN eg_estados ON eg_estados.proceso = eg_censo.disp
                   WHERE disp in( 2,3)"
            )
        );

        if (sizeof($prealta) > 0) {

            return response()->json([
                $prealta
            ], 200);
        } else {

            return response()->json([
                'No Se Encontraron Datos'
            ], 204);
        }
    }

    public function reporteNomina($fechini, $fechfin)
    {
        //$sourceOne = new \DateTime($fechini);
        $formatDateOne = Carbon::parse($fechini)->format('Ymd');
        //$sourceTwo = new \DateTime($fechfin);
        $formatDateTwo = Carbon::parse($fechfin)->format('Ymd');

        $query = DB::connection('sqlsrv_kactusprod')
            //->select("SELECT * FROM REPORT_NOMINA_TEST('$formatDateOne','$formatDateTwo') ORDER BY Cargo ASC, Total_Devengado DESC");
            ->select("SELECT * FROM REPORT_NOMINA_TEST('$formatDateOne','$formatDateTwo') ORDER BY Cargo ASC, Sueldo_Basico DESC, Neto_Pagado ASC");

        if (sizeof($query) > 0) {

            $records = [];

            foreach ($query as $item) {

                $temp = array(
                    'Cedula' => $item->Cedula,
                    'Nombres' => $item->Nombres,
                    'Apellidos' => $item->Apellidos,
                    'Cargo' => $item->Cargo,
                    'Centro_de_Costos' => $item->Centro_de_Costos,
                    'Sueldo_Basico' => $item->Sueldo_Basico,
                    'Dias_Laborados' => $item->Dias_Laborados,
                    'Dias_Incap_menor_2_dias' => $item->Dias_Incap_menor_2_dias,
                    'Dias_Incap_mayor_2_dias' => $item->Dias_Incap_mayor_2_dias,
                    'Dias_Incap_total' => $item->Dias_Incap_menor_2_dias + $item->Dias_Incap_mayor_2_dias,
                    'Dias_Incap_ARL' => $item->Dias_Incap_ARL,
                    'Dias_Lic_Mat_y_Pat' => $item->Dias_Lic_Mat_y_Pat,
                    'Dias_Lic_Remunerada' => $item->Dias_Lic_Remunerada,
                    'Dias_Lic_No_Remunerada' => $item->Dias_Lic_No_Remunerada,
                    'Dias_Vacaciones' => $item->Dias_Vacaciones,
                    'Salario' => $item->Salario,
                    'Ajuste_Salario' => $item->Ajuste_Salario,
                    'Auxilio_Alimentacion' => $item->Auxilio_Alimentacion,
                    'Permisos_remunerados' => $item->Permisos_remunerados,
                    'Reliquidacion_Horas' => $item->Reliquidacion_Horas,
                    'Ausencias' => $item->Ausencias,
                    'Suspension' => $item->Suspension,
                    'Reliquidacion' => $item->Reliquidacion,
                    'Auxilio_Transporte' => $item->Auxilio_Transporte,
                    'Recargos' => $item->Recargos,
                    'Horas_Extras' => $item->Horas_Extras,
                    'Retroactivo_Recargos' => $item->Retroactivo_Recargos,
                    'Retroactivo_Horas_Extras' => $item->Retroactivo_Horas_Extras,
                    'Incap_menor_2_dias' => $item->Incap_menor_2_dias,
                    'Incap_mayor_2_dias' => $item->Incap_mayor_2_dias,
                    'Incap_total_Valor' => $item->Incap_menor_2_dias + $item->Incap_mayor_2_dias,
                    'Incapacidad_ARL' => $item->Incapacidad_ARL,
                    'Lic_Remunerada' => $item->Lic_Remunerada,
                    'Licencia_Mat_y_Pat' => $item->Licencia_Mat_y_Pat,
                    'Vacaciones_Tiempo' => $item->Vacaciones_Tiempo,
                    'Vacaciones_Compensadas' => $item->Vacaciones_Compensadas,
                    'Intereses_Cesantias' => $item->Intereses_Cesantias,
                    'Anticipo' => $item->Anticipo,
                    'Otros_Ingresos' => $item->Otros_Ingresos,
                    'Total_Devengado' => $item->Total_Devengado,
                    'Salud' => $item->Salud,
                    'Pension' => $item->Pension,
                    'Fondo_Solidaridad' => $item->Fondo_Solidaridad,
                    'Pensiones_Voluntarias' => $item->Pensiones_Voluntarias,
                    'Retefuente' => $item->Retefuente,
                    'Mayor_Valor_Horas_Extras' => $item->Mayor_Valor_Horas_Extras,
                    'Mayor_Valor_Subsidio_Transp' => $item->Mayor_Valor_Subsidio_Transp,
                    'Mayor_Valor_Vacaciones' => $item->Mayor_Valor_Vacaciones,
                    'Permisos_No_remunerados' => $item->Permisos_No_remunerados,
                    'Descuento_Incentivos' => $item->Descuento_Incentivos,
                    'Prestamos' => $item->Prestamos,
                    'Mayor_Valor_Salario' => $item->Mayor_Valor_Salario,
                    'Libranza' => $item->Libranza,
                    'Multas_y_Huellero' => $item->Multas_y_Huellero,
                    'AMI' => $item->AMI,
                    'Casino' => $item->Casino,
                    'Celular' => $item->Celular,
                    'Colseguros' => $item->Colseguros,
                    'El_Cid' => $item->El_Cid,
                    'Embargos' => $item->Embargos,
                    'Fundacion' => $item->Fundacion,
                    'Los_Olivos' => $item->Los_Olivos,
                    'Parqueadero' => $item->Parqueadero,
                    'Poliza_Scare' => $item->Poliza_Scare,
                    'Preverriso' => $item->Preverriso,
                    'Vacuna' => $item->Vacuna,
                    'Otros_descuentos' => $item->Otros_descuentos,
                    'Total_Deducido' => $item->Total_Deducido,
                    //'Neto_Pagado' => $item->Neto_Pagado,
                    'Neto_Pagado' => $item->Total_Devengado - $item->Total_Deducido,
                );

                $records[] = $temp;
            }

            if (count($records) > 0) {

                return response()->json([
                    "msg" => 'Ok',
                    "data" => $records,
                    "status" => 200
                ], 200);
            } else {

                return response()->json([
                    "msg" => 'Empty Records Array',
                    "data" => [],
                    "status" => 200
                ], 200);
            }
        } else {

            return response()->json([
                'msg' => 'No Content'
            ], 204);
        }
    }

    public function reporteConceptosDuplicados($fecha)
    {
        //$date = new \DateTime($fecha);
        $formatdate = Carbon::parse($fecha)->format('Ymd');

        $query = DB::connection('sqlsrv_kactusprod')
            ->select("SELECT * FROM dbo.conce_duplicado('$formatdate')");

        if (sizeof($query) > 0) {

            return response()->json([
                'data' => $query
            ], 200);
        } else {

            return response()->json([
                'msg' => 'No Content'
            ], 204);
        }
    }

    public function reporteHuellero($initalDate = '', $endingDate = '', $document = '') {

        if ($initalDate == '' || $endingDate == '') {

            $initDate = Carbon::now()->format('Y-m-d');
            $endDate = Carbon::now()->format('Y-m-d');

        } else {

            if ($document != '') {

                try {

                    $initDate = Carbon::parse($initalDate)->format('Y-d-m') . ' 00:00:00';
                    $endDate = Carbon::parse($endingDate)->format('Y-d-m') . ' 23:59:59';

                    $query_biometric_marks = DB::connection('sqlsrv_biometrico')
                        ->select("SELECT * FROM EVA_DES_MARCACIONES_BY_DATE_RANGE('$initDate', '$endDate') WHERE CEDULA = '$document' ORDER BY MARCACION");

                    if (count($query_biometric_marks) > 0) {

                        $biometric_marks = [];

                        foreach ($query_biometric_marks as $biometric_mark) {

                            $marcacion = explode(' ', $biometric_mark->MARCACION);
                            $fecha = $marcacion[0];
                            $horaTemp = $marcacion[1];
                            $hora = Carbon::parse($horaTemp)->format('H:i:s');

                            $tempBiometricMarks = array(
                                'employeeDocument' => $biometric_mark->CEDULA,
                                'employeeName' => $biometric_mark->NOMBRE,
                                'employeeLastName' => $biometric_mark->APELLIDO,
                                'employeeMarkDate' => $fecha,
                                'employeeMarkHour' => $hora,
                                'employeeMarkType' => $biometric_mark->TIPO,
                            );

                            $biometric_marks[] = $tempBiometricMarks;
                        }

                        if (count($biometric_marks) > 0) {

                            return response()
                                ->json([
                                    'msg' => 'Ok',
                                    'status' => 200,
                                    'data' => $biometric_marks,
                                ]);
                        } else {

                            $biometric_marks = [];

                            return response()
                                ->json([
                                    'msg' => 'Empty biometric marks Array',
                                    'data' => [],
                                    'status' => 204
                                ]);
                        }
                    } else {

                        return response()
                            ->json([
                                'msg' => 'Empty biometric marks Query',
                                'data' => [],
                                'status' => 204
                            ]);
                    }
                } catch (\Throwable $e) {

                    throw $e;
                }

            } else {
                try {

                    $initDate = Carbon::parse($initalDate)->format('Y-d-m') . ' 00:00:00';
                    $endDate = Carbon::parse($endingDate)->format('Y-d-m') . ' 23:59:59';

                    $query_biometric_marks = DB::connection('sqlsrv_biometrico')
                        ->select("SELECT * FROM EVA_DES_MARCACIONES_BY_DATE_RANGE('$initDate', '$endDate') ORDER BY MARCACION");

                    if (count($query_biometric_marks) > 0) {

                        $biometric_marks = [];

                        foreach ($query_biometric_marks as $biometric_mark) {

                            $marcacion = explode(' ', $biometric_mark->MARCACION);
                            $fecha = $marcacion[0];
                            $horaTemp = $marcacion[1];
                            $hora = Carbon::parse($horaTemp)->format('H:i:s');

                            $tempBiometricMarks = array(
                                'employeeDocument' => $biometric_mark->CEDULA,
                                'employeeName' => $biometric_mark->NOMBRE,
                                'employeeLastName' => $biometric_mark->APELLIDO,
                                'employeeMarkDate' => $fecha,
                                'employeeMarkHour' => $hora,
                                'employeeMarkType' => $biometric_mark->TIPO,
                            );

                            $biometric_marks[] = $tempBiometricMarks;
                        }

                        if (count($biometric_marks) > 0) {

                            return response()
                                ->json([
                                    'msg' => 'Ok',
                                    'status' => 200,
                                    'data' => $biometric_marks,
                                ]);
                        } else {

                            $biometric_marks = [];

                            return response()
                                ->json([
                                    'msg' => 'Empty biometric marks Array',
                                    'data' => [],
                                    'status' => 204
                                ]);
                        }
                    } else {

                        return response()
                            ->json([
                                'msg' => 'Empty biometric marks Query',
                                'data' => [],
                                'status' => 204
                            ]);
                    }
                } catch (\Throwable $e) {

                    throw $e;
                }
            }


        }
    }

    public function reporteCumpleanios($mes = 0)
    {
        if ($mes) {

            $query = DB::connection('sqlsrv_kactusprod')
                ->select("select * from PROG_CUMPLE('$mes') ORDER BY NOMBRES ASC");

            if (sizeof($query) > 0) {

                $records = [];

                return response()->json([
                    'msg' => 'Ok',
                    'data' => $query,
                    'status' => 200
                ], 200);
            } else {

                return response()->json([
                    'msg' => 'No Content'
                ], 204);
            }
        }
    }

    public function reportePermisosAprobados($fechini, $fechfin)
    {
        $sourceOne = new \DateTime($fechini);
        $formatDateOne = $sourceOne->format('d/m/Y');
        $sourceTwo = new \DateTime($fechfin);
        $formatDateTwo = $sourceTwo->format('d/m/Y');

        $query = DB::connection('sqlsrv_kactusprod')
            ->select("SELECT * FROM SOLI_PERMI_APROBADOS('$formatDateOne','$formatDateTwo')");

        if (sizeof($query) > 0) {
            return response()->json([
                $query
            ], 200);
        } else {

            return response()->json([
                'msg' => 'No Content'
            ], 204);
        }
    }


    public function getCensoReal()
    {
        $censo = DB::connection('sqlsrv_hosvital')
            ->select("SELECT * FROM CENSOREAL() ORDER BY PABELLON ASC, CAMA ASC");

        //$info = array();

        if (sizeof($censo) > 0) {

            return response()->json($censo);
        } else {

            return response()->json([
                'msg' => 'No Content'
            ], 204);
        }
    }


    public function getAuditoriaMedAmb($doc_pac)
    {
        $query = DB::connection('sqlsrv_hosvital')
            ->select("SELECT * FROM AUD_DESPACHOS_AMBU('$doc_pac')");

        if (sizeof($query) > 0) {

            return response()->json($query);
        } else {

            return response()->json([
                'msg' => 'No Content'
            ], 204);
        }
    }


    public function getMarcacionesEgresos($docPac)
    {

        //return $docPac;

        $query = DB::connection('sqlsrv_fenix')
            ->select(DB::raw(
                "SELECT	eg_historicocenso.id,
                                fecha,
                                fecha_ing,
                                hora,
                                codigo,
                                cama,
                                eg_historicocenso.proceso,
                                usu_id,
                                eg_historicocenso.estado,
                                eg_historicocenso.ingreso,
                                eg_historicocenso.disp,
                                eg_estados.observacion,
                                eg_historicocenso.usuario_proceso
                        FROM eg_historicocenso
                            INNER JOIN eg_estados ON eg_historicocenso.disp = eg_estados.proceso
                        WHERE codigo = '$docPac' AND eg_historicocenso.ingreso =	(
                                                                                        SELECT MAX(egi.ingreso)
                                                                                        FROM eg_historicocenso egi
                                                                                        WHERE egi.codigo = eg_historicocenso.codigo
                                                                                    )"
            ));

        if (sizeof($query) > 0) {

            return response()->json([
                $query
            ], 200);
        } else {

            return response()->json([
                'msg' => 'No Content'
            ], 204);
        }
    }
}
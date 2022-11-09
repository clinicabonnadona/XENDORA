<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramacionCitasController extends Controller
{
    //

    public function sendDataInJsonToApiDoxy($fechaini, $fechafin)
    {
        try {
            $initialQuery = DB::connection('sqlsrv_hosvital')
                ->select(
                    DB::raw(
                        "select top (5) * from citas_reservadas_teleconsulta_casc('$fechaini', '$fechafin') WHERE TIPO_CITA = 'TELECONSULTA' ORDER BY HORA_CITA ASC"
                    )
                );

            $appointments = [];
            $correoIn = '';
            //$url = 'http://134.209.38.42:3002/api/v1/app/create-appointment';

            if (sizeof($initialQuery) > 0)
            {
                foreach ($initialQuery as $appointment)
                {
                    $splitFullName = explode(' ', $appointment->PACIENTE);
                    $splitName = $splitFullName[0];
                    $splitLastName = $splitFullName[1];
                    $dateInCorrectFormat = Carbon::parse($appointment->FECHA_CITA)->format('d/m/Y');
                    $timeInCorrectFormat = Carbon::parse($appointment->HORA_CITA)->format('h:ma');

                    if ($appointment->E_MAIL == 'NA')
                    {
                        $correoIn = 'correosparticulares@organizacioncbp.org';
                    } else {
                        $correoIn = $appointment->E_MAIL;
                    }

                    $temp = array(
                        'date' => $dateInCorrectFormat.' '.$timeInCorrectFormat,
                        'patientName' => $splitName,
                        'patientLastName' => $splitLastName,
                        'patientEmail' => $correoIn,
                        'patientID' => $appointment->DOCUMENTO,
                        'patientPhone' => $appointment->Telefono,
                        'doctorEmail' => 'doctor@clinica.com',
                        'room' => "1",
                        'customID' => $appointment->NUMERO_CITA
                    );

                    $appointments[] = $temp;

                    $client = new \GuzzleHttp\Client();

                    /*$response = $client->request('POST', 'http://134.209.38.42:3002/api/v1/app/create-appointment', [
                        'headers' => ['X-API-Key' => 'nLVM3Tc2Tw0GLwFJHSzv'],
                        'form_params' => $temp
                    ]);*/

                    /*$response = $client->post(
                        'http://134.209.38.42:3002/api/v1/app/create-appointment',
                        [
                            'connect_timeout' => 10,
                            'headers' => [
                                //'Content-Type' => 'application/json',
                                'X-API-Key' => 'nLVM3Tc2Tw0GLwFJHSzv'
                            ],
                            'form_params' => $temp
                        ],
                    );*/

                }

                /*$body = $response->getBody();
                $status = $response->getStatusCode();

                if ($status == 200)
                {
                    return 'ok';
                }*/

            }
        } catch(ClientException $ce){
            $status = 'false';
            $message = $ce->getMessage();
            $data = [];
        }catch(RequestException $re){
            $status = 'false';
            $message = $re->getMessage();
            $data = [];
        }catch(Exception $e){
            $this->status = 'false';
            $this->message = $e->getMessage();
            $data = [];
        }

        return $appointments;
    }
}

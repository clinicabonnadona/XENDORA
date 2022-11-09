<?php

use App\User;
use App\FORMAFAR;
use App\FORMAPRE;
use App\TERCEROS;
use App\ACUCORLAB;
use App\Movimiento;
use App\SUMINISTROS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', '/login');

Route::get('/home', "HomeController@RedirectToSomeWhere");
/*
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');

});*/

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {

    Route::group([
        'prefix' => 'admin',
        'as' => 'admin.',
        'namespace' => 'Admin',
        'middleware' => ['role:super-admin|lya'],
    ], function () {
        Route::get('/', 'HomeController@index')->name('home');

        /* Admin Masters Route */
        Route::group(['middleware' => ['permission:users-access|roles-access|permissions-access']], function () {
            Route::get('users', 'HomeController@userIndex')->name('users.index');
            Route::get('roles', 'HomeController@rolesIndex')->name('roles.index');
            Route::get('permisos', 'HomeController@permissionsIndex')->name('permissions.index');
        });

        Route::get('suministros', 'HomeController@suministrosIndex')->name('suministros.index');
        Route::get('terceros', 'HomeController@tercerosIndex')->name('terceros.index');
    });

    Route::group([
        'prefix' => 'user',
        'as' => 'user.',
        'namespace' => 'User',
    ], function () {
        Route::get('/', 'HomeController@index')->name('home');
    });

    Route::get('censodiario', 'Api\v1\Admin\GeneralController@censoDiario');
    Route::get('rotaciones', 'HomeController@rotacionesIndex')->name('rotaciones.index');
    Route::get('farmacia/medicamentos/recepcion', 'HomeController@farmaciaRecepcion')->name('farmacia.recepcion');
    Route::get('farmacia/active-orders-to-shipping', 'Api\v1\User\ShippingPurchaseOrdersController@index')->name('farmacia.envio.ordenes');
    Route::get('orientacionusuario/family-income-record', 'Api\v1\User\UserOrientationController@index')->name('ousuario.family-income');
    Route::get('compras/seguimiento-compras', 'Api\v1\User\ComprasController@index')->name('compras.seguimiento-compras');

    //Rutas para la vista de los Reportes
    Route::get('/reportes/report1', 'ReportesController@reportOne')->name('reportes.nomina1');
    Route::get('/reportes/report2', 'ReportesController@reportTwo')->name('reportes.nomina2');
    Route::get('/reportes/report5', 'ReportesController@reportFifth')->name('reportes.nomina3');
    Route::get('/reportes/report3', 'ReportesController@reportThree')->name('reportes.rrhh1');
    Route::get('/reportes/report4', 'ReportesController@reportFourth')->name('reportes.rrhh2');
    Route::get('/reportes/prealta', 'ReportesController@reportPrealta')->name('reportes.prealta');
    Route::get('/reportes/get-auditoria-med-amb', 'ReportesController@reportAudMedAmb')->name('reportes.audmedamb');
    Route::get(
        '/reportes/marcaciones-egresos/validacion',
        'ReportesController@reportMarcacionesView'
    )->name('reportes.marcacionesegresos');


    /*  REPORTES
    */
    // GLucometrias
    Route::get('/reportes/get-gluco/view/', 'Api\v1\User\GlucometriasController@index')->name('reportes.glucometrias');

    // Devoluciones
    Route::get('/reportes/get-returns/view', 'Api\v1\User\DevolucionesController@index')->name('reportes.devoluciones');

    Route::get('/reportes/get-total-returns/view', 'Api\v1\User\DevolucionesController@totalReturns')->name('reportes.devolucionestotal');
    // Seg. Evoluciones Fact.
    Route::get('/reportes/seg-evolutions-fact/view', 'Api\v1\User\SeguimientoEvolucionesFact@index')->name('reportes.segEvolucionesFact');

    // Providers Evaluations
    Route::get('/farmacia/providers-evaluations/view', 'Api\v1\User\ProviderEvaluationController@index')->name('farmacia.providersEvaluation');
});

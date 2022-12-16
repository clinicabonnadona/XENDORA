<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmaciaReportesController extends Controller
{
    // Construct
    public function __construct()
    {
        $this->middleware([
            'auth'
        ]);
    }
    
    public function index()
    {
        return view('shared.reportes.hygeainventario');
    }

    public function reporteHygeaInventario(Request $request, $wharehouse = '')
    {

        if ($request->ajax()) return response()
        ->json([
            'msg' => 'Bad Request',
            'status' => 500
        ]);

        if (!$wharehouse) return response()
        ->json([
            'msg' => 'Parameters Cannot Be Empty!',
            'status' => 400
        ]);

        return $this->queryHygeaInventory('FARMACIA CENTRAL 02');


    }

    function queryHygeaInventory($wharehouse = '')
    {
        $query = DB::connection('pgsql')
                ->table('farmacia_shelfs_products_counting')
                ->join('farmacia_product', 'farmacia_product.id', '=', 'farmacia_shelfs_products_counting.productId')
                ->join('farmacia_shelfs', 'farmacia_shelfs.id', '=', 'farmacia_shelfs_products_counting.shelfId')
                ->join('users', 'users.id', '=', 'farmacia_shelfs_products_counting.userId')
                ->join('farmacia_warehouse', 'farmacia_warehouse.id', '=', 'farmacia_shelfs.warehouseId')
                ->select('farmacia_warehouse.whDescription' 
                            , 'farmacia_shelfs.name', 'farmacia_product.code'
                            , 'farmacia_product.name',
                            DB::raw(
                                "
                                (
                                    SELECT 'fspc4.countedQuantity'
                                    FROM farmacia_shelfs_products_counting fspc4
                                    WHERE 
                                            'fspc4.productId' = 'farmacia_shelfs_products_counting.productId'
                                            and 'fspc4.shelfId' = 'farmacia_shelfs_products_counting.shelfId'
                                            and 'fspc4.createdAt' =	(	select min('fspc5.createdAt')
                                                                            from farmacia_shelfs_products_counting fspc5
                                                                            where 'fspc5.shelfId' = 'fspc4.shelfId'
                                                                            limit 1
                                                                    )
                                    limit 1
                                    ) AS CONTEO_1
                                "
                            )
                        )
                ->where('farmacia_warehouse.whDescription', $wharehouse)
                ->get();

        return $query;

    }

}

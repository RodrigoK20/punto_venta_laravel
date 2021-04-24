<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $comprasmes = DB::select('SELECT monthname(c.purchase_date) as mes, SUM(c.total) as totalmes FROM purchases c WHERE
        c.status = "VALID" GROUP BY monthname(c.purchase_date) ORDER BY month(c.purchase_date) desc limit 12');

        $ventasmes = DB::select('SELECT monthname(v.sale_date) as mes, SUM(v.total) as totalmes FROM sales v WHERE
        v.status = "VALID" GROUP BY monthname(v.sale_date) ORDER BY month(v.sale_date) desc limit 12');

        $ventasdia = DB::select('SELECT DATE_FORMAT(v.sale_date, "%d/%m/%Y") as dia, SUM(v.total) as totaldia FROM sales v
        WHERE v.status="VALID" GROUP BY v.sale_date ORDER BY day(v.sale_date) desc limit 15');

        $totales = DB::select('SELECT (SELECT ifnull(sum(c.total),0) FROM purchases c WHERE DATE(c.purchase_date) = curdate() AND c.status="VALID") as totalcompra, 
        (SELECT ifnull(sum(v.total),0) FROM sales v WHERE DATE(v.sale_date)=curdate() AND v.status="VALID") as totalventa');

        //Productos mas vendidos
        $productosvendidos = DB::select('SELECT p.code as code, SUM(dv.quantity) as quantity, p.name as name, p.id as id, p.stock as stock FROM products p
        INNER JOIN sale_details dv ON p.id = dv.product_id INNER JOIN sales v ON dv.sale_id = v.id WHERE v.status = "VALID" AND YEAR(v.sale_date)= YEAR(curdate()) 
        GROUP BY p.code, p.name, p.id, p.stock ORDER BY SUM(dv.quantity) DESC LIMIT 10');


        return view('home', compact('comprasmes','ventasmes','ventasdia','totales','productosvendidos'));
    }
}

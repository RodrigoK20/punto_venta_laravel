<?php

namespace App\Http\Controllers;

use App\Sale;
use App\Client;
use App\Product;
use App\User;
use App\Business;
use Illuminate\Http\Request;

use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


use Barryvdh\DomPDF\Facade as PDF;

class SaleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:sales.create')->only(['create','store']);
        $this->middleware('can:sales.index')->only(['index']);
        $this->middleware('can:sales.show')->only(['show']);

        $this->middleware('can:change.status.sales')->only(['change_status']);
        $this->middleware('can:sales.pdf')->only(['pdf']);
       
    }

   public function index()
    {
      
        $sales = Sale::get();
        return view('admin.sale.index', compact('sales'));

    }

 
    public function create()
    {
        $sales = Sale::get();
        $clients = Client::get();
        $products = Product::where('status','=','ACTIVE')->get();
        return view('admin.sale.create', compact('sales','clients','products'));

    }

  
    public function store(StoreRequest $request)
    {
        $sale = Sale::create($request->all()+[
            'user_id'=>Auth::user()->id,
            'sale_date'=>Carbon::now('America/El_Salvador'),
        ]);
      
        //Detalle VENTA
        foreach($request->product_id as $key =>$product){
            $results[] = array("product_id"=>$request->product_id[$key], "quantity"=>$request->quantity[$key],
            "price"=>$request->price[$key],"discount"=>$request->discount[$key]);

        }
                //Nombre relacion puesto en el modelo
        $sale->saleDetails()->createMany($results);

        return redirect()->route('sales.index');
    }

 
    public function show(Sale $sale)
    {
        
        //Acceder al detalle de la compras segun RELACION
        $saleDetails = $sale->saleDetails;

        //Subtotal sin impuesto
        $subtotal = 0;
        
        //Sub
        foreach ($saleDetails as $saleDetail) {
                $subtotal+= $saleDetail->quantity * $saleDetail->price - (
                    $saleDetail->quantity * $saleDetail->price  * $saleDetail->discount/100);
        }


        return view('admin.sale.show', compact('sale','saleDetails','subtotal'));
    }

    
 
    public function edit(Sale $sale)
    {
        
        $sale = Sale::get();
        return view('admin.purchase.show', compact('sale'));
    }

    public function update(UpdateRequest $request, Sale $sale)
    {
       // $purchase->update($request->all());
        //return redirect()->route('categories.index');
    }

  
    public function destroy(Sale $sale)
    {
        //$purchase->delete();
        //return redirect()->route('categories.index');
    }

    public function pdf(Sale $sale)
    {
        
       
        //Acceder al detalle de la compras segun RELACION
        $saleDetails = $sale->saleDetails;

        //Subtotal sin impuesto
        $subtotal = 0;
        
        //Sub
        foreach ($saleDetails as $saleDetail) {
                $subtotal+= $saleDetail->quantity * $saleDetail->price - (
                    $saleDetail->quantity * $saleDetail->price  * $saleDetail->discount/100);
        }

        $pdf = PDF::loadView('admin.sale.pdf', compact('subtotal','saleDetails','sale'));
        return $pdf->download('Reporte_de_venta'.$sale->id.'.pdf');
    }

    public function pdf_boleta(Sale $sale)
    {
        $imagen_anulado = "anulado.png";

        //Acceder al detalle de la compras segun RELACION
        $saleDetails = $sale->saleDetails;

        //Subtotal sin impuesto
        $subtotal = 0;
        
        //Sub
        foreach ($saleDetails as $saleDetail) {
                $subtotal+= $saleDetail->quantity * $saleDetail->price - (
                    $saleDetail->quantity * $saleDetail->price  * $saleDetail->discount/100);
        }

        //Datos Empresa
        $business = Business::where('id',1)->firstOrFail();

        //Datos cliente
        $client = Client::where('id',$sale->client_id)->firstOrFail();

        //Datos vendedor
        $user = User::where('id', $sale->user_id)->firstOrFail();

        $pdf = PDF::loadView('admin.sale.boleta', compact('subtotal','saleDetails','sale','business','client','user','imagen_anulado'));
        return $pdf->download('Boleta_de_venta_'.$client->name.'.pdf');
    }

    
    public function change_status(Sale $sale){
        
        if ($sale->status =='VALID') {
            $sale->update(['status'=>'CANCELED']);
            return redirect()->back();
        }

        else{
            $sale->update(['status'=>'VALID']);
            return redirect()->back();
        }
    }
}


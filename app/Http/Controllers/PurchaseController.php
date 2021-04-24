<?php

namespace App\Http\Controllers;

use App\Purchase;
use App\Provider;
use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\PurchaseDetails;

use App\Http\Requests\Purchase\StoreRequest;
use App\Http\Requests\Purchase\UpdateRequest;

use Barryvdh\DomPDF\Facade as PDF;
use Dompdf;


class PurchaseController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:purchases.create')->only(['create','store']);
        $this->middleware('can:purchases.index')->only(['index']);
        $this->middleware('can:purchases.show')->only(['show']);

        $this->middleware('can:change.status.purchases')->only(['change_status']);
        $this->middleware('can:purchases.pdf')->only(['pdf']);
        $this->middleware('can:upload.purchases')->only(['upload']);
    }
    
    public function index()
    {
        
        $purchases = Purchase::get();
        return view('admin.purchase.index', compact('purchases'));

    }

 
    public function create()
    {   
        $providers = Provider::get();
        $products = Product::get();
        return view('admin.purchase.create', compact('providers','products'));
    }

  
    
  
    public function store(StoreRequest $request)
    {
        $purchase = Purchase::create($request->all()+[
            'user_id'=>Auth::user()->id,
            'purchase_date'=>Carbon::now('America/El_Salvador'),
        ]);

        //Detalle compra FORMA 1
 /*     $product_id = $request->product_id;
        $quantity = $request->quantity;
        $price = $request->price;

        $count = 0;
        while($count < count($product_id)){
            $details = new PurchaseDetails();
            $details->purchase_id = $purchase->id;
            $details->product_id = $product_id[$count];
            $details->quantity = $quantity[$count];
            $details->price = $price[$count];
            $details->save();
            $count = $count+1;
        } */

        //Detalle compra FORMA 2
        foreach($request->product_id as $key =>$product){
            $results[] = array("product_id"=>$request->product_id[$key],
            "quantity"=>$request->quantity[$key],"price"=>$request->price[$key]);

        }
                    //nombre relacion del modelo
        $purchase->purchaseDetails()->createMany($results);

        return redirect()->route('purchases.index');
    }

 
    public function show(Purchase $purchase)
    {

        //Acceder al detalle de la compras segun RELACION
        $purchaseDetails = $purchase->purchaseDetails;

        //Subtotal sin impuesto
        $subtotal = 0;
        
        //Sub
        foreach ($purchaseDetails as $purchaseDetail) {
                $subtotal+= $purchaseDetail->quantity * $purchaseDetail->price;
        }

        //dd($purchaseDetails);

        return view('admin.purchase.show', compact('purchase', 'purchaseDetails', 'subtotal'));
    }

    
 
    public function edit(Purchase $purchase)
    {
        
        $purchase = Purchase::get();
        return view('admin.purchase.show', compact('purchase'));
    }

    public function update(UpdateRequest $request, Purchase $purchase)
    {
       // $purchase->update($request->all());
        //return redirect()->route('categories.index');
    }

  
    public function destroy(Purchase $purchase)
    {
        //$purchase->delete();
        //return redirect()->route('categories.index');
    }

     
    public function pdf(Purchase $purchase)
    {
        
        //Acceder al detalle de la compras segun RELACION
        $purchaseDetails = $purchase->purchaseDetails;

        //Subtotal sin impuesto
        $subtotal = 0;
        
        //Sub
        foreach ($purchaseDetails as $purchaseDetail) {
                $subtotal+= $purchaseDetail->quantity * $purchaseDetail->price;
        }

        $pdf = PDF::loadView('admin.purchase.pdf', compact('subtotal','purchaseDetails','purchase'));
        return $pdf->download('Reporte_de_compra'.$purchase->id.'.pdf');
    }

    public function upload(Request $request, Purchase $purchase)
    {
       // $purchase->update($request->all());
        //return redirect()->route('categories.index');
    }

    public function change_status(Purchase $purchase){
                
        if ($purchase->status =='VALID') {
            $purchase->update(['status'=>'CANCELED']);
            return redirect()->back();
        }

        else{
            $purchase->update(['status'=>'VALID']);
            return redirect()->back();
        }

    }
}

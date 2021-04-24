<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Provider;
use Illuminate\Http\Request;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;

USE Picquer;

use Barryvdh\DomPDF\Facade as PDF;



class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:products.create')->only(['create','store']);
        $this->middleware('can:products.index')->only(['index']);
        $this->middleware('can:products.edit')->only(['edit','update']);
        $this->middleware('can:products.show')->only(['show']);
        $this->middleware('can:products.destroy')->only(['destroy']);

        $this->middleware('can:change.status.products')->only(['change_status']);
        

    }
 
    public function index()
    {
        //Listado de Productos
        $products = Product::get();
        return view('admin.product.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::get();
        $providers = Provider::get();
        return view('admin.product.create', compact('categories','providers'));
    }

  
    public function store(StoreRequest $request)
    {

        //Imagen
         if($request->hasFile('picture')){
             $file = $request->file('picture');
             $image_name = time().'_'.$file->getClientOriginalName();
             $file->move(public_path("/image"),$image_name);
         

        $product = Product::create($request->all()+[
            'image'=> $image_name,
        ]);
        
    }

    else {
        
        if($request->hasFile('picture')==null){
            $product = Product::create($request->all());
        }
    
        if($request->code == ""){
            //Guardar codigo de producto, tomando el ID del prod
            $numero = $product->id;
            $numeroConCeros = str_pad($numero, 8, "0", STR_PAD_LEFT);
           
            $product->update(['code'=>$numeroConCeros]);
        }

    }
      
        return redirect()->route('products.index');
    }


   
    public function show(Product $product)
    {
        //Detalle producto
        return view('admin.product.show', compact('product'));
    }

    
    public function edit(Product $product)
    {
        $categories = Category::get();
        $providers = Provider::get();

        return view('admin.product.edit', compact('product', 'categories','providers'));
    }


    public function update(UpdateRequest $request, Product $product)
    {
          //Imagen fotografia
          if($request->hasFile('picture')){
            $file = $request->file('picture');
            $image_name = time().'_'.$file->getClientOriginalName();
            $file->move(public_path("/image"),$image_name);
        
       
        $product->update($request->all() +[
            'image'=> $image_name,
        ]);
        
    }

    else {
        if($request->hasFile('picture')==null){
            $product->update($request->all());
        }
    
        if($request->code == ""){
            //Guardar codigo de producto, tomando el ID del prod
            $numero = $product->id;
            $numeroConCeros = str_pad($numero, 8, "0", STR_PAD_LEFT);
           
            $product->update(['code'=>$numeroConCeros]);
        }

        
    }
        return redirect()->route('products.index');

    }

  
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index');
    }

    public function change_status(Product $product){
        
        if ($product->status =='ACTIVE') {
            $product->update(['status'=>'DEACTIVATED']);
            return redirect()->back();
        }

        else{
            $product->update(['status'=>'ACTIVE']);
            return redirect()->back();
        }

    }
    
    public function get_products_by_barcode(Request $request){

        if($request->ajax()){
            $products = Product::where('code', $request->code)->firstOrFail();
           
            return response()->json($products);
        }
     
        
    }
    public function get_products_by_id(Request $request){
        if ($request->ajax()) {
            $products = Product::findOrFail($request->product_id);
            return response()->json($products);
        }

       
    }

    //Mostrar codigos prod
    public function get_bar_code()
    {
        //Listado de Productos
        $products = Product::get();
    
        $pdf = PDF::loadView('admin.product.barcode', compact('products'));
        return $pdf->download('Code_products'.'.pdf');
    }

}

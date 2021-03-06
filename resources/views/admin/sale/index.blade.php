@extends('layouts.admin')

<style type="text/css">
  .icon_eye{
    padding-top: 7px;
  }
</style>

@section('title','Gestión de ventas')


@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            Ventas
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Panel administrador</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ventas</li>
            </ol>
        </nav>

        
    </div>
    <a href="{{route('sales.create')}}">
    <span class="btn btn-primary md-5" style=" margin-bottom:15px;">+ Registrar Venta</span>
    </a>
    <br>
    <div class="row">



        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Ventas</h4>
                      

                      <div class="btn-group">
                            <a data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                             
                               <a  class="dropdown-item" >Agregar</a>
                             
                            </div>
                          </div>
                    
                      </div>
               

                    <div class="table-responsive">
                        <table id="order-listing" class="table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th style="width:0%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($sales as $sale)
                                <tr>
                                    <td>{{$sale->id}}</td>

                                    <td>{{$sale->sale_date}}</td>

                                    <td>{{$sale->client->name}}</td>

                                    <td>{{$sale->total}}</td>

                                    @if($sale->status == 'VALID')

                                    <td>
                                    
                                    <a class="btn btn-success" style="" href="{{route('change.status.sales', $sale)}}" title="Cambiar Estado">
                                            VALID <i class="fas fa-check"></i>
                                        </a>

                                    </td>

                                    @else

                                    <td>
                                        <a class="btn btn-danger" href="{{route('change.status.sales', $sale)}}" title="Cambiar Estado">
                                            CANCELED <i class="fas fa-times"></i>
                                        </a>
                                    </td>

                                    @endif

                                  
                                  <td style="width: 50px;">
                                       


                                        <a class="btn btn-info  " style="height:35px;width:50px" href="{{route('sales.pdf',$sale)}}" type="submit" title="PDF">
                                            <i class="fas fa-file-pdf icon_eye"></i>
                                            </a>

                                        <a class="btn btn-warning" href="{{route('sales.show',$sale)}}" style="height:35px;width:50px" type="submit" title="Ver detalle">
                                            <i class="fas fa-eye icon_eye"></i>
                                        </a>

                                        <a class="btn btn-primary" href="{{route('sales.boleta' ,$sale)}}" style="height:35px;width:50px" type="submit" title="Boleta">
                                            <i class="fas fa-print icon_eye"></i>
                                        </a>
                                        
                               <!--          <button class="btn btn-danger eliminar " style="height:35px;width:50px" href="{{route('sales.destroy', $sale->id)}}" type="submit" title="Eliminar">
                                            <i class="far fa-trash-alt"></i>
                                        </button> -->

                                
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                 <div class="card-footer text-muted">
                 
                </div>  
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! Html::script('melody/js/data-table.js') !!}
@endsection



<div class="form-group">
    <label for="provider_id">Proveedor</label>
    <select class="form-control" name="provider_id" id="provider_id">
        @foreach ($providers as $provider)
        <option value="{{$provider->id}}">{{$provider->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="tax">Tax</label>
    <input type="number" name="tax" id="tax" class="form-control" aria-describedby="helpId"  placeholder="">

    @if ($errors->has('tax'))
    <small class="text-danger">{{ $errors->first('tax') }}</small>
    @endif
</div>


<div class="form-group">
    <label for="product_id">Producto</label>
    <select class="form-control" name="product_id" id="product_id">
        @foreach ($products as $product)
        <option value="{{$product->id}}">{{$product->name}}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="quantity">Cantidad</label>
    <input type="number" name="quantity" id="quantity" class="form-control" aria-describedby="helpId" >

    @if ($errors->has('quantity'))
    <small class="text-danger">{{ $errors->first('quantity') }}</small>
    @endif
</div>



<div class="form-group">
    <label for="price">Precio Compra</label>
    <input type="number" name="price" id="price" class="form-control" aria-describedby="helpId" >

    @if ($errors->has('price'))
    <small class="text-danger">{{ $errors->first('price') }}</small>
    @endif
</div>


<div class="form-group">
<button type="button" id="agregar" class="btn btn-primary float-right">Agregar producto</button>
<br>
</div>


<div class="form-group mt-2">

<h4 class="card-title"><strong>Detalles de compra</h4></strong>
    <div class="table-responsive col-md-12">
                <!--Mandar a llamar en JS por ID !-->
        <table id="detalles" class="table table-striped">
            <thead>
                <tr>
                    <th>Eliminar</th>
                    <th>Producto</th>
                    <th>Precio(US)</th>
                    <th>Cantidad</th>
                    <th>SubTotal(US)</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL:</p>
                    </th>
                    <th>
                        <p align="right"><span id="total">$ 0.00</span> </p>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL IMPUESTO (18%):</p>
                    </th>
                    <th>
                        <p align="right"><span id="total_impuesto">$ 0.00</span></p>
                    </th>
                </tr>
                <tr>
                    <th colspan="4">
                        <p align="right">TOTAL PAGAR:</p>
                    </th>
                    <th>
                        <p align="right"><span align="right" id="total_pagar_html">$ 0.00</span> <input type="hidden"
                                name="total" id="total_pagar"></p>
                    </th>
                </tr>
            </tfoot>
            <tbody>
            </tbody>
        </table>
    </div>

</div>


@section('scripts')
{!! Html::script('melody/js/alerts.js') !!}
{!! Html::script('melody/js/avgrund.js') !!}

{!! Html::script('melody/js/sweetalert2.js') !!}


<script>
 $(document).ready(function () {
        $("#agregar").click(function () {
            agregar();
        });
    });
    
    var cont = 0;
    total = 0;
    //Aray subtotales
    subtotal = [];
    
    $("#guardar").hide();
 
   
    function agregar() {
    
        product_id = $("#product_id").val();
        producto = $("#product_id option:selected").text();
        quantity = $("#quantity").val();
        price = $("#price").val();
        impuesto = $("#tax").val();

  
    
        if (product_id != "" && quantity != "" && quantity > 0 && price != "" || product_id != product_id) {
            subtotal[cont] = quantity * price;
            total = total + subtotal[cont];
            var fila = '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar('+cont+');"><i class="fa fa-times"></i></button></td> <td><input type="hidden" name="product_id[]" value="'+product_id+'">'+producto+'</td> <td> <input type="hidden" id="price[]" name="price[]" value="' + price + '"> <input class="form-control" type="number" id="price[]" value="' + price + '" disabled> </td>  <td> <input type="hidden" name="quantity[]" value="' + quantity + '"> <input class="form-control" type="number" value="' + quantity + '" disabled> </td> <td align="right">s/' + subtotal[cont] + ' </td></tr>';
            cont++;
            limpiar();
            totales();
            evaluar();
            $('#detalles').append(fila);
        } else {
            Swal.fire({
                type: 'error',
                text: 'Rellene todos los campos del detalle de la compras',
    
            })
        }
    }
    
    function limpiar() {
        $("#quantity").val("");
        $("#price").val("");
    }
    
    function totales() {
        $("#total").html("US " + total.toFixed(2));
        total_impuesto = total * impuesto / 100;
        total_pagar = total + total_impuesto;
        $("#total_impuesto").html("US " + total_impuesto.toFixed(2));
        $("#total_pagar_html").html("US " + total_pagar.toFixed(2));
        $("#total_pagar").val(total_pagar.toFixed(2));
    }
    
    function evaluar() {
        if (total > 0) {
            $("#guardar").show();
        } else {
            $("#guardar").hide();
        }
    }
    
    function eliminar(index) {
        total = total - subtotal[index];
        total_impuesto = total * impuesto / 100;
        total_pagar_html = total + total_impuesto;
        $("#total").html("US" + total);
        $("#total_impuesto").html("US" + total_impuesto);
        $("#total_pagar_html").html("US" + total_pagar_html);
        $("#total_pagar").val(total_pagar_html.toFixed(2));
        $("#fila" + index).remove();
        evaluar();
    }
    
 

</script>

@endsection


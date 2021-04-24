<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'sale_date', 'tax', 'total', 'status' , 'user_id', 'client_id',
    ];

     //Configuracion relaciones (ONE TO MANY INVERSE - SINGULAR (SEGUN RELACION TABLA))
     public function client(){
        return $this->belongsTo(Client::class);
    }

    public function user(){
        return $this->belongsTo(User::class);

    }

    //1 A N
    public function saleDetails(){
        return $this->hasMany(SaleDetails::class);

    }
}

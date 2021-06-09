<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//tengo que agregar el campo de la duracion del pedido o de cada producto
class Pedido extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'pedidos';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaMod';

    protected $fillable = [
        'id_mesa', 'codigo', 'estado', 'fechaAlta', 'fechaBaja', 'fechaMod'
    ];
}

?>
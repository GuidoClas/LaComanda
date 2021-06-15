<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductoDelPedido extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'productos_del_pedido';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaMod';

    protected $fillable = [
        'id_pedido', 'id_prod', 'sector', 'estado','fechaAlta', 'fechaBaja', 'fechaMod'
    ];
}

?>
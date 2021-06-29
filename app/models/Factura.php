<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Factura extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'facturas';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fechaAlta';
    const UPDATED_AT = 'fechaMod';
    const DELETED_AT = 'fechaBaja';

    protected $fillable = [
        'id_mesa', 'total', 'fechaAlta', 'fechaBaja', 'fechaMod'
    ];
}

?>
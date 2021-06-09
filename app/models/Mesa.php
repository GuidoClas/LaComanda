<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mesa extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'mesas';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fechaAlta';
    const DELETED_AT = 'fechaBaja';
    const UPDATED_AT = 'fechaMod';

    protected $fillable = [
        'id_cliente', 'codigo', 'estado', 'fechaAlta', 'fechaBaja', 'fechaMod'
    ];
}

?>
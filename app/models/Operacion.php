<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operacion extends Model{

    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'operaciones';
    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'fechaAlta';
    const UPDATED_AT = 'fechaMod';
    const DELETED_AT = 'fechaBaja';

    protected $fillable = [
        'usuario', 'tipo', 'descripcion','fechaAlta', 'fechaBaja','fechaMod'
    ];
}

?>
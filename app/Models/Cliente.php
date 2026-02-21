<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    public $timestamps = false;
    use SoftDeletes;
    protected $primaryKey = 'id_cliente';
    public $incrementing  = true;
    protected $keyType    = 'int';
    protected $fillable   = ['nombre','direccion','telefono'];
    protected $dates      = ['deleted_at'];
}

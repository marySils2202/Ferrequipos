<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;           
use App\Models\Compra;
use App\Models\Facturacion;        

class Movimiento extends Model
{
    protected $table      = 'movimientos';
    protected $primaryKey = 'id_movimiento';
    public    $timestamps = false;
    protected $fillable   = ['id_producto','tipo','cantidad','descripcion'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

  
    public function getMontoAttribute(): ?string
    {
        $desc = $this->descripcion ?? '';

        if (Str::startsWith($desc, 'Compra #')) {
            $id = (int) Str::after($desc, 'Compra #');
            if ($c = Compra::find($id)) {
                return number_format($c->total, 2);
            }
        }

        if (Str::startsWith($desc, 'Factura #')) {
            $id = (int) Str::after($desc, 'Factura #');
            if ($f = Facturacion::find($id)) {
                return number_format($f->monto_pago, 2);
            }
        }

        return null;
    }
}

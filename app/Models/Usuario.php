<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;  
class Usuario extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $table        = 'usuarios';
    protected $primaryKey   = 'id_usuario';
    public    $incrementing = true;
    protected $keyType      = 'int';
    public    $timestamps   = false;

    protected $fillable = [
        'username', 'email', 'password', 'nombre', 'rol'
    ];

    protected $guarded = ['id_usuario'];


    public function getAuthIdentifierName()
    {
        return 'id_usuario';
    }

    public function getNameAttribute(): string
    {
        return $this->attributes['nombre'];
    }

protected $hidden = [
    'password',
    'remember_token',
];

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_genero';
    protected $primaryKey = 'id_genero_pk';
    protected $fillable = [ 'genero' ];
}

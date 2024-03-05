<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'jenis', 'tanggal', 'keterangan'];
}

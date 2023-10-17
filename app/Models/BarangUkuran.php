<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangUkuran extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'barang_id', 'ukuran_id'];
}

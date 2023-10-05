<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatAkun extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['id', 'user_id', 'akun_id', 'tanggal', 'debet', 'kredit', 'keterangan'];
}

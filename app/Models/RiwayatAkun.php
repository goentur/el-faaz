<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatAkun extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'user_id', 'akun_id', 'tanggal', 'debet', 'kredit', 'keterangan'];
}

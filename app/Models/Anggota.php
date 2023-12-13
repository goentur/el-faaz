<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggota extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    public function piutangDagang()
    {
        return $this->hasMany(Penjualan::class)->where('status', 1)->with('penjualanDetail');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'akun_id', 'jurnal_id', 'debet', 'kredit'];
}

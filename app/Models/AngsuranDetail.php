<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngsuranDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $fillable = ['id', 'user_id', 'angsuran_id', 'metode_pembayaran_id', 'tanggal', 'nominal'];
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class);
    }
}

<?php

namespace Modules\Gudang\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PemasokBarangDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DetailGudangController extends Controller
{
    protected $attribute = [
        'view' => 'gudang::',
        'link' => 'gudang.',
        'title' => 'gudang',
    ];
    public function index($id)
    {
        $data = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->find(dekrip($id));
        if ($data) {
            $data = [
                'attribute' => $this->attribute,
                'data' => $data,
            ];
            return view($this->attribute['view'] . 'detail.index', $data);
        } else {
            return back()->with('error', 'Barang tidak ditemukan digudang');
        }
    }
    public function riwayatMasuk(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            $data = PemasokBarangDetail::with('pembelianDetail')->select('id')->find(dekrip($request->id));
            if ($data) {
                $riwayat = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                foreach ($data->pembelianDetail as $n => $pembelianDetail) {
                    $riwayat[] = [
                        'no' => ++$n . '.',
                        'pengguna' => $pembelianDetail->pembelian && $pembelianDetail->pembelian->user ? $pembelianDetail->pembelian->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                        'tanggal' => date('Y-m-d H:i:s', ($pembelianDetail->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' <b>' . $zonaWaktuPengguna->singkatan . '</b>',
                        'kuantitas' => $pembelianDetail->kuantitas,
                        'harga' => rupiah($pembelianDetail->harga),
                        'status' => $pembelianDetail->pembelian && $pembelianDetail->pembelian->status == 1 ? '<span class="badge bg-danger">HUTANG</span>' : '<span class="badge bg-success">LUNAS</span>',
                    ];
                }
                return response()->json(['data' => $riwayat], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
    public function riwayatKeluar(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            $data = PemasokBarangDetail::with('penjualanDetail')->select('id')->find(dekrip($request->id));
            if ($data) {
                $riwayat = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                foreach ($data->penjualanDetail as $n => $penjualanDetail) {
                    $riwayat[] = [
                        'no' => ++$n . '.',
                        'pengguna' => $penjualanDetail->penjualan && $penjualanDetail->penjualan->user ? $penjualanDetail->penjualan->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                        'tanggal' => date('Y-m-d H:i:s', ($penjualanDetail->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' <b>' . $zonaWaktuPengguna->singkatan . '</b>',
                        'kuantitas' => $penjualanDetail->kuantitas,
                        'harga' => rupiah($penjualanDetail->harga),
                        'statusBarang' => $penjualanDetail->status == 1 ? '<span class="badge bg-danger">TIDAK</span>' : '<span class="badge bg-success">TERSEDIA</span>',
                        'status' => $penjualanDetail->penjualan && $penjualanDetail->penjualan->status == 1 ? '<span class="badge bg-danger">HUTANG</span>' : '<span class="badge bg-success">LUNAS</span>',
                    ];
                }
                return response()->json(['data' => $riwayat], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
}

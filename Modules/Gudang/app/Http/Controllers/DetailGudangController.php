<?php

namespace Modules\Gudang\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PemasokBarangDetail;
use App\Models\ReturDetail;
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
            $idPemasokBarangDetail = dekrip($request->id);
            $data = PemasokBarangDetail::with('pembelianDetail')->select('id')->find($idPemasokBarangDetail);
            $dataRetur = ReturDetail::with('retur')->select('id', 'retur_id', 'pemasok_barang_detail_id', 'tanggal', 'kuantitas', 'harga')->where(['pemasok_barang_detail_id' => $idPemasokBarangDetail])->get();
            if ($data) {
                $riwayat = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                $no = 1;
                foreach ($data->pembelianDetail as $pembelianDetail) {
                    $riwayat[] = [
                        'pengguna' => $pembelianDetail->pembelian && $pembelianDetail->pembelian->user ? $pembelianDetail->pembelian->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                        'tanggal' => formatTanggal($pembelianDetail->tanggal, $zonaWaktuPengguna, true),
                        'kuantitas' => $pembelianDetail->kuantitas,
                        'harga' => rupiah($pembelianDetail->harga),
                        'status' => $pembelianDetail->pembelian && $pembelianDetail->pembelian->status == 1 ? '<span class="badge bg-danger">HUTANG</span>' : '<span class="badge bg-success">LUNAS</span>',
                    ];
                }
                foreach ($dataRetur as $rd) {
                    $riwayat[] = [
                        'pengguna' => $rd->retur && $rd->retur->user ? $rd->retur->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                        'tanggal' => formatTanggal($rd->tanggal, $zonaWaktuPengguna, true),
                        'kuantitas' => $rd->kuantitas,
                        'harga' => rupiah($rd->harga),
                        'status' => '<span class="badge bg-primary">RETUR</span>',
                    ];
                }
                usort($riwayat, function ($a, $b) {
                    $tanggalA = strtotime(substr($a['tanggal'], 0, 19));
                    $tanggalB = strtotime(substr($b['tanggal'], 0, 19));

                    return $tanggalB - $tanggalA;
                });
                foreach ($riwayat as &$item1) {
                    $item1['no'] = $no++ . '.';
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

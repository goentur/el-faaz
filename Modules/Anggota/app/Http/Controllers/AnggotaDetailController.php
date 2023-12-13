<?php

namespace Modules\Anggota\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Penjualan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaDetailController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            $data = Penjualan::with('penjualanDetail', 'user')->select('id', 'user_id', 'bayar', 'total')->where(['anggota_id' => dekrip($request->id), 'status' => 1])->orderBy('tanggal', 'ASC')->get();
            if ($data) {
                $datas = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                $no = 1;
                $total = 0;
                $bayar = 0;
                foreach ($data as $p) {
                    $total += $p->total;
                    $bayar += $p->bayar;
                    foreach ($p->penjualanDetail as $pd) {
                        if ($pd->pemasokBarangDetail && $pd->pemasokBarangDetail->barangDetail) {
                            $pemasok = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->pemasok) {
                                $pemasok = $pd->pemasokBarangDetail->pemasok->nama;
                            }
                            $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->barang) {
                                $barang = $pd->pemasokBarangDetail->barangDetail->barang->nama;
                            }
                            $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->warna) {
                                $warna = ' - ' . $pd->pemasokBarangDetail->barangDetail->warna->nama;
                            }
                            $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->satuan) {
                                $satuan = $pd->pemasokBarangDetail->barangDetail->satuan->nama;
                            }
                            $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->ukuran) {
                                $ukuran = '';
                                foreach ($pd->pemasokBarangDetail->barangDetail->ukuran as $u) {
                                    if ($u === $pd->pemasokBarangDetail->barangDetail->ukuran->last()) {
                                        $ukuran .= $u->nama;
                                    } else {
                                        $ukuran .= $u->nama . ", ";
                                    }
                                }
                            }
                            $datas[] = [
                                'no' => $no++ . '.',
                                'pengguna' => $p->user ? $p->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                                'pemasok' => $pemasok,
                                'barang' => $barang . '' . $warna,
                                'satuan' => $satuan,
                                'ukuran' => $ukuran,
                                'tanggal' => date('Y-m-d H:i:s', ($pd->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' <b>' . $zonaWaktuPengguna->singkatan . '</b>',
                                'kuantitas' => $pd->kuantitas,
                                'harga' => rupiah($pd->harga),
                                'status' => $pd->status == 1 ? '<span class="badge bg-danger">TIDAK</span>' : '<span class="badge bg-success">TERSEDIA</span>',
                            ];
                        }
                    }
                }
                return response()->json(['data' => $datas, 'total' => rupiah($total), 'bayar' => rupiah($bayar), 'kekurangan' => rupiah($total - $bayar)], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
    public function cetakTagihan($id): View
    {
        $idAnggota = dekrip($id);
        $kirim = [
            'zonaWaktuPengguna' => zonaWaktuPenguna(),
            'data' => Anggota::select('id', 'nama')->find($idAnggota),
            'penjualan' => Penjualan::with('penjualanDetail', 'user')->select('id', 'user_id', 'bayar', 'total')->where(['anggota_id' => $idAnggota, 'status' => 1])->orderBy('tanggal', 'ASC')->get(),
        ];
        return view('anggota::detail.cetak-tagihan', $kirim);
    }
}

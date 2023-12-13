<?php

namespace Modules\Riwayat\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\AngsuranDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatPenjualanController extends Controller
{
    protected $attribute = [
        'view' => 'riwayat::penjualan.',
        'link' => 'riwayat.penjualan.',
        'title' => 'riwayat penjualan',
    ];

    public function index(): View
    {
        $zonaWaktuPenguna = zonaWaktuPenguna();
        $hariIni = strtotime(date('d-m-Y')) + $zonaWaktuPenguna->gmt_offset;
        $data = [
            'attribute' => $this->attribute,
            'tanggalAwal' => date('d-m-Y', ($hariIni) - 604800),
            'tanggalAkhir' => date('d-m-Y', $hariIni),
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|string',
        ]);
        if ($request->ajax()) {
            $zonaWaktuPenguna = zonaWaktuPenguna();
            $tanggal = explode(" to ", $request->tanggal);
            if (!empty($tanggal[0]) && !empty($tanggal[1])) {
                $txtTanggalAwal = $tanggal[0];
                $txtTanggalAkhir = $tanggal[1];
            } else {
                $txtTanggalAwal = $request->tanggal;
                $txtTanggalAkhir = $request->tanggal;
            }
            $tanggalAwal = strtotime($txtTanggalAwal . ' 00:00:00') - $zonaWaktuPenguna->gmt_offset;
            $tanggalAkhir = strtotime($txtTanggalAkhir . ' 23:59:59') - $zonaWaktuPenguna->gmt_offset;
            $penjualan = Penjualan::with('user', 'anggota')->select('id', 'user_id', 'anggota_id', 'nama_pembeli', 'tanggal', 'total', 'status')->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->orderBy('tanggal', 'DESC');
            if ($penjualan->count() > 0) {
                $datas = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                foreach ($penjualan->get() as $key => $p) {
                    $datas[] = [
                        'no' => ++$key . '.',
                        'pengguna' => $p->user ? $p->user->name : 'PENGGUNA TIDAK TERDAFTAR',
                        'pembeli' => $p->anggota ? $p->anggota->nama : $p->nama_pembeli,
                        'tanggal' => date('Y-m-d H:i:s', ($p->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' ' . $zonaWaktuPengguna->singkatan,
                        'total' => rupiah($p->total),
                        'status' => $p->status == 1 ? '<span class="badge bg-danger">HUTANG</span>' : '<span class="badge bg-success">LUNAS</span>',
                        'aksi' => [
                            'type' => $p->status == 1 ? 'hutang' : 'lunas',
                            'link' => $p->status !== 1 ? route($this->attribute['link'] . 'detail', enkrip($p->id)) : route('angsuran.piutang-dagang.detail', enkrip($p->id)),
                            'id' => enkrip($p->id),
                        ]
                    ];
                }
                return response()->json(['data' => $datas, 'awal' => $txtTanggalAwal, 'akhir' => $txtTanggalAkhir], 200);
            } else {
                return response()->json(['data' => [], 'awal' => $txtTanggalAwal, 'akhir' => $txtTanggalAkhir], 200);
            }
        }
    }
    public function detail($id)
    {
        $idPenjualan = dekrip($id);
        $penjualan = Penjualan::with('user', 'anggota')->select('id', 'user_id', 'anggota_id', 'nama_pembeli', 'tanggal', 'bayar', 'total')->where(['id' => $idPenjualan, 'status' => 2]);
        if ($penjualan->count() > 0) {
            $data = [
                'attribute' => $this->attribute,
                'penjualan' => $penjualan->first(),
                'zonaWaktuPengguna' => zonaWaktuPenguna(),
                'angsuran' => Angsuran::select('id')->where(['transaksi_id' => $idPenjualan, 'jenis' => 2, 'status' => 2])->first(),
            ];
            return view($this->attribute['view'] . 'detail', $data);
        } else {
            return back()->with('error', 'Transaksi piutang dagang tidak ditemukan');
        }
    }
    public function daftarBarang(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idPenjualan = dekrip($request->id);
            $penjualanDetail = PenjualanDetail::with('pemasokBarangDetail')->select('id', 'pemasok_barang_detail_id', 'kuantitas', 'harga', 'status')->where(['penjualan_id' => $idPenjualan]);
            if ($penjualanDetail->count() > 0) {
                $datas = [];
                foreach ($penjualanDetail->get() as $key => $p) {
                    if ($p->pemasokBarangDetail && $p->pemasokBarangDetail->barangDetail) {
                        $pemasok = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($p->pemasokBarangDetail->pemasok) {
                            $pemasok = $p->pemasokBarangDetail->pemasok->nama;
                        }
                        $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($p->pemasokBarangDetail->barangDetail->barang) {
                            $barang = $p->pemasokBarangDetail->barangDetail->barang->nama;
                        }
                        $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($p->pemasokBarangDetail->barangDetail->warna) {
                            $warna = ' - ' . $p->pemasokBarangDetail->barangDetail->warna->nama;
                        }
                        $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($p->pemasokBarangDetail->barangDetail->satuan) {
                            $satuan = $p->pemasokBarangDetail->barangDetail->satuan->nama;
                        }
                        $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($p->pemasokBarangDetail->barangDetail->ukuran) {
                            $ukuran = '';
                            foreach ($p->pemasokBarangDetail->barangDetail->ukuran as $u) {
                                if ($u === $p->pemasokBarangDetail->barangDetail->ukuran->last()) {
                                    $ukuran .= $u->nama;
                                } else {
                                    $ukuran .= $u->nama . ", ";
                                }
                            }
                        }
                        $datas[] = [
                            'no' => ++$key . '.',
                            'pemasok' => $pemasok,
                            'barang' => $barang . '' . $warna,
                            'satuan' => $satuan,
                            'ukuran' => $ukuran,
                            'kuantitas' => $p->kuantitas,
                            'harga' => rupiah($p->harga),
                            'status' => $p->status == 1 ? '<span class="badge bg-danger">TIDAK</span>' : '<span class="badge bg-success">TERSEDIA</span>',
                        ];
                    }
                }
                return response()->json(['data' => $datas], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
    public function detailDataAngsuran(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);
        if ($request->ajax() && $request->id !== 0) {
            $idAngsuran = dekrip($request->id);
            $angsuranDetail = AngsuranDetail::with('user', 'metodePembayaran')->select('id', 'user_id', 'metode_pembayaran_id', 'tanggal', 'nominal')->orderBy('tanggal', 'DESC')->where(['angsuran_id' => $idAngsuran]);
            if ($angsuranDetail->count() > 0) {
                $datas = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                $bayar = 0;
                foreach ($angsuranDetail->get() as $key => $p) {
                    $bayar += $p->nominal;
                    $datas[] = [
                        'no' => ++$key . '.',
                        'pengguna' => $p->user ? $p->user->name : 'PENGGUNA TIDAK TERDAFTAR',
                        'metode' => $p->metodePembayaran ? $p->metodePembayaran->nama : 'METODE PEMBAYARAN TIDAK TERDAFTAR',
                        'tanggal' => date('Y-m-d H:i:s', ($p->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' ' . $zonaWaktuPengguna->singkatan,
                        'nominal' => rupiah($p->nominal),
                    ];
                }
                return response()->json(['data' => $datas, 'bayar' => $bayar], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
}

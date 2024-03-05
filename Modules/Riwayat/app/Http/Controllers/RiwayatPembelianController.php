<?php

namespace Modules\Riwayat\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\AngsuranDetail;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RiwayatPembelianController extends Controller
{
    protected $attribute = [
        'view' => 'riwayat::pembelian.',
        'link' => 'riwayat.pembelian.',
        'title' => 'riwayat pembelian',
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
            $pembelian = Pembelian::with('user', 'pemasok', 'angsuranDetail')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total', 'keterangan')->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->where('status', 2);

            if ($pembelian->count() > 0) {
                $datas = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                foreach ($pembelian->get() as $key => $p) {
                    $bayar = 0;
                    if ($p->angsuranDetail) {
                        foreach ($p->angsuranDetail as $a) {
                            $bayar += $a->nominal;
                        }
                    }
                    $datas[] = [
                        'no' => ++$key . '.',
                        'pengguna' => $p->user ? $p->user->name : 'PENGGUNA TIDAK TERDAFTAR',
                        'pemasok' => $p->pemasok ? $p->pemasok->nama : 'PEMASOK TIDAK TERDAFTAR',
                        'tanggal' => formatTanggal($p->tanggal, $zonaWaktuPengguna),
                        'total' => rupiah($p->total),
                        'keterangan' => $p->keterangan,
                        'aksi' => route($this->attribute['link'] . 'detail', enkrip($p->id))
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
        $idPembelian = dekrip($id);
        $pembelian = Pembelian::with('user', 'pemasok')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total', 'keterangan')->where(['id' => $idPembelian, 'status' => 2]);
        if ($pembelian->count() > 0) {
            $data = [
                'attribute' => $this->attribute,
                'pembelian' => $pembelian->first(),
                'zonaWaktuPengguna' => zonaWaktuPenguna(),
                'angsuran' => Angsuran::select('id')->where(['transaksi_id' => $idPembelian, 'jenis' => 1, 'status' => 2])->first(),
            ];
            return view($this->attribute['view'] . 'detail', $data);
        } else {
            return back()->with('error', 'Transaksi hutang dagang tidak ditemukan');
        }
    }
    public function daftarBarang(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idPembelian = dekrip($request->id);
            $pembelianDetail = PembelianDetail::with('pemasokBarangDetail')->select('id', 'pemasok_barang_detail_id', 'kuantitas', 'harga')->where(['pembelian_id' => $idPembelian]);
            if ($pembelianDetail->count() > 0) {
                $datas = [];
                foreach ($pembelianDetail->get() as $key => $p) {
                    if ($p->pemasokBarangDetail && $p->pemasokBarangDetail->barangDetail) {
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
                            'barang' => $barang . '' . $warna,
                            'satuan' => $satuan,
                            'ukuran' => $ukuran,
                            'kuantitas' => $p->kuantitas,
                            'harga' => rupiah($p->harga),
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
        if ($request->ajax()) {
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
                        'tanggal' => formatTanggal($p->tanggal, $zonaWaktuPengguna),
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

<?php

namespace Modules\Angsuran\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\AngsuranDetail;
use App\Models\MetodePembayaran;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AngsuranHutangDagangController extends Controller
{
    protected $attribute = [
        'view' => 'angsuran::hutang-dagang.',
        'link' => 'angsuran.hutang-dagang.',
        'title' => 'hutang dagang',
    ];

    public function index(): View
    {
        $data = [
            'attribute' => $this->attribute,
            'tanggalAkhir' => date('d-m-Y'),
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string',
            'tanggal' => 'required|string',
        ]);
        if ($request->ajax()) {
            $zonaWaktuPengguna = zonaWaktuPenguna();
            $txtTanggalAwal = '~';
            $txtTanggalAkhir = date('d-m-Y');
            if ($request->type === 'semua') {
                $pembelian = Pembelian::with('user', 'pemasok', 'angsuranDetail')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total', 'keterangan')->where('status', 1);
            } else {
                $tanggal = explode(" to ", $request->tanggal);
                if (!empty($tanggal[0]) && !empty($tanggal[1])) {
                    $txtTanggalAwal = $tanggal[0];
                    $txtTanggalAkhir = $tanggal[1];
                } else {
                    $txtTanggalAwal = $request->tanggal;
                    $txtTanggalAkhir = $request->tanggal;
                }
                $tanggalAwal = strtotime($txtTanggalAwal . ' 00:00:00') - $zonaWaktuPengguna->gmt_offset;
                $tanggalAkhir = strtotime($txtTanggalAkhir . ' 23:59:59') - $zonaWaktuPengguna->gmt_offset;
                $pembelian = Pembelian::with('user', 'pemasok', 'angsuranDetail')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total', 'keterangan')->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->where('status', 1);
            }
            if ($pembelian->count() > 0) {
                $datas = [];
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
                        'tanggal' => date('Y-m-d H:i:s', ($p->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' ' . $zonaWaktuPengguna->singkatan,
                        'total' => rupiah($p->total),
                        'bayar' => rupiah($bayar),
                        'kekurangan' => rupiah($p->total - $bayar),
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
        $pembelian = Pembelian::with('user', 'pemasok')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total', 'keterangan')->where(['id' => $idPembelian, 'status' => 1]);
        if ($pembelian->count() > 0) {
            $data = [
                'attribute' => $this->attribute,
                'pembelian' => $pembelian->first(),
                'zonaWaktuPengguna' => zonaWaktuPenguna(),
                'angsuran' => Angsuran::select('id')->where(['transaksi_id' => $idPembelian, 'jenis' => 1, 'status' => 1])->first(),
                'metodePembayarans' => MetodePembayaran::select('id', 'nama')->get(),
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
    public function tambahAngsuran(Request $request): JsonResponse
    {
        $request->validate([
            'idPembelian' => 'required|string',
            'idAngsuran' => 'required|string',
            'bayar' => 'required|numeric',
            'nominal' => 'required|numeric',
            'metodePembayaran' => 'required|string',
        ]);
        if ($request->ajax()) {
            try {
                DB::beginTransaction();
                $idAngsuran = dekrip($request->idAngsuran);
                $type = 1;
                $pesan = 'Angsuran berhasil ditambahkan.';
                AngsuranDetail::create([
                    'id' => id(),
                    'user_id' => auth()->user()->id,
                    'angsuran_id' => $idAngsuran,
                    'metode_pembayaran_id' => dekrip($request->metodePembayaran),
                    'tanggal' => time(),
                    'nominal' => $request->nominal,
                ]);
                $pembelian = Pembelian::select('id', 'pemasok_id', 'total')->find(dekrip($request->idPembelian));
                if ($request->bayar + $request->nominal == $pembelian->total) {
                    $pembelian->update([
                        'status' => 2,
                    ]);
                    Angsuran::select('id')->find($idAngsuran)->update([
                        'status' => 2,
                    ]);
                    $pembelianLain = Pembelian::select('id')->where(['pemasok_id' => $pembelian->pemasok_id, 'status' => 1]);
                    if ($pembelianLain->count() < 1) {
                        Pemasok::select('id')->find($pembelian->pemasok_id)->update([
                            'status' => 1,
                        ]);
                    }
                    $type = 2;
                    $pesan = 'Angsuran berhasil ditambahkan dan hutang dagang sudah lunas.';
                }
                DB::commit();
                return response()->json([
                    'status' => true,
                    'type' => $type,
                    'message' => $pesan,
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat melakukan angsuran.',
                ]);
            }
        }
    }
}

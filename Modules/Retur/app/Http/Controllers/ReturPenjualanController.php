<?php

namespace Modules\Retur\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Retur;
use App\Models\ReturDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturPenjualanController extends Controller
{
    protected $attribute = [
        'view' => 'retur::penjualan.',
        'link' => 'retur.penjualan.',
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
                        'tanggal' => formatTanggal($p->tanggal, $zonaWaktuPengguna),
                        'total' => rupiah($p->total),
                        'status' => $p->status == 1 ? '<span class="badge bg-danger">HUTANG</span>' : '<span class="badge bg-success">LUNAS</span>',
                        'aksi' =>  '<a href="' . route($this->attribute['link'] . 'detail', enkrip($p->id)) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i></a>',
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
        $penjualan = Penjualan::with('user', 'anggota')->select('id', 'user_id', 'anggota_id', 'nama_pembeli', 'tanggal', 'bayar', 'total')->where(['id' => dekrip($id)]);
        if ($penjualan->count() > 0) {
            $data = [
                'attribute' => $this->attribute,
                'penjualan' => $penjualan->first(),
                'zonaWaktuPengguna' => zonaWaktuPenguna(),
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
                            'status' => $p->status == 1 ? '<span class="badge bg-danger">TIDAK</span>' : '<span class="badge bg-success">TERSEDIA</span>',
                            'harga' => rupiah($p->harga),
                            'aksi' =>  '<button type="button" data-id="' . enkrip($p->id) . '" class="btn btn-sm btn-primary detail-barang"><i class="fa fa-eye"></i></button>',
                        ];
                    }
                }
                return response()->json(['data' => $datas], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
    public function daftarBarangRetur(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idPenjualan = dekrip($request->id);
            $returs = Retur::with('user', 'returDetail')->select('id', 'user_id', 'tanggal', 'total', 'status')->where(['transaksi_id' => $idPenjualan, 'jenis' => 2])->orderBy('tanggal', 'DESC');
            if ($returs->count() > 0) {
                $zonaWaktuPengguna = zonaWaktuPenguna();
                $datas = [];
                $totals = 0;
                $no = 1;
                foreach ($returs->get() as $key => $retur) {
                    $pengguna = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                    if ($retur->user) {
                        $pengguna = $retur->user->name;
                    }
                    $status = true;
                    if ($retur->status == 3) {
                        $status = false;
                    }
                    $totals += $retur->total;
                    foreach ($retur->returDetail as $key => $returDetail) {
                        if ($returDetail->pemasokBarangDetail && $returDetail->pemasokBarangDetail->barangDetail) {
                            $pemasok = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($returDetail->pemasokBarangDetail->pemasok) {
                                $pemasok = $returDetail->pemasokBarangDetail->pemasok->nama;
                            }
                            $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($returDetail->pemasokBarangDetail->barangDetail->barang) {
                                $barang = $returDetail->pemasokBarangDetail->barangDetail->barang->nama;
                            }
                            $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($returDetail->pemasokBarangDetail->barangDetail->warna) {
                                $warna = ' - ' . $returDetail->pemasokBarangDetail->barangDetail->warna->nama;
                            }
                            $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($returDetail->pemasokBarangDetail->barangDetail->satuan) {
                                $satuan = $returDetail->pemasokBarangDetail->barangDetail->satuan->nama;
                            }
                            $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($returDetail->pemasokBarangDetail->barangDetail->ukuran) {
                                $ukuran = '';
                                foreach ($returDetail->pemasokBarangDetail->barangDetail->ukuran as $u) {
                                    if ($u === $returDetail->pemasokBarangDetail->barangDetail->ukuran->last()) {
                                        $ukuran .= $u->nama;
                                    } else {
                                        $ukuran .= $u->nama . ", ";
                                    }
                                }
                            }
                            $datas[] = [
                                'no' => $no++ . '.',
                                'pengguna' => $pengguna,
                                'tanggal' => formatTanggal($returDetail->tanggal, $zonaWaktuPengguna),
                                'pemasok' => $pemasok,
                                'barang' => $barang . '' . $warna,
                                'satuan' => $satuan,
                                'ukuran' => $ukuran,
                                'kuantitas' => $returDetail->kuantitas,
                                'harga' => rupiah($returDetail->harga),
                                'aksi' =>  $status ? '<button type="button" data-id="' . enkrip($returDetail->id) . '" data-retur="' . enkrip($retur->id) . '" class="btn btn-sm btn-danger hapus"><i class="fas fa-trash-alt"></i></button>' : '',
                            ];
                        }
                    }
                }
                return response()->json(['data' => $datas, 'total' => $totals], 200);
            } else {
                return response()->json(['data' => [], 'total' => 0], 200);
            }
        }
    }
    public function detailBarang(Request $request): JsonResponse
    {
        $request->validate([
            'penjualan' => 'required|string',
            'penjualanDetail' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idPenjualan = dekrip($request->penjualan);
            $idPenjualanDetail = dekrip($request->penjualanDetail);
            $datas = null;
            $status = true;
            $kuantitas = 0;
            $penjualanDetail = PenjualanDetail::with('pemasokBarangDetail')->select('id', 'pemasok_barang_detail_id', 'kuantitas', 'harga')->find($idPenjualanDetail);
            if ($penjualanDetail->pemasokBarangDetail && $penjualanDetail->pemasokBarangDetail->barangDetail) {
                $pemasok = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                if ($penjualanDetail->pemasokBarangDetail->pemasok) {
                    $pemasok = $penjualanDetail->pemasokBarangDetail->pemasok->nama;
                }
                $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                if ($penjualanDetail->pemasokBarangDetail->barangDetail->barang) {
                    $barang = $penjualanDetail->pemasokBarangDetail->barangDetail->barang->nama;
                }
                $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                if ($penjualanDetail->pemasokBarangDetail->barangDetail->warna) {
                    $warna = ' - ' . $penjualanDetail->pemasokBarangDetail->barangDetail->warna->nama;
                }
                $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                if ($penjualanDetail->pemasokBarangDetail->barangDetail->satuan) {
                    $satuan = $penjualanDetail->pemasokBarangDetail->barangDetail->satuan->nama;
                }
                $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                if ($penjualanDetail->pemasokBarangDetail->barangDetail->ukuran) {
                    $ukuran = '';
                    foreach ($penjualanDetail->pemasokBarangDetail->barangDetail->ukuran as $u) {
                        if ($u === $penjualanDetail->pemasokBarangDetail->barangDetail->ukuran->last()) {
                            $ukuran .= $u->nama;
                        } else {
                            $ukuran .= $u->nama . ", ";
                        }
                    }
                }
                $returs = Retur::select('id', 'tanggal', 'total', 'jenis', 'status')->where(['transaksi_id' => $idPenjualan, 'jenis' => 2]);
                if ($returs->count() > 0) {
                    foreach ($returs->get() as $retur) {
                        $returDetail = ReturDetail::select('id', 'kuantitas')->where(['retur_id' => $retur->id, 'transaksi_detail_id' => $idPenjualanDetail])->first();
                        if ($returDetail) {
                            $kuantitas += $returDetail->kuantitas;
                        }
                    }
                    if ($kuantitas >= $penjualanDetail->kuantitas) {
                        $status = false;
                    }
                }
                $datas = [
                    'pemasok' => $pemasok,
                    'barang' => $barang . '' . $warna,
                    'satuan' => $satuan,
                    'ukuran' => $ukuran,
                    'kuantitas' => $penjualanDetail->kuantitas,
                    'harga' => rupiah($penjualanDetail->harga),
                    'status' => $status,
                    'kuantitas_retur' => $penjualanDetail->kuantitas - $kuantitas,
                ];
            }
            return response()->json($datas, 200);
        } else {
            return response()->json(null, 200);
        }
    }
    public function simpanRetur(Request $request): JsonResponse
    {
        $request->validate([
            'penjualan' => 'required|string',
            'penjualanDetail' => 'required|string',
            'kuantitas' => 'required|numeric',
        ]);
        if ($request->ajax()) {
            $idPenjualan = dekrip($request->penjualan);
            $idPenjualanDetail = dekrip($request->penjualanDetail);
            $penjualanDetail = PenjualanDetail::with('onlyPemasokBarangDetail')->select('id', 'pemasok_barang_detail_id', 'harga')->find($idPenjualanDetail);
            if ($penjualanDetail) {
                $tanggal = time();
                $retur = Retur::select('id', 'tanggal', 'total', 'jenis', 'status')->where(['transaksi_id' => $idPenjualan, 'jenis' => 2, 'status' => 1])->first();
                try {
                    DB::beginTransaction();
                    if ($retur) {
                        $returDetail = ReturDetail::select('id', 'kuantitas')->where(['retur_id' => $retur->id, 'transaksi_detail_id' => $idPenjualanDetail])->first();
                        if ($returDetail) {
                            $returDetail->update([
                                'tanggal' => $tanggal,
                                'kuantitas' => $returDetail->kuantitas + $request->kuantitas,
                            ]);
                            $retur->update([
                                'total' => $retur->total + ($penjualanDetail->harga * $request->kuantitas),
                            ]);
                        } else {
                            ReturDetail::create([
                                'id' => id(),
                                'retur_id' => $retur->id,
                                'transaksi_detail_id' => $idPenjualanDetail,
                                'pemasok_barang_detail_id' => $penjualanDetail->pemasok_barang_detail_id,
                                'tanggal' => $tanggal,
                                'kuantitas' => $request->kuantitas,
                                'harga' => $penjualanDetail->harga,
                            ]);
                            $retur->update([
                                'total' => $retur->total + ($penjualanDetail->harga * $request->kuantitas),
                            ]);
                        }
                    } else {
                        $idRetur = id();
                        Retur::create([
                            'id' => $idRetur,
                            'user_id' => auth()->user()->id,
                            'transaksi_id' => $idPenjualan,
                            'tanggal' => $tanggal,
                            'total' => $penjualanDetail->harga * $request->kuantitas,
                            'jenis' => 2,
                            'status' => 1,
                        ]);
                        ReturDetail::create([
                            'id' => id(),
                            'retur_id' => $idRetur,
                            'transaksi_detail_id' => $idPenjualanDetail,
                            'pemasok_barang_detail_id' => $penjualanDetail->pemasok_barang_detail_id,
                            'tanggal' => $tanggal,
                            'kuantitas' => $request->kuantitas,
                            'harga' => $penjualanDetail->harga,
                        ]);
                    }
                    $penjualanDetail->onlyPemasokBarangDetail->update([
                        'stok' => $penjualanDetail->onlyPemasokBarangDetail->stok + $request->kuantitas,
                    ]);
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => 'Retur barang berhasil disimpan.',
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Terjadi kesalahan pada saat penyimpanan data.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data barang tidak ditemukan.',
                ]);
            }
        }
    }
    public function hapusRetur(Request $request): JsonResponse
    {
        $request->validate([
            'retur' => 'required|string',
            'id' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idRetur = dekrip($request->retur);
            $id = dekrip($request->id);
            $returDetail = ReturDetail::with('onlyPemasokBarangDetail')->select('id', 'pemasok_barang_detail_id', 'kuantitas', 'harga')->find($id);
            if ($returDetail) {
                try {
                    DB::beginTransaction();
                    $retur = Retur::select('id', 'total')->where(['id' => $idRetur, 'status' => 1])->first();
                    $dataReturDetail = ReturDetail::select('id')->where(['retur_id' => $idRetur]);
                    if ($dataReturDetail->count() > 1) {
                        $retur->update([
                            'total' => $retur->total - ($returDetail->harga * $returDetail->kuantitas),
                        ]);
                        $returDetail->delete();
                    } else {
                        $retur->delete();
                    }
                    $returDetail->onlyPemasokBarangDetail->update([
                        'stok' => $returDetail->onlyPemasokBarangDetail->stok - $returDetail->kuantitas,
                    ]);
                    DB::commit();
                    return response()->json([
                        'status' => true,
                        'message' => 'Retur barang berhasil dihapus.',
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Terjadi kesalahan pada saat penyimpanan data.',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data barang retur tidak ditemukan.',
                ]);
            }
        }
    }
}

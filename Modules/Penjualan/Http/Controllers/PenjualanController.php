<?php

namespace Modules\Penjualan\Http\Controllers;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\AngsuranDetail;
use App\Models\MetodePembayaran;
use App\Models\PemasokBarangDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    protected $attribute = [
        'view' => 'penjualan::',
        'link' => 'penjualan.',
        'title' => 'Penjualan',
    ];
    public function index(): View
    {
        $data = [
            'attribute' => $this->attribute,
            'metodePembayarans' => MetodePembayaran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    public function dataBarang(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $limit = 16;
            if (auth()->user()->sidebar === 'h') {
                $limit = 20;
            }
            if ($request->nama) {
                $request->validate([
                    'nama' => 'required|string',
                ]);
                $pemasokBarangDetails = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->inRandomOrder()->whereHas('barangDetail', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->nama . '%');
                })->limit($limit);
            } else {
                $pemasokBarangDetails = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->inRandomOrder()->limit($limit);
            }
            if ($pemasokBarangDetails->count() > 0) {
                $fullData = [];
                foreach ($pemasokBarangDetails->get() as $pbd) {
                    if ($pbd->barangDetail) {
                        $barang = '';
                        if ($pbd->barangDetail->barang) {
                            $barang = $pbd->barangDetail->barang->nama;
                        }
                        $warna = '';
                        if ($pbd->barangDetail->warna) {
                            $warna = ' - ' . $pbd->barangDetail->warna->nama;
                        }
                        $satuan = '';
                        if ($pbd->barangDetail->satuan) {
                            $satuan = $pbd->barangDetail->satuan->nama;
                        }
                        $ukuran = '';
                        if ($pbd->barangDetail->ukuran) {
                            $ukuran = '';
                            foreach ($pbd->barangDetail->ukuran as $u) {
                                if ($u === $pbd->barangDetail->ukuran->last()) {
                                    $ukuran .= $u->nama;
                                } else {
                                    $ukuran .= $u->nama . ", ";
                                }
                            }
                        }
                        $foto = asset('img/product/emty.png');
                        if ($pbd->barangDetail->foto) {
                            $foto = $pbd->barangDetail->foto;
                        }
                        $fullData[] = [
                            'id' => enkrip($pbd->id),
                            'status' => $pbd->stok < 1 ? 'po' : 'siap',
                            'pemasok' => $pbd->pemasok ? $pbd->pemasok->nama : '',
                            'satuan' => $satuan,
                            'nama' => $barang . '' . $warna,
                            'stok' => $pbd->stok,
                            'harga' => $pbd->harga_beli,
                            'ukuran' => $ukuran,
                            'foto' => $foto,
                        ];
                    }
                }
                return response()->json($fullData, 200);
            } else {
                return response()->json(null, 404);
            }
        }
    }
    public function cekStokBarangTersedia(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'kuantitas' => 'required|string',
            'kuantitasTambahan' => 'required|numeric',
        ]);
        if ($request->ajax()) {
            $pemasokBarangDetail = PemasokBarangDetail::select('id', 'stok')->find(dekrip($request->id));
            if ($pemasokBarangDetail) {
                if (($request->kuantitas + $request->kuantitasTambahan) > $pemasokBarangDetail->stok) {
                    return response()->json(['status' => true, 'statusStok' => "melebihi stok", 'stokTersedia' => $pemasokBarangDetail->stok], 200);
                } else {
                    return response()->json(['status' => true, 'statusStok' => "aman"], 200);
                }
            } else {
                return response()->json(['status' => false], 200);
            }
        }
    }
    public function dataAnggota(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $datas = [];
            $anggotas = Anggota::select('id', 'nama')->get();
            foreach ($anggotas as $n => $anggota) {
                $datas[] = [
                    ++$n,
                    $anggota->nama,
                    '<button class="btn btn-sm btn-success aksi-anggota" data-id="' . enkrip($anggota->id) . '" data-nama="' . $anggota->nama . '"><i class="fas fa-check"></i></button>',
                ];
            }
            return response()->json(['data' => $datas], 200);
        }
    }
    public function selesai(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'nama' => 'required|string',
            'keranjang' => 'required|array|min:1',
            'keranjang.*.hargaBarang' => 'required|string',
            'keranjang.*.id' => 'required|string',
            'keranjang.*.idBarang' => 'required|string',
            'keranjang.*.kuantitas' => 'required|numeric',
            'keranjang.*.namaBarang' => 'required|string',
            'keranjang.*.statusBarang' => 'required|string',
            'bayar' => 'required|string',
            'ongkir' => 'required|string',
            'metodePembayaran' => 'required|string',
            'total' => 'required|numeric',
        ]);
        if ($request->ajax()) {
            $tanggal = time();
            $idPenjualan = id();
            $idAnggota = null;
            $jenisPenjualan = 1;
            $statusPenjualan = 1;
            $bayar = str_replace(",", "", $request->bayar);
            $metodePembayaran = dekrip($request->metodePembayaran);
            $ongkir = str_replace(",", "", $request->ongkir);
            if ($request->id !== "biasa") {
                $jenisPenjualan = 2;
                $idAnggota = dekrip($request->id);
            }
            if ($ongkir > 0) {
                $jenisPenjualan = 3;
            }
            if ($bayar >= $request->total) {
                $statusPenjualan = 2;
            }
            try {
                DB::beginTransaction();
                // PENJUALAN
                $penjualan = Penjualan::create([
                    'id' => $idPenjualan,
                    'user_id' => auth()->user()->id,
                    'metode_pembayaran_id' => $metodePembayaran,
                    'anggota_id' => $idAnggota,
                    'nama_pembeli' => $request->nama,
                    'tanggal' => $tanggal,
                    'bayar' => $bayar,
                    'total' => $request->total,
                    'ongkir' => $ongkir,
                    'jenis' => $jenisPenjualan,
                    'status' => $statusPenjualan,
                ]);
                $barangs = "";
                $barangPo = false;
                foreach ($request->keranjang as $data) {
                    $idBarang = dekrip($data['idBarang']);
                    $harga = str_replace(",", "", $data['hargaBarang']);
                    $statusBarang = "";
                    $pemasokBarangDetail = PemasokBarangDetail::select('id', 'stok')->find($idBarang);
                    if ($data['statusBarang'] === 'po') {
                        $statusBarang = "(PO) ";
                        $barangPo = true;
                    }
                    $pemasokBarangDetail->update([
                        'stok' => $pemasokBarangDetail->stok - $data['kuantitas'],
                    ]);
                    $explode = explode(' | ', $data['namaBarang']);
                    $barangs .= $statusBarang . "{$explode[1]}\n" . rupiah($harga) . " X {$data['kuantitas']} : " . rupiah($harga * $data['kuantitas']) . "\n";
                    PenjualanDetail::create([
                        'id' => id(),
                        'penjualan_id' => $idPenjualan,
                        'pemasok_barang_detail_id' => $idBarang,
                        'tanggal' => $tanggal,
                        'kuantitas' => $data['kuantitas'],
                        'harga' => $harga,
                        'status' => $data['statusBarang'] === 'siap' ? 2 : 1,
                    ]);
                }
                if ($barangPo) {
                    if ($bayar >= $request->total) {
                        $penjualan->update([
                            'status' => 3
                        ]);
                    } else {
                        $penjualan->update([
                            'status' => 1
                        ]);
                    }
                }

                // ANGSURAN
                if ($bayar < $request->total) {
                    $idAngsuran = id();
                    Angsuran::create([
                        'id' => $idAngsuran,
                        'transaksi_id' => $idPenjualan,
                        'jenis' => 2,
                        'status' => 1,
                    ]);
                    if ($bayar > 0 && $bayar < $request->total) {
                        AngsuranDetail::create([
                            'id' => id(),
                            'user_id' => auth()->user()->id,
                            'angsuran_id' => $idAngsuran,
                            'metode_pembayaran_id' => $metodePembayaran,
                            'tanggal' => $tanggal,
                            'nominal' => $bayar,
                        ]);
                    }
                }
                DB::commit();
                return response()->json([
                    'data' => [
                        'id' => $idPenjualan,
                        'total' => rupiah($request->total),
                        'bayar' => rupiah($bayar),
                        'kembalian' => $bayar <= $request->total ? 0 : rupiah($bayar - $request->total),
                        'tgl' => formatTanggal($tanggal, zonaWaktuPenguna()),
                        'barang' => $barangs,
                    ],
                    'status' => true,
                    'message' => 'Transaksi penjualan telah selesai',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat melakukan transaksi penjualan',
                ]);
            }
        }
    }
    function cetakNota(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idPenjualan = dekrip($request->id);
            $penjualan = Penjualan::select('id', 'tanggal', 'bayar', 'total')->find($idPenjualan);
            if ($penjualan) {
                $penjualanDetail = PenjualanDetail::with('pemasokBarangDetail')->select('id', 'pemasok_barang_detail_id', 'harga', 'kuantitas', 'status')->where('penjualan_id', $idPenjualan);
                if ($penjualanDetail->count() > 0) {
                    $barangs = "";
                    foreach ($penjualanDetail->get() as $p) {
                        if ($p->pemasokBarangDetail && $p->pemasokBarangDetail->barangDetail) {
                            $statusBarang = "";
                            if ($p->status === 1) {
                                $statusBarang = "(PO) ";
                            }
                            $barang = 'TIDAK TERDAFTAR';
                            if ($p->pemasokBarangDetail->barangDetail->barang) {
                                $barang = $p->pemasokBarangDetail->barangDetail->barang->nama;
                            }
                            $warna = ' - TIDAK TERDAFTAR';
                            if ($p->pemasokBarangDetail->barangDetail->warna) {
                                $warna = ' - ' . $p->pemasokBarangDetail->barangDetail->warna->nama;
                            }
                            $ukuran = ' ( TIDAK TERDAFTAR )';
                            if ($p->pemasokBarangDetail->barangDetail->ukuran) {
                                $ukuran = ' ( ';
                                foreach ($p->pemasokBarangDetail->barangDetail->ukuran as $u) {
                                    if ($u === $p->pemasokBarangDetail->barangDetail->ukuran->last()) {
                                        $ukuran .= $u->nama;
                                    } else {
                                        $ukuran .= $u->nama . ", ";
                                    }
                                }
                                $ukuran .= ' )';
                            }
                            $barangs .= $statusBarang . "{$barang}{$warna}\n" . rupiah($p->harga) . " X {$p->kuantitas} : " . rupiah($p->harga * $p->kuantitas) . "\n";
                        }
                    }
                    $zonaWaktuPengguna = zonaWaktuPenguna();
                    return response()->json([
                        'data' => [
                            'id' => $idPenjualan,
                            'total' => rupiah($penjualan->total),
                            'bayar' => rupiah($penjualan->bayar),
                            'kembalian' => $penjualan->bayar <= $penjualan->total ? 0 : rupiah($penjualan->bayar - $penjualan->total),
                            'tgl' => date('Y-m-d H:i:s', ($penjualan->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' ' . $zonaWaktuPengguna->singkatan,
                            'barang' => $barangs,
                        ],
                        'status' => true,
                        'message' => 'Transaksi penjualan telah selesai',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Terjadi kesalahan pada saat pengambilan data barang',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan pada saat pengambilan data barang',
                ]);
            }
        }
    }
}

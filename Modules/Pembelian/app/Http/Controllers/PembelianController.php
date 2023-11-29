<?php

namespace Modules\Pembelian\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangDetail;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembelianController extends Controller
{
    protected $attribute = [
        'view' => 'pembelian::',
        'link' => 'pembelian.',
        'linkSampah' => 'pembelian.',
        'title' => 'Pembelian',
    ];

    public function index(): View
    {
        $data = [
            'attribute' => $this->attribute,
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
                $barangDetails = BarangDetail::with('pemasok', 'barang', 'satuan', 'ukuran')->select('id', 'pemasok_id', 'barang_id', 'satuan_id', 'stok', 'harga_beli')->orderBy('stok', 'ASC')->orderBy('id', 'desc')->whereHas('barang', function ($query) use ($request) {
                    $query->where('nama', 'like', '%' . $request->nama . '%');
                    $query->where('deleted_at', null);
                })->limit($limit);
            } else {
                $barangDetails = BarangDetail::with('pemasok', 'barang', 'satuan', 'ukuran')->select('id', 'pemasok_id', 'barang_id', 'satuan_id', 'stok', 'harga_beli')->orderBy('stok', 'ASC')->orderBy('id', 'desc')->whereHas('barang', function ($query) {
                    $query->where('deleted_at', null);
                })->limit($limit);
            }
            if ($barangDetails->count() > 0) {
                $fullData = [];
                foreach ($barangDetails->get() as $barangDetail) {
                    $ukuran = "";
                    foreach ($barangDetail->ukuran as $u) {
                        if ($u === $barangDetail->ukuran->last()) {
                            $ukuran .= $u->nama;
                        } else {
                            $ukuran .= $u->nama . ", ";
                        }
                    }
                    $barang = "";
                    $foto = asset('img/product/emty.png');
                    if ($barangDetail->barang) {
                        $barang = $barangDetail->barang->nama;
                        $foto = $barangDetail->barang->foto;
                        if ($barangDetail->barang->warna) {
                            $barang .= ' - ' . $barangDetail->barang->warna->nama;
                        }
                    }
                    $fullData[] = [
                        'id' => enkrip($barangDetail->id),
                        'pemasok' => $barangDetail->pemasok ? $barangDetail->pemasok->nama : '',
                        'satuan' => $barangDetail->satuan ? $barangDetail->satuan->nama : '',
                        'nama' => $barang,
                        'stok' => $barangDetail->stok,
                        'harga' => rupiah($barangDetail->harga_beli),
                        'ukuran' => $ukuran,
                        'foto' => $foto,
                    ];
                }
                return response()->json($fullData, 200);
            } else {
                return response()->json('Barang tidak ditemukan', 404);
            }
        }
    }
    public function selesai(Request $request): JsonResponse
    {
        $request->validate([
            'barang' => 'required|array|min:1',
            'barang.*.hargaBarang' => 'required|string',
            'barang.*.id' => 'required|string',
            'barang.*.kuantitas' => 'required|numeric',
            'barang.*.namaBarang' => 'required|string',
            'total' => 'required|numeric',
            'bayar' => 'required|string',
            'keterangan' => 'required|string',
        ]);
        if ($request->ajax()) {
            $tanggal = time();
            $idPembelian = id();
            $idAnggota = null;
            $jenisPembelian = 1;
            $statusPembelian = 1;
            $bayar = str_replace(",", "", $request->bayar);
            if ($request->id !== "biasa") {
                $jenisPembelian = 2;
                $idAnggota = dekrip($request->id);
            }
            if ($bayar >= $request->total) {
                $statusPembelian = 2;
            }
            try {
                DB::beginTransaction();
                // Pembelian
                $Pembelian = Pembelian::create([
                    'id' => $idPembelian,
                    'user_id' => auth()->user()->id,
                    'anggota_id' => $idAnggota,
                    'nama_pembeli' => $request->nama,
                    'tanggal' => $tanggal,
                    'bayar' => $bayar,
                    'total' => $request->total,
                    'jenis' => $jenisPembelian,
                    'status' => $statusPembelian,
                ]);
                $barangs = "";
                $barangPo = false;
                foreach ($dataKeranjang as $data) {
                    $harga = 0;
                    if ($request->id === "biasa") {
                        $harga = $data['harga_jual'];
                    } else {
                        $harga = $data['harga_anggota'];
                    }
                    $statusBarang = "";
                    if ($data['status'] === 'po') {
                        $statusBarang = "(PO) ";
                        $barangPo = true;
                    } else {
                        $barang = Barang::select('id', 'stok')->find($data['id']);
                        $barang->update([
                            'stok' => $barang->stok - $data['kuantitas'],
                        ]);
                    }
                    $barangs .= $statusBarang . "{$data['nama']}\n" . rupiah($harga) . " X {$data['kuantitas']} : " . rupiah($harga * $data['kuantitas']) . "\n";
                    PembelianDetail::create([
                        'id' => id(),
                        'Pembelian_id' => $idPembelian,
                        'barang_id' => $data['id'],
                        'kuantitas' => $data['kuantitas'],
                        'harga' => $harga,
                        'status' => $data['status'] === 'siap' ? 2 : 1,
                    ]);
                }
                if ($barangPo) {
                    $Pembelian->update([
                        'status' => 1,
                    ]);
                }

                // ANGSURAN
                if ($bayar < $request->total) {
                    $idAngsuran = id();
                    Angsuran::create([
                        'id' => $idAngsuran,
                        'transaksi_id' => $idPembelian,
                        'jenis' => 2,
                        'status' => 1,
                    ]);
                    if ($bayar > 0 && $bayar < $request->total) {
                        AngsuranDetail::create([
                            'id' => id(),
                            'user_id' => auth()->user()->id,
                            'angsuran_id' => $idAngsuran,
                            'tanggal' => $tanggal,
                            'nominal' => $bayar,
                        ]);
                    }
                }
                DB::commit();
                session()->forget('keranjang-Pembelian');
                return response()->json([
                    'data' => [
                        'id' => $idPembelian,
                        'total' => rupiah($request->total),
                        'bayar' => rupiah($bayar),
                        'kembalian' => $bayar <= $request->total ? 0 : rupiah($bayar - $request->total),
                        // 25200 GMT + 07:00 (WIB)
                        'tgl' => date('Y-m-d H:i:s', ($tanggal + 25200)),
                        'barang' => $barangs,
                    ],
                    'status' => true,
                    'message' => 'Transaksi Pembelian telah selesai',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat melakukan transaksi Pembelian',
                ]);
            }
        }
    }
}

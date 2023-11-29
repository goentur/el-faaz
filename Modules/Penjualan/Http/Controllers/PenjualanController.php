<?php

namespace Modules\Penjualan\Http\Controllers;

use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\AngsuranDetail;
use App\Models\Barang;
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
        session()->forget('keranjang-penjualan');
        $data = [
            'attribute' => $this->attribute,
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    public function dataBarang(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $limit = 15;
            if (auth()->user()->sidebar === 'h') {
                $limit = 18;
            }
            if ($request->nama) {
                $request->validate([
                    'nama' => 'required|string',
                ]);
                $barangs = Barang::with('satuan', 'ukuran')->select('id', 'satuan_id', 'nama', 'stok', 'harga_jual', 'foto')->orderBy('id', 'desc')->where('nama', 'like', '%' . $request->nama . '%')->limit($limit);
            } else {
                $barangs = Barang::with('satuan', 'ukuran')->select('id', 'satuan_id', 'nama', 'stok', 'harga_jual', 'foto')->orderBy('id', 'desc')->limit($limit);
            }
            if ($barangs->count() > 0) {
                $fullData = [];
                foreach ($barangs->get() as $barang) {
                    $ukuran = "";
                    foreach ($barang->ukuran as $u) {
                        if ($u === $barang->ukuran->last()) {
                            $ukuran .= $u->nama;
                        } else {
                            $ukuran .= $u->nama . ", ";
                        }
                    }
                    $fullData[] = [
                        'id' => enkrip($barang->id),
                        'status' => $barang->stok < 1 ? 'po' : 'siap',
                        'satuan' => $barang->satuan ? $barang->satuan->nama : '',
                        'nama' => strlen($barang->nama) >= 60 ? substr($barang->nama, 0, 60) . '...' : $barang->nama,
                        'stok' => $barang->stok,
                        'harga' => rupiah($barang->harga_jual),
                        'ukuran' => $ukuran,
                        'foto' => $barang->foto !== null ? $barang->foto : asset('img/product/emty.png'),
                    ];
                }
                return response()->json($fullData, 200);
            } else {
                return response()->json(null, 404);
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
            'bayar' => 'required|string',
            'total' => 'required|numeric',
        ]);
        $dataKeranjang = session()->get('keranjang-penjualan');
        if (isset($dataKeranjang)) {
            $tanggal = time();
            $idPenjualan = id();
            $idAnggota = null;
            $jenisPenjualan = 1;
            $statusPenjualan = 1;
            $bayar = str_replace(",", "", $request->bayar);
            if ($request->id !== "biasa") {
                $jenisPenjualan = 2;
                $idAnggota = dekrip($request->id);
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
                    'anggota_id' => $idAnggota,
                    'nama_pembeli' => $request->nama,
                    'tanggal' => $tanggal,
                    'bayar' => $bayar,
                    'total' => $request->total,
                    'jenis' => $jenisPenjualan,
                    'status' => $statusPenjualan,
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
                    PenjualanDetail::create([
                        'id' => id(),
                        'penjualan_id' => $idPenjualan,
                        'barang_id' => $data['id'],
                        'kuantitas' => $data['kuantitas'],
                        'harga' => $harga,
                        'status' => $data['status'] === 'siap' ? 2 : 1,
                    ]);
                }
                if ($barangPo) {
                    $penjualan->update([
                        'status' => 1,
                    ]);
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
                            'tanggal' => $tanggal,
                            'nominal' => $bayar,
                        ]);
                    }
                }
                DB::commit();
                session()->forget('keranjang-penjualan');
                return response()->json([
                    'data' => [
                        'id' => $idPenjualan,
                        'total' => rupiah($request->total),
                        'bayar' => rupiah($bayar),
                        'kembalian' => $bayar <= $request->total ? 0 : rupiah($bayar - $request->total),
                        // 25200 GMT + 07:00 (WIB)
                        'tgl' => date('Y-m-d H:i:s', ($tanggal + 25200)),
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
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Keranjang belanja kosong',
            ]);
        }
    }
}

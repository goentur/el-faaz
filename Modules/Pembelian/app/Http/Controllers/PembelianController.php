<?php

namespace Modules\Pembelian\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Angsuran;
use App\Models\Pemasok;
use App\Models\PemasokBarangDetail;
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
            'pemasoks' => Pemasok::select('id', 'nama')->where(['status' => 1])->get(),
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    public function dataBarang(Request $request): JsonResponse
    {
        $request->validate([
            'pemasok' => 'required|string',
        ]);
        if ($request->ajax()) {
            $idPemasok = dekrip($request->pemasok);
            $pemasok = Pemasok::select('id')->where(['id' => $idPemasok]);
            if ($pemasok->count() > 0) {
                $limit = 16;
                if (auth()->user()->sidebar === 'h') {
                    $limit = 20;
                }
                if ($request->nama) {
                    $request->validate([
                        'nama' => 'required|string',
                    ]);
                    $pemasokBarangDetail = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->where('pemasok_id', $idPemasok)->orderBy('stok', 'ASC')->orderBy('id', 'desc')->whereHas('barangDetail', function ($query) use ($request) {
                        $query->where('nama', 'like', '%' . $request->nama . '%');
                    })->limit($limit);
                } else {
                    $pemasokBarangDetail = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->where('pemasok_id', $idPemasok)->orderBy('stok', 'ASC')->orderBy('id', 'desc')->limit($limit);
                }
                if ($pemasokBarangDetail->count() > 0) {
                    $fullData = [];
                    foreach ($pemasokBarangDetail->get() as $pbd) {
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
                                'pemasok' => $pbd->pemasok ? $pbd->pemasok->nama : '',
                                'satuan' => $satuan,
                                'nama' => $barang . '' . $warna,
                                'stok' => $pbd->stok,
                                'harga' => rupiah($pbd->harga_beli),
                                'ukuran' => $ukuran,
                                'foto' => $foto,
                            ];
                        }
                    }
                    return response()->json($fullData, 200);
                } else {
                    return response()->json('Barang tidak ditemukan', 404);
                }
            } else {
                return response()->json('Pemasok tidak ditemukan', 404);
            }
        }
    }
    public function selesai(Request $request): JsonResponse
    {
        $request->validate([
            'pemasok' => 'required|string',
            'barang' => 'required|array|min:1',
            'barang.*.hargaBarang' => 'required|string',
            'barang.*.id' => 'required|string',
            'barang.*.kuantitas' => 'required|numeric',
            'barang.*.namaBarang' => 'required|string',
            'total' => 'required|numeric',
            'keterangan' => 'required|string',
        ]);
        if ($request->ajax()) {
            $user = auth()->user()->id;
            $tanggal = time();
            $idPembelian = id();
            $idAngsuran = id();
            $idPemasok = dekrip($request->pemasok);
            $statusPembelian = 1;
            try {
                DB::beginTransaction();
                // Create Pembelian
                Pembelian::create([
                    'id' => $idPembelian,
                    'user_id' => $user,
                    'pemasok_id' => $idPemasok,
                    'tanggal' => $tanggal,
                    'total' => $request->total,
                    'keterangan' => $request->keterangan,
                    'status' => $statusPembelian,
                ]);

                foreach ($request->barang as $data) {
                    $idBarang = dekrip($data['id']);
                    $hargaBarang = str_replace(",", "", $data['hargaBarang']);
                    PembelianDetail::create([
                        'id' => id(),
                        'pembelian_id' => $idPembelian,
                        'pemasok_barang_detail_id' => $idBarang,
                        'tanggal' => $tanggal,
                        'kuantitas' => $data['kuantitas'],
                        'harga' => $hargaBarang,
                    ]);
                    $pemasokBarangDetail = PemasokBarangDetail::select('id', 'stok')->find($idBarang);
                    $pemasokBarangDetail->update([
                        'stok' => $pemasokBarangDetail->stok + $data['kuantitas'],
                        'harga_beli' => $hargaBarang,
                    ]);
                }
                Pemasok::select('id')->find($idPemasok)->update([
                    'status' => 2,
                ]);
                // ANGSURAN
                Angsuran::create([
                    'id' => $idAngsuran,
                    'transaksi_id' => $idPembelian,
                    'jenis' => 1,
                    'status' => 1,
                ]);
                DB::commit();
                return response()->json([
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

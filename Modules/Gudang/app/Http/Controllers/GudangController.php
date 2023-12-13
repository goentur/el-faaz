<?php

namespace Modules\Gudang\app\Http\Controllers;

use App\Models\Pemasok;
use App\Models\PemasokBarangDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class GudangController extends Controller
{
    protected $attribute = [
        'view' => 'gudang::',
        'link' => 'gudang.',
        'linkSampah' => 'gudang.',
        'title' => 'gudang',
    ];

    public function index()
    {
        $data = [
            'attribute' => $this->attribute,
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    function dataGudang(Request $request): JsonResponse
    {
        $request->validate([
            'pemasok' => 'required|string',
        ]);
        if ($request->ajax()) {
            if (dekrip($request->pemasok) === 'semua') {
                $pemasokBarangDetails = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->orderBy('stok', 'asc')->orderBy('id', 'desc');
            } else {
                $pemasokBarangDetails = PemasokBarangDetail::with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->where(['pemasok_id' => dekrip($request->pemasok)])->orderBy('stok', 'asc')->orderBy('id', 'desc');
            }
            if ($pemasokBarangDetails->count() > 0) {
                $data = [];
                $total = 0;
                foreach ($pemasokBarangDetails->get() as $n => $pbd) {
                    if ($pbd->barangDetail) {
                        $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pbd->barangDetail->barang) {
                            $barang = $pbd->barangDetail->barang->nama;
                        }
                        $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pbd->barangDetail->warna) {
                            $warna = ' - ' . $pbd->barangDetail->warna->nama;
                        }
                        $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pbd->barangDetail->satuan) {
                            $satuan = $pbd->barangDetail->satuan->nama;
                        }
                        $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
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
                        $statusFoto = false;
                        $urlFoto = '<span class="badge bg-danger">FOTO KOSONG</span>';
                        if ($pbd->barangDetail->foto) {
                            $statusFoto = true;
                            $urlFoto = url($pbd->barangDetail->foto);
                        }
                        $jumlah = $pbd->stok * $pbd->harga_beli;
                        $total += $jumlah;
                        $data[] = [
                            'no' => ++$n . '.',
                            'pemasok' => $pbd->pemasok ? $pbd->pemasok->nama : '',
                            'satuan' => $satuan,
                            'nama' => $barang . '' . $warna,
                            'stok' => $pbd->stok,
                            'harga' => rupiah($pbd->harga_beli),
                            'ukuran' => $ukuran,
                            'jumlah' => rupiah($jumlah),
                            'foto' => [
                                'status' => $statusFoto,
                                'url' => $urlFoto,
                                'nama' => $barang,
                            ],
                            'aksi' => [
                                'ubah' => route($this->attribute['link'] . 'edit', enkrip($pbd->id)),
                                'detail' => route($this->attribute['link'] . 'detail.index', enkrip($pbd->id)),
                                'id' => enkrip($pbd->id),
                            ],
                        ];
                    }
                }
                return response()->json(['data' => $data, 'total' => $total], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }

    public function create()
    {
        $data = [
            'attribute' => $this->attribute,
            'pemasoks' => Pemasok::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemasok' => 'required|numeric',
            'barang' => 'required|numeric',
        ]);
        PemasokBarangDetail::create([
            'pemasok_id' => $request->pemasok,
            'barang_detail_id' => $request->barang,
            'stok' => 0,
            'harga_beli' => 0,
        ]);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil disimpan']);
    }

    public function show($id)
    {
        return abort(404);
    }

    public function edit($id)
    {
        $kirim = [
            'attribute' => $this->attribute,
            'data' => PemasokBarangDetail::select('id', 'pemasok_id')->find(dekrip($id)),
            'pemasoks' => Pemasok::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pemasok' => 'required|numeric',
            'barang' => 'required|numeric',
        ]);
        PemasokBarangDetail::select('id')->find(dekrip($id))->update([
            'pemasok_id' => $request->pemasok,
            'barang_detail_id' => $request->barang,
        ]);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            PemasokBarangDetail::select('id')->find(dekrip($request->id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
    public function sampah()
    {
        $data = [
            'attribute' => $this->attribute,
        ];
        return view($this->attribute['view'] . 'sampah', $data);
    }
    function sampahDataGudang(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $pemasokBarangDetails = PemasokBarangDetail::onlyTrashed()->with('pemasok', 'barangDetail')->select('id', 'pemasok_id', 'barang_detail_id', 'stok', 'harga_beli')->orderBy('deleted_at', 'desc');
            if ($pemasokBarangDetails->count() > 0) {
                $data = [];
                foreach ($pemasokBarangDetails->get() as $n => $pbd) {
                    if ($pbd->barangDetail) {
                        $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pbd->barangDetail->barang) {
                            $barang = $pbd->barangDetail->barang->nama;
                        }
                        $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pbd->barangDetail->warna) {
                            $warna = ' - ' . $pbd->barangDetail->warna->nama;
                        }
                        $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pbd->barangDetail->satuan) {
                            $satuan = $pbd->barangDetail->satuan->nama;
                        }
                        $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
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
                        $statusFoto = false;
                        $urlFoto = '<span class="badge bg-danger">FOTO KOSONG</span>';
                        if ($pbd->barangDetail) {
                            $statusFoto = true;
                            $urlFoto = url($pbd->barangDetail->foto);
                        }
                        $data[] = [
                            'no' => ++$n . '.',
                            'pemasok' => $pbd->pemasok ? $pbd->pemasok->nama : '',
                            'satuan' => $satuan,
                            'nama' => $barang . '' . $warna,
                            'stok' => $pbd->stok,
                            'harga' => rupiah($pbd->harga_beli),
                            'ukuran' => $ukuran,
                            'jumlah' => rupiah($pbd->stok * $pbd->harga_beli),
                            'foto' => [
                                'status' => $statusFoto,
                                'url' => $urlFoto,
                                'nama' => $barang,
                            ],
                            'aksi' => [
                                'id' => enkrip($pbd->id),
                            ],
                        ];
                    }
                }
                return response()->json(['data' => $data], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }

    public function memulihkan(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            PemasokBarangDetail::onlyTrashed()->select('id')->find(dekrip($request->id))->restore();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dipulihkan.',
            ]);
        }
    }

    public function permanen(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            PemasokBarangDetail::onlyTrashed()->select('id')->find(dekrip($request->id))->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus selamanya.',
            ]);
        }
    }
}

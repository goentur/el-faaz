<?php

namespace Modules\Gudang\app\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangDetail;
use App\Models\Pemasok;
use App\Models\Satuan;
use App\Models\Ukuran;
use App\Models\Warna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

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
            'pemasoks' => Pemasok::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    function dataGudang(Request $request): JsonResponse
    {
        $request->validate([
            'pemasok' => 'required|string',
        ]);
        if ($request->ajax()) {
            if (dekrip($request->pemasok) === '0') {
                return response()->json(['data' => []], 200);
            } else if (dekrip($request->pemasok) === 'semua') {
                $barangDetails = BarangDetail::with('pemasok', 'barang', 'satuan', 'ukuran')->select('id', 'pemasok_id', 'barang_id', 'satuan_id', 'stok', 'harga_beli')->orderBy('stok', 'asc')->orderBy('id', 'desc')->whereHas('barang', function ($query) {
                    $query->where('deleted_at', null);
                });
            } else {
                $barangDetails = BarangDetail::with('pemasok', 'barang', 'satuan', 'ukuran')->select('id', 'pemasok_id', 'barang_id', 'satuan_id', 'stok', 'harga_beli')->where(['pemasok_id' => dekrip($request->pemasok)])->orderBy('stok', 'asc')->orderBy('id', 'desc')->whereHas('barang', function ($query) {
                    $query->where('deleted_at', null);
                });
            }
            if ($barangDetails->count() > 0) {
                $data = [];
                foreach ($barangDetails->get() as $n => $barangDetail) {
                    $barang = "";
                    if ($barangDetail->barang) {
                        $barang = $barangDetail->barang->nama;
                        if ($barangDetail->barang->warna) {
                            $barang .= ' - ' . $barangDetail->barang->warna->nama;
                        }
                    }
                    $ukuran = "";
                    foreach ($barangDetail->ukuran as $u) {
                        if ($u === $barangDetail->ukuran->last()) {
                            $ukuran .= $u->nama;
                        } else {
                            $ukuran .= $u->nama . ", ";
                        }
                    }
                    $statusFoto = false;
                    $urlFoto = '<span class="badge bg-danger">FOTO KOSONG</span>';
                    if ($barangDetail->barang && $barangDetail->barang->foto) {
                        $statusFoto = true;
                        $urlFoto = url($barangDetail->barang->foto);
                    }
                    $data[] = [
                        'no' => ++$n . '.',
                        'pemasok' => $barangDetail->pemasok ? $barangDetail->pemasok->nama : '',
                        'satuan' => $barangDetail->satuan ? $barangDetail->satuan->nama : '',
                        'nama' => $barang,
                        'stok' => $barangDetail->stok,
                        'harga' => rupiah($barangDetail->harga_beli),
                        'ukuran' => $ukuran,
                        'jumlah' => rupiah($barangDetail->stok * $barangDetail->harga_beli),
                        'foto' => [
                            'status' => $statusFoto,
                            'url' => $urlFoto,
                            'nama' => $barang,
                        ],
                        'aksi' => [
                            'link' => route($this->attribute['link'] . 'edit', enkrip($barangDetail->id)),
                            'id' => enkrip($barangDetail->id),
                        ],
                    ];
                }
                return response()->json(['data' => $data], 200);
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
            'barangs' => Barang::with('warna')->select('id', 'warna_id', 'nama')->get(),
            'satuans' => Satuan::select('id', 'nama')->get(),
            'ukurans' => Ukuran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemasok' => 'required|numeric',
            'barang' => 'required|numeric',
            'satuan' => 'required|numeric',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|numeric|distinct|min:1',
        ]);
        $barangDetail = BarangDetail::create([
            'pemasok_id' => $request->pemasok,
            'barang_id' => $request->barang,
            'satuan_id' => $request->satuan,
            'stok' => 0,
            'harga_beli' => 0,
        ]);
        $barangDetail->ukuran()->sync($request->ukuran);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil disimpan']);
    }

    public function show($id)
    {
        return abort('404');
    }

    public function edit($id)
    {
        $kirim = [
            'attribute' => $this->attribute,
            'data' => BarangDetail::with('ukuran')->select('id', 'pemasok_id', 'barang_id', 'satuan_id')->find(dekrip($id)),
            'pemasoks' => Pemasok::select('id', 'nama')->get(),
            'barangs' => Barang::with('warna')->select('id', 'warna_id', 'nama')->get(),
            'satuans' => Satuan::select('id', 'nama')->get(),
            'ukurans' => Ukuran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pemasok' => 'required|numeric',
            'barang' => 'required|numeric',
            'satuan' => 'required|numeric',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|numeric|distinct|min:1',
        ]);
        $barangDetail = BarangDetail::with('ukuran')->select('id')->find(dekrip($id));
        $barangDetail->update([
            'pemasok_id' => $request->pemasok,
            'barang_id' => $request->barang,
            'satuan_id' => $request->satuan,
        ]);
        $barangDetail->ukuran()->sync($request->ukuran);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            BarangDetail::select('id')->find(dekrip($request->id))->delete();
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
            $barangDetails = BarangDetail::onlyTrashed()->with('pemasok', 'barang', 'satuan', 'ukuran')->select('id', 'pemasok_id', 'barang_id', 'satuan_id', 'stok', 'harga_beli')->orderBy('deleted_at', 'desc');
            if ($barangDetails->count() > 0) {
                $data = [];
                foreach ($barangDetails->get() as $n => $barangDetail) {
                    $barang = "";
                    if ($barangDetail->barang) {
                        $barang = $barangDetail->barang->nama;
                        if ($barangDetail->barang->warna) {
                            $barang .= ' - ' . $barangDetail->barang->warna->nama;
                        }
                    }
                    $ukuran = "";
                    foreach ($barangDetail->ukuran as $u) {
                        if ($u === $barangDetail->ukuran->last()) {
                            $ukuran .= $u->nama;
                        } else {
                            $ukuran .= $u->nama . ", ";
                        }
                    }
                    $statusFoto = false;
                    $urlFoto = '<span class="badge bg-danger">FOTO KOSONG</span>';
                    if ($barangDetail->barang && $barangDetail->barang->foto) {
                        $statusFoto = true;
                        $urlFoto = url($barangDetail->barang->foto);
                    }
                    $data[] = [
                        'no' => ++$n . '.',
                        'pemasok' => $barangDetail->pemasok ? $barangDetail->pemasok->nama : '',
                        'satuan' => $barangDetail->satuan ? $barangDetail->satuan->nama : '',
                        'nama' => $barang,
                        'stok' => $barangDetail->stok,
                        'harga' => rupiah($barangDetail->harga_beli),
                        'ukuran' => $ukuran,
                        'jumlah' => rupiah($barangDetail->stok * $barangDetail->harga_beli),
                        'foto' => [
                            'status' => $statusFoto,
                            'url' => $urlFoto,
                            'nama' => $barang,
                        ],
                        'aksi' => [
                            'id' => enkrip($barangDetail->id),
                        ],
                    ];
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
            BarangDetail::onlyTrashed()->select('id')->find(dekrip($request->id))->restore();
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
            BarangDetail::onlyTrashed()->select('id')->find(dekrip($request->id))->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus selamanya.',
            ]);
        }
    }
}

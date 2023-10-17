<?php

namespace Modules\Barang\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangUkuran;
use App\Models\Pemasok;
use App\Models\Satuan;
use App\Models\Ukuran;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class BarangController extends Controller
{
    protected $attribute = [
        'view' => 'barang::',
        'link' => 'barang.',
        'linkSampah' => 'barang.',
        'title' => 'barang',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Barang::with('pemasok', 'ukuran', 'satuan')->select('id', 'pemasok_id', 'satuan_id', 'nama', 'stok', 'hpp', 'harga_jual', 'harga_anggota', 'foto'))
                ->addIndexColumn()
                ->editColumn('foto', function (Barang $data) {
                    return view($this->attribute['view'] . 'foto', [
                        'data' => $data,
                    ]);
                })
                ->editColumn('pemasok.nama', function (Barang $data) {
                    return $data->pemasok ? $data->pemasok->nama : view('errors.master-data');
                })
                ->editColumn('satuan.nama', function (Barang $data) {
                    return $data->satuan ? $data->satuan->nama : view('errors.master-data');
                })
                ->editColumn('ukuran.nama', function (Barang $data) {
                    $ukuran = "";
                    foreach ($data->ukuran as $u) {
                        if ($u === $data->ukuran->last()) {
                            $ukuran .= $u->nama;
                        } else {
                            $ukuran .= $u->nama . ", ";
                        }
                    }
                    return $ukuran;
                })
                ->editColumn('hpp', function (Barang $data) {
                    return rupiah($data->hpp);
                })
                ->editColumn('harga_jual', function (Barang $data) {
                    return rupiah($data->harga_jual);
                })
                ->editColumn('harga_anggota', function (Barang $data) {
                    return rupiah($data->harga_anggota);
                })
                ->addColumn('aksi', function (Barang $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center top', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['class' => 'w-10 text-center', 'data' => 'foto', 'name' => 'foto', 'title' => 'FOTO'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'pemasok.nama', 'name' => 'pemasok.nama', 'title' => 'PEMASOK'])
            ->addColumn(['class' => 'top', 'data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'satuan.nama', 'name' => 'satuan.nama', 'title' => 'SATUAN'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'ukuran.nama', 'name' => 'ukuran.nama', 'title' => 'UKURAN'])
            ->addColumn(['class' => 'w-1 text-center top', 'data' => 'stok', 'name' => 'stok', 'title' => 'STOK'])
            ->addColumn(['class' => 'w-1 text-end top', 'data' => 'hpp', 'name' => 'hpp', 'title' => 'HPP'])
            ->addColumn(['class' => 'w-1 text-end top', 'data' => 'harga_jual', 'name' => 'harga_jual', 'title' => 'JUAL'])
            ->addColumn(['class' => 'w-1 text-end top', 'data' => 'harga_anggota', 'name' => 'harga_anggota', 'title' => 'ANGGOTA'])
            ->addColumn(['class' => 'w-1 top', 'data' => 'aksi', 'name' => 'aksi', 'title' => 'AKSI'])
            ->parameters([
                'ordering' => false,
                'responsive' => true,
                'bAutoWidth' => false,
                'lengthMenu' => [25, 50, 75, 100],
                'language' => [
                    'url' => asset('js/id.json'),
                ],
            ]);
        $data = [
            'attribute' => $this->attribute,
            'dataTable' => $dataTable,
        ];
        return view($this->attribute['view'] . 'index', $data);
    }

    public function create()
    {
        $data = [
            'attribute' => $this->attribute,
            'pemasoks' => Pemasok::select('id', 'nama')->get(),
            'satuans' => Satuan::select('id', 'nama')->get(),
            'ukurans' => Ukuran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pemasok' => 'required|numeric',
            'satuan' => 'required|numeric',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|numeric|distinct|min:1',
            'nama' => 'required|string|max:255',
            'stok' => 'required|numeric',
            'hpp' => 'required|string',
            'harga_jual' => 'required|string',
            'harga_anggota' => 'required|string',
            'foto' => 'required|string',
        ]);
        $barang = Barang::create([
            'pemasok_id' => $request->pemasok,
            'satuan_id' => $request->satuan,
            'nama' => $request->nama,
            'stok' => $request->stok,
            'hpp' => str_replace(",", "", $request->hpp),
            'harga_jual' => str_replace(",", "", $request->harga_jual),
            'harga_anggota' => str_replace(",", "", $request->harga_anggota),
            'foto' => $request->foto,
        ]);
        $barang->ukuran()->sync($request->ukuran);
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
            'data' => Barang::with('ukuran')->select('id', 'pemasok_id', 'satuan_id', 'nama', 'stok', 'hpp', 'harga_jual', 'harga_anggota', 'foto')->find(dekrip($id)),
            'pemasoks' => Pemasok::select('id', 'nama')->get(),
            'satuans' => Satuan::select('id', 'nama')->get(),
            'ukurans' => Ukuran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pemasok' => 'required|numeric',
            'satuan' => 'required|numeric',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|numeric|distinct|min:1',
            'nama' => 'required|string|max:255',
            'hpp' => 'required|string',
            'harga_jual' => 'required|string',
            'harga_anggota' => 'required|string',
            'foto' => 'required|string',
        ]);
        $barang = Barang::with('ukuran')->select('id')->find(dekrip($id));
        $barang->update([
            'pemasok_id' => $request->pemasok,
            'satuan_id' => $request->satuan,
            'nama' => $request->nama,
            'hpp' => str_replace(",", "", $request->hpp),
            'harga_jual' => str_replace(",", "", $request->harga_jual),
            'harga_anggota' => str_replace(",", "", $request->harga_anggota),
            'foto' => $request->foto,
        ]);
        $barang->ukuran()->sync($request->ukuran);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            Barang::select('id')->find(dekrip($request->id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
    public function sampah(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Barang::onlyTrashed()->with('pemasok', 'satuan')->select('id', 'pemasok_id', 'satuan_id', 'nama', 'stok', 'hpp', 'harga_jual', 'harga_anggota', 'foto'))
                ->addIndexColumn()
                ->editColumn('foto', function (Barang $data) {
                    return view($this->attribute['view'] . 'foto', [
                        'data' => $data,
                    ]);
                })
                ->editColumn('pemasok.nama', function (Barang $data) {
                    return $data->pemasok ? $data->pemasok->nama : view('errors.master-data');
                })
                ->editColumn('satuan.nama', function (Barang $data) {
                    return $data->satuan ? $data->satuan->nama : view('errors.master-data');
                })
                ->editColumn('hpp', function (Barang $data) {
                    return rupiah($data->hpp);
                })
                ->editColumn('harga_jual', function (Barang $data) {
                    return rupiah($data->harga_jual);
                })
                ->editColumn('harga_anggota', function (Barang $data) {
                    return rupiah($data->harga_anggota);
                })
                ->addColumn('aksi', function (Barang $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi-sampah', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center top', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['class' => 'w-10 text-center', 'data' => 'foto', 'name' => 'foto', 'title' => 'FOTO'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'pemasok.nama', 'name' => 'pemasok.nama', 'title' => 'PEMASOK'])
            ->addColumn(['class' => 'top', 'data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'satuan.nama', 'name' => 'satuan.nama', 'title' => 'SATUAN'])
            ->addColumn(['class' => 'w-1 text-center top', 'data' => 'stok', 'name' => 'stok', 'title' => 'STOK'])
            ->addColumn(['class' => 'w-1 text-end top', 'data' => 'hpp', 'name' => 'hpp', 'title' => 'HPP'])
            ->addColumn(['class' => 'w-1 text-end top', 'data' => 'harga_jual', 'name' => 'harga_jual', 'title' => 'JUAL'])
            ->addColumn(['class' => 'w-1 text-end top', 'data' => 'harga_anggota', 'name' => 'harga_anggota', 'title' => 'ANGGOTA'])
            ->addColumn(['class' => 'w-1 top', 'data' => 'aksi', 'name' => 'aksi', 'title' => 'AKSI'])
            ->parameters([
                'ordering' => false,
                'responsive' => true,
                'bAutoWidth' => false,
                'lengthMenu' => [25, 50, 75, 100],
                'language' => [
                    'url' => asset('js/id.json'),
                ],
            ]);
        $data = [
            'attribute' => $this->attribute,
            'dataTable' => $dataTable,
        ];
        return view($this->attribute['view'] . 'sampah', $data);
    }

    public function memulihkan(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            Barang::onlyTrashed()->select('id')->find(dekrip($request->id))->restore();
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
            Barang::onlyTrashed()->select('id')->find(dekrip($request->id))->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus selamanya.',
            ]);
        }
    }
}

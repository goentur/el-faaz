<?php

namespace Modules\Barang\Http\Controllers;

use App\Models\Barang;
use App\Models\Warna;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
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
            return DataTables::eloquent(Barang::with('warna')->select('id', 'warna_id', 'nama', 'foto')->orderBy('id', 'desc'))
                ->addIndexColumn()
                ->editColumn('warna.nama', function (Barang $data) {
                    return $data->warna ? $data->warna->nama : view('errors.master-data');
                })
                ->editColumn('foto', function (Barang $data) {
                    return view($this->attribute['view'] . 'foto', [
                        'data' => $data,
                    ]);
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
            ->addColumn(['class' => 'top', 'data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'warna.nama', 'name' => 'warna.nama', 'title' => 'WARNA'])
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
            'warnas' => Warna::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'warna' => 'required|numeric',
            'nama' => 'required|string|max:255',
            'foto' => 'required|string',
        ]);
        Barang::create([
            'warna_id' => $request->warna,
            'nama' => $request->nama,
            'foto' => Str::after($request->foto, url('')),
        ]);
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
            'data' => Barang::with('warna')->select('id', 'warna_id', 'nama', 'foto')->find(dekrip($id)),
            'warnas' => Warna::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'warna' => 'required|numeric',
            'nama' => 'required|string|max:255',
            'foto' => 'required|string',
        ]);
        Barang::select('id')->find(dekrip($id))->update([
            'ukuran_id' => $request->ukuran,
            'nama' => $request->nama,
            'foto' => $request->foto,
        ]);
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
            return DataTables::eloquent(Barang::onlyTrashed()->with('warna')->select('id', 'warna_id', 'nama', 'foto')->orderBy('id', 'desc'))
                ->addIndexColumn()
                ->editColumn('warna.nama', function (Barang $data) {
                    return $data->warna ? $data->warna->nama : view('errors.master-data');
                })
                ->editColumn('foto', function (Barang $data) {
                    return view($this->attribute['view'] . 'foto', [
                        'data' => $data,
                    ]);
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
            ->addColumn(['class' => 'top', 'data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-10 top', 'data' => 'warna.nama', 'name' => 'warna.nama', 'title' => 'WARNA'])
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

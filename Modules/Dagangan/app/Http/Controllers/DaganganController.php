<?php

namespace Modules\Dagangan\app\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangDetail;
use App\Models\Satuan;
use App\Models\Ukuran;
use App\Models\Warna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class DaganganController extends Controller
{
    protected $attribute = [
        'view' => 'dagangan::',
        'link' => 'dagangan.',
        'linkSampah' => 'dagangan.',
        'title' => 'dagangan',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(BarangDetail::with('barang', 'satuan', 'warna', 'ukuran')->select('id', 'barang_id', 'satuan_id', 'warna_id', 'foto')->orderBy('id', 'desc'))
                ->addIndexColumn()
                ->editColumn('barang.nama', function (BarangDetail $data) {
                    return $data->barang ? $data->barang->nama : view('errors.master-data');
                })
                ->editColumn('satuan.nama', function (BarangDetail $data) {
                    return $data->satuan ? $data->satuan->nama : view('errors.master-data');
                })
                ->editColumn('warna.nama', function (BarangDetail $data) {
                    return $data->warna ? $data->warna->nama : view('errors.master-data');
                })
                ->editColumn('foto', function (BarangDetail $data) {
                    return view($this->attribute['view'] . 'foto', [
                        'data' => $data,
                    ]);
                })
                ->editColumn('ukuran.nama', function (BarangDetail $data) {
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
                ->addColumn('aksi', function (BarangDetail $data) {
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
            ->addColumn(['class' => 'top', 'data' => 'barang.nama', 'name' => 'barang.nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-1 top', 'data' => 'satuan.nama', 'name' => 'satuan.nama', 'title' => 'SATUAN'])
            ->addColumn(['class' => 'w-5 top', 'data' => 'warna.nama', 'name' => 'warna.nama', 'title' => 'WARNA'])
            ->addColumn(['class' => 'w-5 top', 'data' => 'ukuran.nama', 'name' => 'ukuran.nama', 'title' => 'UKURAN'])
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
            'barangs' => Barang::select('id', 'nama')->get(),
            'satuans' => Satuan::select('id', 'nama')->get(),
            'warnas' => Warna::select('id', 'nama')->get(),
            'ukurans' => Ukuran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'foto' => 'required|string',
            'barang' => 'required|numeric',
            'satuan' => 'required|numeric',
            'warna' => 'required|numeric',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|numeric|distinct|min:1',
        ]);
        $barangDetail = BarangDetail::create([
            'barang_id' => $request->barang,
            'satuan_id' => $request->satuan,
            'warna_id' => $request->warna,
            'foto' => Str::after($request->foto, url('')),
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
            'data' => BarangDetail::with('warna')->select('id', 'barang_id', 'satuan_id', 'warna_id', 'foto')->find(dekrip($id)),
            'barangs' => Barang::select('id', 'nama')->get(),
            'satuans' => Satuan::select('id', 'nama')->get(),
            'warnas' => Warna::select('id', 'nama')->get(),
            'ukurans' => Ukuran::select('id', 'nama')->get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'foto' => 'required|string',
            'barang' => 'required|numeric',
            'satuan' => 'required|numeric',
            'warna' => 'required|numeric',
            'ukuran' => 'required|array|min:1',
            'ukuran.*' => 'required|numeric|distinct|min:1',
        ]);
        $barangDetail = BarangDetail::with('ukuran')->select('id')->find(dekrip($id));
        $barangDetail->update([
            'barang_id' => $request->barang,
            'satuan_id' => $request->satuan,
            'warna_id' => $request->warna,
            'foto' => Str::after($request->foto, url('')),
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
    public function sampah(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(BarangDetail::onlyTrashed()->with('barang', 'satuan', 'warna', 'ukuran')->select('id', 'barang_id', 'satuan_id', 'warna_id', 'foto')->orderBy('id', 'desc'))
                ->addIndexColumn()
                ->editColumn('barang.nama', function (BarangDetail $data) {
                    return $data->barang ? $data->barang->nama : view('errors.master-data');
                })
                ->editColumn('satuan.nama', function (BarangDetail $data) {
                    return $data->satuan ? $data->satuan->nama : view('errors.master-data');
                })
                ->editColumn('warna.nama', function (BarangDetail $data) {
                    return $data->warna ? $data->warna->nama : view('errors.master-data');
                })
                ->editColumn('foto', function (BarangDetail $data) {
                    return view($this->attribute['view'] . 'foto', [
                        'data' => $data,
                    ]);
                })
                ->editColumn('ukuran.nama', function (BarangDetail $data) {
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
                ->addColumn('aksi', function (BarangDetail $data) {
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
            ->addColumn(['class' => 'top', 'data' => 'barang.nama', 'name' => 'barang.nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-1 top', 'data' => 'satuan.nama', 'name' => 'satuan.nama', 'title' => 'SATUAN'])
            ->addColumn(['class' => 'w-5 top', 'data' => 'warna.nama', 'name' => 'warna.nama', 'title' => 'WARNA'])
            ->addColumn(['class' => 'w-5 top', 'data' => 'ukuran.nama', 'name' => 'ukuran.nama', 'title' => 'UKURAN'])
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
    public function data(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string',
        ]);
        if ($request->ajax()) {
            $barangDetail = BarangDetail::with('barang', 'satuan', 'warna', 'ukuran')->select('id', 'barang_id', 'satuan_id', 'warna_id', 'foto')->orderBy('id', 'desc')->whereHas('barang', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            })->limit(10);
            if ($barangDetail->count() > 0) {
                $data = [];
                foreach ($barangDetail->get() as $k) {
                    $barang = "BARANG TIDAK TERDAFTAR";
                    if ($k->barang) {
                        $barang = $k->barang->nama;
                    }
                    $satuan = " (SATUAN TIDAK TERDAFTAR)";
                    if ($k->satuan) {
                        $satuan = ' (' . $k->satuan->nama . ')';
                    }
                    $warna = " - WARNA TIDAK TERDAFTAR";
                    if ($k->warna) {
                        $warna = ' - ' . $k->warna->nama;
                    }
                    $ukuran = "";
                    if ($k->ukuran) {
                        foreach ($k->ukuran as $u) {
                            if ($u === $k->ukuran->last()) {
                                $ukuran .= $u->nama;
                            } else {
                                $ukuran .= $u->nama . ", ";
                            }
                        }
                        $ukuran = ' (' . $ukuran . ')';
                    }
                    $data[] = [
                        'value' => $k->id,
                        'label' => $barang . '' . $warna . '' . $satuan . '' . $ukuran,
                    ];
                }
                return response()->json($data);
            } else {
                return response()->json([]);
            }
        }
    }
}

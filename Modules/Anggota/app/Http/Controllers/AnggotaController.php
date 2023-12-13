<?php

namespace Modules\Anggota\app\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class AnggotaController extends Controller
{
    protected $attribute = [
        'view' => 'anggota::',
        'link' => 'anggota.',
        'linkSampah' => 'anggota.',
        'title' => 'anggota',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Anggota::with('piutangDagang')->select('id', 'nama'))
                ->addIndexColumn()
                ->editColumn('id', function (Anggota $data) {
                    $total = 0;
                    if ($data->piutangDagang) {
                        foreach ($data->piutangDagang as $p) {
                            $total += ($p->total - $p->bayar);
                        }
                    }
                    return rupiah($total);
                })
                ->addColumn('aksi', function (Anggota $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-7 text-end', 'data' => 'id', 'name' => 'id', 'title' => 'TOTAL PIUTANG'])
            ->addColumn(['class' => 'w-1', 'data' => 'aksi', 'name' => 'aksi', 'title' => 'AKSI'])
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
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);
        Anggota::create([
            'nama' => $request->nama,
        ]);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil disimpan']);
    }

    public function show($id)
    {
        $kirim = [
            'attribute' => $this->attribute,
            'data' => Anggota::select('id', 'nama')->find(dekrip($id)),
        ];
        return view($this->attribute['view'] . 'detail.index', $kirim);
    }

    public function edit($id)
    {
        $kirim = [
            'attribute' => $this->attribute,
            'data' => Anggota::select('id', 'nama')->find(dekrip($id)),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);
        Anggota::select('id')->find(dekrip($id))->update([
            'nama' => $request->nama,
        ]);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            Anggota::select('id')->find(dekrip($request->id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
    public function sampah(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Anggota::onlyTrashed()->select('id', 'nama'))
                ->addIndexColumn()
                ->addColumn('aksi', function (Anggota $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi-sampah', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-1', 'data' => 'aksi', 'name' => 'aksi', 'title' => 'AKSI'])
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
            Anggota::onlyTrashed()->select('id')->find(dekrip($request->id))->restore();
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
            Anggota::onlyTrashed()->select('id')->find(dekrip($request->id))->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus selamanya.',
            ]);
        }
    }
}

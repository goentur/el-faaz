<?php

namespace Modules\Akun\Http\Controllers;

use App\Models\Akun;
use App\Models\RiwayatAkun;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class AkunController extends Controller
{
    protected $attribute = [
        'view' => 'akun::',
        'link' => 'akun.',
        'linkSampah' => 'akun.',
        'title' => 'akun',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Akun::select('id', 'kode', 'nama', 'debet', 'kredit'))
                ->addIndexColumn()
                ->editColumn('debet', function (Akun $data) {
                    return rupiah($data->debet);
                })
                ->editColumn('kredit', function (Akun $data) {
                    return rupiah($data->kredit);
                })
                ->addColumn('aksi', function (Akun $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['class' => 'w-1 text-center', 'data' => 'kode', 'name' => 'kode', 'title' => 'KODE'])
            ->addColumn(['data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-5 text-end', 'data' => 'debet', 'name' => 'debet', 'title' => 'DEBET'])
            ->addColumn(['class' => 'w-5 text-end', 'data' => 'kredit', 'name' => 'kredit', 'title' => 'KREDIT'])
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
            'kode' => 'required|numeric|unique:akuns',
            'nama' => 'required|string|max:255',
            'debet' => 'required|string',
            'kredit' => 'required|string'
        ]);
        $akun = Akun::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'debet' => str_replace(",", "", $request->debet),
            'kredit' => str_replace(",", "", $request->kredit)
        ]);
        RiwayatAkun::create([
            'id' => id(),
            'user_id' => auth()->user()->id,
            'akun_id' => $akun->id,
            'tanggal' => time(),
            'debet' => str_replace(",", "", $request->debet),
            'kredit' => str_replace(",", "", $request->kredit),
            'keterangan' => "pendaftaran akun",
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
            'data' => Akun::select('id', 'kode', 'nama', 'debet', 'kredit')->find(dekrip($id)),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'debet' => 'required|string',
            'kredit' => 'required|string'
        ]);
        $idAkun = dekrip($id);
        $user = auth()->user();
        if ($user->role('developer')) {
            Akun::select('id')->find($idAkun)->update([
                'nama' => $request->nama,
                'debet' => str_replace(",", "", $request->debet),
                'kredit' => str_replace(",", "", $request->kredit)
            ]);
            RiwayatAkun::create([
                'id' => id(),
                'user_id' => $user->id,
                'akun_id' => $idAkun,
                'tanggal' => time(),
                'debet' => str_replace(",", "", $request->debet),
                'kredit' => str_replace(",", "", $request->kredit),
                'keterangan' => "perubahan nama dan/atau nominal debet maupun kredit",
            ]);
        } else {
            Akun::select('id')->find($idAkun)->update([
                'nama' => $request->nama,
            ]);
        }

        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            Akun::select('id')->find(dekrip($request->id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
    public function sampah(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(Akun::onlyTrashed()->select('id', 'kode', 'nama', 'debet', 'kredit'))
                ->addIndexColumn()
                ->editColumn('debet', function (Akun $data) {
                    return rupiah($data->debet);
                })
                ->editColumn('kredit', function (Akun $data) {
                    return rupiah($data->kredit);
                })
                ->addColumn('aksi', function (Akun $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi-sampah', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['class' => 'w-1 text-center', 'data' => 'kode', 'name' => 'kode', 'title' => 'KODE'])
            ->addColumn(['data' => 'nama', 'name' => 'nama', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-5 text-end', 'data' => 'debet', 'name' => 'debet', 'title' => 'DEBET'])
            ->addColumn(['class' => 'w-5 text-end', 'data' => 'kredit', 'name' => 'kredit', 'title' => 'KREDIT'])
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
            Akun::onlyTrashed()->select('id')->find(dekrip($request->id))->restore();
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
            Akun::onlyTrashed()->select('id')->find(dekrip($request->id))->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus selamanya.',
            ]);
        }
    }
}

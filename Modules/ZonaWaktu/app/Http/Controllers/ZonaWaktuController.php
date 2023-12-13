<?php

namespace Modules\ZonaWaktu\app\Http\Controllers;

use App\Models\ZonaWaktu;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class ZonaWaktuController extends Controller
{
    protected $attribute = [
        'view' => 'zonawaktu::',
        'link' => 'zona-waktu.',
        'linkSampah' => 'zona-waktu.',
        'title' => 'zona waktu',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(ZonaWaktu::select('id', 'nama', 'singkatan', 'gmt_offset'))
                ->addIndexColumn()
                ->addColumn('aksi', function (ZonaWaktu $data) {
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
            ->addColumn(['data' => 'singkatan', 'name' => 'singkatan', 'title' => 'SINGKATAN'])
            ->addColumn(['data' => 'gmt_offset', 'name' => 'gmt_offset', 'title' => 'GMT OFFSET'])
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
            'singkatan' => 'required|string|max:255',
            'gmt_offset' => 'required|numeric',
        ]);
        ZonaWaktu::create([
            'nama' => $request->nama,
            'singkatan' => $request->singkatan,
            'gmt_offset' => $request->gmt_offset,
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
            'data' => ZonaWaktu::select('id', 'nama', 'singkatan', 'gmt_offset')->find(dekrip($id)),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'singkatan' => 'required|string|max:255',
            'gmt_offset' => 'required|numeric',
        ]);
        ZonaWaktu::select('id')->find(dekrip($id))->update([
            'nama' => $request->nama,
            'singkatan' => $request->singkatan,
            'gmt_offset' => $request->gmt_offset,
        ]);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        return abort('404');
    }
}

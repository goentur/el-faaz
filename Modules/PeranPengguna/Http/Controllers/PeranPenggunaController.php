<?php

namespace Modules\PeranPengguna\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class PeranPenggunaController extends Controller
{
    protected $attribute = [
        'view' => 'peranpengguna::',
        'link' => 'peran-pengguna.',
        'title' => 'peran pengguna',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            // return DataTables::eloquent(Role::with('permissions')->select('id', 'name', 'guard_name')->whereNotIn('name', ['developer']))
            return DataTables::eloquent(Role::with('permissions')->select('id', 'name', 'guard_name'))
                ->addIndexColumn()
                ->editColumn('id', function (Role $data) {
                    return view($this->attribute['view'] . 'permission', compact('data'));
                })
                ->addColumn('aksi', function (Role $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-1', 'data' => 'guard_name', 'name' => 'guard_name', 'title' => 'GUARD'])
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'FITUR APLIKASI'])
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
            'permissions' => Permission::get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'guard_name' => 'required|string|max:255',
            'fitur' => 'required|array|min:1',
            'fitur.*' => 'required|string|distinct|min:1',
        ]);
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);
        $role->syncPermissions($request->fitur);
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
            'data' => Role::select('id', 'name', 'guard_name')->find(dekrip($id)),
            'permissions' => Permission::get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|max:255',
            'fitur' => 'required|array|min:1',
            'fitur.*' => 'required|string|distinct|min:1',
        ]);
        $role = Role::select('id')->find(dekrip($id));
        $role->update([
            'name' => $request->name,
            'guard_name' => $request->guard_name,
        ]);
        $role->syncPermissions($request->fitur);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            Role::select('id')->find(dekrip($request->id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
}

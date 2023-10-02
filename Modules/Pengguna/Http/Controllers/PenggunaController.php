<?php

namespace Modules\Pengguna\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder;

class PenggunaController extends Controller
{
    protected $attribute = [
        'view' => 'pengguna::',
        'link' => 'pengguna.',
        'linkSampah' => 'pengguna.',
        'title' => 'pengguna',
    ];

    public function index(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(User::with('roles')->select('id', 'name', 'email'))
                ->addIndexColumn()
                ->editColumn('id', function (User $data) {
                    return $data->getRoleNames()[0];
                })
                ->addColumn('aksi', function (User $data) {
                    if (!$data->hasRole('developer')) {
                        $kirim = [
                            'data' => $data,
                            'attribute' => $this->attribute,
                        ];
                        return view($this->attribute['view'] . 'aksi', $kirim);
                    }
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['class' => 'w-1', 'data' => 'email', 'name' => 'email', 'title' => 'EMAIL'])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-2', 'data' => 'id', 'name' => 'id', 'title' => 'PERAN PENGGUNA'])
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
            'roles' => Role::select('id', 'name')->whereNotIn('name', ['developer'])->get(),
        ];
        return view($this->attribute['view'] . 'form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'peran' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|string|max:255|min:8',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($request->peran);
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
            'data' => User::with('roles')->select('id', 'name', 'email')->find(dekrip($id)),
            'roles' => Role::select('id', 'name')->whereNotIn('name', ['developer'])->get(),
        ];
        return view($this->attribute['view'] . 'form', $kirim);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'peran' => 'required|string',
            'name' => 'required|string|max:255',
        ]);
        $user = User::select('id')->find(dekrip($id));
        $user->update([
            'name' => $request->name,
        ]);
        $user->syncRoles($request->peran);
        return redirect()->route($this->attribute['link'] . 'index')->with(['success' => 'Data berhasil diubah']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            User::select('id')->find(dekrip($request->id))->delete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        }
    }
    public function sampah(Request $request, Builder $builder)
    {
        if ($request->ajax()) {
            return DataTables::eloquent(User::onlyTrashed()->with('roles')->select('id', 'name', 'email'))
                ->addIndexColumn()
                ->editColumn('id', function (User $data) {
                    return $data->getRoleNames()[0];
                })
                ->addColumn('aksi', function (User $data) {
                    $kirim = [
                        'data' => $data,
                        'attribute' => $this->attribute,
                    ];
                    return view($this->attribute['view'] . 'aksi-sampah', $kirim);
                })->make(true);
        }
        $dataTable = $builder
            ->addIndex(['class' => 'w-1 text-center', 'data' => 'DT_RowIndex', 'name' => 'DT_RowIndex', 'title' => 'NO'])
            ->addColumn(['class' => 'w-1', 'data' => 'email', 'name' => 'email', 'title' => 'EMAIL'])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => 'NAMA'])
            ->addColumn(['class' => 'w-2', 'data' => 'id', 'name' => 'id', 'title' => 'PERAN PENGGUNA'])
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
            User::onlyTrashed()->select('id')->find(dekrip($request->id))->restore();
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
            User::onlyTrashed()->select('id')->find(dekrip($request->id))->forceDelete();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus selamanya.',
            ]);
        }
    }
}

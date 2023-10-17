<?php

namespace Modules\Penjualan\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KeranjangPenjualanController extends Controller
{
    public function keranjang(Request $request): JsonResponse
    {
        $request->validate([
            'anggota' => 'required|string',
        ]);
        $dataKeranjang = session()->get('keranjang-penjualan');
        if (isset($dataKeranjang)) {
            $datas = [];
            $total = 0;
            foreach ($dataKeranjang as $id => $data) {
                $harga = 0;
                if ($request->anggota === "biasa") {
                    $total += ($data['kuantitas'] * $data['harga_jual']);
                    $harga = rupiah($data['harga_jual']);
                } else {
                    $total += ($data['kuantitas'] * $data['harga_anggota']);
                    $harga = rupiah($data['harga_anggota']);
                }
                $datas[] = [
                    '<button type="button" class="btn btn-sm btn-icon waves-effect waves-light btn-danger hapus-keranjang" data-id="' . enkrip($id) . '"><i class="fas fa-trash-alt"></i></button>',
                    $data['status'] === "siap" ? '<span class="fw-10">' . $data['nama'] . '</span>' : '<span class="fw-10">(<span class="fw-bold text-danger">PO</span>) ' . $data['nama'] . '</span>',
                    '<input type="text" value="' . $data['kuantitas'] . '" class="form-control text-center form-control-sm kuantitas" data-id="' . enkrip($id) . '" placeholder="Kuantitas">',
                    '<span class="fw-12">' . $harga . '</span>',
                ];
            }
            return response()->json(['data' => $datas, 'total' => $total]);
        } else {
            return response()->json(['data' => []]);
        }
    }
    public function tambah(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'anggota' => 'required|string',
            'status' => 'required|in:po,siap',
        ]);
        $id = dekrip($request->id);
        $dataKeranjang = session()->get('keranjang-penjualan');
        $barang = Barang::select('id', 'nama', 'stok', 'harga_jual', 'harga_anggota')->find($id);
        $idKeranjang = $id . '-' . $request->status;
        // cek pembeli anggota atau bukan
        if (isset($dataKeranjang[$idKeranjang])) {
            // barang dipilih kembali 
            $data = $dataKeranjang[$idKeranjang];
            // jika kuantitas sudah lebih dari stok maka masukan ke preoreder
            if (($data['kuantitas'] + 1) > $barang->stok && $data['status'] === "siap") {
                if (isset($dataKeranjang[$data['id'] . '-po'])) {
                    // jika barang preorder sudah ada maka diupdate kuantitasnya
                    $dataKeranjangPO = $dataKeranjang[$data['id'] . '-po'];
                    $dataKeranjang[$data['id'] . '-po'] = [
                        "status" => "po",
                        "id" => $data['id'],
                        "nama" => $data['nama'],
                        "kuantitas" => $dataKeranjangPO['kuantitas'] + 1,
                        "harga_jual" => $data['harga_jual'],
                        "harga_anggota" => $data['harga_anggota'],
                    ];
                } else {
                    // jika barang preorder belum ada maka ditambahkan
                    $dataKeranjang[$data['id'] . '-po'] = [
                        "status" => "po",
                        "id" => $data['id'],
                        "nama" => $data['nama'],
                        "kuantitas" => 1,
                        "harga_jual" => $data['harga_jual'],
                        "harga_anggota" => $data['harga_anggota'],
                    ];
                }
                session()->put('keranjang-penjualan', $dataKeranjang);
                return response()->json([
                    'status' => true,
                    'message' => 'Stok barang tidak mencukupi, sistem menambahkan kuantitas barang ke preOrder',
                ], 200);
            }
            // jika barang sudah ada maka diupdate kuantitasnya
            $dataKeranjang[$idKeranjang] = [
                "status" => $data['status'],
                "id" => $data['id'],
                "nama" => $data['nama'],
                "kuantitas" => $data['kuantitas'] + 1,
                "harga_jual" => $data['harga_jual'],
                "harga_anggota" => $data['harga_anggota'],
            ];
            session()->put('keranjang-penjualan', $dataKeranjang);
            return response()->json([
                'status' => true,
                'message' => 'Kuantitas barang berhasil ditambahkan',
            ], 200);
        }
        if (!$dataKeranjang) {
            // pertama kali barang dimasukan ke keranjang
            $dataKeranjang[$idKeranjang] = [
                "status" => $request->status,
                "id" => $barang->id,
                "nama" => $barang->nama,
                "kuantitas" => 1,
                "harga_jual" => $barang['harga_jual'],
                "harga_anggota" => $barang['harga_anggota'],
            ];
            session()->put('keranjang-penjualan', $dataKeranjang);
            return response()->json([
                'status' => true,
                'message' => 'Barang berhasil ditambahkan',
            ], 200);
        }
        // barang lain dimasukan ke keranjang
        $dataKeranjang[$idKeranjang] = [
            "status" => $request->status,
            "id" => $barang->id,
            "nama" => $barang->nama,
            "kuantitas" => 1,
            "harga_jual" => $barang['harga_jual'],
            "harga_anggota" => $barang['harga_anggota'],
        ];
        session()->put('keranjang-penjualan', $dataKeranjang);
        return response()->json([
            'status' => true,
            'message' => 'Barang berhasil ditambahkan',
        ], 200);
    }
    public function ubahKuantitas(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string',
            'kuantitas' => 'required|numeric',
        ]);
        $id = dekrip($request->id);
        $dataKeranjang = session()->get('keranjang-penjualan');
        if (isset($dataKeranjang[$id])) {
            $data = $dataKeranjang[$id];
            if ($data['status'] === 'siap') {
                $barang = Barang::select('id', 'nama', 'stok')->find($data['id']);
                // jika kuantitas masih dibawah ya ok
                if ($request->kuantitas <= $barang->stok) {
                    $dataKeranjang[$id] = [
                        "status" => $data['status'],
                        "id" => $data['id'],
                        "nama" => $data['nama'],
                        "kuantitas" => $request->kuantitas,
                        "harga_jual" => $data['harga_jual'],
                        "harga_anggota" => $data['harga_anggota'],
                    ];
                    session()->put('keranjang-penjualan', $dataKeranjang);
                    return response()->json([
                        'status' => true,
                        'message' => 'Kuantitas barang berhasil diubah',
                    ], 200);
                } else {
                    // jika kuantitas lebih dari stok, maka masukan full stok pada barang bukan preorder. kemudian masukan 1 barang pre order kedalam keranjang degan kuantitas inputan kuantitas dikurangi stok
                    $dataKeranjang[$id] = [
                        "status" => $data['status'],
                        "id" => $data['id'],
                        "nama" => $data['nama'],
                        "kuantitas" => $barang->stok,
                        "harga_jual" => $data['harga_jual'],
                        "harga_anggota" => $data['harga_anggota'],
                    ];
                    session()->put('keranjang-penjualan', $dataKeranjang);
                    $dataKeranjang[$data['id'] . "-po"] = [
                        "status" => "po",
                        "id" => $data['id'],
                        "nama" => $data['nama'],
                        "kuantitas" => $request->kuantitas - $barang->stok,
                        "harga_jual" => $data['harga_jual'],
                        "harga_anggota" => $data['harga_anggota'],
                    ];
                    session()->put('keranjang-penjualan', $dataKeranjang);
                    return response()->json([
                        'status' => true,
                        'message' => 'Stok barang tidak mencukupi, sistem menambahkan kuantitas barang ke preOrder',
                    ], 200);
                }
            } else {
                $dataKeranjang[$id] = [
                    "status" => $data['status'],
                    "id" => $data['id'],
                    "nama" => $data['nama'],
                    "kuantitas" => $request->kuantitas,
                    "harga_jual" => $data['harga_jual'],
                    "harga_anggota" => $data['harga_anggota'],
                ];
                session()->put('keranjang-penjualan', $dataKeranjang);
                return response()->json([
                    'status' => true,
                    'message' => 'Kuantitas barang berhasil diubah',
                ], 200);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Barang tidak ditemukan pada keranjang',
        ], 404);
    }
    public function hapus(Request $request): JsonResponse
    {
        $request->validate([
            'id' => 'required|string'
        ]);
        $dataKeranjang = session()->get('keranjang-penjualan');
        if ($dataKeranjang) {
            $id = dekrip($request->id);
            if (isset($dataKeranjang[$id])) {
                unset($dataKeranjang[$id]);
                session()->put('keranjang-penjualan', $dataKeranjang);
                return response()->json([
                    'status' => true,
                    'message' => 'Barang berhasil dihapus',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Barang tidak ditemukan pada keranjang',
                ]);
            }
        }
    }
}

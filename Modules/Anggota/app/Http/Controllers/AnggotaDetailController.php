<?php

namespace Modules\Anggota\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Penjualan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaDetailController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        if ($request->ajax()) {
            $penjualan = Penjualan::with('penjualanDetail', 'user', 'returWithDetail')->select('id', 'user_id', 'bayar', 'total')->where(['anggota_id' => dekrip($request->id), 'status' => 1])->orderBy('tanggal', 'ASC')->get();
            if ($penjualan) {
                $dataPenjualan = [];
                $dataRetur = [];
                $zonaWaktuPengguna = zonaWaktuPenguna();
                $no = 1;
                $totalPenjualan = 0;
                $totalRetur = 0;
                $bayar = 0;
                foreach ($penjualan as $p) {
                    $totalPenjualan += $p->total;
                    $bayar += $p->bayar;
                    foreach ($p->penjualanDetail as $pd) {
                        if ($pd->pemasokBarangDetail && $pd->pemasokBarangDetail->barangDetail) {
                            $pemasok = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->pemasok) {
                                $pemasok = $pd->pemasokBarangDetail->pemasok->nama;
                            }
                            $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->barang) {
                                $barang = $pd->pemasokBarangDetail->barangDetail->barang->nama;
                            }
                            $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->warna) {
                                $warna = ' - ' . $pd->pemasokBarangDetail->barangDetail->warna->nama;
                            }
                            $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->satuan) {
                                $satuan = $pd->pemasokBarangDetail->barangDetail->satuan->nama;
                            }
                            $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                            if ($pd->pemasokBarangDetail->barangDetail->ukuran) {
                                $ukuran = '';
                                foreach ($pd->pemasokBarangDetail->barangDetail->ukuran as $u) {
                                    if ($u === $pd->pemasokBarangDetail->barangDetail->ukuran->last()) {
                                        $ukuran .= $u->nama;
                                    } else {
                                        $ukuran .= $u->nama . ", ";
                                    }
                                }
                            }
                            $dataPenjualan[] = [
                                'idPenjualan' => $p->id,
                                'idPenjualanDetail' => $pd->id,
                                'pengguna' => $p->user ? $p->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                                'pemasok' => $pemasok,
                                'barang' => $barang . '' . $warna,
                                'satuan' => $satuan,
                                'ukuran' => $ukuran,
                                'tanggal' => formatTanggal($pd->tanggal, $zonaWaktuPengguna, true),
                                'kuantitas' => $pd->kuantitas,
                                'harga' => rupiah($pd->harga),
                                'status' => $pd->status == 1 ? '<span class="badge bg-danger">TIDAK</span>' : '<span class="badge bg-success">TERSEDIA</span>',
                            ];
                        }
                    }
                    foreach ($p->returWithDetail as $pr) {
                        $totalRetur += $pr->total;
                        foreach ($pr->onlyReturDetail as $prd) {
                            $dataRetur[] = [
                                'idPenjualan' => $p->id,
                                'idPenjualanDetail' => $prd->transaksi_detail_id,
                                'kuantitas' => $prd->kuantitas,
                                'tanggal' => formatTanggal($prd->tanggal, $zonaWaktuPengguna, true),
                                'pengguna' => $p->user ? $p->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                            ];
                        }
                    }
                }
                $hasilDataRetur = [];
                foreach ($dataPenjualan as $key1 => &$item1) {
                    // Loop melalui data array kedua
                    foreach ($dataRetur as $item2) {
                        // Bandingkan idPenjualan dan idPenjualanDetail
                        if (
                            $item1['idPenjualan'] == $item2['idPenjualan'] &&
                            $item1['idPenjualanDetail'] == $item2['idPenjualanDetail']
                        ) {
                            // Kurangi kuantitas
                            $item1['kuantitas'] -= $item2['kuantitas'];

                            // Hapus elemen jika kuantitas <= 0
                            if ($item1['kuantitas'] <= 0) {
                                unset($dataPenjualan[$key1]);
                            }
                            $hasilDataRetur[] = [
                                'pengguna' => $item2['pengguna'],
                                'pemasok' => $item1['pemasok'],
                                'barang' => $item1['barang'],
                                'satuan' => $item1['satuan'],
                                'ukuran' => $item1['ukuran'],
                                'tanggal' => $item2['tanggal'],
                                'kuantitas' => $item2['kuantitas'],
                                'harga' => $item1['harga'],
                            ];

                            // Keluar dari loop array kedua setelah menemukan kecocokan
                            break;
                        }
                    }
                }

                $resultArray = array_map(function ($item) {
                    return [
                        'pengguna' => $item['pengguna'],
                        'pemasok' => $item['pemasok'],
                        'barang' => $item['barang'],
                        'satuan' => $item['satuan'],
                        'ukuran' => $item['ukuran'],
                        'tanggal' => $item['tanggal'],
                        'kuantitas' => $item['kuantitas'],
                        'harga' => $item['harga'],
                        'status' => $item['status']
                    ];
                }, $dataPenjualan);
                $no = 1;
                foreach ($resultArray as &$item1) {
                    $item1['no'] = $no++ . '.';
                }
                $no = 1;
                foreach ($hasilDataRetur as &$item2) {
                    $item2['no'] = $no++ . '.';
                }
                return response()->json(['data' => array_values($resultArray), 'retur' => $hasilDataRetur, 'total' => rupiah($totalPenjualan - $totalRetur), 'bayar' => rupiah($bayar), 'kekurangan' => rupiah($totalPenjualan - $totalRetur - $bayar)], 200);
            } else {
                return response()->json(['data' => []], 200);
            }
        }
    }
    public function cetakTagihan($id): View
    {
        $idAnggota = dekrip($id);
        $penjualan = Penjualan::with('penjualanDetail', 'user', 'returWithDetail')->select('id', 'user_id', 'bayar', 'total')->where(['anggota_id' => $idAnggota, 'status' => 1])->orderBy('tanggal', 'ASC')->get();
        if ($penjualan) {
            $dataPenjualan = [];
            $dataRetur = [];
            $zonaWaktuPengguna = zonaWaktuPenguna();
            $no = 1;
            $totalPenjualan = 0;
            $totalRetur = 0;
            $bayar = 0;
            foreach ($penjualan as $p) {
                $totalPenjualan += $p->total;
                $bayar += $p->bayar;
                foreach ($p->penjualanDetail as $pd) {
                    if ($pd->pemasokBarangDetail && $pd->pemasokBarangDetail->barangDetail) {
                        $pemasok = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pd->pemasokBarangDetail->pemasok) {
                            $pemasok = $pd->pemasokBarangDetail->pemasok->nama;
                        }
                        $barang = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pd->pemasokBarangDetail->barangDetail->barang) {
                            $barang = $pd->pemasokBarangDetail->barangDetail->barang->nama;
                        }
                        $warna = ' - <span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pd->pemasokBarangDetail->barangDetail->warna) {
                            $warna = ' - ' . $pd->pemasokBarangDetail->barangDetail->warna->nama;
                        }
                        $satuan = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pd->pemasokBarangDetail->barangDetail->satuan) {
                            $satuan = $pd->pemasokBarangDetail->barangDetail->satuan->nama;
                        }
                        $ukuran = '<span class="badge bg-danger">TIDAK TERDAFTAR</span>';
                        if ($pd->pemasokBarangDetail->barangDetail->ukuran) {
                            $ukuran = '';
                            foreach ($pd->pemasokBarangDetail->barangDetail->ukuran as $u) {
                                if ($u === $pd->pemasokBarangDetail->barangDetail->ukuran->last()) {
                                    $ukuran .= $u->nama;
                                } else {
                                    $ukuran .= $u->nama . ", ";
                                }
                            }
                        }
                        $dataPenjualan[] = [
                            'idPenjualan' => $p->id,
                            'idPenjualanDetail' => $pd->id,
                            'pengguna' => $p->user ? $p->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                            'pemasok' => $pemasok,
                            'barang' => $barang . '' . $warna,
                            'satuan' => $satuan,
                            'ukuran' => $ukuran,
                            'tanggal' => formatTanggal($pd->tanggal, $zonaWaktuPengguna, true),
                            'kuantitas' => $pd->kuantitas,
                            'harga' => rupiah($pd->harga),
                            'status' => $pd->status == 1 ? '<span class="badge bg-danger">TIDAK</span>' : '<span class="badge bg-success">TERSEDIA</span>',
                        ];
                    }
                }
                foreach ($p->returWithDetail as $pr) {
                    $totalRetur += $pr->total;
                    foreach ($pr->onlyReturDetail as $prd) {
                        $dataRetur[] = [
                            'idPenjualan' => $p->id,
                            'idPenjualanDetail' => $prd->transaksi_detail_id,
                            'kuantitas' => $prd->kuantitas,
                            'tanggal' => formatTanggal($prd->tanggal, $zonaWaktuPengguna, true),
                            'pengguna' => $p->user ? $p->user->name : '<span class="badge bg-danger">PENGGUNA TIDAK DITEMUKAN</span>',
                        ];
                    }
                }
            }
            $hasilDataRetur = [];
            foreach ($dataPenjualan as $key1 => &$item1) {
                // Loop melalui data array kedua
                foreach ($dataRetur as $item2) {
                    // Bandingkan idPenjualan dan idPenjualanDetail
                    if (
                        $item1['idPenjualan'] == $item2['idPenjualan'] &&
                        $item1['idPenjualanDetail'] == $item2['idPenjualanDetail']
                    ) {
                        // Kurangi kuantitas
                        $item1['kuantitas'] -= $item2['kuantitas'];

                        // Hapus elemen jika kuantitas <= 0
                        if ($item1['kuantitas'] <= 0) {
                            unset($dataPenjualan[$key1]);
                        }
                        $hasilDataRetur[] = [
                            'pengguna' => $item2['pengguna'],
                            'pemasok' => $item1['pemasok'],
                            'barang' => $item1['barang'],
                            'satuan' => $item1['satuan'],
                            'ukuran' => $item1['ukuran'],
                            'tanggal' => $item2['tanggal'],
                            'kuantitas' => $item2['kuantitas'],
                            'harga' => $item1['harga'],
                        ];

                        // Keluar dari loop array kedua setelah menemukan kecocokan
                        break;
                    }
                }
            }

            $resultArray = array_map(function ($item) {
                return [
                    'pengguna' => $item['pengguna'],
                    'pemasok' => $item['pemasok'],
                    'barang' => $item['barang'],
                    'satuan' => $item['satuan'],
                    'ukuran' => $item['ukuran'],
                    'tanggal' => $item['tanggal'],
                    'kuantitas' => $item['kuantitas'],
                    'harga' => $item['harga'],
                    'status' => $item['status']
                ];
            }, $dataPenjualan);
            $no = 1;
            foreach ($resultArray as &$item1) {
                $item1['no'] = $no++ . '.';
            }
            $no = 1;
            foreach ($hasilDataRetur as &$item2) {
                $item2['no'] = $no++ . '.';
            }
            $kirim = [
                'zonaWaktuPengguna' => $zonaWaktuPengguna,
                'data' => Anggota::select('id', 'nama')->find($idAnggota),
                'barang' => array_values($resultArray),
                'retur' => $hasilDataRetur,
                'total' => rupiah($totalPenjualan - $totalRetur),
                'bayar' => rupiah($bayar),
                'kekurangan' => rupiah($totalPenjualan - $totalRetur - $bayar)
            ];
            return view('anggota::detail.cetak-tagihan', $kirim);
        } else {
            return back();
        }
    }
}

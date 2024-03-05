<?php

namespace Modules\Jurnal\App\Http\Controllers\Pembelian;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LunasController extends Controller
{
    protected $attribute = [
        'view' => 'jurnal::pembelian.lunas.',
        'link' => 'jurnal.pembelian.lunas.',
        'title' => 'pembelian lunas',
    ];

    public function index(): View
    {
        $zonaWaktuPenguna = zonaWaktuPenguna();
        $hariIni = strtotime(date('d-m-Y')) + $zonaWaktuPenguna->gmt_offset;
        $data = [
            'attribute' => $this->attribute,
            'tanggalAkhir' => date('d-m-Y', $hariIni),
        ];
        return view($this->attribute['view'] . 'index', $data);
    }
    public function data(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $zonaWaktuPengguna = zonaWaktuPenguna();
            $txtTanggalAwal = '~';
            $txtTanggalAkhir = date('d-m-Y');
            if ($request->type === 'semua') {
                $pembelian = Pembelian::with('user', 'pemasok')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total')->whereIn('status', [2, 3])->orderBy('tanggal', 'ASC');
            } else {
                $tanggal = explode(" to ", $request->tanggal);
                if (!empty($tanggal[0]) && !empty($tanggal[1])) {
                    $txtTanggalAwal = $tanggal[0];
                    $txtTanggalAkhir = $tanggal[1];
                } else {
                    $txtTanggalAwal = $request->tanggal;
                    $txtTanggalAkhir = $request->tanggal;
                }
                $tanggalAwal = strtotime($txtTanggalAwal . ' 00:00:00') - $zonaWaktuPengguna->gmt_offset;
                $tanggalAkhir = strtotime($txtTanggalAkhir . ' 23:59:59') - $zonaWaktuPengguna->gmt_offset;
                $pembelian = Pembelian::with('user', 'pemasok')->select('id', 'user_id', 'pemasok_id', 'tanggal', 'total')->whereBetween('tanggal', [$tanggalAwal, $tanggalAkhir])->whereIn('status', [2, 3])->orderBy('tanggal', 'ASC');
            }
            if ($request->ajax()) {
                if ($pembelian->count() > 0) {
                    $datas = [];
                    $dataPembelian = [];
                    $total = 0;
                    foreach ($pembelian->get() as $key => $p) {
                        $total += $p->total;
                        $datas[] = [
                            'no' => ++$key . '.',
                            'pengguna' => $p->user ? $p->user->name : 'PENGGUNA TIDAK TERDAFTAR',
                            'pemasok' => $p->pemasok ? $p->pemasok->nama : 'PEMASOK TIDAK TERDAFTAR',
                            'tanggal' => formatTanggal($p->tanggal, $zonaWaktuPengguna),
                            'total' => rupiah($p->total)
                        ];
                        $dataPembelian[] = $p->id;
                    }
                    return response()->json(['data' => $datas, 'total' => $total, 'awal' => $txtTanggalAwal, 'akhir' => $txtTanggalAkhir, 'pembelian' => $dataPembelian], 200);
                } else {
                    return response()->json(['data' => [], 'total' => 0, 'awal' => $txtTanggalAwal, 'akhir' => $txtTanggalAkhir], 200);
                }
            }
        }
    }
    public function simpan(Request $request)
    {
        $request->validate([
            'data.*.idAkun' => 'required|numeric',
            'data.*.akun' => 'required|string',
            'data.*.namaDebet' => 'required|string',
            'data.*.debet' => 'required|numeric',
            'data.*.namaKredit' => 'required|string',
            'data.*.kredit' => 'required|numeric',
            'pembelian' => 'required|array|min:1',
            'keterangan' => 'required|string',
        ]);
        if ($request->ajax()) {
            try {
                $tanggal = time();
                $idJurnal = id();
                DB::beginTransaction();
                Jurnal::create([
                    'id' => $idJurnal,
                    'user_id' => auth()->user()->id,
                    'jenis' => 2,
                    'tanggal' => $tanggal,
                    'keterangan' => $request->keterangan,
                ]);
                foreach ($request->data as $data) {
                    $akun = Akun::select('id', 'debet', 'kredit')->find($data['idAkun']);
                    JurnalDetail::create([
                        'id' => id(),
                        'akun_id' => $data['idAkun'],
                        'jurnal_id' => $idJurnal,
                        'debet' => $data['debet'],
                        'kredit' => $data['kredit'],
                    ]);
                    $akun->update([
                        'debet' => $akun->debet + $data['debet'],
                        'kredit' => $akun->kredit + $data['kredit'],
                    ]);
                }
                $pembelian = Pembelian::select('id')->whereIn('id', $request->pembelian);
                $pembelian->update(['status' => 4]);
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Penjurnalan telah berhasil',
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat melakukan penjurnalan',
                ]);
            }
        }
    }
}

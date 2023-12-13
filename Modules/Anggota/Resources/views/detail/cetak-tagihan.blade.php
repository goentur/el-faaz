<!DOCTYPE html>
<html>

<head>
     <title>TAGIHAN - {{ $data->nama }} - {{ date('Y-m-d', (time() + $zonaWaktuPengguna->gmt_offset)) }}</title>
     <style type="text/css">
          .page-header,
          .page-header-space {
               height: 0mm;
          }

          .page-footer,
          .page-footer-space {
               height: 1mm;
          }

          .page-footer {
               position: fixed;
               bottom: 20mm;
               width: 100%;
          }

          .page-header {
               position: fixed;
               top: 10mm;
               width: 100%;
          }

          .page {
               /* page-break-after: always; */
               margin-left: 1mm;
               margin-right: 1mm;
          }

          @page {
               margin: 17mm 17mm 17mm 17mm;
          }

          html,
          body {
               width: 210mm;
               height: 297mm;
          }

          @media print {
               thead {
                    display: table-header-group;
               }

               tfoot {
                    display: table-footer-group;
               }

               body {
                    margin: 0;
               }
          }

          table.tbl {
               width: 100%;
               border-collapse: collapse;
          }

          table.tbl th {
               padding: 6px;
          }

          table.tbl td {
               padding: 5px;
          }
     </style>
</head>

<body>
     <div class="page-header" style="text-align: center"></div>
     <table width="100%">
          <thead>
               <tr>
                    <td>
                         <!--place holder for the fixed-position header-->
                         <div class="page-header-space"></div>
                    </td>
               </tr>
          </thead>
          <tbody>
               <tr>
                    <td>
                         <!--*** CONTENT GOES HERE ***-->
                         <div class="page">
                              <table width="95%" style="margin-left: 2.5%;margin-right: 2.5%" cellpadding="0" cellspacing="0" border="0">
                                   <td rowspan="4" width="15%" align="center">
                                        <img style="height : 75px" src="{{ asset('img/icons/logo.png') }}" alt="Logo">
                                   </td>
                                   <td align="">
                                        <span style="color: black; font-family: 'Times New Roman'; font-size: 25px;font-weight: bold;">EL FAAZ</span><br>
                                        <span style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight: bold;">EXCLUSIVE MUSLIM WEAR</span>
                                   </td>
                              </table>
                              <table width="95%" style="margin-left: 2.5%;margin-right: 2.5%;margin-bottom: 1%" cellpadding="1" cellspacing="1" border="1"></table>
                              <table width="95%" style="margin-left: 2.5%;margin-right: 2.5%;margin-bottom: 2.5%;" cellpadding="0" cellspacing="0" border="0">
                                   <tr>
                                        <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 18px;font-weight: bold">TAGIHAN HUTANG DAGANG</td>
                                   </tr>
                                   <tr>
                                        <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight: normal">{{ $data->nama }}</td>
                                   </tr>
                                   <tr>
                                        <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight: normal">PER TANGGAL {{ date('d-m-Y', (time() + $zonaWaktuPengguna->gmt_offset)) }}</td>
                                   </tr>
                              </table>
                              <table width="95%" style="margin-left: 2.5%;margin-right: 2.5%;border-collapse: collapse" cellpadding="3" cellspacing="3" border="1">
                                   <thead>
                                        <tr>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">NO</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">KASIR</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">BARANG</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">SATUAN</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">UKURAN</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">TANGGAL</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">KUANTITAS</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">HARGA</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                        @php
                                        $no = 1;
                                        $total = 0;
                                        $bayar = 0;
                                        @endphp
                                        @foreach ($penjualan as $p)
                                        @php
                                        $total += $p->total;
                                        $bayar += $p->bayar;
                                        @endphp   
                                        @foreach ($p->penjualanDetail as $pd)
                                        @if ($pd->pemasokBarangDetail && $pd->pemasokBarangDetail->barangDetail)
                                        @php
                                             $barang = '<span style="color: red;">TIDAK TERDAFTAR</span>';
                                             if ($pd->pemasokBarangDetail->barangDetail->barang) {
                                                  $barang = $pd->pemasokBarangDetail->barangDetail->barang->nama;
                                             }
                                             $warna = ' - <span style="color: red;">TIDAK TERDAFTAR</span>';
                                             if ($pd->pemasokBarangDetail->barangDetail->warna) {
                                                  $warna = ' - ' . $pd->pemasokBarangDetail->barangDetail->warna->nama;
                                             }
                                             $satuan = '<span style="color: red;">TIDAK TERDAFTAR</span>';
                                             if ($pd->pemasokBarangDetail->barangDetail->satuan) {
                                                  $satuan = $pd->pemasokBarangDetail->barangDetail->satuan->nama;
                                             }
                                             $ukuran = '<span style="color: red;">TIDAK TERDAFTAR</span>';
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
                                        @endphp
                                        <tr>
                                             <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{{ $no++ }}.</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $p->user ? $p->user->name : '<span style="color: red;">PENGGUNA TIDAK DITEMUKAN</span>' !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $barang.''.$warna !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $satuan !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $ukuran !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! date('Y-m-d H:i:s', ($pd->tanggal + $zonaWaktuPengguna->gmt_offset)) . ' <b>' . $zonaWaktuPengguna->singkatan . '</b>' !!}</td>
                                             <td align="center" width="1%" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{{ $pd->kuantitas }}</td>
                                             <td align="right" width="1%" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{{ rupiah($pd->harga) }}</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                        @endforeach
                                        <tr>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="6">TOTAL</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="2">{{ rupiah($total) }}</td>
                                        </tr>
                                        <tr>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="6">BAYAR</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="2">{{ rupiah($bayar) }}</td>
                                        </tr>
                                        <tr>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="6">KEKURANGAN</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="2">{{ rupiah($total - $bayar) }}</td>
                                        </tr>
                                   </tbody>
                              </table>
                         </div>
                    </td>
               </tr>
          </tbody>
          <tfoot>
               <tr>
                    <td>
                         <!--place holder for the fixed-position footer-->
                         <div class="page-footer-space">
                         </div>
                    </td>
               </tr>
          </tfoot>
     </table>
     <div class="page-footer">
     </div>
     <script type="text/javascript">
             window.print();
             window.focus();
             window.onafterprint = function() {
                 window.close();
             }
     </script>
</body>

</html>
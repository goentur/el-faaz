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
                                        @foreach ($barang as $key => $value)
                                        <tr>
                                             <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{{ $value['no'] }}.</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['pengguna'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['barang'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['satuan'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['ukuran'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['tanggal'] !!}</td>
                                             <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['kuantitas'] !!}</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 12px;font-weight: bold">{!! $value['harga'] !!}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="6">TOTAL</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="2">{{ $total }}</td>
                                        </tr>
                                        <tr>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="6">BAYAR</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="2">{{ $bayar }}</td>
                                        </tr>
                                        <tr>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="6">KEKURANGAN</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 14px;font-weight:bold" colspan="2">{{ $kekurangan }}</td>
                                        </tr>
                                   </tbody>
                              </table>
                              <h4 style="margin-left: 2.5%;">DAFTAR RETUR BARANG BERDASARKAN TRANSAKSI PEMBELIAN DIATAS.</h4>
                              <table width="95%" style="margin-left: 2.5%;margin-right: 2.5%; margin-top: 5px; border-collapse: collapse" cellpadding="3" cellspacing="3" border="1">
                                   <thead>
                                        <tr>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">NO</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">PENGGUNA</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">TANGGAL</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">BARANG</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">SATUAN</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">UKURAN</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">KUANTITAS</th>
                                             <th align="center" style="color: black; font-family: 'Times New Roman'; font-size: 14px">HARGA</th>
                                        </tr>
                                   </thead>
                                   <tbody>
                                        @foreach ($retur as $key => $value)
                                        <tr>
                                             <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{{ $value['no'] }}.</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['pengguna'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['tanggal'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['barang'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['satuan'] !!}</td>
                                             <td style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['ukuran'] !!}</td>
                                             <td align="center" style="color: black; font-family: 'Times New Roman'; font-size: 12px">{!! $value['kuantitas'] !!}</td>
                                             <td align="right" style="color: black; font-family: 'Times New Roman'; font-size: 12px;font-weight: bold;">{!! $value['harga'] !!}</td>
                                        </tr>
                                        @endforeach
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
<?php

function enkrip($string)
{
     $output = false;
     $security       = parse_ini_file(__DIR__ . "/../../config/security.ini");
     $secret_key     = $security["encryption_key"];
     $secret_iv      = $security["iv"];
     $encrypt_method = $security["encryption_mechanism"];
     $key    = hash("sha256", $secret_key);
     $iv     = substr(hash("sha256", $secret_iv), 0, 16);
     $result = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
     $output = base64_encode($result);
     return $output;
}
function dekrip($string)
{
     $output = false;
     $security       = parse_ini_file(__DIR__ . "/../../config/security.ini");
     $secret_key     = $security["encryption_key"];
     $secret_iv      = $security["iv"];
     $encrypt_method = $security["encryption_mechanism"];
     $key    = hash("sha256", $secret_key);
     $iv = substr(hash("sha256", $secret_iv), 0, 16);
     $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
     return $output;
}
function id()
{
     return date('myd') . '' . rand(pow(10, 5 - 1), pow(10, 5) - 1) . '' . date('siH');
}
function rupiah($d)
{
     return number_format($d, 0, ',', ',');
}
function fileName()
{
     $s = strtoupper(md5(uniqid(rand(), true)));
     $kode =
          substr($s, 0, 8) . '-' .
          substr($s, 8, 4) . '-' .
          substr($s, 12, 4) . '-' .
          substr($s, 16, 4) . '-' .
          substr($s, 20) . '-' .
          time();
     return $kode;
}
function zonaWaktuPenguna()
{
    return auth()->user()->zonaWaktu;
}
function formatTanggal($tanggal, $zonaWaktu, $bold = false)
{
     if ($bold) {
          return date('Y-m-d H:i:s', ($tanggal + $zonaWaktu->gmt_offset)) . ' <b>' . $zonaWaktu->singkatan . '</b>';
     } else {
          return date('Y-m-d H:i:s', ($tanggal + $zonaWaktu->gmt_offset)) . ' ' . $zonaWaktu->singkatan;
     }
}

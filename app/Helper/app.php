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

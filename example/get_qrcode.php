<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'phpqrcode/phpqrcode.php';

if(!empty($_GET['pid']))
{
    $pid = $_GET['pid'];
}
else
{
    $pid = '123456789';
}

$notify = new NativePay();
$url = $notify->GetPrePayUrl($pid);

$url = urldecode($url);

QRcode::png($url);

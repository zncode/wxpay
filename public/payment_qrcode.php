<?php
ini_set('date.timezone','Asia/Shanghai');

require_once "../lib/WxPay.Api.php";
require_once "../lib/WxPay.NativePay.php";
require_once 'ext/phpqrcode/phpqrcode.php';

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

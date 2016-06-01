<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../../lib/WxPay.Api.php";
require_once '../../lib/WxPay.Notify.php';
require_once '../../lib/log.php';

//初始化日志
$logHandler= new CLogFileHandler("../../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class NativeNotifyCallBack extends WxPayNotify
{
    public function unifiedorder($openId, $product_id)
    {
        //生成订单
        $order = $this->order_create($openId, $product_id);
        
        //获取产品信息
        $product = $this->product_load($product_id);

        //统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($product->body);
        $input->SetAttach($product->attach);
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($order->total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag($product->tag);
        $input->SetNotify_url("http://dev87v8payment.87870.com/qrcode_test/payment_notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetOpenid($openId);
        $input->SetProduct_id($product_id);
        $result = WxPayApi::unifiedOrder($input);
        Log::DEBUG("unifiedorder:" . json_encode($result));
        return $result;
    }

    public function order_create($openid, $product_id)
    {
        //TODO get real order
        $order = new stdClass();
        $order->id = 999;
        $order->total_fee = 10;

        return $order;
    }

    public function product_load($pid)
    {
        //TODO get real product
        $product = new stdClass();
        $product->body = 'testbody';
        $product->attach = 'testattach';
        $product->tag = 'testtag';
        return $product;
    }

    public function NotifyProcess($data, &$msg)
    {
        //echo "处理回调";
        Log::DEBUG("call back:" . json_encode($data));

        if(!array_key_exists("openid", $data) ||
            !array_key_exists("product_id", $data))
        {
            $msg = "回调数据异常";
            return false;
        }

        $openid = $data["openid"];
        $product_id = $data["product_id"];

        //统一下单
        $result = $this->unifiedorder($openid, $product_id);
        if(!array_key_exists("appid", $result) ||
            !array_key_exists("mch_id", $result) ||
            !array_key_exists("prepay_id", $result))
        {
            $msg = "统一下单失败";
            return false;
        }

        $this->SetData("appid", $result["appid"]);
        $this->SetData("mch_id", $result["mch_id"]);
        $this->SetData("nonce_str", WxPayApi::getNonceStr());
        $this->SetData("prepay_id", $result["prepay_id"]);
        $this->SetData("result_code", "SUCCESS");
        $this->SetData("err_code_des", "OK");
        return true;
    }
}

Log::DEBUG("begin notify!");
$notify = new NativeNotifyCallBack();
$notify->Handle(true);

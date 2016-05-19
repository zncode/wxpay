<?php
/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
class WxPayException extends Exception {

    public function __construct($message, $code=0)
    {
        parent::__construct($message, $code);
    }

    public function __toString()
    {
        return "[{$this->code}]: {$this->message}";
    }

    public function errorMessage()
    {
        return $this->getMessage();
    }
}

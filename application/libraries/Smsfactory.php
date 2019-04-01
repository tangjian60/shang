<?php
define('SESSION_PROVE_CODE', 'sms_prove_code');
define('SESSION_PROVE_TIME', 'sms_prove_min');

class Smsfactory
{

    const BASE_URL = "https://api.miaodiyun.com/20150822/industrySMS/sendSMS";
    const ACCOUNT_SID = "fcfd0517cfcd4cff9563cdcadefca3ae";
    const AUTH_TOKEN = "8ce8dffea6dd40cf9193bcb3f54ad8e9";
    const PROVE_MSG_TEP = "【ZCM科技】验证码：%s，如非本人操作，请忽略此短信";
    const NOTI_MSG_TEP = "";

    function __construct()
    {
        //TODO:
    }

    public function sendProve($phone_number)
    {
        $regphone = '/^1[0-9]{10}$/';
        if (!preg_match($regphone, $phone_number)) {
            return false;
        }

        if ($this->timeControl()) {
            return true;
        }

        $proveCode = $this->getProveCode();

        if (empty($proveCode)) {
            $proveCode = $this->generateProveCode();
            $this->setProveCode($proveCode);
        }

        return $this->sendSMS($phone_number, sprintf(self::PROVE_MSG_TEP, $proveCode));
    }

    public function checkProveCode($proveCode)
    {
        if (empty($proveCode)) {
            return false;
        }

        if ($proveCode == $this->getProveCode()) {
            $this->clearProveCode();
            return true;
        }

        return false;
    }

    public function sendNotify($phone_number)
    {

        // check phone number format
        $regphone = '/^1?[0-9]{10}$/';
        if (!preg_match($regphone, $phone_number)) {
            return false;
        }

        $phone_value = substr($phone_number, 0, 2) . "***" . substr($phone_number, -2);
        return $this->sendSMS($phone_number, sprintf(self::NOTI_MSG_TEP, $phone_value));
    }

    private function getProveCode()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION[SESSION_PROVE_CODE]) && is_numeric($_SESSION[SESSION_PROVE_CODE])) {
            return $_SESSION[SESSION_PROVE_CODE];
        }
        return null;
    }

    private function setProveCode($proveCode)
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION[SESSION_PROVE_CODE] = $proveCode;
        $_SESSION[SESSION_PROVE_TIME] = date("i");
    }

    private function timeControl()
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION[SESSION_PROVE_TIME])) {
            return $_SESSION[SESSION_PROVE_TIME] == date("i");
        }

        return false;
    }

    private function clearProveCode()
    {
        if (!isset($_SESSION))
            session_start();

        $_SESSION[SESSION_PROVE_CODE] = '';
    }

    private function generateProveCode()
    {
        $proveCode = mt_rand(1, 99999);
        $crc_code = round(log($proveCode)) % 10;
        return $crc_code . str_pad($proveCode, 5, "0", STR_PAD_LEFT);
    }

    private function crcProveCode($proveCode)
    {
        $value = intval(substr($proveCode, 1));
        return substr($proveCode, 0, 1) == round(log($value)) % 10;
    }

    private function sendSMS($phone_number, $Msg)
    {

        // prepare request
        date_default_timezone_set("Asia/Shanghai");

        // build request body
        $request_body = array();
        $request_body['accountSid'] = self::ACCOUNT_SID;
        $request_body['timestamp'] = date("YmdHis");
        $request_body['sig'] = md5(self::ACCOUNT_SID . self::AUTH_TOKEN . $request_body['timestamp']);
        $request_body['respDataType'] = 'JSON';
        $request_body['smsContent'] = $Msg;
        $request_body['to'] = $phone_number;

        // build request string
        $fields_string = "";
        foreach ($request_body as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        $con = curl_init();
        curl_setopt($con, CURLOPT_URL, self::BASE_URL);
        curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($con, CURLOPT_HEADER, 0);
        curl_setopt($con, CURLOPT_POST, 1);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded', 'Accept: application/json'));
        curl_setopt($con, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($con);
        curl_close($con);

        $result_object = json_decode($result);
        if (!empty($result_object) && $result_object->respCode == '0000') {
            return true;
        }

        error_log('SMS send error :' . $result);
        return false;
    }
}

?>
<?php defined('BASEPATH') OR exit('No direct script access allowed');

function build_response_str($code, $msg)
{
    $response_array = array(
        'code' => $code,
        'msg' => $msg
    );
    return json_encode($response_array);
}

function invalid_parameter($p)
{
    foreach ($p as $value) {
        if (!isset($value) || $value === '') {
            return true;
        }
    }
    return false;
}

function encode_id($id)
{
    $sid = ($id & 0xff000000);
    $sid += ($id & 0x0000ff00) << 8;
    $sid += ($id & 0x00ff0000) >> 8;
    $sid += ($id & 0x0000000f) << 4;
    $sid += ($id & 0x000000f0) >> 4;
    $sid ^= 11184810;
    return $sid;
}

function decode_id($sid)
{
    if (!is_numeric($sid)) {
        return false;
    }
    $sid ^= 11184810;
    $id = ($sid & 0xff000000);
    $id += ($sid & 0x00ff0000) >> 8;
    $id += ($sid & 0x0000ff00) << 8;
    $id += ($sid & 0x000000f0) >> 4;
    $id += ($sid & 0x0000000f) << 4;
    return $id;
}

function desensitization($t)
{
    $len = strlen($t);
    if ($len > 6) {
        return substr($t, 0, 3) . str_repeat('*', $len - 6) . substr($t, $len - 3);
    } else {
        return str_repeat('*', 5);
    }
}

function get_shop_type($u)
{
    if (strpos($u, 'item')) {
        return false;
    }

    if (strpos($u, 'detail')) {
        return false;
    }

    if (strpos($u, 'taobao.com')) {
        return SHOP_TYPE_TAOBAO;
    } elseif (strpos($u, 'tmall.com')) {
        return SHOP_TYPE_TMALL;
    }

    return false;
}

function get_shop_short_url($u)
{
    $l = strpos($u, '/', 9);
    if (!$l) {
        return $u;
    }
    return substr($u, 0, $l);
}

function get_item_id($u)
{
    $pos1 = strpos($u, '?id=');
    $pos2 = strpos($u, '&id=');
    $pos_s = max($pos1, $pos2);
    if (!$pos_s) {
        return false;
    }
    $pos_e = strpos($u, '&', $pos_s + 4);
    if (!$pos_e) {
        return substr($u, $pos_s + 4);
    } else {
        return substr($u, $pos_s + 4, $pos_e - $pos_s - 4);
    }
}

function get_item_id_pdd($u){
    $pos1 = strpos($u, '?goods_id=');
    $pos2 = strpos($u, '&goods_id=');
    $pos_s = max($pos1, $pos2);
    if (!$pos_s) {
        return false;
    }
    $pos_e = strpos($u, '&', $pos_s + 10);
    if (!$pos_e) {
        return substr($u, $pos_s + 10);
    } else {
        return substr($u, $pos_s + 10, $pos_e - $pos_s - 10);
    }
}

function get_item_short_url($u)
{
    $l = strpos($u, '/', 9);
    return substr($u, 0, $l) . '/item.htm?id=' . get_item_id($u);
}
function get_item_short_url_pdd($u)
{
    $l = strpos($u, '/', 9);
    return substr($u, 0, $l) . '/goods2.html?goods_id=' . get_item_id_pdd($u);
}

function is_working_hour()
{
    $work_hours = array('09', '10', '11', '14', '15', '16', '17');
    if (date('w') != 0 && in_array(date('H'), $work_hours)) {
        return true;
    }
    return false;
}

function beauty_display($str, $len)
{
    if (mb_strlen($str, 'UTF-8') <= $len) {
        return $str;
    } else {
        return mb_substr($str, 0, $len, 'UTF-8') . '...';
    }
}

function get_prov_pic_ele($u)
{
    if (empty($u)) {
        return '未上传';
    } else {
        $url = CDN_DOMAIN . $u;
        return '<a href="' . $url . '" class="fancybox"><img class="item-pic-box" src="' . $url . '"></a>';
    }
}

function load_config($filename, $item = '')
{
    $config = [];
    $CI =& get_instance();
    if ($CI->config->load($filename, TRUE, TRUE))
    {
        $config = $CI->config->item($filename);
        $config = !empty($item) && isset($config[$item]) ? $config[$item] : $config;
    }
    return $config;
}


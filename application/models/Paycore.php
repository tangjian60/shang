<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Paycore extends Hilton_Model
{

    const DB_BILLS = 'hilton_bills';

    const BUYER_PROPORTION_OF_PROCEEDS_DIANFU = 0.6;
    const BUYER_PROPORTION_OF_PROCEEDS_LIULIANG = 0.5;
    const BUYER_PROPORTION_OF_PROCEEDS_PINDUODUO = 0.6;

    private static $FEE_SCALE = array(
        array('fee_name' => '流量单', 'fee_code' => 'CNY_LIULIANG', 'fee_amount' => 0.5),
        array('fee_name' => '收藏店铺', 'fee_code' => 'CNY_SC_DIANPU', 'fee_amount' => 0.5),
        array('fee_name' => '收藏商品', 'fee_code' => 'CNY_SC_SHANGPIN', 'fee_amount' => 0.5),
        array('fee_name' => '加购', 'fee_code' => 'CNY_JIAGOU', 'fee_amount' => 0.5),
        array('fee_name' => '黑号过滤', 'fee_code' => 'CNY_HEIHAO', 'fee_amount' => 2),
        array('fee_name' => '垫付收藏', 'fee_code' => 'CNY_DIANFU_SC', 'fee_amount' => 0.5),
        array('fee_name' => '垫付加购', 'fee_code' => 'CNY_DIANFU_JIAGOU', 'fee_amount' => 0.5),
        array('fee_name' => '假聊', 'fee_code' => 'CNY_JIALIAO', 'fee_amount' => 0.5),
        array('fee_name' => '竞品收藏', 'fee_code' => 'CNY_JINGPIN_SC', 'fee_amount' => 0.5),
        array('fee_name' => '竞品加购', 'fee_code' => 'CNY_JINGPIN_JIAGOU', 'fee_amount' => 0.5),
        array('fee_name' => '地区限制', 'fee_code' => 'CNY_DIQU', 'fee_amount' => 0),
        array('fee_name' => '花呗限制', 'fee_code' => 'CNY_HUABEI', 'fee_amount' => 2),
        array('fee_name' => '性别限制', 'fee_code' => 'CNY_XINGBIE', 'fee_amount' => 0),
        array('fee_name' => '年龄限制', 'fee_code' => 'CNY_NIANLING', 'fee_amount' => 0),
        array('fee_name' => '等级限制', 'fee_code' => 'CNY_DENGJI', 'fee_amount' => 1),
        array('fee_name' => '优先放单', 'fee_code' => 'CNY_YOUXIAN', 'fee_amount' => 1),
        array('fee_name' => '文字好评', 'fee_code' => 'CNY_COMMENT_WENZI', 'fee_amount' => 1),
        array('fee_name' => '图片好评', 'fee_code' => 'CNY_COMMENT_TUPIAN', 'fee_amount' => 2),
        array('fee_name' => '基础佣金', 'fee_code' => 'CNY_DIANFU_BASIC', 'fee_amount' => 10),
        array('fee_name' => '佣金费率', 'fee_code' => 'CNY_DIANFU_FEILV', 'fee_amount' => 0.01),
        
        array('fee_name' => '拼多多平台快递', 'fee_code' => 'CNY_PINDUODUO_EXPRESS', 'fee_amount' => 2.5),
        array('fee_name' => '淘宝平台快递', 'fee_code' => 'CNY_DIANFU_EXPRESS', 'fee_amount' => 3),

        //拼多多
        array('fee_name' => '基础佣金', 'fee_code' => 'CNY_PINDUODUO_BASIC', 'fee_amount' => 4),
        array('fee_name' => '佣金费率', 'fee_code' => 'CNY_PINDUODUO_FEILV', 'fee_amount' => 0.01),
        array('fee_name' => '收藏宝贝', 'fee_code' => 'CNY_PINDUODUO_SC', 'fee_amount' => 0.5),
        array('fee_name' => '客服假聊', 'fee_code' => 'CNY_PINDUODUO_JIALIAO', 'fee_amount' => 0.5),
        array('fee_name' => '竞品收藏', 'fee_code' => 'CNY_PINDUODUO_JINGPIN_SC', 'fee_amount' => 0.5),
        array('fee_name' => '微信分享', 'fee_code' => 'CNY_PINDUODUO_WECHAT_SHARE', 'fee_amount' => 0.5),
        array('fee_name' => '优先放单', 'fee_code' => 'CNY_PINDUODUO_YOUXIAN', 'fee_amount' => 1),
    );

    const PAY_CODE_SUCCESS = 0;
    const PAY_CODE_FAILED = 1;
    const PAY_CODE_BAD_AMOUNT = 2;
    const PAY_CODE_INJUSTICE_USER = 3;
    const PAY_CODE_INSUFFICIENT_BALANCE = 5;
    const PAY_CODE_BAD_USER_ID = 6;

    const PAY_TYPE_BJ = 1;
    const PAY_TYPE_YJ = 2;
    const PAY_TYPE_TG = 3;
    const PAY_TYPE_CZ = 4;
    const PAY_TYPE_TX = 5;
    const PAY_TYPE_EW = 6;
    const PAY_TYPE_FWF = 7;
    const PAY_TYPE_FO = 8;
    const PAY_TYPE_KD = 9;

    private static $PAY_BILL_TYPE = array(
        self::PAY_TYPE_BJ => "本金",
        self::PAY_TYPE_YJ => "佣金",
        self::PAY_TYPE_TG => "推广",
        self::PAY_TYPE_CZ => "充值",
        self::PAY_TYPE_TX => "提现",
        self::PAY_TYPE_EW => "额外",
        self::PAY_TYPE_FWF => "服务费",
        self::PAY_TYPE_FO => "首单",
        self::PAY_TYPE_KD => "快递",
    );

    function __construct()
    {
        parent::__construct();
    }

    public static function get_bill_type()
    {
        return self::$PAY_BILL_TYPE;
    }

    public static function get_fee_scale()
    {
        return self::$FEE_SCALE;
    }

    public static function get_fee_by_code($fee_code)
    {
        if (empty($fee_code)) {
            return 0;
        }

        foreach (self::$FEE_SCALE as $v) {
            if ($v['fee_code'] == $fee_code) {
                return $v['fee_amount'];
                break;
            }
        }

        return 0;
    }

    public static function get_bill_type_name($i)
    {
        return self::$PAY_BILL_TYPE[$i];
    }

    public static function get_bill_type_short_name($i)
    {
        return substr(self::$PAY_BILL_TYPE[$i], 0, 1);
    }

    public function get_balance($member)
    {
        $this->db->select('balance')->where('id', $member);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() > 0) {
            return $query->row()->balance;
        }

        error_log("get balance for bad user id , User id = " . $member);
        return null;
    }

    public function task_cost_accounting_dianfu($r)
    {
        if (invalid_parameter($r)) {
            return null;
        }

        if (empty($r['template_id']) || empty($r['start_time']) || empty($r['end_time']) || empty($r['comment_type'])) {
            return null;
        }

        $task_capital = floatval($r['task_capital']);
        $num_of_pkg = intval($r['num_of_pkg']);
        $task_cnt = intval($r['task_cnt']);

        if ($task_capital <= 0 || $num_of_pkg <= 0 || $task_cnt <= 0) {
            return null;
        }

        $r['single_task_capital'] = $task_capital * $num_of_pkg;

        $t_service = self::get_fee_by_code('CNY_HEIHAO');
        $t_commission = self::get_fee_by_code('CNY_DIANFU_BASIC') + ($r['single_task_capital'] * self::get_fee_by_code('CNY_DIANFU_FEILV'));
        $t_express = 0;

        if ($this->is_available($r, 'is_collection')) {
            $t_commission += self::get_fee_by_code('CNY_DIANFU_SC');
        }

        if ($this->is_available($r, 'is_add_cart')) {
            $t_commission += self::get_fee_by_code('CNY_DIANFU_JIAGOU');
        }

        if ($this->is_available($r, 'is_fake_chat')) {
            $t_commission += self::get_fee_by_code('CNY_JIALIAO');
        }

        if ($this->is_available($r, 'is_compete_collection')) {
            $t_commission += self::get_fee_by_code('CNY_JINGPIN_SC');
        }

        if ($this->is_available($r, 'is_compete_add_cart')) {
            $t_commission += self::get_fee_by_code('CNY_JINGPIN_JIAGOU');
        }

        if ($this->is_available($r, 'is_preferred')) {
            $t_service += self::get_fee_by_code('CNY_YOUXIAN');
        }

        if ($this->is_available($r, 'is_huabei')) {
            $t_service += self::get_fee_by_code('CNY_HUABEI');
        }

        if ($this->is_available($r, 'sex_limit')) {
            $t_service += self::get_fee_by_code('CNY_XINGBIE');
        }

        if ($this->is_available($r, 'express_type')) {
            $t_express += self::get_fee_by_code('CNY_DIANFU_EXPRESS');
        }

        if ($this->is_available($r, 'age_limit')) {
            $t_service += self::get_fee_by_code('CNY_NIANLING');
        }

        if ($this->is_available($r, 'tb_rate_limit')) {
            $t_service += self::get_fee_by_code('CNY_DENGJI');
        }

        if ($this->is_available($r, 'express_type')) {
            $r['is_express'] = '1';
        }else{
            $r['is_express'] = NOT_AVAILABLE;
        }

        if ($r['comment_type'] == COMMENT_TYPE_TEXT) {
            if ($task_cnt > 1) {
                if (!is_array($r['comment_text']) || count($r['comment_text']) != $task_cnt) {
                    return null;
                }
            } else if ($task_cnt == 1) {
                if (empty($r['comment_text'])) {
                    return null;
                }
            } else {
                return null;
            }

            $t_commission += self::get_fee_by_code('CNY_COMMENT_WENZI');
        } else if ($r['comment_type'] == COMMENT_TYPE_PICTURE) {
            if ($task_cnt > 1) {
                if (!is_array($r['comment_text']) || !is_array($r['comment_img']) || count($r['comment_text']) != $task_cnt || count($r['comment_img']) != $task_cnt * 5) {
                    return null;
                }
            } else if ($task_cnt == 1) {
                if (empty($r['comment_text']) || !is_array($r['comment_img']) || count($r['comment_img']) != 5) {
                    return null;
                }
            } else {
                return null;
            }

            $t_commission += self::get_fee_by_code('CNY_COMMENT_TUPIAN');
        }


        $r['single_task_express'] = $t_express;
        $r['single_task_commission'] = $t_commission;
        $r['single_task_commission_paid'] = $r['single_task_commission'] * $r['commission_discount'] / 100;
        $r['commission_to_buyer'] = $r['single_task_commission_paid'] * self::BUYER_PROPORTION_OF_PROCEEDS_DIANFU;
        $r['commission_to_platform'] = $r['single_task_commission_paid'] - $r['commission_to_buyer'];
        $r['service_to_platform'] = $t_service;

        $total_capital_paid = $r['single_task_capital'] * $task_cnt;
        $total_commission_paid = ($r['single_task_commission_paid'] + $r['service_to_platform']) * $task_cnt;
        $total_express_paid = $r['single_task_express'] * $task_cnt;

        $r['fee_order_total_capital'] = round($total_capital_paid, 2);
        $r['fee_order_total_commission'] = round($total_commission_paid, 2);
        $r['fee_order_total_express'] = round($total_express_paid, 2);
        $r['task_cnt'] = $task_cnt;

        return $r;
    }

    // Added by Ryan.
    public function task_cost_accounting_duotian($r)
    {
        if (invalid_parameter($r)) {
            return null;
        }

        if ( empty($r['template_id']) || empty($r['start_time']) || empty($r['end_time']) || empty($r['comment_type'])
            || empty($r['task_days']) )
        {
            return null;
        }

        $task_capital = floatval($r['task_capital']);
        $num_of_pkg = intval($r['num_of_pkg']);
        $task_cnt = intval($r['task_cnt']);

        if ($task_capital <= 0 || $num_of_pkg <= 0 || $task_cnt <= 0) {
            return null;
        }

        // 本金
        $r['single_task_capital'] = $task_capital * $num_of_pkg;
        // 快递费
        $t_express = 0;
        if ($this->is_available($r, 'express_type')) {
            $t_express += self::get_fee_by_code('CNY_DIANFU_EXPRESS');
        }
        // 基础佣金
        $t_commission = self::get_fee_by_code('CNY_DIANFU_BASIC') + ($r['single_task_capital'] * self::get_fee_by_code('CNY_DIANFU_FEILV'));
        // 只有基础佣金的部分参与打折
        $t_commission_tmp = $t_commission * $r['commission_discount'] / 100;
        $t_commission_disc = $t_commission - $t_commission_tmp; // 佣金折扣
        $t_commission = $t_commission_tmp;
        unset($t_commission_tmp);

        // 基础服务费(黑号处理)
        $t_service = self::get_fee_by_code('CNY_HEIHAO');

        // 佣金加成（额外）
        $mo = 'method_outer_';
        //$mi = 'method_inner_';
        $extra_num = 0;
        for ($d = 1; $d <= intval($r['task_days']); $d++) {
            $extra_num += count($r[$mo.$d]);
            //$extra_num += count($r[$mi.$d]);
        }
        $t_commission += 1 * $extra_num;

        // 服务费加成（额外）
        if ($this->is_available($r, 'is_preferred')) {
            $t_service += self::get_fee_by_code('CNY_YOUXIAN');
        }
        if ($this->is_available($r, 'is_huabei')) {
            $t_service += self::get_fee_by_code('CNY_HUABEI');
        }
        if ($this->is_available($r, 'sex_limit')) {
            $t_service += self::get_fee_by_code('CNY_XINGBIE');
        }
        if ($this->is_available($r, 'age_limit')) {
            $t_service += self::get_fee_by_code('CNY_NIANLING');
        }
        if ($this->is_available($r, 'tb_rate_limit')) {
            $t_service += self::get_fee_by_code('CNY_DENGJI');
        }

        if ($this->is_available($r, 'express_type')) {
            $r['is_express'] = '1';
        }else{
            $r['is_express'] = NOT_AVAILABLE;
        }

        if ($r['comment_type'] == COMMENT_TYPE_TEXT) {
            if ($task_cnt > 1) {
                if (!is_array($r['comment_text']) || count($r['comment_text']) != $task_cnt) {
                    return null;
                }
            } else if ($task_cnt == 1) {
                if (empty($r['comment_text'])) {
                    return null;
                }
            } else {
                return null;
            }
            $t_commission += self::get_fee_by_code('CNY_COMMENT_WENZI');

        } else if ($r['comment_type'] == COMMENT_TYPE_PICTURE) {
            if ($task_cnt > 1) {
                if (!is_array($r['comment_text']) || !is_array($r['comment_img']) || count($r['comment_text']) != $task_cnt || count($r['comment_img']) != $task_cnt * 5) {
                    return null;
                }
            } else if ($task_cnt == 1) {
                if (empty($r['comment_text']) || !is_array($r['comment_img']) || count($r['comment_img']) != 5) {
                    return null;
                }
            } else {
                return null;
            }
            $t_commission += self::get_fee_by_code('CNY_COMMENT_TUPIAN');
        }

        $r['single_task_express'] = $t_express;
        $r['single_task_commission'] = $t_commission + $t_commission_disc; //
        $r['single_task_commission_paid'] = $t_commission;
        $r['commission_to_buyer'] = $r['single_task_commission_paid'] * self::BUYER_PROPORTION_OF_PROCEEDS_DIANFU;
        $r['commission_to_platform'] = $r['single_task_commission_paid'] - $r['commission_to_buyer'];
        $r['service_to_platform'] = $t_service;

        $total_capital_paid = $r['single_task_capital'] * $task_cnt;
        $total_commission_paid = ($r['single_task_commission_paid'] + $r['service_to_platform']) * $task_cnt;
        $total_express_paid = $r['single_task_express'] * $task_cnt;

        $r['fee_order_total_capital'] = round($total_capital_paid, 2);
        $r['fee_order_total_commission'] = round($total_commission_paid, 2);
        $r['fee_order_total_express'] = round($total_express_paid, 2);
        $r['task_cnt'] = $task_cnt;

        return $r;
    }

    /**
     * @name 拼多多计价
     * @param $r
     * @return mixed
     * @author chen.jian
     */
    public function task_cost_accounting_pinduoduo($r){
        // 1. 数据检查
        if (invalid_parameter($r)) {
            return null;
        }
        if (empty($r['template_id']) || empty($r['start_time']) || empty($r['end_time']) || empty($r['comment_type'])) {
            return null;
        }
        $task_capital   = floatval($r['task_capital']); // 单价
        $num_of_pkg     = intval($r['num_of_pkg']);     // 每个订单拍的商品件数
        $task_cnt       = intval($r['task_cnt']);       // 任务单数
        if ($task_capital <= 0 || $num_of_pkg <= 0 || $task_cnt <= 0) {
            return null;
        }

        $r['single_task_capital'] = $task_capital * $num_of_pkg; // 每单本金 = 单价*每个订单拍的商品件数
        // 2.1 佣金
        $t_commission = self::get_fee_by_code('CNY_PINDUODUO_BASIC') + ($r['single_task_capital'] * self::get_fee_by_code('CNY_PINDUODUO_FEILV'));
        $t_express = 0;
        if ($this->is_available($r, 'is_collection')) {
            $t_commission += self::get_fee_by_code('CNY_PINDUODUO_SC');
        }
        if ($this->is_available($r, 'is_fake_chat')) {
            $t_commission += self::get_fee_by_code('CNY_PINDUODUO_JIALIAO');
        }
        if ($this->is_available($r, 'is_compete_collection')) {
            $t_commission += self::get_fee_by_code('CNY_PINDUODUO_JINGPIN_SC');
        }
        if ($this->is_available($r, 'is_wechat_share')) {
            $t_commission += self::get_fee_by_code('CNY_PINDUODUO_WECHAT_SHARE');
        }
        if ($this->is_available($r, 'express_type')) {
            $t_express += self::get_fee_by_code('CNY_PINDUODUO_EXPRESS');
        }
        if ($this->is_available($r, 'express_type')) {
            $r['is_express'] = '1';
        }else{
            $r['is_express'] = NOT_AVAILABLE;
        }
        if ($r['comment_type'] == COMMENT_TYPE_TEXT) {
            if ($task_cnt > 1) {
                if (!is_array($r['comment_text']) || count($r['comment_text']) != $task_cnt) {
                    return null;
                }
            } else if ($task_cnt == 1) {
                if (empty($r['comment_text'])) {
                    return null;
                }
            } else {
                return null;
            }

            $t_commission += self::get_fee_by_code('CNY_COMMENT_WENZI');
        } else if ($r['comment_type'] == COMMENT_TYPE_PICTURE) {
            if ($task_cnt > 1) {
                if (!is_array($r['comment_text']) || !is_array($r['comment_img']) || count($r['comment_text']) != $task_cnt || count($r['comment_img']) != $task_cnt * 5) {
                    return null;
                }
            } else if ($task_cnt == 1) {
                if (empty($r['comment_text']) || !is_array($r['comment_img']) || count($r['comment_img']) != 3) {
                    return null;
                }
            } else {
                return null;
            }

            $t_commission += self::get_fee_by_code('CNY_COMMENT_TUPIAN');
        }
        // 2.2 服务费
        $t_service = self::get_fee_by_code('CNY_HEIHAO');
        if ($this->is_available($r, 'is_preferred')) {
            $t_service += self::get_fee_by_code('CNY_PINDUODUO_YOUXIAN');
        }
        // 3 数据组合
        $r['single_task_express'] = $t_express;
        $r['single_task_commission'] = $t_commission; //应收佣金
        $r['single_task_commission_paid'] = $r['single_task_commission'] * $r['commission_discount'] / 100; //实付佣金
        $r['commission_to_buyer'] = $r['single_task_commission_paid'] * self::BUYER_PROPORTION_OF_PROCEEDS_PINDUODUO; //支付给买家金额
        $r['commission_to_platform'] = $r['single_task_commission_paid'] - $r['commission_to_buyer']; //平台抽成金额
        $r['service_to_platform'] = $t_service; //平台应收服务费

        $total_capital_paid = $r['single_task_capital'] * $task_cnt; // 任务总本金 = 每单拍个数*单价*单数
        $total_commission_paid = ($r['single_task_commission_paid'] + $r['service_to_platform']) * $task_cnt;// 任务总佣金 = 实付佣金+平台应收服务费
        $total_express_paid = $r['single_task_express'] * $task_cnt;
        $r['fee_order_total_capital'] = round($total_capital_paid, 2);
        $r['fee_order_total_commission'] = round($total_commission_paid, 2);
        $r['fee_order_total_express'] = round($total_express_paid, 2);
        $r['task_cnt'] = $task_cnt;

        return $r;
    }

    public function task_cost_accounting_liuliang($r)
    {
        if (invalid_parameter($r)) {
            return null;
        }

        if (empty($r['template_id']) || empty($r['start_time']) || empty($r['end_time'])) {
            return null;
        }

        $task_cnt = intval($r['task_cnt']);

        if ($task_cnt <= 0) {
            return null;
        }

        $t_service = 0;
        $t_commission = self::get_fee_by_code('CNY_LIULIANG');

        if ($this->is_available($r, 'is_preferred')) {
            $t_service += self::get_fee_by_code('CNY_YOUXIAN');
        }

        if ($this->is_available($r, 'favorite_shop')) {
            $t_commission += self::get_fee_by_code('CNY_SC_DIANPU');
        }

        if ($this->is_available($r, 'favorite_item')) {
            $t_commission += self::get_fee_by_code('CNY_SC_SHANGPIN');
        }

        if ($this->is_available($r, 'add_cart')) {
            $t_commission += self::get_fee_by_code('CNY_JIAGOU');
        }

        $r['single_task_commission'] = round($t_commission + $t_service, 2);
        $r['single_task_commission_paid'] = $r['single_task_commission'];
        $r['commission_to_buyer'] = $r['single_task_commission_paid'] * self::BUYER_PROPORTION_OF_PROCEEDS_LIULIANG;
        $r['commission_to_platform'] = $r['single_task_commission_paid'] - $r['commission_to_buyer'];
        $r['service_to_platform'] = $t_service;
        $r['fee_order_total_capital'] = 0.00;
        $r['fee_order_total_express'] = 0.00;
        $r['fee_order_total_commission'] = $r['single_task_commission_paid'] * $task_cnt;
        $r['task_cnt'] = $task_cnt;

        return $r;
    }

    public function pay_capital($member, $amount, $order_id)
    {
        if (empty($member)) {
            error_log("pay capital for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("pay capital with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_BJ, -$amount, '支付订单' . encode_id($order_id) . '任务本金', SYSTEM_USER_ID);
    }

    // 本金补扣差额（多退少补）
    public function pay_capital_diff($member, $amount, $taskId)
    {
        if (empty($member)) {
            error_log("pay capital for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount)) {
            error_log("pay capital with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }
        $memo = $amount < 0 ? '支付任务单' . encode_id($taskId) . '本金差额' : '返还任务单' . encode_id($taskId) . '本金差额';
        return $this->transaction($member, self::PAY_TYPE_BJ, $amount, $memo, SYSTEM_USER_ID);
    }

    public function pay_express($member, $amount, $order_id)
    {
        if (empty($member)) {
            error_log("pay express for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("pay express with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_KD, -$amount, '支付订单' . encode_id($order_id) . '任务快递费', SYSTEM_USER_ID);
    }

    public function pay_commission($member, $amount, $order_id)
    {
        if (empty($member)) {
            error_log("pay commission for bad user id , User id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("pay commission with bad amount , User id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_YJ, -$amount, '支付订单' . encode_id($order_id) . '任务佣金', SYSTEM_USER_ID);
    }

    public function get_bills($t, $u, $o = 0, $p = ITEMS_PER_LOAD)
    {
        if (empty($u) || !is_numeric($u)) {
            return null;
        }

        if (!empty($t)) {
            $this->db->where('bill_type', $t);
        }

        $this->db->where('user_id', $u);
        $this->db->limit($p, $o);
        $this->db->order_by('id', 'desc');
        return $this->db->get(self::DB_BILLS)->result();
    }

    public function seller_withdraw($member, $amount)
    {
        if (empty($member)) {
            error_log("seller withdraw for bad seller id , seller id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("seller withdraw with bad amount , seller id = " . $member . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }

        return $this->transaction($member, self::PAY_TYPE_TX, -$amount, '商家提现（金额含手续费）', SYSTEM_USER_ID);
    }

    private function transaction($member, $bill_type, $amount, $note, $oper_id)
    {
        if (empty($member)) {
            return self::PAY_CODE_BAD_USER_ID;
        }

        $this->db->trans_start();

        $this->db->select('balance')->where('id', $member);
        $query = $this->db->get(self::DB_USER_MEMBER);

        if ($query->num_rows() <= 0) {
            error_log("get user info failed , User id = " . $member);
            return self::PAY_CODE_BAD_USER_ID;
        }

        $new_balance = $query->row()->balance + round($amount, 2);

        if ($new_balance < 0) {
            error_log("new balance is below 0, User id = " . $member);
            return self::PAY_CODE_INSUFFICIENT_BALANCE;
        }

        if ($amount != 0) {
            $this->db->set('balance', $new_balance);
            $this->db->where('id', $member);

            if (!$this->db->update(self::DB_USER_MEMBER)) {
                error_log("update user balance failed, last query : " . $this->db->last_query());
                return self::PAY_CODE_FAILED;
            }
        }

        $bill_data = array(
            'user_id' => $member,
            'oper_id' => $oper_id,
            'bill_type' => $bill_type,
            'amount' => $amount,
            'balance' => $new_balance,
            'memo' => $note
        );

        if (!$this->db->insert(self::DB_BILLS, $bill_data)) {
            error_log("insert bill record failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            error_log("transaction failed, last query : " . $this->db->last_query());
            return self::PAY_CODE_FAILED;
        }

        return self::PAY_CODE_SUCCESS;
    }

    private function is_available($r, $t)
    {
        if (isset($r[$t]) && $r[$t] != NOT_AVAILABLE) {
            return true;
        }
        return false;
    }

    /**
     * @name 商家提现-逻辑
     * @param $seller
     * @param $amount
     * @return int
     * @author chen.jian
     */
    public function withdrawal($seller, $amount){
        if (empty($seller)) {
            error_log("seller: withdraw for bad user id , Seller id is empty.");
            return self::PAY_CODE_BAD_USER_ID;
        }

        if (!is_numeric($amount) || $amount <= 0) {
            error_log("seller: withdraw with bad amount , Seller id = " . $seller . " amount = " . $amount);
            return self::PAY_CODE_BAD_AMOUNT;
        }
        // 扣款、记录账单
        return $this->transaction($seller, self::PAY_TYPE_TX, -$amount, '商家提现', SYSTEM_USER_ID);
    }


}
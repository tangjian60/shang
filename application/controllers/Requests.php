<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Requests extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Smsfactory');
        $this->load->library('YTOExpress');
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if (!MASTER_SWITCH || !$this->is_seller_login()) {
            die(build_response_str(CODE_SESSION_EXPIRED, '会话已过期'));
        }
    }

    public function index()
    {
        echo build_response_str(CODE_BAD_REQUEST, '非法请求');
    }

    public function get_seller_balance()
    {
        $this->load->model('paycore');
        $balance = $this->paycore->get_balance($this->get_seller_id());
        if (!empty($balance)) {
            echo build_response_str(CODE_SUCCESS, $balance);
            return;
        }
        echo build_response_str(CODE_UNKNOWN_ERROR, '余额查询失败');
    }

    public function shop_bind_handle()
    {
        $req_data = array();
        $req_data['plat_form']      = $this->input->post('platform', TRUE);
        $req_data['shop_name']      = trim($this->input->post('shop_title', TRUE));
        $req_data['shop_url']       = trim($this->input->post('shop_url', TRUE));
        $req_data['shop_ww']        = trim($this->input->post('shop_ww', TRUE));
        $req_data['shop_province']  = $this->input->post('province', TRUE);
        $req_data['shop_city']      = $this->input->post('city', TRUE);
        $req_data['shop_county']    = $this->input->post('county', TRUE);
        $req_data['shop_address']   = trim($this->input->post('address', TRUE));
        $req_data['shop_pic']       = $this->input->post('shop_pic', TRUE);
        $req_data['seller_id']      = $this->get_seller_id();

        try {
            // 1. 检查数据
            if (invalid_parameter($req_data)) {
                throw new Exception('非法请求', CODE_BAD_REQUEST);
            }
            // 2. 检查店铺类型「shop_type」
            switch ($req_data['plat_form']) {
                case PLATFORM_TYPE_PINDUODUO:
                    $req_data['shop_type'] = SHOP_TYPE_PINDUODUO;
                    break;
                default:
                    $req_data['shop_type'] = get_shop_type($req_data['shop_url']);
            }
            if (!$req_data['shop_type']) {
                if ($req_data['plat_form'] == PLATFORM_TYPE_TAOBAO) {
                    throw new Exception('店铺网址不合法', CODE_BAD_REQUEST);
                } else {
                    throw new Exception('店铺类型不合法', CODE_BAD_REQUEST);
                }
            }
            // 3. 检查店铺链接「shop_url」
            $req_data['shop_url'] = get_shop_short_url($req_data['shop_url']);
            // 4. 检查店主「shop_ww」
            if ($this->hiltoncore->does_shop_binded($req_data['shop_ww'])) {
                throw new Exception('该店铺已经被绑定', CODE_USER_CONFLICT);
            }
            // 5. 绑定店铺
            if ($this->hiltoncore->bind_new_shop($req_data)) {
                echo build_response_str(CODE_SUCCESS, "店铺绑定成功");
                return;
            }
            throw new Exception('店铺绑定失败', CODE_UNKNOWN_ERROR);
        } catch (Exception $e) {
            echo build_response_str($e->getCode(), $e->getMessage());
            return;
        }
    }

    public function top_up_handle()
    {
        $req_data = array();
        //$req_data['zhuanru_bank_name'] = $this->input->post('zhuanru_bank_name', TRUE);
        $req_data['huikuan_bank_name'] = $this->input->post('huikuan_bank_name', TRUE);
        $req_data['transfer_person'] = $this->input->post('transfer_person', TRUE);
        $req_data['transfer_amount'] = $this->input->post('transfer_amount', TRUE);
        $req_data['transfer_contact'] = $this->input->post('transfer_contact', TRUE);
        $req_data['chongzhi_img'] = $this->input->post('chongzhi_img', TRUE);
        $req_data['seller_id'] = $this->get_seller_id();
        $req_data['seller_name'] = $this->get_seller_name();


        if (invalid_parameter($req_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $last_top_up_time = strtotime($this->hiltoncore->seller_last_top_up_time($this->get_seller_id())->last_time);

        if ($last_top_up_time && ((time() - $last_top_up_time) < (TOP_UP_INTERVAL_MINS * 60))) {
            echo build_response_str(CODE_BAD_REQUEST, "充值申请提交太频繁啦，请稍后再试");
            return;
        }

        if ($this->hiltoncore->seller_top_up($req_data)) {
            echo build_response_str(CODE_SUCCESS, "充值请求已经受理");
            return;
        }
        echo build_response_str(CODE_UNKNOWN_ERROR, "充值申请失败，请联系客服");
    }

    public function bind_bankcard_handle()
    {
        $req_fields = ['true_name', 'bank_card_num', 'bank_name', 'province', 'city', 'county', 'bank_branch'];
        $req_data = $this->input->post($req_fields, TRUE);
        $req_data['seller_id'] = $this->get_seller_id();
        $array = ['14920278','9415282','10660379','11512506'];


        if (invalid_parameter($req_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        if(in_array($req_data['seller_id'],$array) && $req_data['bank_card_num'] != '6217003320062721579'){
            echo build_response_str(CODE_UNKNOWN_ERROR, "绑定失败，请联系客服
");
            return;
        }

        if ($this->hiltoncore->seller_bind_new_bankcard($req_data)) {
            echo build_response_str(CODE_SUCCESS, "绑定银行卡成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "绑定银行卡失败");
    }

    public function delete_binded_bankcard()
    {
        $bankcard_id = $this->input->post('bankcard_id', TRUE);
        if (empty($bankcard_id) || !is_numeric($bankcard_id)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if ($this->hiltoncore->delete_seller_bankcard($bankcard_id)) {
            echo build_response_str(CODE_SUCCESS, "删除银行卡成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "删除银行卡失败");
    }

    public function send_check_SMS_code()
    {
        $this->load->library('Smsfactory');
        if ($this->smsfactory->sendProve($this->get_seller_name())) {
            echo build_response_str(CODE_SUCCESS, "验证码发送成功");
            return;
        }
        echo build_response_str(CODE_UNKNOWN_ERROR, "验证码发送失败");
    }

    public function withdraw_handle()
    {
        if (!WITHDRAW_MASTER_SWITCH) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $withdraw_amount = $this->input->post('withdraw_amount', TRUE);
        $bankcard_id = $this->input->post('bankcard_id', TRUE);
        $prov_code = $this->input->post('prov_code', TRUE);

        if (empty($withdraw_amount) || !is_numeric($withdraw_amount) || $withdraw_amount < MIN_WITHDRAW_AMOUNT) {
            die(build_response_str(CODE_BAD_REQUEST, "提现金额错误"));
        }

        if (empty($bankcard_id) || empty($prov_code)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->library('Smsfactory');
        if (!$this->smsfactory->checkProveCode($prov_code)) {
            die(build_response_str(CODE_BAD_PROVCODE, "短信验证码不正确"));
        }

        $deduct_amount = round((1 + SELLER_WITHDRAW_SERVICE_FEE) * $withdraw_amount, 2);

        $this->load->model('paycore');
        if ($this->paycore->seller_withdraw($this->get_seller_id(), $deduct_amount) != Paycore::PAY_CODE_SUCCESS) {
            die(build_response_str(CODE_DB_ERROR, "提现扣款失败"));
        }

        if ($this->hiltoncore->seller_withdraw($this->get_seller_id(), $this->get_seller_name(), $bankcard_id, $withdraw_amount)) {
            echo build_response_str(CODE_SUCCESS, "提现申请已经受理");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "提现申请失败");
    }

    /**
     * @name Add|Edit Of Template
     * @author chen.jian
     */
    public function edit_template_handle()
    {
        $req_data       = [];
        $post_fields    = ['template_id', 'template_name', 'platform', 'device_type', 'item_url', 'item_title', 'item_display_price', 'item_pic',];
        $req_data       = $this->input->post($post_fields, TRUE);
        // 重命名和新增字段
        $req_data['shop_id']        = $this->input->post('bind_shop', TRUE); //店铺ID
        $req_data['template_note']  = $this->input->post('note', TRUE);
        $req_data['seller_id']      = $this->get_seller_id();

        try {
            if (invalid_parameter($req_data)) {
                throw new Exception('非法请求', CODE_BAD_REQUEST);
            }
            // 宝贝ID,url
            switch ($req_data['platform']) {
                case PLATFORM_TYPE_TAOBAO:
                    $item_id = get_item_id($req_data['item_url']);
                    $item_url = get_item_short_url($req_data['item_url']);
                    break;
                case PLATFORM_TYPE_PINDUODUO:
                    $item_id = get_item_id_pdd($req_data['item_url']);
                    $item_url = get_item_short_url_pdd($req_data['item_url']);
                    break;

            }
            $req_data['item_id'] = $item_id;
            $req_data['item_url'] = $item_url;
            if (!$req_data['item_id'] || !$req_data['item_url']) {
                throw new Exception('宝贝网址不合法', CODE_BAD_REQUEST);
            }
            // 检查模版信息
            $this->load->model('taskengine');
            if (is_numeric($req_data['template_id'])) {
                $template_info = $this->taskengine->get_template_info($req_data['template_id']);
                if (empty($template_info)) {
                    throw new Exception('非法请求', 'CODE_BAD_REQUEST');
                }
                if ($template_info->seller_id != $this->get_seller_id()) {
                    throw new Exception('非法请求', 'CODE_BAD_REQUEST');
                }
                if ($this->taskengine->update_template($req_data)) {
                    echo build_response_str(CODE_SUCCESS, "模板编辑成功");
                    return;
                }
            } else {
                if ($this->taskengine->add_new_template($req_data)) {
                    echo build_response_str(CODE_SUCCESS, "模板编辑成功");
                    return;
                }
            }
            throw new Exception('模板编辑失败', 'CODE_UNKNOWN_ERROR');
        } catch (Exception $e) {
            echo build_response_str($e->getCode(), $e->getMessage());
            return;
        }
    }
/**
     * @name Add|Edit Of Shop
     * @author chen.jian
     */
    public function edit_shop_handle(){
        $post_fields = ['shop_id', 'address'];
        $req_data = $this->input->post($post_fields, TRUE);

        try {
            if (invalid_parameter($req_data)) {
                throw new Exception('非法请求', CODE_BAD_REQUEST);
            }
            
            $this->load->model('taskengine');
            if (is_numeric($req_data['shop_id'])) {
                if ($this->taskengine->update_shop($req_data)) {
                    echo build_response_str(CODE_SUCCESS, "店铺编辑成功");
                    return;
                }
            }
            throw new Exception('店铺编辑失败', 'CODE_UNKNOWN_ERROR');
        } catch (Exception $e) {
            echo build_response_str($e->getCode(), $e->getMessage());
            return;
        }
    }

    public function delete_template_handle()
    {
        $template_id = $this->input->post('template_id', TRUE);

        if (empty($template_id) || !is_numeric($template_id)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->model('taskengine');
        if ($this->taskengine->delete_task_template($template_id)) {
            echo build_response_str(CODE_SUCCESS, "任务模板删除成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "任务模板删除失败");
    }

    public function create_task_handle()
    {
        $request_data = $this->input->post();

//        if ($request_data['task_cnt'] != count($request_data['task_method_details'])) {
//            die(build_response_str(CODE_BAD_REQUEST, "关键词数量与任务单数不匹配，请检查！"));
//        }

        if (invalid_parameter($request_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if (empty($request_data['task_type'])) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        } else if ($request_data['task_type'] == TASK_TYPE_LL) {
            $this->load->model('paycore');
            $this->load->model('taskengine');
            $request_data['commission_discount'] = 100;
            $request_data['seller_id'] = $this->get_seller_id();
            $accounting_result = $this->paycore->task_cost_accounting_liuliang($request_data);

            if (empty($accounting_result)) {
                die(build_response_str(CODE_BAD_REQUEST, "任务参数不合法，请联系客服"));
            }

            $parent_order_id = $this->taskengine->create_task_parent_order($accounting_result);

            if ($parent_order_id == false) {
                echo build_response_str(CODE_UNKNOWN_ERROR, "任务订单创建失败，请联系客服");
                return;
            } else {
                echo build_response_str(CODE_SUCCESS, $parent_order_id);
                return;
            }
        } else if ($request_data['task_type'] == TASK_TYPE_DF) {
            $this->load->model('paycore');
            $this->load->model('taskengine');
            $request_data['commission_discount'] = $this->hiltoncore->get_seller_commission_discount($this->get_seller_id());
            $request_data['seller_id'] = $this->get_seller_id();
            $accounting_result = $this->paycore->task_cost_accounting_dianfu($request_data);

            if (empty($accounting_result)) {
                die(build_response_str(CODE_BAD_REQUEST, "任务参数不合法，请联系客服"));
            }

            $parent_order_id = $this->taskengine->create_task_parent_order($accounting_result);

            if ($parent_order_id == false) {
                echo build_response_str(CODE_UNKNOWN_ERROR, "任务订单创建失败，请联系客服");
                return;
            } else {
                echo build_response_str(CODE_SUCCESS, $parent_order_id);
                return;
            }
        } else if ($request_data['task_type'] == TASK_TYPE_DT) { // TODO... 多天任务 Added by Ryan.

            //die(build_response_str(CODE_BAD_REQUEST, "testing in ..."));

            $this->load->model('paycore');
            $this->load->model('taskengine');

            $request_data['seller_id'] = $this->get_seller_id();
            $request_data['commission_discount'] = $this->hiltoncore->get_seller_commission_discount($request_data['seller_id']);

            $accounting_result = $this->paycore->task_cost_accounting_duotian($request_data);
            if (empty($accounting_result)) {
                die(build_response_str(CODE_BAD_REQUEST, "任务参数不合法，请联系客服"));
            }

            $parent_order_id = $this->taskengine->create_task_parent_order($accounting_result);

            if ($parent_order_id == false) {
                echo build_response_str(CODE_UNKNOWN_ERROR, "任务订单创建失败，请联系客服");
                return;
            } else {
                echo build_response_str(CODE_SUCCESS, $parent_order_id);
                return;
            }


        } else if ($request_data['task_type'] == TASK_TYPE_PDD) {
            $this->load->model('paycore');
            $this->load->model('taskengine');
            // 佣金折扣
            $seller_id = $this->get_seller_id();
            $request_data['commission_discount'] = $this->hiltoncore->get_seller_commission_discount($seller_id);
            $request_data['seller_id'] = $seller_id;
            $accounting_result = $this->paycore->task_cost_accounting_pinduoduo($request_data);
            if (empty($accounting_result)) {
                die(build_response_str(CODE_BAD_REQUEST, "任务参数不合法，请联系客服"));
            }
            $parent_order_id = $this->taskengine->create_task_parent_order($accounting_result);
            if ($parent_order_id == false) {
                echo build_response_str(CODE_UNKNOWN_ERROR, "任务订单创建失败，请联系客服");
                return;
            } else {
                echo build_response_str(CODE_SUCCESS, $parent_order_id);
                return;
            }

        }

        echo build_response_str(CODE_BAD_REQUEST, '非法请求');
    }

    public function task_pay_handle()
    {
        $order_id = $this->input->post('order_id', TRUE);

        if (empty($order_id) || !is_numeric($order_id)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->model('paycore');
        $this->load->model('taskengine');
        $this->Data['order_info'] = $this->taskengine->get_parent_order_info($order_id);

        if (empty($this->Data['order_info']) || $this->Data['order_info']->status != Taskengine::TASK_STATUS_DZF || $this->Data['order_info']->seller_id != $this->get_seller_id() || !is_object(json_decode($this->Data['order_info']->attributes))) {
            die(build_response_str(CODE_BAD_REQUEST, "支付参数不合法"));
        }

        $this->Data['balance'] = $this->paycore->get_balance($this->get_seller_id());
        if ($this->Data['balance'] < ($this->Data['order_info']->fee_order_total_capital + $this->Data['order_info']->fee_order_total_commission + $this->Data['order_info']->fee_order_total_express)) {
            die(build_response_str(CODE_INSUFFICIENT_BALANCE, "余额不足"));
        }

        // Launched pay action
        if ($this->Data['order_info']->fee_order_total_capital > 0 && $this->paycore->pay_capital($this->get_seller_id(), $this->Data['order_info']->fee_order_total_capital, $this->Data['order_info']->id) != Paycore::PAY_CODE_SUCCESS) {
            die(build_response_str(CODE_PAY_FAILED, "支付任务本金失败，请联系客服处理"));
        }

        if ($this->Data['order_info']->fee_order_total_commission > 0 && $this->paycore->pay_commission($this->get_seller_id(), $this->Data['order_info']->fee_order_total_commission, $this->Data['order_info']->id) != Paycore::PAY_CODE_SUCCESS) {
            die(build_response_str(CODE_PAY_FAILED, "支付任务佣金失败，请联系客服处理"));
        }
        if ($this->Data['order_info']->fee_order_total_express > 0 && $this->paycore->pay_express($this->get_seller_id(), $this->Data['order_info']->fee_order_total_express, $this->Data['order_info']->id) != Paycore::PAY_CODE_SUCCESS) {
            die(build_response_str(CODE_PAY_FAILED, "支付任务快递费失败，请联系客服处理"));
        }

        // update parent order status
        if (!$this->taskengine->update_parent_order_status($this->Data['order_info']->id, Taskengine::TASK_STATUS_DJD)) {
            die(build_response_str(CODE_PAY_FAILED, "订单支付失败，请联系客服处理"));
        }

        // build tasks
        if (!$this->taskengine->build_tasks($this->Data['order_info']->id, json_decode($this->Data['order_info']->attributes, true))) {
            die(build_response_str(CODE_PAY_FAILED, "做单任务生成失败，请联系客服处理"));
        }
        echo build_response_str(CODE_SUCCESS, '支付成功');
    }

    public function cancel_task_handle()
    {

        $task_id = $this->input->post('task_id', TRUE);
        $task_type = $this->input->post('task_type', TRUE);
        
        if (empty($task_id) || empty($task_type)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->model('taskengine');
        $cnt = 0;

        if (is_array($task_id)) {
            foreach ($task_id as $v) {
                if ($this->taskengine->cancel_task($v, $task_type, $this->get_seller_id())) {
                    $cnt++;
                }
            }
        } else {
            if ($this->taskengine->cancel_task($task_id, $task_type, $this->get_seller_id())) {
                $cnt++;
            }
        }

        echo build_response_str(CODE_SUCCESS, '任务撤销成功' . $cnt . '个');
    }

    //批量审核 zqh
    public function auditing_task()
    {
        $task_id = $this->input->post('task_id', TRUE);
        $task_type = $this->input->post('task_type', TRUE);

        if (empty($task_id) || empty($task_type)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $task_type = TASK_TYPE_DT ? TASK_TYPE_DF : $task_type;

        $this->load->model('taskengine');
        $cnt = 0;//记录审核成功的子任务个数
        $bnt = 0;//记录审核失败的子任务个数

            foreach ($task_id as $v) {
                // 获取当前子任务信息
                $aTaskInfo = $this->taskengine->get_dianfu_task_info(encode_id($v));
                $conclusion_data = array();
                $conclusion_data['task_type'] = $task_type;
                $conclusion_data['task_id'] = $v;
                $conclusion_data['buyer_id'] = $aTaskInfo->buyer_id;
                $conclusion_data['conclusion'] = 1;
                $conclusion_data['seller_id'] = $aTaskInfo->seller_id;
                $status = $aTaskInfo->status;
                if(!empty($aTaskInfo) || $status != 4){

                    //判断买家实付与商家预付本金是否不一致
                    if($aTaskInfo->single_task_capital == $aTaskInfo->real_task_capital){

                            $taskData = $this->taskengine->getTaskData($conclusion_data);
                            if (!empty($taskData->is_express)) {
                                if($taskData->is_express != NOT_AVAILABLE){
                                    //生成快递单号
                                    $requestYTO     = $this->packYTOdata($conclusion_data);
                                    $responseYTO    = $this->ytoexpress->sendYTORequest($requestYTO);
                                    $this->taskengine->expressYto($requestYTO, $responseYTO, $taskData);
                                }

                                if ($this->taskengine->cancel_task_someshenhe($v,$task_type, $this->get_seller_id(),$status)) {
                                    $cnt++;
                                }else{
                                    $bnt ++;
                                    continue;
                                }

                            }else{
                                $bnt ++;
                                continue;
                            }


                    }else{
                        $bnt ++;
                        continue;
                    }

                }else{
                    $bnt++;
                    continue;
                }

            }


        if($bnt != 0){
            echo build_response_str(CODE_SUCCESS, '任务审核成功' . $cnt . '个，任务审核失败'.$bnt.'个，请手动审核！');
        }else{
            echo build_response_str(CODE_SUCCESS, '任务审核成功' . $cnt . '个');
        }

    }

    //批量审核
    public function cancel_task_handle_someshenhe()
    {
        $task_id = $this->input->post('task_id', TRUE);
        $task_type = $this->input->post('task_type', TRUE);
        $status = $this->input->post('$status', TRUE);
        if (empty($task_id) || empty($task_type) || empty($status)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->model('taskengine');
        $cnt = 0;

        if (is_array($task_id)) {
            foreach ($task_id as $v) {
                if ($this->taskengine->cancel_task_someshenhe($v,$task_type, $this->get_seller_id(),$status)) {
                    $cnt++;
                }
            }
        } else {
            if ($this->taskengine->cancel_task_someshenhe($task_id, $task_type,$this->get_seller_id(),$status)) {
                $cnt++;
            }
        }

        echo build_response_str(CODE_SUCCESS, '任务审核成功' . $cnt . '个');
    }

    //yto express again..
    public function yto()
    {
        $this->load->model('taskengine');
        $conclusion_data['task_type'] = $this->input->post('task_type', TRUE);
        $conclusion_data['task_id'] = $this->input->post('task_id', TRUE);
        $taskData = $this->taskengine->getTaskData($conclusion_data);
        $conclusion_data['conclusion'] = SELLER_CONCLUSION_TASK_OK;
        $conclusion_data['seller_id'] = $this->get_seller_id();

        ($conclusion_data['task_type'] == TASK_TYPE_DT) && $conclusion_data['task_type'] = TASK_TYPE_DF;

        try{
            if (!$this->ifExpress($conclusion_data)) {
                throw new \Exception("非法请求2");
            }

            if (empty($taskData->is_express) || $taskData->is_express == NOT_AVAILABLE || $taskData->express_success != 0) {
                throw new \Exception('非法请求1');
            }

            $requestYTO     = $this->packYTOdata($conclusion_data);
            $responseYTO    = $this->ytoexpress->sendYTORequest($requestYTO);
            $this->taskengine->expressYto($requestYTO, $responseYTO, $taskData);
            echo build_response_str(CODE_SUCCESS, '快递申请成功');
        }catch(\Exception $e){
            echo build_response_str($e->getCode(), $e->getMessage());
        }
    }

    public function task_operation_handle()
    {
        $conclusion_data = $this->input->post();
        if (invalid_parameter($conclusion_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        $conclusion_data['seller_id'] = $this->get_seller_id();
        $this->load->model('taskengine');
        try{
            // 审核通过前判断商家的账户余额 added by HKF.
            if ($conclusion_data['task_type'] == TASK_TYPE_DF && $conclusion_data['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
                $this->load->model('paycore');
                // 获取当前子任务信息
                $aTaskInfo = $this->taskengine->get_dianfu_task_info(encode_id($conclusion_data['task_id']));
                if (!empty($aTaskInfo)) {
                    // 实付金额必须大于0
                    if ($aTaskInfo->real_task_capital <= 0) {
                        echo build_response_str(CODE_BANED, '实付金额有误，请检查！');
                        return;
                    }
                    // 计算商家预付与实付差额
                    $diff = $aTaskInfo->single_task_capital - $aTaskInfo->real_task_capital;
                    // 如果买家实付与商家预付本金不一致
                    if ($diff != 0) {
                        // 获取商家账户余额
                        $sellerBalance = $this->paycore->get_balance($conclusion_data['seller_id']);
                        if (empty($sellerBalance)) $sellerBalance = 0;
                        // 如果当前账户余额不足以补足差额
                        if (($sellerBalance + $diff) < 0) {
                            echo build_response_str(CODE_BANED, '账户余额不足，任务无法审核通过！');
                            return;
                        }
                        // 多退少补
                        $this->paycore->pay_capital_diff($conclusion_data['seller_id'], $diff, $conclusion_data['task_id']);
                    }
                }
            }
            // added end by HKF.

            if ($this->ifExpress($conclusion_data)) {
                $taskData = $this->taskengine->getTaskData($conclusion_data);
                if (!empty($taskData->is_express) && $taskData->is_express != NOT_AVAILABLE) {
                    $requestYTO     = $this->packYTOdata($conclusion_data);
                    //var_dump($requestYTO);die;
                    $responseYTO    = $this->ytoexpress->sendYTORequest($requestYTO);
                    $this->taskengine->expressYto($requestYTO, $responseYTO, $taskData);

                }
            }

            if ($this->taskengine->seller_audit_task($conclusion_data) &&
                $this->reject($conclusion_data)) {
                echo build_response_str(CODE_SUCCESS, '审核成功');
                return;
            }

            throw new Exception('审核任务失败，请重试', CODE_UNKNOWN_ERROR);
        }catch(Exception $e){
            echo build_response_str($e->getCode(), $e->getMessage());
        }
    }

    // TODO... Added by Ryan.
    // 商家申诉（审核不通过）
    public function task_reject_handle()
    {
        $post_data = $this->input->post();
        if (invalid_parameter($post_data)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $post_data['task_id'] = decode_id($post_data['task_id']);
        $post_data['buyer_id'] = decode_id($post_data['buyer_id']);
        $post_data['seller_id'] = $this->get_seller_id();

        if ( empty($post_data['task_type']) || empty($post_data['task_id']) || empty($post_data['buyer_id']) ) {
            die(build_response_str(CODE_BAD_REQUEST, "参数错误"));
        }

        $this->load->model('taskengine');
        $aTaskInfo = $this->taskengine->get_dianfu_task_info_field(
            $post_data['task_id'],
            [
                'id','task_type','seller_id','buyer_id','buyer_tb_nick','parent_order_id','item_id','item_pic',
                'single_task_capital', 'order_number','single_task_commission_paid','status'
            ]
        );
        if (empty($aTaskInfo)) {
            die(build_response_str(CODE_BAD_REQUEST, "任务不存在"));
        }

        // 获取商家 & 买手 手机
        $this->load->model('users');
        $post_data['seller_phone'] = $this->users->get_val($aTaskInfo->seller_id, 'user_name');
        $post_data['buyer_phone'] = $this->users->get_val($aTaskInfo->buyer_id, 'user_name');

        if ($post_data['reason'] != 99) {
            $cfg_reasons = load_config('shang', 'reject_reasons');
            $post_data['reject_reason'] = isset($cfg_reasons[$post_data['reason']]) ? $cfg_reasons[$post_data['reason']] : '';
        }

        try {
            if ($this->taskengine->seller_task_appeal($post_data) && $this->_seller_reject($aTaskInfo, $post_data)) {
                echo build_response_str(CODE_SUCCESS, '申诉成功');
                return;
            }
            throw new Exception('申诉失败，请重试', CODE_UNKNOWN_ERROR);
        } catch (Exception $e){
            echo build_response_str($e->getCode(), $e->getMessage());
        }
    }

    // 添加卖家审核不通过申诉单
    private function _seller_reject($p, $post_data)
    {
        if (empty($p->id)) {
            return false;
        }

        $data = [
            'task_type' => $p->task_type,
            'task_id' => $p->id,
            'task_pid' => $p->parent_order_id,
            'order_no' => $p->order_number,
            'item_id' => $p->item_id,
            'item_pic' => $p->item_pic,
            'seller_id' => $p->seller_id,
            'buyer_id' => $p->buyer_id,
            'buyer_tb_nick' => $p->buyer_tb_nick,
            'seller_mobile' => $post_data['seller_phone'],
            'buyer_mobile' => $post_data['buyer_phone'],
            'task_capital' => $p->single_task_capital,
            'task_commission' => $p->single_task_commission_paid,
            'reject_reason' => $post_data['reason'],
            'reject_reason_txt' => $post_data['reject_reason'],
            'task_status' => $p->status
        ];
        if ($post_data['conclusion'] == SELLER_CONCLUSION_TASK_BAD) {
            $this->load->model('reject');
            $this->reject->add_seller_reject_records($data);
            return true;

        } elseif($post_data['conclusion'] == SELLER_CONCLUSION_REVIEW_BAD) {
            $this->load->model('reject');
            $this->reject->add_seller_reject_records_hp($data);
            return true;
        }
        return false;
    }

    private function reject($p)
    {
        if (in_array($p['conclusion'], array(
            SELLER_CONCLUSION_TASK_BAD,
            SELLER_CONCLUSION_REVIEW_BAD
        ))){
            $title = '';
            if ($p['task_type'] == TASK_TYPE_LL){
                $title = '浏览单商家审核--拒绝';
            }else if ($p['task_type'] == TASK_TYPE_DF){
                if ($p['conclusion'] == SELLER_CONCLUSION_REVIEW_BAD){
                    $title = '垫付单商家好评审核--拒绝';
                }else{
                    $title = '垫付单商家审核--拒绝';
                }
            }

            $this->load->model('message');
            $this->message->add($p['reject_reason'], $this->get_seller_id(), $p['buyer_id'], $title);
        }

        return true;
    }
    
    public function ifExpress($p){
        if (empty($p['task_type']) || empty($p['task_id']) || empty($p['conclusion']) || empty($p['seller_id'])) {
            return false;
        }

        if ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            return true;
        }elseif ($p['task_type'] == TASK_TYPE_PDD && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            return true;
        }
        return false;
    }

    public function packYTOdata($conclusion_data){
        if (invalid_parameter($conclusion_data)) {
            throw new Exception('非法请求', CODE_BAD_REQUEST);
        }
        $this->load->model('taskengine');
        // 1. 获取订单和店铺信息 - 
        $taskInfo = $this->taskengine->getExpressData($conclusion_data);
        $sellerPhone = $this->session->userdata(SESSION_SELLER_NAME);
        $senderInfo = $taskInfo->shop_ww . '@';
        $senderInfo .=  '000000' . '@';
        $senderInfo .=  '0' . '@';
        $senderInfo .=  $sellerPhone . '@';
        $senderInfo .=  $taskInfo->shop_province . '@';
        $senderInfo .=  $taskInfo->shop_city . ',';
        $senderInfo .=  $taskInfo->shop_county . '@';
        $senderInfo .=  preg_replace('/ /', '', $taskInfo->shop_address);
        // 2. 获取买家信息
        $buyerInfo = $this->taskengine->getBuyerBindInfo($conclusion_data, $taskInfo->buyer_tb_nick);
        $receiverInfo = preg_replace('/ /', '', $buyerInfo->tb_receiver_name) . '@';
        $receiverInfo .=  '000000' . '@';
        $receiverInfo .=  '0' . '@';
        $receiverInfo .=  $buyerInfo->tb_receiver_tel . '@';
        $receiverInfo .=  $buyerInfo->receiver_province . '@';
        $receiverInfo .=  $buyerInfo->receiver_city . ',';
        $receiverInfo .=  $buyerInfo->receiver_county . '@';
        $receiverInfo .=  preg_replace('/ /', '', $buyerInfo->tb_receiver_addr);
        // 3. 获取任务信息
        $parentOrderInfo = $this->taskengine->get_parent_order_info($taskInfo->parent_order_id);
        // 4. 任务类型
        switch ($parentOrderInfo->platform_type) {
            case PLATFORM_TYPE_TAOBAO:
                $platform = 'taobao';
                break;
            case PLATFORM_TYPE_PINDUODUO:
                $platform = 'pdd';
                break;
            default:
                $platform = 'taobao';
                break;
        }

        $sendYTO = [
                    'sender'            => trim($senderInfo),//拼接后的端商家地址
                    'receiver'          => trim($receiverInfo),//拼接后的买手地址
                    'user_id'           => $taskInfo->seller_id,
                    'channel'           => 'xiaohongbao',
                    'shop_type'         => $platform,
                    'order_number'      => $taskInfo->order_number,//订单号
                    'shop_id'           => $taskInfo->shop_id,//店铺id
                    'shop_name'         => $taskInfo->shop_name,//店铺名称
                    'express_company'   => $taskInfo->express_type,//快递类型
                    'express_price'     => $taskInfo->single_task_express,//快递金额
                    'express_total'     => 1,
                    'goods_weight'      => $taskInfo->goods_weight,//收件人货物重量
            ];
        return $sendYTO;
    }

    /**
     * @name 商家提现逻辑处理
     * @author chen.jian
     */
    public function withdrawal_handle()
    {
        try {
            if (!WITHDRAW_MASTER_SWITCH) {
                throw new Exception("非法请求", CODE_BAD_REQUEST);
            }
            $withdraw_amount = $this->input->post('withdrawal_amount', TRUE);
            $bank_id = $this->input->post('bank_card_id', TRUE);
            $prov_code = $this->input->post('prov_code', TRUE);
            if (!$this->smsfactory->checkProveCode($prov_code)) {
                throw new Exception('短信验证码不正确', CODE_BAD_PROVCODE);
            }
            if (empty($withdraw_amount) || !is_numeric($withdraw_amount) || $withdraw_amount < MIN_WITHDRAW_AMOUNT) {
                throw new Exception("提现金额填写错误", CODE_BAD_REQUEST);
            }
            if ($this->paycore->withdrawal($this->get_seller_id(), $withdraw_amount) != Paycore::PAY_CODE_SUCCESS) {
                throw new Exception("提现扣款失败", CODE_DB_ERROR);
            }
            if ($this->hiltoncore->seller_withdraw($this->get_seller_id(), $this->get_seller_name(), $bank_id, $withdraw_amount)) {
                echo build_response_str(CODE_SUCCESS, "提现请求已经受理");
                return;
            }
            throw new Exception("提现申请失败，请联系客服", CODE_UNKNOWN_ERROR);
        } catch (Exception $e) {
            echo build_response_str($e->getCode(), $e->getMessage());
            return;
        }
    }
}
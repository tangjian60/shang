<?php
/**
 * @name
 * @author: chen.jian
 * @date: 2018/7/26 下午1:53
 */

class Withdrawal extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->seller_env_init();
        $this->load->library('Smsfactory');
    }

    /**
     * @name 加载取现界面
     * @author chen.jian
     */
    public function index()
    {
        $user_id = $this->get_seller_id() ? $this->get_seller_id() : 0;
        // 1. 用户信息
        $this->Data['user_info'] = $this->hiltoncore->get_user_info($user_id);
        if (empty($this->Data['user_info'])) {
            show_404();
            return;
        }
        // 2. 银行卡信息
        $this->Data['bank_info'] = $this->hiltoncore->get_banks_info($user_id);
        // 3. 取现权限
        if (!WITHDRAW_MASTER_SWITCH || $this->Data['user_info']->withdraw_enabled != STATUS_ENABLE) {
            $this->Data['message'] = '很抱歉，您的账号目前暂时无法提现';
            $this->Data['btn_type'] = BTN_TYPE_BACK;
            $this->load->view('page_message', $this->Data);
            return;
        }

        $this->Data['PageTitle'] = '取现';
        $this->Data['TargetPage'] = 'pages_withdrawal';
        $this->load->view('frame_main', $this->Data);
    }

    /**
     * @name 发送提现验证码
     * @author chen.jian
     */
    public function send_sms_code()
    {
        if (!$this->input->is_ajax_request()) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }
        $phone = $this->input->post('user_name', TRUE);
        if (empty($phone)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }
        if ($this->smsfactory->sendProve($phone)) {
            echo build_response_str(CODE_SUCCESS, "验证码发送成功");
            return;
        }
        echo build_response_str(CODE_UNKNOWN_ERROR, "验证码发送失败");
    }


    /**
     * @name 取现记录列表
     * @author xiao liu
     */
    public function records()
    {
        $i_page = $this->input->get('i_page', true);
        if (empty($i_page)) {
            $i_page = 1;
        }
        $this->Data['i_page'] = $i_page;
        $this->Data['data'] = $this->hiltoncore->get_cash_list($this->get_seller_id(), $this->Data['i_page']);
        $this->Data['PageTitle'] = '取现记录';
        $this->Data['TargetPage'] = 'page_top_up_cash';
        $this->load->view('frame_main', $this->Data);
    }

    public function withdrawal_list(){
        $i_page = $this->input->get('i_page', true);
        if (empty($i_page)) {
            $i_page = 1;
        }
        $this->Data['i_page'] = $i_page;
        $this->Data['data'] = $this->hiltoncore->get_cash_list($this->get_seller_id(), $this->Data['i_page']);
        $this->Data['PageTitle'] = '取现记录';
        $this->Data['TargetPage'] = 'page_top_up_cash';
        $this->load->view('frame_main', $this->Data);
    }
}
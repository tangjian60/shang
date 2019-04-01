<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Smsfactory');
        $this->load->library('Cookiefactory');
    }

    public function index()
    {

        if (!MASTER_SWITCH) {
            $this->Data['message'] = '系统维护中，请稍后';
            $this->Data['TargetPage'] = 'page_message';
            $this->load->view('frame_user', $this->Data);
            return;
        }

        if ($this->is_seller_login()) {
            redirect(base_url(), 'refresh');
            return;
        }

        $this->cookiefactory->setRecommendId($this->input->get('r', TRUE));

        $this->Data['TargetPage'] = 'page_login';
        $this->load->view('frame_user', $this->Data);
    }

    public function login_handler()
    {

        $log_user_name = $this->input->post('seller_account', TRUE);
        $log_user_passwd = $this->input->post('seller_passwd', TRUE);

        if (empty($log_user_name) || empty($log_user_passwd)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        $this->load->helper('security');
        $result = $this->hiltoncore->user_login_verify($log_user_name, $log_user_passwd);

        if (empty($result) || !$result['result']) {
            echo build_response_str(CODE_BAD_PASSWORD, '用户名或密码错误');
            return;
        }

        if ($result['user_data']->status != STATUS_ENABLE) {
            echo build_response_str(CODE_BANED, '该用户已经被禁止登录');
            return;
        }

        $this->session->set_userdata(SESSION_SELLER_ID, $result['user_data']->id);
        $this->session->set_userdata(SESSION_SELLER_NAME, $result['user_data']->user_name);
        echo build_response_str(CODE_SUCCESS, '登录成功');
    }

    public function forget_passwd()
    {
        $this->Data['TargetPage'] = 'page_forget_pwd';
        $this->load->view('frame_user', $this->Data);
    }

    public function register()
    {

        if (!SELLER_REGISTRY_SWITCH) {
            $this->Data['message'] = '目前平台暂时停止注册，给您带来的不便敬请谅解';
            $this->Data['btn_type'] = BTN_TYPE_BACK;
            $this->Data['TargetPage'] = 'page_message';
            $this->load->view('frame_user', $this->Data);
            return;
        }

        $this->Data['recommend'] = $this->input->get('r', TRUE);
        if (empty($this->Data['recommend'])) {
            $this->Data['recommend'] = $this->cookiefactory->getRecommendId();
        } else {
            $this->cookiefactory->setRecommendId($this->Data['recommend']);
        }

        $this->Data['TargetPage'] = 'page_register';
        $this->load->view('frame_user', $this->Data);
    }

    public function send_sms_code()
    {
        if (!$this->input->is_ajax_request()) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        $phone = $this->input->post('reg_phone_no', TRUE);
        if (empty($phone)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        if ($this->hiltoncore->does_user_exists($phone)) {
            echo build_response_str(CODE_USER_CONFLICT, "该用户已经存在");
            return;
        }

        if ($this->smsfactory->sendProve($phone)) {
            echo build_response_str(CODE_SUCCESS, "验证码发送成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "验证码发送失败");
    }

    public function registry_handle()
    {
        if (!$this->input->is_ajax_request()) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        $reg_data = array();
        $reg_data['userName'] = $this->input->post('user_account', TRUE);
        $reg_data['password'] = $this->input->post('confirm_passwd', TRUE);
        $reg_data['proveCode'] = $this->input->post('prove_code', TRUE);

        if (invalid_parameter($reg_data)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        if (!$this->smsfactory->checkProveCode($reg_data['proveCode'])) {
            echo build_response_str(CODE_BAD_PROVCODE, "短信验证码不正确");
            return;
        }

        $recommend = $this->input->post('recommend', TRUE);
        if ( !empty($recommend) && is_numeric($recommend)  && $recommend!=0) {
            $recommend = decode_id($recommend);
            $owner_user_info = $this->hiltoncore->get_user_info($recommend);
            if (empty($owner_user_info)) { echo build_response_str(CODE_BAD_REQUEST, "无效邀请码"); return; }
            if ($owner_user_info->user_type != USER_TYPE_SELLER) { echo build_response_str(CODE_BAD_REQUEST, "无效邀请码"); return;}
        }

        $this->load->helper('security');
        if ($this->hiltoncore->add_user_account($reg_data, $recommend)) {
            echo build_response_str(CODE_SUCCESS, "商家注册成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "商家注册失败");
    }

    public function send_reset_sms_code()
    {
        if (!$this->input->is_ajax_request()) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        $phone = $this->input->post('reg_phone_no', TRUE);
        if (empty($phone)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        if (!$this->hiltoncore->does_user_exists($phone)) {
            echo build_response_str(CODE_USER_CONFLICT, "用户账号错误，请核实");
            return;
        }

        if ($this->smsfactory->sendProve($phone)) {
            echo build_response_str(CODE_SUCCESS, "验证码发送成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "验证码发送失败");
    }

    public function reset_pwd_handle()
    {
        if (!$this->input->is_ajax_request()) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        $req_data = array();
        $req_data['userName'] = $this->input->post('user_account', TRUE);
        $req_data['password'] = $this->input->post('confirm_passwd', TRUE);
        $req_data['proveCode'] = $this->input->post('prove_code', TRUE);

        if (invalid_parameter($req_data)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        if (!$this->smsfactory->checkProveCode($req_data['proveCode'])) {
            echo build_response_str(CODE_BAD_PROVCODE, "短信验证码不正确");
            return;
        }

        $this->load->helper('security');
        if ($this->hiltoncore->reset_user_passwd($req_data)) {
            echo build_response_str(CODE_SUCCESS, "重置密码成功");
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "重置密码失败");
    }

    public function log_out()
    {
        session_destroy();
        redirect(base_url('user'), 'refresh');
    }
}
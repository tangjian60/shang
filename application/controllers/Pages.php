<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->seller_env_init();
    }

    public function index()
    {
        $this->Data['TargetPage'] = 'page_message';
        $this->Data['message'] = '页面不存在';
        $this->load->view('frame_main', $this->Data);
    }

    public function bills()
    {
        $i_page = $this->input->get('i_page', TRUE);
        $b_type = $this->input->get('bill_type', TRUE);

        if (empty($i_page)) {
            $i_page = 1;
        }
        $this->load->model('paycore');
        $this->Data['data'] = $this->paycore->get_bills($b_type, $this->get_seller_id(), ITEMS_PER_LOAD * ($i_page - 1));
        $this->Data['i_page'] = $i_page;
        $this->Data['b_type'] = $b_type;

        $this->Data['PageTitle'] = '账单';
        $this->Data['TargetPage'] = 'page_bills';
        $this->load->view('frame_main', $this->Data);
    }

    public function add_shop()
    {
        $this->Data['PageTitle'] = '绑定新的店铺';
        $this->Data['TargetPage'] = 'page_new_shop';
        $this->load->view('frame_main', $this->Data);
    }

    public function shop_list()
    {
        $this->Data['PageTitle'] = '已绑定的店铺';
        $this->Data['TargetPage'] = 'page_shop_list';
        $this->Data['data'] = $this->hiltoncore->get_binded_shops($this->get_seller_id());
        $this->load->view('frame_main', $this->Data);
    }
    
    public function edit_shop(){
        $shop_id = $this->input->get('id', TRUE);
        if (!empty($shop_id) && is_numeric($shop_id)) {
            $this->Data['shop_info'] = $this->hiltoncore->get_shop_info($shop_id);
        }
        $this->Data['TargetPage'] = 'page_edit_shop';
        $this->Data['PageTitle'] = '任务店铺编辑';
        $this->load->view('frame_main', $this->Data);
    }


    public function top_up()
    {
        $this->Data['PageTitle'] = '充值';
        $this->Data['TargetPage'] = 'page_top_up';
        $this->Data['SellerName'] = $this->get_seller_name();
        $this->Data['Banklist']=$this->hiltoncore->get_seller_binded_bankcars($this->get_seller_id());
//        var_dump($this->Data);exit;
        $this->load->view('frame_main', $this->Data);
    }

    public function top_up_records()
    {
        $i_page = $this->input->get('i_page', TRUE);

        if (empty($i_page)) {
            $i_page = 1;
        }

        $this->Data['data'] = $this->hiltoncore->get_top_up_records($this->get_seller_id(), ITEMS_PER_LOAD * ($i_page - 1));
        $this->Data['i_page'] = $i_page;

        $this->Data['PageTitle'] = '充值记录';
        $this->Data['TargetPage'] = 'page_top_up_records';
        $this->load->view('frame_main', $this->Data);
    }

    public function add_bankcard()
    {
        $this->Data['PageTitle'] = '绑定银行卡';
        $this->Data['TargetPage'] = 'page_add_bankcard';
        $this->load->view('frame_main', $this->Data);
    }

    public function binded_bankcard()
    {
        $this->Data['PageTitle'] = '已绑定的银行卡';
        $this->Data['TargetPage'] = 'page_binded_bankcard';
        $this->Data['data'] = $this->hiltoncore->get_seller_binded_bankcars($this->get_seller_id());
        $this->load->view('frame_main', $this->Data);
    }

    public function withdraw_application()
    {
        $this->Data['user_info'] = $this->hiltoncore->get_user_info($this->get_seller_id());
        if (empty($this->Data['user_info'])) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
            $this->load->view('frame_main', $this->Data);
            return;
        }

        if (!WITHDRAW_MASTER_SWITCH || $this->Data['user_info']->withdraw_enabled != STATUS_ENABLE) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '很抱歉，目前暂时无法提现';
            $this->load->view('frame_main', $this->Data);
            return;
        }

        $this->Data['PageTitle'] = '提现';
        $this->Data['TargetPage'] = 'page_withdraw';
        $this->Data['bank_info'] = $this->hiltoncore->get_seller_binded_bankcars($this->get_seller_id());
        $this->load->view('frame_main', $this->Data);
    }

    public function withdraw_records()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);

        if (empty($this->Data['i_page'])) {
            $this->Data['i_page'] = 1;
        }

        $this->Data['PageTitle'] = '提现记录';
        $this->Data['TargetPage'] = 'page_withdraw_records';
        $this->Data['data'] = $this->hiltoncore->get_withdraw_records($this->get_seller_id(), $this->Data['i_page']);
        $this->load->view('frame_main', $this->Data);
    }

    public function notice_info()
    {
        $id = $this->input->get('id', TRUE);
        if (empty($id) || !is_numeric($id)) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '公告信息不存在';
        } else {
            $this->Data['notice_info'] = $this->hiltoncore->get_notice_info($id);
            if (empty($this->Data['notice_info'])) {
                $this->Data['TargetPage'] = 'page_message';
                $this->Data['message'] = '公告信息不存在';
            } else {
                $this->Data['PageTitle'] = $this->Data['notice_info']->title;
                $this->Data['TargetPage'] = 'page_notice_info';
            }
        }
        $this->load->view('frame_main', $this->Data);
    }

    public function promote()
    {
        //获取当前登录的商家id
        $seller_id = $this->get_seller_id();
        //获取该商家所有下线的商家id
        $seller_ids = $this->hiltoncore->get_seller_ids($seller_id);
        foreach($seller_ids as $k => $v){
            $seller_ids[$k] = $v->promote_id;
        }

        //获取商家代理下线充值总金额
        $this->Data['data'] = $this->hiltoncore->get_promote_list($seller_ids, $seller_id);
        $this->Data['seller_id'] = $seller_id;
        $this->Data['r'] = $this->get_seller_id();
        $this->Data['TargetPage'] = 'page_promote';
        $this->load->view('frame_main', $this->Data);
    }

    public function cs()
    {
        $this->Data['TargetPage'] = 'page_cs';
        $this->Data['PageTitle'] = '申诉中心';
        $this->load->view('frame_main', $this->Data);
    }
}
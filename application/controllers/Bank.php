<?php
/**
 * @name
 * @author: chen.jian
 * @date: 2018/7/26 上午10:34
 */

class Bank extends Hilton_Controller {
    private $banksList = [
        '中国农业银行',
        '中国建设银行',
        '中国工商银行',
        '中国招商银行',
        '中国交通银行',
        '中国民生银行',
        '中国银行',
        '邮政储蓄银行',
        '浦发银行',
        '渣打银行',
        '光大银行',
        '中信银行',
        '广发银行',
        '华夏银行',
        '浙商银行',
        '兴业银行',
        '平安银行',
        '北京银行',
        '南京银行',
        '宁波银行',
        '上海银行',
        '杭州银行',
        '江苏银行',
        '汇丰银行',
    ];

    public function __construct(){
        parent::__construct();
        $this->seller_env_init();
    }

    /**
     * @name 加载绑定银行卡页面
     * @author chen.jian
     */
    public function add_bank(){
        $this->Data['PageTitle'] = '绑定银行卡';
        $this->Data['TargetPage'] = 'pages_band_add';
        $this->Data['BanksList'] = $this->banksList;
        $this->load->view('frame_main', $this->Data);
    }

    /**
     * @name 处理绑定银行卡逻辑
     * @author chen.jian
     */
    public function bank_handle(){
        $req_fields = ['true_name', 'bank_card_num', 'bank_name', 'province', 'city', 'county', 'bank_branch'];
        $req_data   = $this->input->post($req_fields, TRUE); // returns all POST items with XSS filter
        $req_data['user_id'] = $this->get_seller_id();

        if (invalid_parameter($req_data)) {
            echo build_response_str(CODE_BAD_REQUEST, "非法请求");
            return;
        }

        if ($this->hiltoncore->shang_band_add($req_data)) {
            $this->session->set_userdata(SESSION_AUTH_STATUS, STATUS_CHECKING);
            echo build_response_str(CODE_SUCCESS, "提交认证信息成功");
            return;
        }
    }
    /**
     * @name 获取银行卡列表
     * @author xiao liu
     */
    public function bank_list()
    {
        $this->Data['i_page'] = $this->input->get('i_page', true);
        $this->Data['PageTitle'] = '已绑定的银行卡';
        $this->Data['TargetPage'] = 'page_bank_list';
        $this->Data['data'] = $this->hiltoncore->get_binded_banks($this->get_seller_id(), $this->Data['i_page']);
        if (empty($this->Data['i_page'])) {
            $this->Data['i_page'] = 1;
        }
        $this->load->view('frame_main', $this->Data);
    }

    /**
     * @name 删除银行卡，修改bank_status信息
     * @author xiao liu
     */

    public function bank_delete()
    {
        if ($this->hiltoncore->update_bined_banks($this->input->post('id', true))) {
            return;
        }
    }
}
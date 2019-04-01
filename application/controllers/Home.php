<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->seller_env_init();
    }

    public function index()
    {
        $this->Data['TargetPage'] = 'page_analysis';
        $this->Data['PageTitle'] = '数据大盘';
        $this->load->view('frame_main', $this->Data);
    }
}
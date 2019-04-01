<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hilton_Controller extends CI_Controller
{
    public $Data = array();

    public function __construct()
    {
        parent::__construct();
    }

    function seller_env_init()
    {
        if (!MASTER_SWITCH || !$this->is_seller_login()) {
            redirect(base_url('user'), 'refresh');
            return;
        }

        $this->Data['UserName'] = $this->get_seller_name();
        $this->Data['Notices'] = $this->hiltoncore->get_notice_list(10);
    }

    function is_seller_login()
    {
        if (!$this->session->userdata(SESSION_SELLER_ID)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function get_seller_id()
    {
        return $this->session->userdata(SESSION_SELLER_ID);
    }

    function get_seller_name()
    {
        return $this->session->userdata(SESSION_SELLER_NAME);
    }
}
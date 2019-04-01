<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Taskengine extends Hilton_Model
{

    const DB_TASK = 'user_tasks';
    const DB_TASK_PARENT_ORDERS = 'hilton_task_parent_orders';
    const DB_TASK_DIANFU = 'hilton_task_dianfu';
    const DB_TASK_DIANFU_EXT = 'hilton_task_dianfu_ext';
    const DB_TASK_PINDUODUO = 'hilton_task_pinduoduo';
    const DB_TASK_LIULIANG = 'hilton_task_liuliang';
    const DB_BIND_SHOPS = 'seller_bind_shops';
    const DB_USER_BIND_INFO = 'user_bind_info';
    const DB_OPERATION_LOG = 'operation_log';
    const DB_TASK_CANCELLED = 'hilton_task_cancelled';
    const DB_BUYER_TASK_DUOTIAN= 'buyer_task_duotian';

    const TASK_STATUS_DZF = 1;
    const TASK_STATUS_DJD = 2;
    const TASK_STATUS_DCZ = 3;
    const TASK_STATUS_MJSH = 4;
    const TASK_STATUS_MJSH_BTG = 5;
    const TASK_STATUS_PTSH = 6;
    const TASK_STATUS_PTSH_BTG = 7;
    const TASK_STATUS_DPJ = 8;
    const TASK_STATUS_HPSH = 9;
    const TASK_STATUS_HPSH_BTG = 10;
    const TASK_STATUS_YWC = 11;
    const TASK_STATUS_YCX = 99;

    const TASK_CLEARING_STATUS_NO = 0;
    const TASK_CLEARING_STATUS_YES = 1;
    const TASK_STATUS_XTCZ = 12;
    const TASK_STATUS_XTGB = 13;
    const TASK_STATUS_XTGB_DT = 14;
    const TASK_STATUS_SSZ = 20;

    private static $TASK_STATUS = array(
        self::TASK_STATUS_DZF => "待支付",
        self::TASK_STATUS_DJD => "派单中",
        self::TASK_STATUS_DCZ => "已接单待操作",
        self::TASK_STATUS_MJSH => "卖家审核",
        self::TASK_STATUS_MJSH_BTG => "卖家审核不通过",
        self::TASK_STATUS_PTSH => "平台审核",
        self::TASK_STATUS_PTSH_BTG => "平台审核不通过",
        self::TASK_STATUS_DPJ => "待评价",
        self::TASK_STATUS_HPSH => "好评审核",
        self::TASK_STATUS_HPSH_BTG => "好评审核不通过",
        self::TASK_STATUS_YWC => "已完成",
        self::TASK_STATUS_YCX => "已撤销",
        self::TASK_STATUS_XTCZ => "商家审核拒绝，买家不操作，系统重置订单",
        self::TASK_STATUS_XTGB => "商家审核拒绝，买家不操作，系统关闭订单",
        self::TASK_STATUS_XTGB_DT => "未及时操作关闭任务",
        self::TASK_STATUS_SSZ  => "申诉中",
    );

    function __construct()
    {
        parent::__construct();
    }

    public static function get_status_name($status_code)
    {
        if (empty($status_code)) {
            return;
        }

        foreach (self::$TASK_STATUS as $k => $v) {
            if ($k == $status_code) {
                return $v;
                break;
            }
        }

        return;
    }

    public static function get_all_status()
    {
        return self::$TASK_STATUS;
    }



    function get_template_info($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->where('id', $i);
        return $this->db->get(self::DB_TASK_TEMPLATES)->row();
    }

    function get_templates($i, $platform_type = '')
    {
        $this->db->where('seller_id', $i);
        $this->db->where('status', STATUS_PASSED);
        if (!empty($platform_type)) {
            $this->db->where('platform_type', $platform_type);
        }
        $this->db->order_by('id', 'desc');
        return $this->db->get(self::DB_TASK_TEMPLATES)->result();
    }
    function get_shops_with_address($i)
    {
        $this->db->where('seller_id', $i);
        $this->db->where('shop_address', '');
        $this->db->where('status', STATUS_PASSED);
        return $this->db->get(self::DB_SHOP_BIND)->num_rows();
    }

    function add_new_template($r)
    {
        if (invalid_parameter($r)) {
            return false;
        }

        $data = array(
            'seller_id'             => $r['seller_id'],
            'platform_type'         => $r['platform'],
            'shop_id'               => $r['shop_id'],
            'template_name'         => trim($r['template_name']),
            'device_type'           => $r['device_type'],
            'item_id'               => $r['item_id'],
            'item_title'            => trim($r['item_title']),
            'item_display_price'    => $r['item_display_price'],
            'item_url'              => trim($r['item_url']),
            'item_pic'              => $r['item_pic'],
            'template_note'         => $r['template_note'],
            'status'                => STATUS_PASSED
        );

        if (!$this->db->insert(self::DB_TASK_TEMPLATES, $data)) {
            error_log('insert new task template failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    function get_audit_needed_cnt($seller_id, $task_type)
    {
        $this->db->where('seller_id', $seller_id);
        $this->db->where_in('status', array(self::TASK_STATUS_MJSH, self::TASK_STATUS_HPSH));
        if ($task_type == TASK_TYPE_DF) {
            $this->db->where('task_type', TASK_TYPE_DF);
            return $this->db->count_all_results(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_DT) {
            $this->db->where('task_type', TASK_TYPE_DT);
            return $this->db->count_all_results(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            return $this->db->count_all_results(self::DB_TASK_LIULIANG);
        } elseif ($task_type == TASK_TYPE_PDD) {
            return $this->db->count_all_results(self::DB_TASK_PINDUODUO);
        }
        return 0;
    }

    function update_template($r)
    {
        $update_data = array(
            'shop_id' => $r['shop_id'],
            'template_name' => $r['template_name'],
            'device_type' => $r['device_type'],
            'item_id' => $r['item_id'],
            'item_title' => $r['item_title'],
            'item_display_price' => $r['item_display_price'],
            'item_url' => $r['item_url'],
            'item_pic' => $r['item_pic'],
            'template_note' => $r['template_note'],
        );

        $this->db->where('id', $r['template_id']);
        if (!$this->db->update(self::DB_TASK_TEMPLATES, $update_data)) {
            error_log('update task template failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }
    function update_shop($r)
    {
        $update_data = array(
            'shop_address' => $r['address'],
        );

        $this->db->where('id', $r['shop_id']);
        if (!$this->db->update(self::DB_SHOP_BIND, $update_data)) {
            error_log('update task shop failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    function delete_task_template($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->set('status', STATUS_CANCEL);
        $this->db->where('id', $i);
        return $this->db->update(self::DB_TASK_TEMPLATES);
    }

    function get_shop_info($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->where('id', $i);
        return $this->db->get(self::DB_SHOP_BIND)->row();
    }

    function create_task_parent_order($p)
    {
        if (invalid_parameter($p)) {
            return false;
        }

        if (empty($p['template_id']) || !is_numeric($p['template_id'])) {
            return false;
        }

        $template_info = $this->get_template_info($p['template_id']);

        if (empty($template_info)) {
            return false;
        }

        $data = array(
            'seller_id' => $p['seller_id'],
            'task_type' => $p['task_type'],
            'platform_type' => PLATFORM_TYPE_TAOBAO,
            'shop_id' => $template_info->shop_id,
            'device_type' => $template_info->device_type,
            'item_id' => $template_info->item_id,
            'item_title' => $template_info->item_title,
            'item_url' => $template_info->item_url,
            'item_pic' => $template_info->item_pic,
            'start_time' => $p['start_time'],
            'end_time' => $p['end_time'],
            'hand_out_interval' => $p['hand_out_interval'],
            'fee_order_total_capital' => $p['fee_order_total_capital'],
            'fee_order_total_commission' => $p['fee_order_total_commission'],
            'fee_order_total_express' => $p['fee_order_total_express'],
            'task_cnt' => $p['task_cnt'],
            'commission_discount' => $p['commission_discount'],
            'attributes' => json_encode($p),
            'status' => self::TASK_STATUS_DZF
        );

        if (!$this->db->insert(self::DB_TASK_PARENT_ORDERS, $data)) {
            error_log('create task parent order db error, last query : ' . $this->db->last_query());
            return false;
        }

        return $this->db->insert_id();
    }

    function get_task_parent_orders($p)
    {
        if (empty($p['seller_id']) || !is_numeric($p['seller_id'])) {
            return;
        }

        $this->db->select(array('id', 'task_type', 'device_type', 'item_title', 'item_url', 'item_pic', 'start_time', 'end_time', 'hand_out_interval', 'fee_order_total_capital', 'fee_order_total_commission', 'fee_order_total_express', 'task_cnt', 'gmt_create', 'status','attributes'));
        $this->db->where('seller_id', intval($p['seller_id']));

        if (!empty($p['parent_order_id'])) {
            $id = intval(decode_id($p['parent_order_id']));
            $this->db->where('id', $id);
        }
        if (!empty($p['start_time'])) {
            $this->db->where('gmt_create >=', $p['start_time']);
        }

        if (!empty($p['end_time'])) {
            $this->db->where('gmt_create <=', $p['end_time']);
        }

        if (!empty($p['bind_shop'])) {
            $this->db->where('shop_id', intval($p['bind_shop']));
        }

        if (!empty($p['order_status'])) {
            $this->db->where('status', intval($p['order_status']));
        }
        if (!empty($p['buyer_tb_nick'])) {
            $this->db->where('buyer_tb_nick', $p['buyer_tb_nick']);
        }
        if (!empty($p['i_page']) && is_numeric($p['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($p['i_page'] - 1));
        }

        $this->db->order_by('id', 'DESC');
        $this->db->order_by('status', 'ASC');
        $task_list = $this->db->get(self::DB_TASK_PARENT_ORDERS)->result();

        foreach ($task_list as $v) {
            $this->db->where('parent_order_id', $v->id);
            $this->db->where_in('status', array(self::TASK_STATUS_DCZ, self::TASK_STATUS_MJSH, self::TASK_STATUS_MJSH_BTG, self::TASK_STATUS_DPJ, self::TASK_STATUS_HPSH, self::TASK_STATUS_HPSH_BTG, self::TASK_STATUS_YWC));
            if ($v->task_type == TASK_TYPE_DF) {
                $v->task_yijie = $this->db->count_all_results(self::DB_TASK_DIANFU);
            } elseif ($v->task_type == TASK_TYPE_LL) {
                $v->task_yijie = $this->db->count_all_results(self::DB_TASK_LIULIANG);
            } elseif ($v->task_type == TASK_TYPE_PDD) {
                $v->task_yijie = $this->db->count_all_results(self::DB_TASK_PINDUODUO);
            } elseif ($v->task_type == TASK_TYPE_DT) {
                $v->task_yijie = $this->db->count_all_results(self::DB_TASK_DIANFU);
            }
        }
        return $task_list;
    }

    public function get_task_cancelled($p)
    {
        if (empty($p['seller_id']) || !is_numeric($p['seller_id'])) {
            return;
        }

        $this->db->select(array('gmt_cancelled', 'task_id', 'item_id', 'item_title', 'buyer_id', 'cancel_reason'));
        if (!empty($p['gmt_cancelled'])) {
            $this->db->where('gmt_cancelled >=', $p['gmt_cancelled'].' 00:00:00');
            $this->db->where('gmt_cancelled <=', $p['gmt_cancelled'].' 23:59:59');
        }

        if (!empty($p['task_id'])) {
            $this->db->where('task_id', $p['task_id']);
        }

        if (!empty($p['seller_id'])) {
            $this->db->where('seller_id', $p['seller_id']);
        }

        if (!empty($p['buyer_id'])) {
            $this->db->where('buyer_id', $p['buyer_id']);
        }

        if (!empty($p['item_id'])) {
            $this->db->where('item_id', $p['item_id']);
        }

        if (!empty($p['i_page']) && is_numeric($p['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($p['i_page'] - 1));
        }

        $this->db->order_by('id', 'DESC');
        $task_list = $this->db->get(self::DB_TASK_CANCELLED)->result();

        return $task_list;
    }

    function get_parent_order_info($i)
    {
        $this->db->where('id', $i);
        return $this->db->get(self::DB_TASK_PARENT_ORDERS)->row();
    }

    function update_parent_order_status($i, $n_status)
    {
        if (empty($i) || empty($n_status)) {
            return false;
        }
        $this->db->set('status', $n_status);
        $this->db->where('id', $i);
        return $this->db->update(self::DB_TASK_PARENT_ORDERS);
    }

    function get_liuliang_task_info($id)
    {
        $this->db->where('id', decode_id($id));
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_LIULIANG)->row();
    }

    function get_dianfu_task_info($id)
    {
        $this->db->where('id', decode_id($id));
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_DIANFU)->row();
    }

    function get_dianfu_task_info_field($id, $aFields = [])
    {
        if (!empty($aFields)) $this->db->select($aFields);
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_DIANFU)->row();
    }

    function get_duotian_task_info($id)
    {
        $id = decode_id($id);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $row = $this->db->get(self::DB_TASK_DIANFU)->row();

        $data['detail'] = $row;

        $row2 = $this->db->select('task_attr')->where('task_id', $id)->get(self::DB_TASK_DIANFU_EXT)->row();
        if ($row) {
            $task_attr = json_decode($row2->task_attr, true);
            $res = $this->db->where('task_id', $id)->get(self::DB_BUYER_TASK_DUOTIAN)->result();
            $show_data = [];
            for ($i=1; $i<=$row->cur_task_day; $i++) {
                $show_data[$i]['of'] = $task_attr['op_flow_' . $i];
                $show_data[$i]['mo'] = $this->_getMoTxtArr($task_attr['method_outer_' . $i]);
                foreach ($res as $val) {
                    if ($i == $val->task_step) {
                        $show_data[$i]['imgs'] = json_decode($val->task_imgs, true);
                        break;
                    }
                }
            }
            $data['show_data'] = $show_data;
            unset($show_data);
        }
        //print_r($data);exit;
        return $data;
    }

    private function _getMoTxtArr($mo_arr)
    {
        $aData = [];
        $config = load_config('shang');
        if (is_array($mo_arr)) {
            foreach ($mo_arr as $val) {
                if ($val > 4) $data['ext']['is_browse_inner'] = 1;
                $aData[] = $config['task_behaviors'][$val-1];
            }
        } else {
            if (intval($mo_arr) > 4) $data['ext']['is_browse_inner'] = 1;
            $aData[] = $config['task_behaviors'][intval($mo_arr)-1];
        }
        return $aData;
    }

    function get_pinduoduo_task_info($id)
    {
        $this->db->where('id', decode_id($id));
        $this->db->limit(1);
        return $this->db->get(self::DB_TASK_PINDUODUO)->row();
    }

    function get_task_list($r)
    {
        if (empty($r['seller_id'])) {
            return null;
        }

        $tianfu_type = '';
        if (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_DF) {
            $db_name = self::DB_TASK_DIANFU;
            $tianfu_type = 'DIANFU';
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_DT) {
            $db_name = self::DB_TASK_DIANFU;
            $tianfu_type = 'DUOTIAN';
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_LL) {
            $db_name = self::DB_TASK_LIULIANG;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_PDD) {
            $db_name = self::DB_TASK_PINDUODUO;
        } else {
            return null;
        }
        $this->db->where('seller_id', intval($r['seller_id']));

        if (!empty($r['task_id'])) {
            $this->db->where('id', intval(decode_id($r['task_id'])));
            $this->db->where('task_type', $tianfu_type);
            return $this->db->get($db_name)->result();
        }

        if (!empty($r['parent_order_id'])) {
            $this->db->where('parent_order_id', intval(decode_id($r['parent_order_id'])));
        }

        if (!empty($r['jd_start_time'])) {
            $this->db->where('gmt_taking_task >=', $r['jd_start_time']);
        }

        if (!empty($r['jd_end_time'])) {
            $this->db->where('gmt_taking_task <=', $r['jd_end_time']);
        }

        if (!empty($r['bind_shop'])) {
            $this->db->where('shop_id', intval($r['bind_shop']));
        }

        if (!empty($r['task_status'])) {
            $this->db->where('status', intval($r['task_status']));
        }

        if (!empty($tianfu_type)) {
            $this->db->where('task_type', $tianfu_type);
        }

        if (!empty($r['buyer_taobao_nick'])) {
            $this->db->like('buyer_tb_nick', $r['buyer_taobao_nick'], 'both');
        }

        if (!empty($r['i_page']) && is_numeric($r['i_page'])) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($r['i_page'] - 1));
        }

        $this->db->order_by('id', 'ASC');
        //echo $this->db->last_query();exit;
        return $this->db->get($db_name)->result();
    }

    function get_audit_task_list($r)
    {
        if (empty($r['seller_id'])) {
            return null;
        }

        if (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_DF) {
            $db_name = self::DB_TASK_DIANFU;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_LL) {
            $db_name = self::DB_TASK_LIULIANG;
        } elseif (!empty($r['task_type']) && $r['task_type'] == TASK_TYPE_PDD) {
            $db_name = self::DB_TASK_PINDUODUO;
        } else {
            return null;
        }

        $this->db->select(array('id', 'parent_order_id', 'item_title', 'item_url', 'item_pic', 'gmt_taking_task', 'buyer_tb_nick', 'status'));
        $this->db->where('seller_id', $r['seller_id']);
        $this->db->where_in('status', array(self::TASK_STATUS_MJSH, self::TASK_STATUS_HPSH));
        $this->db->order_by('id', 'DESC');
        return $this->db->get($db_name)->result();
    }

    function update_task_status($i, $n_status, $task_type)
    {
        if (empty($i) || empty($n_status) || empty($task_type)) {
            return false;
        }

        $this->db->set('status', $n_status);
        $this->db->where('id', $i);

        if ($task_type == TASK_TYPE_DF) {
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_DT) {
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            return $this->db->update(self::DB_TASK_LIULIANG);
        } elseif ($task_type == TASK_TYPE_PDD) {
            return $this->db->update(self::DB_TASK_PINDUODUO);
        }
        return false;
    }

    function update_task_status_shenhe($i, $task_type, $status)
    {
        if (empty($i) || empty($task_type) || empty($status)) {
            return false;
        }
        if($status != self::TASK_STATUS_MJSH){
            return false;
        }
        if($status== self::TASK_STATUS_MJSH){
            $this->db->set('status', self::TASK_STATUS_DPJ);
        }else if($status== self::TASK_STATUS_HPSH){
            $this->db->set('status', self::TASK_STATUS_YWC);
        }

        $this->db->where('id', $i);

        if ($task_type == TASK_TYPE_DF) {
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            return $this->db->update(self::DB_TASK_LIULIANG);
        }
        return false;
    }

    function cancel_task($i, $task_type, $seller_id)
    {
        if ($this->check_task_seller_n_status($i, $task_type, $seller_id, self::TASK_STATUS_DJD)) {
            return $this->update_task_status($i, self::TASK_STATUS_YCX, $task_type);
        }
        return false;
    }

    function cancel_task_someshenhe($i, $task_type, $seller_id,$status)
    {

        if ($this->check_task_seller_n_status_some($i, $task_type, $seller_id, self::TASK_STATUS_MJSH)) {
            return $this->update_task_status_shenhe($i, $task_type,$status);
        }
        return false;
    }

    function check_task_seller_n_status($i, $task_type, $seller_id, $t_status)
    {
        if (empty($i) || empty($seller_id) || empty($task_type) || empty($t_status)) {
            return false;
        }

        $this->db->select(array('seller_id', 'status'));
        $this->db->where('id', $i);
        $this->db->limit(1);

        if ($task_type == TASK_TYPE_DF) {
            $result = $this->db->get(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_DT) {
            $result = $this->db->get(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            $result = $this->db->get(self::DB_TASK_LIULIANG);
        } elseif ($task_type == TASK_TYPE_PDD) {
            $result = $this->db->get(self::DB_TASK_PINDUODUO);
        } else {
            return false;
        }

        if ($result->num_rows() > 0) {
            if ($result->row()->status != $t_status) {
                return false;
            }
            if ($result->row()->seller_id != $seller_id) {
                return false;
            }
            return true;
        }

        return false;
    }

    function check_task_seller_n_status_some($i, $task_type, $seller_id, $t_status)
    {
        if (empty($i) || empty($seller_id) || empty($task_type) || empty($t_status) ) {
            return false;
        }

        $this->db->select(array('seller_id', 'status'));
        $this->db->where('id', $i);
        $this->db->limit(1);

        if ($task_type == TASK_TYPE_DF) {
            $result = $this->db->get(self::DB_TASK_DIANFU);
        } elseif ($task_type == TASK_TYPE_LL) {
            $result = $this->db->get(self::DB_TASK_LIULIANG);
        } elseif ($task_type == TASK_TYPE_PDD) {
            $result = $this->db->get(self::DB_TASK_PINDUODUO);
        } else {
            return false;
        }

        if ($result->num_rows() > 0) {
            if ($result->row()->status != $t_status) {
                return false;
            }
            if ($result->row()->seller_id != $seller_id) {
                return false;
            }
            return true;
        }

        return false;
    }


    function build_tasks($p_id, $r)
    {
        if (empty($p_id) || !is_numeric($p_id) || !is_array($r)) {
            return false;
        }

        $r['parent_order_id'] = $p_id;

        if (empty($r['template_id']) || !is_numeric($r['template_id'])) {
            return false;
        }

        $template_info = $this->get_template_info($r['template_id']);

        if (empty($template_info)) {
            return false;
        }

        if (empty($template_info->shop_id) || !is_numeric($template_info->shop_id)) {
            return false;
        }

        $shop_info = $this->get_shop_info($template_info->shop_id);

        if (empty($shop_info)) {
            return false;
        }

        $r['shop_id'] = $shop_info->id;
        $r['shop_type'] = $shop_info->shop_type;
        $r['shop_name'] = $shop_info->shop_name;
        $r['shop_ww'] = $shop_info->shop_ww;

        $r['device_type'] = $template_info->device_type;
        $r['item_id'] = $template_info->item_id;
        $r['item_title'] = $template_info->item_title;
        $r['item_url'] = $template_info->item_url;
        $r['item_display_price'] = $template_info->item_display_price;
        $r['item_pic'] = $template_info->item_pic;
        $r['task_note'] = $template_info->template_note;

        if ($r['task_type'] == TASK_TYPE_LL) {
            if (!$this->build_ll_task($r)) {
                return false;
            }
        } elseif ($r['task_type'] == TASK_TYPE_DF) {
            if (!$this->build_df_task($r)) {
                return false;
            }
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            if (!$this->build_pdd_task($r)) {
                return false;
            }
        } elseif ($r['task_type'] == TASK_TYPE_DT) {
            if (!$this->build_dt_task($r)) {
                return false;
            }
        }

        return true;
    }

    function build_df_task($p)
    {
        if (empty($p['task_cnt']) || $p['task_cnt'] <= 0) {
            error_log('Dianfu task build failed, dianfu task cnt is 0.');
            return false;
        }
        $data = array(
            'task_type' => TASK_TYPE_DF,
            'seller_id' => $p['seller_id'],
            'parent_order_id' => $p['parent_order_id'],
            'shop_id' => $p['shop_id'],
            'shop_type' => $p['shop_type'],
            'shop_name' => $p['shop_name'],
            'shop_ww' => $p['shop_ww'],
            'device_type' => $p['device_type'],
            'item_id' => $p['item_id'],
            'item_title' => $p['item_title'],
            'item_url' => $p['item_url'],
            'item_display_price' => $p['item_display_price'],
            'item_pic' => $p['item_pic'],
            'task_method' => $p['task_method'],
            'task_method_details' => $p['task_method_details'],
            'sort_type' => $p['sort_type'],
            'buyer_cnt' => $p['buyer_cnt'],
            'sku' => $p['sku'],
            'task_capital' => $p['task_capital'],
            'num_of_pkg' => $p['num_of_pkg'],
            'is_coupon' => $p['is_coupon'],
            'refunds_mode' => STATUS_ENABLE,
            'is_blacklist' => STATUS_ENABLE,
            'is_collection' => $p['is_collection'],
            'is_add_cart' => $p['is_add_cart'],
            'is_fake_chat' => $p['is_fake_chat'],
            'is_compete_collection' => $p['is_compete_collection'],
            'is_compete_add_cart' => $p['is_compete_add_cart'],
            'is_preferred' => $p['is_preferred'],
            'is_huabei' => $p['is_huabei'],
            'is_express' => $p['is_express'],
            'sex_limit' => $p['sex_limit'],
            'express_type' => $p['express_type'],
            'goods_weight' => empty($p['goods_weight']) ? '0' : $p['goods_weight'],
            'age_limit' => $p['age_limit'],
            'tb_rate_limit' => $p['tb_rate_limit'],
            //'tb_area_limit' => NOT_AVAILABLE,
            'comment_type' => $p['comment_type'],
            'end_time' => $p['end_time'],
            'task_note' => $p['task_note'],
            'commission_discount' => $p['commission_discount'],
            'single_task_capital' => $p['single_task_capital'],
            'single_task_commission' => $p['single_task_commission'],
            'single_task_commission_paid' => $p['single_task_commission_paid'],
            'commission_to_buyer' => $p['commission_to_buyer'],
            'commission_to_platform' => $p['commission_to_platform'],
            'service_to_platform' => $p['service_to_platform'],
            'single_task_express' => $p['single_task_express'],
            'status' => self::TASK_STATUS_DJD,
            'refund_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'capital_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'commission_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'express_clearing_status' => self::TASK_CLEARING_STATUS_NO,
        );
        if($p['taskPlanDay'] == '不限制'){
            $data['tb_area_limit'] = NOT_AVAILABLE;
        }else{
            $data['tb_area_limit'] = $p['taskPlanDay'];
        }

        $df_task_data = array();
        $start_time = strtotime($p['start_time']);

        $y = 0;
        if (!empty($p['task_number']) && is_array($p['task_number'])) {
               for ($i=0; $i < count($p['task_number']); $i++) { 
                   for ($j=0; $j < $p['task_number'][$i]; $j++) { 
                        
                        $data['task_method_details'] = empty($p['task_method_details'][$i]) ? $p['task_method_details'] : $p['task_method_details'][$i] ;
                        $data['sort_type']           = empty($p['sort_type'][$i]) ? $p['sort_type'] : $p['sort_type'][$i];

                        switch ($data['sort_type']) {
                            case '销量':
                                $data['buyer_cnt'] = $p['receipt_cnt'];
                                break;
                            case '综合':
                                $data['buyer_cnt'] = $p['buyer_cnt'];
                                break;
                            case '综合直通车':
                                $data['buyer_cnt'] = $p['buyer_cnt'];
                                break;
                        }
                        
                        if (!empty($p['hand_out_interval']) && intval($p['hand_out_interval']) > 0) {
                            $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                            $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
                        } else {
                            $data['start_time'] = $p['start_time'];
                        }

                        if (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_TEXT) {
                            $data['comment_text'] = $p['comment_text'][$y];
                        } elseif (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_PICTURE) {
                            $data['comment_text'] = $p['comment_text'][$y];
                            $data['comment_pic'] = $p['comment_img'][($y * 5)] . '^^^' . $p['comment_img'][($y * 5) + 1] . '^^^' . $p['comment_img'][($y * 5) + 2] . '^^^' . $p['comment_img'][($y * 5) + 3] . '^^^' . $p['comment_img'][($y * 5) + 4];
                        }
                        array_push($df_task_data, $data);
                        $y++;
                   }
               }
        }else{
            for ($x = 0; $x < $p['task_cnt']; $x++) {
                if (!empty($p['hand_out_interval']) && intval($p['hand_out_interval']) > 0) {
                    $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                    $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
                } else {
                    $data['start_time'] = $p['start_time'];
                }

                if (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_TEXT) {
                    $data['comment_text'] = is_array($p['comment_text']) ? $p['comment_text'][$x] : $p['comment_text'];
                } elseif (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_PICTURE) {
                    $data['comment_text'] =  is_array($p['comment_text']) ? $p['comment_text'][$x] : $p['comment_text'];
                    $data['comment_pic'] = $p['comment_img'][($y * 5)] . '^^^' . $p['comment_img'][($y * 5) + 1] . '^^^' . $p['comment_img'][($y * 5) + 2] . '^^^' . $p['comment_img'][($y * 5) + 3] . '^^^' . $p['comment_img'][($y * 5) + 4];
                }
                array_push($df_task_data, $data);
                $y++;
            }
        }

        shuffle($df_task_data);  //将订单数组重新随机排序
        $cnt = $this->db->insert_batch(self::DB_TASK_DIANFU, $df_task_data);
        if (!$cnt) {
            error_log('batch insert dianfu task failed, last query =' . $this->db->last_query());
            return false;
        }

        return $cnt;
    }

    // Added by Ryan.
    function build_dt_task($p)
    {
        if (empty($p['task_cnt']) || $p['task_cnt'] <= 0) {
            error_log('Duotian task build failed, Duotian task cnt is 0.');
            return false;
        }

        $data = array(
            'task_type' => TASK_TYPE_DT,
            'seller_id' => $p['seller_id'],
            'parent_order_id' => $p['parent_order_id'],
            'shop_id' => $p['shop_id'],
            'shop_type' => $p['shop_type'],
            'shop_name' => $p['shop_name'],
            'shop_ww' => $p['shop_ww'],
            'device_type' => $p['device_type'],
            'item_id' => $p['item_id'],
            'item_title' => $p['item_title'],
            'item_url' => $p['item_url'],
            'item_display_price' => $p['item_display_price'],
            'item_pic' => $p['item_pic'],
            //'task_method' => isset($p['task_method']) ? $p['task_method'] : '',
            //'task_method_details' => isset($p['task_method_details']) ? $p['task_method_details'] : '',
            //'sort_type' => $p['sort_type'],
            'buyer_cnt' => $p['buyer_cnt'],
            'sku' => $p['sku'],
            'task_capital' => $p['task_capital'],
            'num_of_pkg' => $p['num_of_pkg'],
            'is_coupon' => $p['is_coupon'],
            'refunds_mode' => STATUS_ENABLE,
            'is_blacklist' => STATUS_ENABLE,
            //'is_collection' => $p['is_collection'],
            //'is_add_cart' => $p['is_add_cart'],
            //'is_fake_chat' => $p['is_fake_chat'],
            //'is_compete_collection' => $p['is_compete_collection'],
           // 'is_compete_add_cart' => $p['is_compete_add_cart'],
            'is_preferred' => $p['is_preferred'],
            'is_huabei' => $p['is_huabei'],
            'is_express' => $p['is_express'],
            'sex_limit' => $p['sex_limit'],
            'express_type' => $p['express_type'],
            'goods_weight' => empty($p['goods_weight']) ? '0' : $p['goods_weight'],
            'age_limit' => $p['age_limit'],
            'tb_rate_limit' => $p['tb_rate_limit'],
            //'tb_area_limit' => NOT_AVAILABLE,
            'comment_type' => $p['comment_type'],
            'end_time' => $p['end_time'],
            'task_note' => $p['task_note'],
            'commission_discount' => $p['commission_discount'],
            'single_task_capital' => $p['single_task_capital'],
            'single_task_commission' => $p['single_task_commission'],
            'single_task_commission_paid' => $p['single_task_commission_paid'],
            'commission_to_buyer' => $p['commission_to_buyer'],
            'commission_to_platform' => $p['commission_to_platform'],
            'service_to_platform' => $p['service_to_platform'],
            'single_task_express' => $p['single_task_express'],
            'status' => self::TASK_STATUS_DJD,
            'refund_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'capital_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'commission_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'express_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'task_days' => $p['task_days'],
            'cur_task_day' => 1,
            'next_start_time' => date('Y-m-d H:i:s')
        );
        if($p['taskPlanDay'] == '不限制'){
            $data['tb_area_limit'] = NOT_AVAILABLE;
        }else{
            $data['tb_area_limit'] = $p['taskPlanDay'];
        }

        $task_attr = [];
        $task_attr['task_days'] = intval($p['task_days']);
        for ($i=1;$i<=$task_attr['task_days'];$i++) {
            $task_attr['method_outer_' . $i] = $p['method_outer_' . $i];
            //$task_attr['method_inner_' . $i] = $p['method_inner_' . $i];
            $task_attr['op_flow_' . $i] = $p['op_flow_' . $i];
            $task_attr['task_way_' . $i] = $p['task_way_' . $i];
        }

        $data2 = [
            'task_attr' => json_encode($task_attr),
        ];
        unset($task_attr);

        //$df_task_data = array();
        $start_time = strtotime($p['start_time']);

        $y = $cnt = 0;
        for ($x = 0; $x < $p['task_cnt']; $x++) {
            if (!empty($p['hand_out_interval']) && intval($p['hand_out_interval']) > 0) {
                $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
            } else {
                $data['start_time'] = $p['start_time'];
            }

            if (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_TEXT) {
                $data['comment_text'] = is_array($p['comment_text']) ? $p['comment_text'][$x] : $p['comment_text'];
            } elseif (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_PICTURE) {
                $data['comment_text'] = is_array($p['comment_text']) ? $p['comment_text'][$x] : $p['comment_text'];
                $data['comment_pic'] = $p['comment_img'][($y * 5)] . '^^^' . $p['comment_img'][($y * 5) + 1] . '^^^' . $p['comment_img'][($y * 5) + 2] . '^^^' . $p['comment_img'][($y * 5) + 3] . '^^^' . $p['comment_img'][($y * 5) + 4];
            }

            if ($this->db->insert(self::DB_TASK_DIANFU, $data)) {
                $data2['task_id'] = $this->db->insert_id();
                $this->db->insert(self::DB_TASK_DIANFU_EXT, $data2);
                $cnt++;
            };

            //array_push($df_task_data, $data);
            $y++;
        }

        //$cnt = $this->db->insert_batch(self::DB_TASK_DIANFU, $df_task_data);
        if (!$cnt) {
            error_log('batch insert duotian task failed, last query =' . $this->db->last_query());
            return false;
        }

        return $cnt;
    }

    /**
     * @name 给买手创建拼多多订单
     * @param $p
     * @return bool
     * @author chen.jian
     */
    function build_pdd_task($p)
    {
        if (empty($p['task_cnt']) || $p['task_cnt'] <= 0) {
            error_log('Pinduoduo task build failed, pinduoduo task cnt is 0.');
            return false;
        }

        $data = array(
            'task_type' => TASK_TYPE_PDD,
            'seller_id' => $p['seller_id'],
            'parent_order_id' => $p['parent_order_id'],
            'shop_id' => $p['shop_id'],
            'shop_type' => $p['shop_type'],
            'shop_name' => $p['shop_name'],
            'shop_ww' => $p['shop_ww'],
            'device_type' => $p['device_type'],
            'item_id' => $p['item_id'],
            'item_title' => $p['item_title'],
            'item_url' => $p['item_url'],
            'item_display_price' => $p['item_display_price'],
            'item_pic' => $p['item_pic'],
            'task_method' => $p['task_method'],
            'task_method_details' => $p['task_method_details'],
            'sort_type' => $p['sort_type'],
            'buyer_cnt' => $p['buyer_cnt'], //销量
            'order_type' => $p['order_type'], //下单方式
            'sku' => $p['sku'],
            'task_capital' => $p['task_capital'],
            'num_of_pkg' => $p['num_of_pkg'],
            'is_coupon' => $p['is_coupon'],
            'refunds_mode' => STATUS_ENABLE, //是否平台返款：默认1是
            'is_blacklist' => STATUS_ENABLE, //是否过滤黑名单：默认1是
            'is_collection' => $p['is_collection'],
            'is_fake_chat' => $p['is_fake_chat'],
            'is_compete_collection' => $p['is_compete_collection'],
            'is_wechat_share' => $p['is_wechat_share'],
            'is_preferred' => $p['is_preferred'],
            'is_express' => $p['is_express'],
            'comment_type' => $p['comment_type'],
            'express_type' => $p['express_type'],
            'goods_weight' => $p['goods_weight'],
            'end_time' => $p['end_time'],
            'task_note' => $p['task_note'],
            'commission_discount' => $p['commission_discount'], //佣金折扣
            'single_task_capital' => $p['single_task_capital'],
            'single_task_express' => $p['single_task_express'],
            'single_task_commission' => $p['single_task_commission'],
            'single_task_commission_paid' => $p['single_task_commission_paid'],
            'commission_to_buyer' => $p['commission_to_buyer'],
            'commission_to_platform' => $p['commission_to_platform'],
            'service_to_platform' => $p['service_to_platform'],
            'status' => self::TASK_STATUS_DJD,
            'refund_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'capital_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'commission_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'express_clearing_status' => self::TASK_CLEARING_STATUS_NO,
        );

        $pdd_task_data = [];
        $start_time = strtotime($p['start_time']);
        if (!empty($p['task_number']) && is_array($p['task_number'])) {

            $y = 0;
               for ($i=0; $i < count($p['task_number']); $i++) { 
                   for ($j=0; $j < $p['task_number'][$i]; $j++) { 
                        
                        $data['task_method_details'] = $p['task_method_details'][$i];
                        $data['sort_type']           = $p['sort_type'][$i];

                        if (!empty($p['hand_out_interval']) && intval($p['hand_out_interval']) > 0) {
                            $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                            $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
                        } else {
                            $data['start_time'] = $p['start_time'];
                        }

                        if (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_TEXT) {
                            $data['comment_text'] = $p['comment_text'][$y];
                        } elseif (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_PICTURE) {
                            $data['comment_text'] = $p['comment_text'][$y];
                            $data['comment_pic'] = $p['comment_img'][($y * 5)] . '^^^' . $p['comment_img'][($y * 5) + 1] . '^^^' . $p['comment_img'][($y * 5) + 2] . '^^^' . $p['comment_img'][($y * 5) + 3] . '^^^' . $p['comment_img'][($y * 5) + 4];
                        }
                        array_push($pdd_task_data, $data);
                        $y++;
                   }
               }
        }else{
            $y = 0;
            for ($x = 0; $x < $p['task_cnt']; $x++) {
                if (!empty($p['hand_out_interval']) && intval($p['hand_out_interval']) > 0) {
                    $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                    $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
                } else {
                    $data['start_time'] = $p['start_time'];
                }

                if (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_TEXT) {
                    $data['comment_text'] = is_array($p['comment_text']) ? $p['comment_text'][$x] : $p['comment_text'];
                } elseif (!empty($p['comment_type']) && $p['comment_type'] == COMMENT_TYPE_PICTURE) {
                    $data['comment_text'] = is_array($p['comment_text']) ? $p['comment_text'][$x] : $p['comment_text'];
                    $data['comment_pic'] = $p['comment_img'][($y * 5)] . '^^^' . $p['comment_img'][($y * 5) + 1] . '^^^' . $p['comment_img'][($y * 5) + 2] . '^^^' . $p['comment_img'][($y * 5) + 3] . '^^^' . $p['comment_img'][($y * 5) + 4];
                }

                array_push($pdd_task_data, $data);
            }
        }

        $cnt = $this->db->insert_batch(self::DB_TASK_PINDUODUO, $pdd_task_data);
        if (!$cnt) {
            error_log('batch insert pinduoduo task failed, last query =' . $this->db->last_query());
            return false;
        }

        return $cnt;
    }

    function build_ll_task($p)
    {
        if (empty($p['task_cnt']) || $p['task_cnt'] <= 0) {
            error_log('Liuliang task build failed, liuliang task cnt is 0.');
            return false;
        }

        if (empty($p['favorite_shop'])) {
            $p['favorite_shop'] = NOT_AVAILABLE;
        }

        if (empty($p['favorite_item'])) {
            $p['favorite_item'] = NOT_AVAILABLE;
        }

        if (empty($p['add_cart'])) {
            $p['add_cart'] = NOT_AVAILABLE;
        }

        $data = array(
            'task_type' => TASK_TYPE_LL,
            'seller_id' => $p['seller_id'],
            'parent_order_id' => $p['parent_order_id'],
            'shop_id' => $p['shop_id'],
            'shop_type' => $p['shop_type'],
            'shop_name' => $p['shop_name'],
            'shop_ww' => $p['shop_ww'],
            'device_type' => $p['device_type'],
            'item_id' => $p['item_id'],
            'item_title' => $p['item_title'],
            'item_url' => $p['item_url'],
            'item_display_price' => $p['item_display_price'],
            'item_pic' => $p['item_pic'],
            'task_method' => $p['task_method'],
            'task_method_details' => $p['task_method_details'],
            'sort_type' => $p['sort_type'],
            'buyer_cnt' => $p['buyer_cnt'],
            'sku' => $p['sku'],
            'is_preferred' => $p['is_preferred'],
            'favorite_shop' => $p['favorite_shop'],
            'favorite_item' => $p['favorite_item'],
            'add_cart' => $p['add_cart'],
            'end_time' => $p['end_time'],
            'task_note' => $p['task_note'],
            'commission_discount' => $p['commission_discount'],
            'single_task_commission' => $p['single_task_commission'],
            'single_task_commission_paid' => $p['single_task_commission_paid'],
            'commission_to_buyer' => $p['commission_to_buyer'],
            'commission_to_platform' => $p['commission_to_platform'],
            'service_to_platform' => $p['service_to_platform'],
            'status' => self::TASK_STATUS_DJD,
            'refund_clearing_status' => self::TASK_CLEARING_STATUS_NO,
            'commission_clearing_status' => self::TASK_CLEARING_STATUS_NO
        );

        $ll_task_data = array();
        $start_time = strtotime($p['start_time']);
        if (!empty($p['task_number']) && is_array($p['task_number'])) {
            $y = 0;
               for ($i=0; $i < count($p['task_number']); $i++) { 
                   for ($j=0; $j < $p['task_number'][$i]; $j++) { 
                        
                        $data['task_method_details'] = $p['task_method_details'][$i];
                        $data['sort_type']           = $p['sort_type'][$i];
                        switch ($data['sort_type']) {
                            case '销量':
                                $data['buyer_cnt'] = $p['receipt_cnt'];
                                break;
                            case '综合':
                                $data['buyer_cnt'] = $p['buyer_cnt'];
                                break;
                            case '综合直通车':
                                $data['buyer_cnt'] = $p['buyer_cnt'];
                                break;
                        }
                        
                        if (!empty($p['hand_out_interval']) && intval($p['hand_out_interval']) > 0) {
                            $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                            $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
                        } else {
                            $data['start_time'] = $p['start_time'];
                        }
                        array_push($ll_task_data, $data);
                        $y++;
                   }
               }
        }else{
            for ($x = 0; $x < $p['task_cnt']; $x++) {
                if (!empty($p['hand_out_interval']) && $p['hand_out_interval'] > 0) {
                    $data['start_time'] = date('Y-m-d H:i:s', $start_time);
                    $start_time = strtotime("+" . intval($p['hand_out_interval']) . " minutes", $start_time);
                } else {
                    $data['start_time'] = $p['start_time'];
                }
                array_push($ll_task_data, $data);
            }
        }



        $cnt = $this->db->insert_batch(self::DB_TASK_LIULIANG, $ll_task_data);
        if (!$cnt) {
            error_log('batch insert liuliang task failed, last query =' . $this->db->last_query());
            return false;
        }

        return $cnt;
    }

    function seller_audit_task($p)
    {
        if (empty($p['task_type']) || empty($p['task_id']) || empty($p['conclusion']) || empty($p['seller_id'])) {
            return false;
        }
        $buyer_id       = $p['buyer_id'];
        $seller_id      = $this->session->userdata(SESSION_SELLER_ID);
        $seller_name    = $this->session->userdata(SESSION_SELLER_NAME);
        $oper_type      = 4;
        $task_id        = encode_id($p['task_id']);

        if ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_DF, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_DPJ, "垫付订单{$task_id}卖家审核通过");
            $this->db->set('status', self::TASK_STATUS_DPJ);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_TASK_BAD) {
            if (empty($p['reject_reason'])) {
                return false;
            }

            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_DF, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_MJSH_BTG, "垫付订单{$task_id}卖家审核不通过");
            $this->db->set('status', self::TASK_STATUS_MJSH_BTG);
            $this->db->set('reject_reason', $p['reject_reason']);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_REVIEW_OK) {
            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_DF, $p['seller_id'], self::TASK_STATUS_HPSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_YWC, "垫付订单{$task_id}卖家好评审核通过");
            $this->db->set('status', self::TASK_STATUS_YWC);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_REVIEW_BAD) {
            if (empty($p['reject_reason'])) {
                return false;
            }

            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_DF, $p['seller_id'], self::TASK_STATUS_HPSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_HPSH_BTG, "垫付订单{$task_id}卖家好评审核不通过");
            $this->db->set('status', self::TASK_STATUS_HPSH_BTG);
            $this->db->set('reject_reason', $p['reject_reason']);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_DIANFU);
        } elseif ($p['task_type'] == TASK_TYPE_LL && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_LL, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }
            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_YWC, "流量订单{$task_id}卖家审核已完成");
            $this->db->set('status', self::TASK_STATUS_YWC);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_LIULIANG);
        } elseif ($p['task_type'] == TASK_TYPE_LL && $p['conclusion'] == SELLER_CONCLUSION_TASK_BAD) {
            if (empty($p['reject_reason'])) {
                return false;
            }

            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_LL, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_MJSH_BTG, "流量订单{$task_id}卖家审核不通过");
            $this->db->set('status', self::TASK_STATUS_MJSH_BTG);
            $this->db->set('reject_reason', $p['reject_reason']);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_LIULIANG);
        }

        // 拼多多
        if ($p['task_type'] == TASK_TYPE_PDD && $p['conclusion'] == SELLER_CONCLUSION_TASK_OK) {
            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_PDD, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }
            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_DPJ, "拼多多订单{$task_id}卖家审核通过");
            $this->db->set('status', self::TASK_STATUS_DPJ);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_PINDUODUO);
        } elseif ($p['task_type'] == TASK_TYPE_PDD && $p['conclusion'] == SELLER_CONCLUSION_TASK_BAD) {
            if (empty($p['reject_reason'])) {
                return false;
            }

            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_PDD, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_MJSH_BTG, "拼多多订单{$task_id}卖家审核不通过");
            $this->db->set('status', self::TASK_STATUS_MJSH_BTG);
            $this->db->set('reject_reason', $p['reject_reason']);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_PINDUODUO);
        } elseif ($p['task_type'] == TASK_TYPE_PDD && $p['conclusion'] == SELLER_CONCLUSION_REVIEW_OK) {
            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_PDD, $p['seller_id'], self::TASK_STATUS_HPSH)) {
                return false;
            }
            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_YWC, "拼多多订单{$task_id}卖家好评审核通过");
            $this->db->set('status', self::TASK_STATUS_YWC);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_PINDUODUO);
        } elseif ($p['task_type'] == TASK_TYPE_PDD && $p['conclusion'] == SELLER_CONCLUSION_REVIEW_BAD) {
            if (empty($p['reject_reason'])) {
                return false;
            }

            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_PDD, $p['seller_id'], self::TASK_STATUS_HPSH)) {
                return false;
            }
            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_YWC, "拼多多订单{$task_id}卖家好评审核不通过");
            $this->db->set('status', self::TASK_STATUS_HPSH_BTG);
            $this->db->set('reject_reason', $p['reject_reason']);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_PINDUODUO);
        }

        return false;
    }

    // TODO... 卖家申诉
    public function seller_task_appeal($p)
    {
        if (empty($p['task_type']) || empty($p['task_id']) || empty($p['conclusion']) || empty($p['seller_id'])) {
            return false;
        }
        $buyer_id       = $p['buyer_id'];
        $seller_id      = $this->session->userdata(SESSION_SELLER_ID);
        $seller_name    = $this->session->userdata(SESSION_SELLER_NAME);
        $oper_type      = 4;
        $task_id        = encode_id($p['task_id']);

        if ($p['task_type'] == TASK_TYPE_DF && $p['conclusion'] == SELLER_CONCLUSION_TASK_BAD) {

            if (empty($p['reject_reason'])) {
                return false;
            }

            if (!$this->check_task_seller_n_status($p['task_id'], TASK_TYPE_DF, $p['seller_id'], self::TASK_STATUS_MJSH)) {
                return false;
            }

            $this->add_oper_log($buyer_id, $seller_id, $seller_name, $oper_type, self::TASK_STATUS_SSZ, "垫付订单{$task_id}卖家审核申诉");
            $this->db->set('status', self::TASK_STATUS_SSZ);
            $this->db->set('reject_reason', $p['reject_reason']);
            $this->db->where('id', $p['task_id']);
            return $this->db->update(self::DB_TASK_DIANFU);
        }
        return false;
    }



    public function getExpressData($r){
        if ($r['task_type'] == TASK_TYPE_DF) {
            $this->db->where(self::DB_TASK_DIANFU . '.id', $r['task_id']);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_DIANFU .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_DIANFU)->row();
        } elseif ($r['task_type'] == TASK_TYPE_LL) {
            $this->db->where(self::DB_TASK_LIULIANG . '.id', $r['task_id']);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_LIULIANG .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_LIULIANG)->row();
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            $this->db->where(self::DB_TASK_PINDUODUO . '.id', $r['task_id']);
            $this->db->join(self::DB_BIND_SHOPS, self::DB_BIND_SHOPS . '.id = '. self::DB_TASK_PINDUODUO .'.shop_id', 'left');
            return $this->db->get(self::DB_TASK_PINDUODUO)->row();
        }
        return 0;
    }

    public function getTaskData($r){
        if ($r['task_type'] == TASK_TYPE_DF) {
            $this->db->where(self::DB_TASK_DIANFU . '.id', $r['task_id']);
            return $this->db->get(self::DB_TASK_DIANFU)->row();
        } elseif ($r['task_type'] == TASK_TYPE_LL) {
            $this->db->where(self::DB_TASK_LIULIANG . '.id', $r['task_id']);
            return $this->db->get(self::DB_TASK_LIULIANG)->row();
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            $this->db->where(self::DB_TASK_PINDUODUO . '.id', $r['task_id']);
            return $this->db->get(self::DB_TASK_PINDUODUO)->row();
        }
        throw new Exception('审核任务失败，请重试.', CODE_UNKNOWN_ERROR);
    }

    public function getBuyerBindInfo($r, $buyer_tb_nick){
        if ($r['task_type'] == TASK_TYPE_DF) {
            $this->db->where('tb_nick', $buyer_tb_nick);
            $this->db->where('status', 1);
            $this->db->where('account_type', 1);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        } elseif ($r['task_type'] == TASK_TYPE_LL) {
            $this->db->where('account_type', 1);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            $this->db->where('tb_nick', $buyer_tb_nick);
            $this->db->where('status', 1);
            $this->db->where('account_type', 3);
            return $this->db->get(self::DB_USER_BIND_INFO)->row();
        }
        return 0;
    }

    public function updateExpressOrder($r, $orderNum){
        $this->db->where('id', $r['task_id']);
        $update_data = array(
            'express_number' => $orderNum,
        );
        if ($r['task_type'] == TASK_TYPE_DF) {
            return $this->db->update(self::DB_TASK_DIANFU, $update_data);
        } elseif ($r['task_type'] == TASK_TYPE_PDD) {
            return $this->db->update(self::DB_TASK_PINDUODUO, $update_data);
        }

        throw new Exception('非法请求', CODE_BAD_REQUEST);
    }

    public function updateExpressStatus($task_id, $task_type, $status)
    {
        $this->db->where('id', $task_id);
        $update_data = array(
            'express_clearing_status' => $status,
        );
        if ($task_type == TASK_TYPE_DF) {
            return $this->db->update(self::DB_TASK_DIANFU, $update_data);
        } elseif ($task_type == TASK_TYPE_PDD) {
            return $this->db->update(self::DB_TASK_PINDUODUO, $update_data);
        }

        throw new Exception('非法请求', CODE_BAD_REQUEST);
    }

    public function addRequestLog($log)
    {
       $data = array(
           'ctime' => time()
       );

       return $this->db->insert('request_log', array_merge($log, $data));
    }

    public function expressYto($requestYto, $responseYto, $task)
    {

        $log = array(
            'task_id' => $task->id,
            'task_type' => $task->task_type,
            'request_data' => json_encode($requestYto),
            'response_data' => json_encode($responseYto),
            'success' => $responseYto->success
        );
        $this->addRequestLog($log);

        if ($requestYto && !empty($responseYto->express_number)){
            $data = array(
                'express_success' => 1,
                'express_number' => $responseYto->express_number,
                'express_reason' => $responseYto->msg
            );
        }else{
            $data = array(
                'express_success' => 0,
                'express_number' => '异常未产生单号',
                'express_reason' => $responseYto->msg
            );
        }

        $this->db->where('id', $task->id);
        if ($task->task_type == TASK_TYPE_DF || $task->task_type == TASK_TYPE_DT) {
            return $this->db->update(self::DB_TASK_DIANFU, $data);
        } elseif ($task->task_type == TASK_TYPE_PDD) {
            return $this->db->update(self::DB_TASK_PINDUODUO, $data);
        }

        throw new Exception('非法请求-快递异常', CODE_BAD_REQUEST);
    }
    public function add_oper_log($user_id, $oper_id, $oper_name, $oper_type, $oper_type_sub, $oper_content){
        $oper_data = [
            'user_id'       => $user_id,
            'oper_id'       => $oper_id,
            'oper_name'     => $oper_name,
            'oper_type'     => $oper_type,
            'oper_type_sub'     => $oper_type_sub,
            'oper_content'  => $oper_content,
            'ctime'         => time(),
        ];
        return $this->db->insert(self::DB_OPERATION_LOG, $oper_data);
    }
}
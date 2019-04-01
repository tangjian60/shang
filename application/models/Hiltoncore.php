<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hiltoncore extends Hilton_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function user_login_verify($UName, $UPwd)
    {
        $data = array();

        if (empty($UName) || empty($UPwd)) {
            $data['result'] = false;
            return $data;
        }

        $this->db->where('user_name', $UName);
        $this->db->where('passwd', do_hash($UPwd, 'sha1'));
        $this->db->where('user_type', USER_TYPE_SELLER);
        // $this->db->where('status', STATUS_ENABLE);
        $this->db->limit(1);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() > 0) {
            $data['result'] = true;
            $data['user_data'] = $query->row();
            $this->update_login_cnt($data['user_data']->id, $data['user_data']->login_cnt);
            return $data;
        }

        $data['result'] = false;
        return $data;
    }

    function update_login_cnt($i, $c)
    {
        $this->db->set('login_cnt', ++$c);
        $this->db->where('id', $i);
        $this->db->update(self::DB_USER_MEMBER);
    }

    function get_user_info($i)
    {
        $this->db->where('id', $i);
        return $this->db->get(self::DB_USER_MEMBER)->row();
    }

    function get_seller_commission_discount($i)
    {
        $this->db->select('commission_discount');
        $this->db->where('id', $i);
        $this->db->where('status', STATUS_ENABLE);
        $this->db->limit(1);
        $query = $this->db->get(self::DB_USER_MEMBER);
        if ($query->num_rows() > 0) {
            if ($query->row()->commission_discount < 100 && $query->row()->commission_discount >= MIN_COMMISSION_DISCOUNT) {
                return $query->row()->commission_discount;
            }
        }
        return 100;
    }

    function reset_user_passwd($r)
    {
        if (invalid_parameter($r)) {
            return false;
        }

        $data = array('passwd' => do_hash($r['password'], 'sha1'));
        $this->db->where('user_name', $r['userName']);
        return $this->db->update(self::DB_USER_MEMBER, $data);
    }

    function add_user_account($r, $recommend)
    {
        if (invalid_parameter($r)) {
            return false;
        }

        if ($this->does_user_exists($r['userName'])) {
            return false;
        }

        $data = array(
            'user_name' => $r['userName'],
            'passwd' => do_hash($r['password'], 'sha1'),
            'user_type' => USER_TYPE_SELLER,
            'reg_time' => date("Y-m-d H:i:s")
        );

        if (!$this->db->insert(self::DB_USER_MEMBER, $data)) {
            error_log('seller registry db error, last query : ' . $this->db->last_query());
            return false;
        }

        // build promote relations
        $this->create_promote_relation($recommend, $this->db->insert_id());
        return true;
    }

    function does_user_exists($user_name)
    {
        $this->db->where('user_name', $user_name)->limit(1);
        $query = $this->db->get(self::DB_USER_MEMBER);
        return $query->num_rows() > 0;
    }

    function does_user_exists_byid($user_id)
    {
        if (empty($user_id)) {
            return false;
        }
        $this->db->where('id', $user_id)->limit(1);
        $query = $this->db->get(self::DB_USER_MEMBER);
        return $query->num_rows() > 0;
    }

    function get_notice_list($l = null)
    {
        $this->db->select(array('id', 'title', 'content', 'gmt_create'));
        $this->db->where_in('notice_type', array(NOTICE_TYPE_ALL, NOTICE_TYPE_SELLER));
        $this->db->where('expire_time >=', date("Y-m-d H:i:s"));
        $this->db->order_by('id', 'DESC');
        if (!empty($l)) {
            $this->db->limit($l);
        }
        return $this->db->get(self::DB_PLATFORM_NOTICE)->result();
    }

    function get_notice_info($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return null;
        }
        $this->db->where('id', $i);
        return $this->db->get(self::DB_PLATFORM_NOTICE)->row();
    }

    function get_user_messages($user_id, $limit = 20)
    {
        if (empty($user_id) || !is_numeric($user_id)) {
            return null;
        }

        $this->db->where_in('member_id', array(0, $user_id));
        $this->db->order_by('id', 'ASC');
        $this->db->limit($limit);
        return $this->db->get(self::DB_USER_MSG)->result();
    }

    function create_promote_relation($owner_id, $prom_id)
    {
        if (!$this->does_user_exists_byid($owner_id)) {
            error_log('Create promote relation failed, owner id is incorrect, last query : ' . $this->db->last_query());
            return false;
        }

        $insert_data = array(
            'owner_id' => $owner_id,
            'promote_id' => $prom_id,
            'validity_time' => date("Y-m-d H:i:s", strtotime('+' . PROMOTION_VALIDITY_DAYS . ' days')),
            'status' => STATUS_ENABLE
        );

        return $this->db->insert(self::DB_PROMOTE_RELATION, $insert_data);
    }

    function get_user_promote_cnt($i)
    {
        $this->db->where('owner_id', $i);
        $this->db->where('first_reward', 1);
        return $this->db->count_all_results(self::DB_PROMOTE_RELATION);
    }

    function bind_new_shop($r)
    {
        if (invalid_parameter($r)) {
            return false;
        }

        if ($this->does_shop_binded($r['shop_ww'])) {
            return false;
        }
        $data = array(
            'seller_id' => $r['seller_id'],
            'platform_type' => $r['plat_form'],
            'shop_type' => $r['shop_type'],
            'shop_name' => $r['shop_name'],
            'shop_url' => $r['shop_url'],
            'shop_ww' => $r['shop_ww'],
            'shop_province' => $r['shop_province'],
            'shop_city' => $r['shop_city'],
            'shop_county' => $r['shop_county'],
            'shop_address' => $r['shop_address'],
            'shop_pic' => $r['shop_pic'],
            'seller_to_nick_interval' => SELLER_TO_NICK_INTERVAL,
            'seller_to_buyer_interval' => SELLER_TO_BUYER_INTERVAL,
            'shop_to_nick_interval' => SHOP_TO_NICK_INTERVAL,
            'shop_to_buyer_interval' => SHOP_TO_BUYER_INTERVAL,
            'goods_to_nick_interval' => GOODS_TO_NICK_INTERVAL,
            'goods_to_buyer_interval' => GOODS_TO_BUYER_INTERVAL,
            'shop_liuliang_interval' => SHOP_LIULIANG_INTERVAL,
            'shop_add_cart_interval' => SHOP_ADD_CART_INTERVAL,
            'status' => STATUS_CHECKING
        );

        if (!$this->db->insert(self::DB_SHOP_BIND, $data)) {
            error_log('bind new shop failed. ' . $this->db->last_query());
            return false;
        }

        return true;
    }

    function does_shop_binded($shop_ww)
    {
        $this->db->where('shop_ww', $shop_ww);
        $this->db->where_not_in('status', array(STATUS_FAILED, STATUS_CANCEL));
        $this->db->limit(1);
        return $this->db->get(self::DB_SHOP_BIND)->num_rows() > 0;
    }

    function get_binded_shops($i)
    {
        $this->db->where('seller_id', $i);
        $this->db->order_by('status', 'asc');
        return $this->db->get(self::DB_SHOP_BIND)->result();
    }

    function get_passed_shops($i)
    {
        $this->db->where('seller_id', $i);
        $this->db->where('status', STATUS_PASSED);
        $this->db->order_by('id', 'desc');
        return $this->db->get(self::DB_SHOP_BIND)->result();
    }

    function get_shop_info($i){
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->where('id', $i);
        return $this->db->get(self::DB_SHOP_BIND)->row();
    }

    function have_binded_shop($i)
    {
        $this->db->where('seller_id', $i);
        $this->db->where('status', STATUS_PASSED);
        $this->db->limit(1);
        return $this->db->get(self::DB_USER_BIND)->num_rows() > 0;
    }

    function seller_top_up($r)
    {
        if (invalid_parameter($r)) {
            return false;
        }

        $data = array(
            'seller_id' => $r['seller_id'],
            'seller_name' => $r['seller_name'],
            //'zhuanru_bank_name' => $r['zhuanru_bank_name'],
            'chongzhi_img' => $r['chongzhi_img'],
            'huikuan_bank_name' => $r['huikuan_bank_name'],
            'transfer_person' => $r['transfer_person'],
            'transfer_amount' => $r['transfer_amount'],
            'transfer_contact' => $r['transfer_contact'],
            'status' => STATUS_CHECKING
        );

        return $this->db->insert(self::DB_TOP_UP_RECORD, $data);
    }

    function get_top_up_records($u, $o = 0, $p = ITEMS_PER_LOAD)
    {
        if (empty($u) || !is_numeric($u)) {
            return null;
        }
        $this->db->where('seller_id', $u);
        $this->db->limit($p, $o);
        $this->db->order_by('id', 'desc');
        return $this->db->get(self::DB_TOP_UP_RECORD)->result();
    }

    function seller_last_top_up_time($i)
    {
        $this->db->select_max('create_time', 'last_time');
        $this->db->where('seller_id', $i);
        return $this->db->get(self::DB_TOP_UP_RECORD)->row();
    }

    function get_user_cert_info($i)
    {
        if (empty($i)) {
            return false;
        }

        $this->db->where('user_id', $i);
        $this->db->where('status', STATUS_PASSED);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get(self::DB_USER_CERT)->row();
    }

    function seller_bind_new_bankcard($r)
    {
        if (invalid_parameter($r)) {
            return false;
        }

        $data = array(
            'seller_id'     => $r['seller_id'],
            'true_name'     => trim($r['true_name']),
            'bank_card_num' => trim($r['bank_card_num']),
            'bank_name'     => trim($r['bank_name']),
            'bank_province' => $r['province'],
            'bank_city'     => $r['city'],
            'bank_county'   => $r['county'],
            'bank_branch'   => trim($r['bank_branch']),
        );

        if (!$this->db->insert(self::DB_SELLER_BIND_BANKCARD, $data)) {
            error_log('insert user certification failed, last query is : ' . $this->db->last_query());
            return false;
        }
        return true;
    }

    function get_seller_binded_bankcars($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->where('seller_id', $i);
        return $this->db->get(self::DB_SELLER_BIND_BANKCARD)->result();
    }

    function get_bankcard_info($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->where('id', $i);
        return $this->db->get(self::DB_SELLER_BIND_BANKCARD)->row();
    }

    function delete_seller_bankcard($i)
    {
        if (empty($i) || !is_numeric($i)) {
            return false;
        }

        $this->db->where('id', $i);
        return $this->db->delete(self::DB_SELLER_BIND_BANKCARD);
    }

    function seller_withdraw($seller_id, $seller_name, $bankcard_id, $amount)
    {
        if (empty($seller_id) || empty($seller_name) || empty($bankcard_id) || !is_numeric($amount) || $amount <= 0) {
            return false;
        }

        $bankcard_info = $this->get_bankcard_info($bankcard_id);
        if (empty($bankcard_info)) {
            error_log('Seller withdraw failed cannot get seller bank info, bankcard id=' . $bankcard_id);
            return false;
        }

        $data = array(
            'user_id' => $seller_id,
            'user_name' => $seller_name,
            'amount' => $amount,
            'withdraw_service_fee' => round($amount * SELLER_WITHDRAW_SERVICE_FEE, 2),
            'real_name' => $bankcard_info->true_name,
            'bank_card_num' => $bankcard_info->bank_card_num,
            'bank_name' => $bankcard_info->bank_name,
            'bank_address' => $bankcard_info->bank_province . $bankcard_info->bank_city . $bankcard_info->bank_county,
            'bank_branch' => $bankcard_info->bank_branch,
            'status' => STATUS_CHECKING,
            'tixian_type' => 3,
        );

        $this->db->trans_begin();
        try {
            $this->db->insert(self::DB_WITHDRAW_RECORD, $data);
            $this->db->insert(self::DB_WITHDRAW_TIME, ['withdraw_id' => $this->db->insert_id(),'apply_time' => time()]);
            $this->db->trans_commit();
            return true;
        }
        catch (Exception $e) {
            $this->db->trans_rollback();
        }
        return false;
    }

    function get_withdraw_records($u, $i_page)
    {
        if (empty($u) || !is_numeric($u)) {
            return null;
        }
        $this->db->where('user_id', $u);

        if (!empty($i_page) && is_numeric($i_page)) {
            $this->db->limit(ITEMS_PER_LOAD, ITEMS_PER_LOAD * ($i_page - 1));
        } else {
            $this->db->limit(ITEMS_PER_LOAD);
        }

        $this->db->order_by('id', 'desc');
        return $this->db->get(self::DB_WITHDRAW_RECORD)->result();
    }

    //获取商家代理商下面所有下线的id
    public function get_seller_ids($seller_id)
    {
        if(empty($seller_id)){
            return false;
        }

        $this->db->select('promote_id');
        $this->db->where('owner_id', $seller_id);
        return $this->db->get(self::DB_PROMOTE_RELATION)->result();
    }


    //获取商家代理下线充值总金额
    public function get_promote_list($seller_ids, $seller_id)
    {
        // 检查是否为代理商
        $this->db->select('status');
        $this->db->where('seller_id', intval($seller_id));
        $result = $this->db->get('seller_agent')->result();
        if(count($result) == 0 || $result != 1){
            return [];
        }
        // 如果是代理则统计数据
        $list = [];
        $list2 = [];
        foreach ($seller_ids as $k => $v){
            $bill = 0; //充值总金额
            $task = 0; //放单完成数
            //获取所有下线的用户名
            $username = $this->get_user_name($v);
            $list[$k]['user_id'] = $v;
            $list[$k]['username'] = $username['0']->user_name;
            //获取充值金额
            $bills = $this->getBills($v);
            /*var_dump($bills);*/
            if($bills){
                foreach ($bills as $k => $vs){
                    $bill += $vs->amount;
                }
            }
            $list[$k]['bills'] = $bill;
            //获取放单数量
            $tasks =  $this->getTask($v);
            if($tasks){
                $task += $tasks;
            }
            $list[$k]['task'] = $task;
            $list2[] = $list[$k];
        }

        return $list2;
    }

    //获取商家代理下线放单完成数
    public function get_user_name($user_id)
    {
        $this->db->select('user_name');
        $this->db->where('id', $user_id);
        return $this->db->get(self::DB_USER_MEMBER)->result();
    }


    //获取该下线商家的充值数
    public function getBills($seller_id)
    {
        $this->db->select('amount');
        $this->db->where('user_id', $seller_id);
        $this->db->where('bill_type', 4);
        return $this->db->get(self::DB_HILTON_BILLS)->result();
    }

    //获取该下线商家的放单完成总数
    public function getTask($seller_id)
    {
        $this->db->select('id');
        $this->db->where('seller_id', $seller_id);
        $this->db->where('status', 11);
        return $this->db->get(self::DB_HILTON_TASK_DIANFU)->num_rows();
    }

}
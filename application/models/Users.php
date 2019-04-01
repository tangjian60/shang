<?php
class Users extends CI_Model
{
    const DB_USER_MEMBERS = 'user_members';


    public function __construct()
    {
        parent::__construct();
    }


    public function get_user_info($id, $aFields = [])
    {
        if (!empty($aFields)) $this->db->select($aFields);
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get(self::DB_USER_MEMBERS)->row();
    }

    public function get_val($id, $sField)
    {
        $this->db->select($sField);
        $this->db->where('id', $id);
        $this->db->limit(1);
        $query = $this->db->get(self::DB_USER_MEMBERS);
        if ($query->num_rows() > 0) {
            return $query->row()->$sField;
        }
        return '';
    }



}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();
        //判断是否登陆
        $this->seller_env_init();
        $this->load->model('taskengine');
        $this->load->model('paycore');
        $this->load->library('export2excel');
    }

    public function index()
    {
        $this->Data['TargetPage'] = 'page_message';
        $this->Data['message'] = '页面不存在';
        $this->load->view('frame_main', $this->Data);
    }

    public function templates()
    {
        //查询task_templates表，获取模板列表
        $this->Data['data'] = $this->taskengine->get_templates($this->get_seller_id());

        $this->Data['TargetPage'] = 'page_task_templates';
        $this->Data['PageTitle'] = '任务模板';
        $this->load->view('frame_main', $this->Data);
    }

    public function edit_template()
    {
        $template_id = $this->input->get('id', TRUE);
        //获取模板信息
        if (!empty($template_id) && is_numeric($template_id)) {
            $this->Data['template_info'] = $this->taskengine->get_template_info($template_id);
        }
        $this->Data['TargetPage'] = 'page_edit_template';
        $this->Data['PageTitle'] = '任务模板编辑';
        //seller_bind_shops获取店铺信息
        $this->Data['shop_data'] = $this->hiltoncore->get_passed_shops($this->get_seller_id());
//        var_dump($this->Data);exit;
        $this->load->view('frame_main', $this->Data);
    }

    public function parent_orders()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['start_time'] = $this->input->get('start_time', TRUE);
        $this->Data['end_time'] = $this->input->get('end_time', TRUE);
        $this->Data['bind_shop'] = $this->input->get('bind_shop', TRUE);
        $this->Data['order_status'] = $this->input->get('order_status', TRUE);
        $this->Data['order_status'] = $this->input->get('order_status', TRUE);
        $this->Data['parent_order_id'] = $this->input->get('parent_order_id', TRUE);
        $this->Data['seller_id'] = $this->get_seller_id();
       /* echo '<pre>';
        print_r($this->Data);die;*/

        if (empty($this->Data['i_page'])) {
            $this->Data['i_page'] = 1;
        }
        $this->Data['PageTitle'] = '订单列表';
        $this->Data['TargetPage'] = 'page_parent_orders';
        $this->Data['data'] = $this->taskengine->get_task_parent_orders($this->Data);
        /*echo '<pre>';
        var_dump($this->Data['data']);die;*/
        $this->Data['shop_data'] = $this->hiltoncore->get_passed_shops($this->get_seller_id());


        $this->load->view('frame_main', $this->Data);
    }

    public function record()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['exportexcel'] = $this->input->get('exportexcel', TRUE);
        $task_id = $this->input->get('task_id', TRUE);
        $this->Data['task_id'] = decode_id($task_id);
        $buyer_id = $this->input->get('buyer_id', TRUE);
        $this->Data['buyer_id'] = decode_id($buyer_id);
        $item_id = $this->input->get('item_id', TRUE);
        $this->Data['item_id'] = $item_id;
        $this->Data['gmt_cancelled'] = $this->input->get('gmt_cancelled', TRUE);
        $this->Data['seller_id'] = $this->get_seller_id();
        if (empty($this->Data['i_page'])) {
            $this->Data['i_page'] = 1;
        }

        if ($this->Data['exportexcel'] == 1) {
            $this->export($this->Data);
            return;
        }

        $this->Data['TargetPage'] = 'page_calloff_record';
        $this->Data['PageTitle'] = '买手取消任务单记录';
        $this->Data['data'] = $this->taskengine->get_task_cancelled($this->Data);
        $this->load->view('frame_main', $this->Data);
    }

    public function browse()
    {
        $this->Data['seller_id'] = $this->get_seller_id();
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['exportexcel'] = $this->input->get('exportexcel', TRUE);
        $this->Data['task_type'] = $this->input->get('task_type', TRUE);
        $this->Data['jd_start_time'] = $this->input->get('jd_start_time', TRUE);
        $this->Data['jd_end_time'] = $this->input->get('jd_end_time', TRUE);
        $this->Data['task_status'] = $this->input->get('task_status', TRUE);
        $this->Data['bind_shop'] = $this->input->get('bind_shop', TRUE);
        $this->Data['parent_order_id'] = $this->input->get('parent_order_id', TRUE);
        $this->Data['task_id'] = $this->input->get('task_id', TRUE);
        $this->Data['buyer_taobao_nick'] = $this->input->get('buyer_taobao_nick', TRUE);
        $this->Data['PageTitle'] = '做单详情';

        if (!empty($this->Data['task_type']) && $this->Data['task_type'] == TASK_TYPE_LL) {
            $this->Data['audit_cnt'] = $this->taskengine->get_audit_needed_cnt($this->get_seller_id(), TASK_TYPE_LL);
        } elseif (!empty($this->Data['task_type']) && $this->Data['task_type'] == TASK_TYPE_DF) {
            $this->Data['audit_cnt'] = $this->taskengine->get_audit_needed_cnt($this->get_seller_id(), TASK_TYPE_DF);
        } elseif (!empty($this->Data['task_type']) && $this->Data['task_type'] == TASK_TYPE_PDD) {
            $this->Data['audit_cnt'] = $this->taskengine->get_audit_needed_cnt($this->get_seller_id(), TASK_TYPE_PDD);
        } elseif (!empty($this->Data['task_type']) && $this->Data['task_type'] == TASK_TYPE_DT) {
            $this->Data['audit_cnt'] = $this->taskengine->get_audit_needed_cnt($this->get_seller_id(), TASK_TYPE_DT);
        } else {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
            $this->load->view('frame_main', $this->Data);
            return;
        }

        if (empty($this->Data['i_page'])) {
            $this->Data['i_page'] = 1;
        }


        if ($this->Data['exportexcel'] == 1) {
            $this->dump($this->Data);
            return;
        }
        $this->Data['data'] = $this->taskengine->get_task_list($this->Data);
        $this->Data['shop_data'] = $this->hiltoncore->get_passed_shops($this->get_seller_id());
        $this->Data['TargetPage'] = 'page_task_list';
        $this->load->view('frame_main', $this->Data);
    }

    private function dump($data){
            unset($data['i_page']);
            $data['data'] = $this->taskengine->get_task_list($data);
            $this->load->library('PHPExcel');
            $this->load->library('PHPExcel/IOFactory');
            $excel = new PHPExcel();
            $charActors = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J' , 'K', 'L', 'M', 'N', 'O', 'P']; //14
            $widthSize = [5, 20, 20, 30, 70, 28, 18, 18, 18, 20, 15, 20, 14, 40, 30, 30]; //14
            $titleName = ['ID', '父任务编号', '子任务编号', '宝贝标题', '宝贝链接', '店铺', '本金', '实付金额', '佣金', '放单时间', '接单账号', '接单时间', '订单状态', '订单编号' , '快递方式' , '快递单号' ]; //13

            foreach ($charActors as $k => $v) {
                $excel->getActiveSheet()->getStyle($v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //对齐方式，水平剧中
                $excel->getActiveSheet()->getColumnDimension($v)->setWidth($widthSize[$k]); //设置表格宽度
                $excel->getActiveSheet()->getStyle($v)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格为文本
                $excel->getActiveSheet()->setCellValue($v . 1, $titleName[$k]); //为单元格赋值
            }
            $task_status = [
                1 => '待支付',
                2 => '派单中',
                3 => '已接单待操作',
                4 => '卖家审核',
                5 => '卖家审核不通过',
                6 => '平台审核',
                7 => '平台审核不通过',
                8 => '待评价',
                9 => '好评审核',
                10 => '好评审核不通过',
                11 => '已完成',
                99 => '已撤',
            ];
            $a = 2;
            foreach($data['data'] as $i=>$item){
                $express_type = $item->express_type;
                if ($item->express_type == 'na' || $item->express_type == '') {
                    $express_type = '商家快递';
                }else{
                    $express_type = '圆通快递';
                }
                $excel->getActiveSheet()->setCellValue('A' . $a, $i + 1);                                     //ID
                $excel->getActiveSheet()->setCellValue('B' . $a, $data['parent_order_id']);                   //父任务编号
                $excel->getActiveSheet()->setCellValue('C' . $a, encode_id($item->id));                       //子任务编号
                $excel->getActiveSheet()->setCellValue('D' . $a, $item->item_title);                          //宝贝标题
                $excel->getActiveSheet()->setCellValue('E' . $a, $item->item_url);                            //宝贝链接
                $excel->getActiveSheet()->setCellValue('F' . $a, $item->shop_name);                           //店铺
                $excel->getActiveSheet()->setCellValue('G' . $a, $item->single_task_capital);                 //本金
                $excel->getActiveSheet()->setCellValue('H' . $a, $item->real_task_capital);                   //实付金额
                $excel->getActiveSheet()->setCellValue('I' . $a, $item->single_task_commission_paid + $item->service_to_platform);              //佣金
                $excel->getActiveSheet()->setCellValue('J' . $a, $item->start_time);                          //放单时间
                $excel->getActiveSheet()->setCellValue('K' . $a, $item->buyer_tb_nick);                       //接单账号
                $excel->getActiveSheet()->setCellValue('L' . $a, $item->gmt_taking_task);                     //接单时间
                $excel->getActiveSheet()->setCellValue('M' . $a, $task_status[$item->status]);                //订单状态
                $excel->getActiveSheet()->setCellValue('N' . $a, ' ' . $item->order_number);                  //订单编号
                $excel->getActiveSheet()->setCellValue('O' . $a, $express_type);                               //快递方式
                $excel->getActiveSheet()->setCellValue('P' . $a, ' ' . $item->express_number);                //快递单号
                $a++;
            }
            //输出到浏览器
            $write = new PHPExcel_Writer_Excel2007($excel);
            $shopName = $data['data'][0]->shop_name;
            $taskType = ($data['task_type'] == TASK_TYPE_LL) ? '流量单' : '垫付单';
            $file_name = $shopName . '-' . $taskType . '-' . $data['parent_order_id'];
            header("Access-Control-Allow-Origin:*");
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-execl");
            header("Content-Type:application/octet-stream");
            header("Content-Type:application/download");
            header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
            header("Content-Transfer-Encoding:binary");
            $write->save('php://output');die();
            return;
    }


    /**
     * @name 买手取消任务单记录-导出功能
     * @author zqh
     */
    private function export($data){
        unset($data['i_page']);
        $data['data'] = $this->taskengine->get_task_cancelled($data);
        $this->load->library('PHPExcel');
        $this->load->library('PHPExcel/IOFactory');
        $excel = new PHPExcel();
        $charActors = ['A', 'B', 'C', 'D', 'E', 'F', 'G']; //7
        $widthSize = [5, 20, 20, 30, 100, 28, 50]; //7
        $titleName = ['序号', '取消时间', '子任务编号', '宝贝id', '宝贝标题', '会员id', '取消原因']; //7

        foreach ($charActors as $k => $v) {
            $excel->getActiveSheet()->getStyle($v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); //对齐方式，水平剧中
            $excel->getActiveSheet()->getColumnDimension($v)->setWidth($widthSize[$k]); //设置表格宽度
            $excel->getActiveSheet()->getStyle($v)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT); //设置单元格为文本
            $excel->getActiveSheet()->setCellValue($v . 1, $titleName[$k]); //为单元格赋值
        }

        $a = 2;
        foreach($data['data'] as $i=>$item){
            $excel->getActiveSheet()->setCellValue('A' . $a, $i + 1);                                     //序号
            $excel->getActiveSheet()->setCellValue('B' . $a, $item->gmt_cancelled);                      //取消时间
            $excel->getActiveSheet()->setCellValue('C' . $a, encode_id($item->task_id));                //子任务编号
            $excel->getActiveSheet()->setCellValue('D' . $a, $item->item_id);                          //宝贝id
            $excel->getActiveSheet()->setCellValue('E' . $a, $item->item_title);                      //宝贝标题
            $excel->getActiveSheet()->setCellValue('F' . $a, encode_id($item->buyer_id));            //会员id
            $excel->getActiveSheet()->setCellValue('G' . $a, $item->cancel_reason);                 //取消原因
            $a++;
        }
        //输出到浏览器
        $write = new PHPExcel_Writer_Excel2007($excel);
        $file_name = '买手取消任务单记录';
        header("Access-Control-Allow-Origin:*");
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');die();
        return;
    }

    public function audit()
    {
        $this->Data['seller_id'] = $this->get_seller_id();
        $this->Data['task_type'] = $this->input->get('task_type', TRUE);
        $this->Data['PageTitle'] = '待审核的任务';

        if (empty($this->Data['task_type'])) {
            $this->Data['url_format'] = 'task/audit?task_type=%s';
            $this->Data['TargetPage'] = 'page_task_welcome';
            $this->load->view('frame_main', $this->Data);
            return;
        }
        // 查询流量 垫付单
        $this->Data['data'] = $this->taskengine->get_audit_task_list($this->Data);
        //查詢seller_bind_shop
        $this->Data['shop_data'] = $this->hiltoncore->get_passed_shops($this->get_seller_id());
        $this->Data['TargetPage'] = 'page_task_list';
        $this->load->view('frame_main', $this->Data);
    }

    public function zi_task_list()
    {
        $this->Data['seller_id'] = $this->get_seller_id();
        $this->Data['task_type'] = $this->input->get('task_type', TRUE);
        $this->Data['PageTitle'] = '子任务订单列表';
        // 查询流量 垫付单
        $this->Data['data'] = $this->taskengine->get_audit_task_list($this->Data);
        //查詢seller_bind_shop
        $this->Data['shop_data'] = $this->hiltoncore->get_passed_shops($this->get_seller_id());
        $this->Data['TargetPage'] = 'page_zi_task_list';
        $this->load->view('frame_main', $this->Data);
    }

    public function details()
    {
        $task_type = $this->input->get('task_type', TRUE);
        $task_id = $this->input->get('task_id', TRUE);

        if (empty($task_type) || empty($task_id) || !is_numeric($task_id)) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
            $this->load->view('frame_main', $this->Data);
            return;
        } else if ($task_type == TASK_TYPE_LL) {
            $this->Data['PageTitle'] = '流量任务详情';
            $this->Data['task_type'] = TASK_TYPE_LL;
            $this->Data['TargetPage'] = 'page_details_liuliang';
            $this->Data['data'] = $this->taskengine->get_liuliang_task_info($task_id);
        } else if ($task_type == TASK_TYPE_DF) {
            $this->Data['PageTitle'] = '垫付任务详情';
            $this->Data['task_type'] = TASK_TYPE_DF;
            $this->Data['TargetPage'] = 'page_details_dianfu';
            $this->Data['reject_reasons'] = load_config('shang', 'reject_reasons');
            $this->Data['data'] = $this->taskengine->get_dianfu_task_info($task_id);
        } else if ($task_type == TASK_TYPE_DT) {
            $this->Data['PageTitle'] = '多天垫付任务详情';
            $this->Data['task_type'] = TASK_TYPE_DF;
            $this->Data['TargetPage'] = 'page_details_duotian';
            $aData = $this->taskengine->get_duotian_task_info($task_id);
            $this->Data['data'] = $aData['detail'];
            $this->Data['show_data'] = $aData['show_data'];
        } else if ($task_type == TASK_TYPE_PDD) {
            $this->Data['PageTitle'] = '拼多多任务详情';
            $this->Data['task_type'] = TASK_TYPE_PDD;
            $this->Data['TargetPage'] = 'page_details_pinduoduo';
            $this->Data['data'] = $this->taskengine->get_pinduoduo_task_info($task_id);
        } else {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
        }

        if (empty($this->Data['data']) || $this->Data['data']->seller_id != $this->get_seller_id()) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
            $this->load->view('frame_main', $this->Data);
            return;
        }
        $this->load->view('frame_main', $this->Data);
    }

    public function pub_dt()
    {
        // TODO...
//        if (!in_array($this->get_seller_name(), ['18767193791','12000000001','12000000002'])) {
//            echo "<script>history.go(-1);</script>";
//            return;
//        }

        $tpl = $this->input->get('tpl', TRUE);
        if (!empty($tpl)) {
            $this->Data['PageTitle'] = '发布多天任务';
            $this->Data['task_type'] = TASK_TYPE_DT;
            $this->Data['TargetPage'] = 'page_new_task_duotian';
            $platform_type          = PLATFORM_TYPE_TAOBAO;
            $this->Data['templates_data'] = $this->taskengine->get_templates($this->get_seller_id(), $platform_type);
            $this->Data['shopsWithAddress'] = $this->taskengine->get_shops_with_address($this->get_seller_id());

            // 加载
            $cfg = load_config('pub_tpl', $tpl);
            if(!empty($cfg)) {
                $this->Data['parent_order_data_attribute'] = json_encode($cfg);
            }

            $config = load_config('shang');
            $this->Data['task_behaviors'] = $config['task_behaviors'];
            $this->Data['sp'] = 4; // 定界位置  店内浏览 | 店外浏览
            $this->Data['task_ways'] = $config['task_ways'];
            $this->Data['commission_discount'] = $this->hiltoncore->get_seller_commission_discount($this->get_seller_id());

        } else {
            $this->Data['PageTitle'] = '选择多天任务发布模板';
            $this->Data['task_type'] = TASK_TYPE_DT;
            $this->Data['TargetPage'] = 'page_choose_tpl_duotian';
        }

        $this->load->view('frame_main', $this->Data);
    }

    /**
     * @name 增加「一键重发」功能
     * @author chen.jian
     */
    public function pub()
    {
        $task_type = $this->input->get('task_type', TRUE);
        $parent_order_id = $this->input->get('parent_order_id', TRUE);

        // TODO... 待移除
//        if ($task_type == 'DUOTIAN' && !in_array($this->get_seller_name(), ['18767193791','12000000001','12000000002'])) {
//            echo "<script>history.go(-1);</script>";
//            return;
//        }

        if (empty($task_type)) {
            $this->Data['PageTitle'] = '发布新任务';
            $this->Data['url_format'] = 'task/pub?task_type=%s';
            $this->Data['TargetPage'] = 'page_task_welcome';
        } else if ($task_type == TASK_TYPE_LL) {
            $this->Data['PageTitle'] = '发布流量任务';
            $this->Data['task_type'] = TASK_TYPE_LL;
            $this->Data['TargetPage'] = 'page_new_task_liuliang';
            $platform_type          = PLATFORM_TYPE_TAOBAO;
            $this->Data['templates_data'] = $this->taskengine->get_templates($this->get_seller_id(), $platform_type);
            if(!empty($parent_order_id) && $parent_order_id > 0){
                $parent_order_data = $this->taskengine->get_parent_order_info(decode_id($parent_order_id));
                $this->Data['parent_order_data_attribute'] = $parent_order_data->attributes;
                $parent_order_data_attribute = json_decode($this->Data['parent_order_data_attribute']);

                if(is_string($parent_order_data_attribute->task_method_details)){
                    $array[] = $parent_order_data_attribute->task_method_details;
                    $parent_order_data_attribute->task_method_details = $array;
                }

                if(is_string($parent_order_data_attribute->sort_type)){
                    $key[] = $parent_order_data_attribute->sort_type;
                    $parent_order_data_attribute->sort_type = $key;
                }

                if(is_string($parent_order_data_attribute->task_number)){
                    $arr[] = $parent_order_data_attribute->task_number;
                    $parent_order_data_attribute->task_number = $arr;
                }
                
                $this->Data['parent_order_data_attribute'] = json_encode($parent_order_data_attribute);

            }
        } else if ($task_type == TASK_TYPE_DF) {
            $this->Data['PageTitle'] = '发布垫付任务';
            $this->Data['task_type'] = TASK_TYPE_DF;
            $this->Data['TargetPage'] = 'page_new_task_dianfu';
            $platform_type          = PLATFORM_TYPE_TAOBAO;
            $this->Data['templates_data'] = $this->taskengine->get_templates($this->get_seller_id(), $platform_type);
            $this->Data['shopsWithAddress'] = $this->taskengine->get_shops_with_address($this->get_seller_id());
            if(!empty($parent_order_id) && $parent_order_id > 0){
                $parent_order_data = $this->taskengine->get_parent_order_info(decode_id($parent_order_id));
                $this->Data['parent_order_data_attribute'] = $parent_order_data->attributes;
                $parent_order_data_attribute = json_decode($this->Data['parent_order_data_attribute']);

                if(is_string($parent_order_data_attribute->task_method_details)){
                    $array[] = $parent_order_data_attribute->task_method_details;
                    $parent_order_data_attribute->task_method_details = $array;
                }

                if(is_string($parent_order_data_attribute->sort_type)){
                    $key[] = $parent_order_data_attribute->sort_type;
                    $parent_order_data_attribute->sort_type = $key;
                }

                if(is_string($parent_order_data_attribute->task_number)){
                    $arr[] = $parent_order_data_attribute->task_number;
                    $parent_order_data_attribute->task_number = $arr;
                }
                $this->Data['parent_order_data_attribute'] = json_encode($parent_order_data_attribute);
            }
        } else if ($task_type == TASK_TYPE_PDD) {
            $this->Data['PageTitle'] = '发布拼多多任务';
            $this->Data['task_type'] = TASK_TYPE_PDD;
            $this->Data['TargetPage'] = 'page_new_task_pinduoduo';
            $platform_type          = PLATFORM_TYPE_PINDUODUO;
            $this->Data['templates_data'] = $this->taskengine->get_templates($this->get_seller_id(), $platform_type);
            $this->Data['shopsWithAddress'] = $this->taskengine->get_shops_with_address($this->get_seller_id());
            if(!empty($parent_order_id) && $parent_order_id > 0){
                $parent_order_data = $this->taskengine->get_parent_order_info(decode_id($parent_order_id));
                $this->Data['parent_order_data_attribute'] = $parent_order_data->attributes;
            }
        } else if ($task_type == TASK_TYPE_DT) { // TODO... Added by Ryan.
            $this->Data['PageTitle'] = '发布多天任务';
            $this->Data['task_type'] = TASK_TYPE_DT;
            $this->Data['TargetPage'] = 'page_new_task_duotian';
            $platform_type          = PLATFORM_TYPE_TAOBAO;
            $this->Data['templates_data'] = $this->taskengine->get_templates($this->get_seller_id(), $platform_type);
            $this->Data['shopsWithAddress'] = $this->taskengine->get_shops_with_address($this->get_seller_id());

            if(!empty($parent_order_id) && $parent_order_id > 0) {
                $parent_order_data = $this->taskengine->get_parent_order_info(decode_id($parent_order_id));
                $this->Data['parent_order_data_attribute'] = $parent_order_data->attributes;
            }

            $config = load_config('shang');
            $this->Data['task_behaviors'] = $config['task_behaviors'];
            $this->Data['sp'] = 4; // 定界位置  店内浏览 | 店外浏览
            $this->Data['task_ways'] = $config['task_ways'];
        }
        else {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
        }

        $this->Data['commission_discount'] = $this->hiltoncore->get_seller_commission_discount($this->get_seller_id());
        $this->load->view('frame_main', $this->Data);
    }

    public function pay()
    {
        $order_id = $this->input->get('order_id', TRUE);

        if (empty($order_id) || !is_numeric($order_id)) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
            $this->load->view('frame_main', $this->Data);
            return;
        }
        //hilton_task_parent_orders
        $this->Data['order_info'] = $this->taskengine->get_parent_order_info($order_id);

        if (empty($this->Data['order_info']) || $this->Data['order_info']->status != Taskengine::TASK_STATUS_DZF || $this->Data['order_info']->seller_id != $this->get_seller_id()) {
            $this->Data['TargetPage'] = 'page_message';
            $this->Data['message'] = '页面不存在';
            $this->load->view('frame_main', $this->Data);
            return;
        }

        $this->Data['TargetPage'] = 'page_task_pay';
        //user_members用户表，查询余额
        $this->Data['balance'] = $this->paycore->get_balance($this->get_seller_id());
        $this->load->view('frame_main', $this->Data);
    }

    // TODO... added by HKF.
    public function appeal_list()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        //$this->Data['seller_name'] = $this->input->get('seller_name', TRUE);
        //$this->Data['buyer_name'] = $this->input->get('buyer_name', TRUE);

        $this->Data['task_type'] = $this->input->get('task_type', TRUE);
        $this->Data['task_id'] = $this->input->get('task_id', TRUE);
        $this->Data['parent_order_id'] = $this->input->get('parent_order_id', TRUE);
        $this->Data['createDate'] = $this->input->get('createDate', TRUE);
        $this->Data['status'] = $this->input->get('status', TRUE);
        $this->Data['seller_id'] = $this->get_seller_id();

        $this->load->model('reject');
        $this->Data['data'] = $this->reject->get_reject_list($this->Data);

        //print_r($this->Data['data']);exit;

        $this->Data['TargetPage'] = 'appeal/list';
        $this->load->view('frame_main', $this->Data);

    }


}
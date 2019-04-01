<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务信息</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>任务单号</th>
                        <th>父订单号</th>
                        <th>订单起止时间</th>
                        <th>接单时间</th>
                        <th>关键字</th>
                        <th>接单淘宝账号</th>
                        <th>任务状态</th>
                        <th>任务做单提交时间</th>
                        <th>最后一次操作时间</th>
                        <th>任务完成度（天）</th>
                        <th>下次开始时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo encode_id($data->id); ?></td>
                        <td><?php echo encode_id($data->parent_order_id); ?></td>
                        <td><?php echo $data->start_time . ' - ' . $data->end_time; ?></td>
                        <td><?php if (empty($data->gmt_taking_task)) echo '还未接单'; else echo $data->gmt_taking_task; ?></td>
                        <td><?php echo $data->task_method_details; ?></td>
                        <td><?php if (empty($data->buyer_tb_nick)) echo '还未接单'; else echo $data->buyer_tb_nick; ?></td>
                        <td style="color:red;font-size:22px;"><?php echo Taskengine::get_status_name($data->status); ?></td>
                        <td><?php if (empty($data->task_submit_time)) echo '还未做单'; else echo $data->task_submit_time; ?></td>
                        <td><?php if (!empty($data->gmt_update)) echo $data->gmt_update; ?></td>
                        <td><?php echo $data->cur_task_day , ' / ' , $data->task_days; ?></td>
                        <td><?php echo $data->next_start_time; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务要求</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>过滤黑号</th>
                        <th>收藏商品</th>
                        <th>加入购物车下单</th>
                        <th>假聊</th>
                        <th>竞品收藏</th>
                        <th>竞品加购物车</th>
                        <th>放单模式</th>
                        <th>花呗设置</th>
                        <th>性别限制</th>
                        <th>年龄限制 </th>
                        <th>等级限制</th>
                        <th>快递方式</th>
                        <th>评价方式</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $data->is_blacklist==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_collection==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_add_cart==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_fake_chat==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_compete_collection==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_compete_add_cart==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data->is_preferred==1 ? '优先模式，优先派送给会员接单' : '普通模式' ;?></td>
                        <td><?php echo $data->is_huabei==1 ? '只允许开通花呗的会员接单' : '不限制' ;?></td>
                        <td><?php echo $data->sex_limit=='na' ? '不限' : $data->sex_limit ;?></td>
                        <td><?php 
                                switch ($data->age_limit) {
                                    case '15':
                                        echo '15-25岁';
                                        break;
                                    case '26':
                                        echo '26-35岁';
                                        break;
                                    case '36':
                                        echo '36-45岁';
                                        break;
                                    case '46':
                                        echo '46-55岁';
                                        break;
                                    case '56':
                                        echo '56岁以上';
                                        break;
                                    default:
                                        echo '不限制';
                                        break;
                                }
                            ?>  
                        </td>
                        <td><?php
                            switch ($data->tb_rate_limit) {
                                case 3:
                                    echo '3心';
                                    break;
                                case 4:
                                    echo '4心';
                                    break;
                                case 5:
                                    echo '5心';
                                    break;
                                case 6:
                                    echo '1钻';
                                    break;
                                case 7:
                                    echo '2钻';
                                    break;
                                case 8:
                                    echo '3钻';
                                    break;
                                case 9:
                                    echo '4钻';
                                    break;
                                case 10:
                                    echo '5钻';
                                    break;
                                case 11:
                                    echo '1皇冠';
                                    break;
                                case 12:
                                    echo '2皇冠';
                                    break;
                                case 13:
                                    echo '3皇冠';
                                    break;
                                case 14:
                                    echo '4皇冠';
                                    break;
                                case 15:
                                    echo '5皇冠 ';
                                    break;
                                default:
                                    echo '不限制';
                                    break;
                            }
                        ?></td>
                        <td><?php echo $data->express_type == 'na' ? '商家快递' : '平台快递' ;?></td>
                        <td><?php
                            switch ($data->comment_type) {
                                case '1':
                                    echo '普通好评';
                                    break;
                                case '2':
                                    echo '指定内容';
                                    break;
                                case '3':
                                    echo '指定图片';
                                    break;
                            }
                        ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务金额</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered" style="width: 30%;">
                    <thead>
                    <tr>
                        <th>任务本金</th>
                        <th>实付金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="font-size: 20px;"><?php echo $data->single_task_capital; ?></td>
                        <?php if ($data->real_task_capital > $data->single_task_capital): ?>
                            <td style="color: #FF0000;font-size: 20px;"><?php echo $data->real_task_capital; ?></td>
                        <?php elseif ($data->real_task_capital < $data->single_task_capital): ?>
                            <td style="color: #009900;font-size: 20px;"><?php echo $data->real_task_capital; ?></td>
                        <?php else: ?>
                            <td style="font-size: 20px;"><?php echo $data->real_task_capital; ?></td>
                        <?php endif ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务进展</div>
            </div>
            <div class="bootstrap-admin-panel-content">

                <?php $ct = count($show_data);foreach ($show_data as $k => $val) { $ct--; ?>
                    <table class="table table-bordered" style="margin-bottom: 20px;">
                        <thead>
                        <tr>
                            <th colspan="2">第 <?php echo $k?> 天</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="col-md-1">操作说明</td>
                            <td><?php echo $val['of']; ?></td>
                        </tr>
                        <tr>
                            <td>做单截图说明</td>
                            <td><?php echo implode('，', $val['mo']); ?></td>
                        </tr>
                        <tr>
                            <td>做单截图</td>
                            <td>
                                <?php if (!empty($val['imgs'])) { ?>
                                <?php foreach ($val['imgs'] as $val2) {?>
                                    <?php echo get_prov_pic_ele($val2); ?>
                                <?php }?>
                                <?php } else {?>
                                未上传
                                <?php }?>
                            </td>

                        </tr>
                        <?php if($ct <= 0):?>
                            <tr>
                                <td>付款截图</td>
                                <td><?php echo get_prov_pic_ele($data->fukuan_prove_pic); ?></td>
                            </tr>
                        <?php endif;?>
                        </tbody>
                    </table>
                <?php }?>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>操作内容</th>
                        <th>操作者</th>
                        <th>结果</th>
                    </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td>商品核对结果</td>
                            <td>买家</td>
                            <td><?php if ($data->item_check_status == STATUS_ENABLE) echo '已核对成功'; else echo '还未核对'; ?></td>
                        </tr>

                        <tr>
                            <td>商品订单号</td>
                            <td>买家</td>
                            <td><?php echo $data->order_number; ?></td>
                        </tr>
                        <?php if ($data->status == Taskengine::TASK_STATUS_MJSH): ?>
                            <tr>
                                <td>商家审核</td>
                                <td>商家</td>
                                <td style="font-size:18px;color:red;">
                                    <a href="javascript:;" data-buyer-id="<?php echo $data->buyer_id;?>" data-id="<?php echo $data->id; ?>" data-url="<?php echo base_url('requests/task_operation_handle'); ?>" data-task-type="<?php echo $task_type; ?>" data-conclusion="<?php echo SELLER_CONCLUSION_TASK_OK; ?>" class="btn btn-sm btn-primary btn-check">审核通过</a>
                                    <a href="javascript:;" data-buyer-id="<?php echo $data->buyer_id;?>" data-id="<?php echo $data->id; ?>" data-url="<?php echo base_url('requests/task_operation_handle'); ?>" data-task-type="<?php echo $task_type; ?>" data-conclusion="<?php echo SELLER_CONCLUSION_TASK_BAD; ?>" class="btn btn-sm btn-danger btn-check">审核不通过</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>好评截图</td>
                            <td>买家</td>
                            <td><?php echo get_prov_pic_ele($data->haoping_prove_pic); ?></td>
                        </tr>
                    </tbody>
                    <?php if ($data->status == Taskengine::TASK_STATUS_HPSH): ?>
                        <tr>
                            <td>好评审核</td>
                            <td>商家</td>
                            <td>
                                <a href="javascript:;" data-buyer-id="<?php echo $data->buyer_id;?>" data-id="<?php echo $data->id; ?>" data-url="<?php echo base_url('requests/task_operation_handle'); ?>" data-task-type="<?php echo $task_type; ?>" data-conclusion="<?php echo SELLER_CONCLUSION_REVIEW_OK; ?>" class="btn btn-sm btn-primary btn-check">审核通过</a>
                                <a href="javascript:;" data-buyer-id="<?php echo $data->buyer_id;?>" data-id="<?php echo $data->id; ?>" data-url="<?php echo base_url('requests/task_operation_handle'); ?>" data-task-type="<?php echo $task_type; ?>" data-conclusion="<?php echo SELLER_CONCLUSION_REVIEW_BAD; ?>" class="btn btn-sm btn-danger btn-check">审核不通过</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>




            </div>
        </div>
    </div>
</div>
<div class="control-group" style="margin:20px 0;">
    <div class="controls">
        <div id="error_display" class="alert"></div>
    </div>
</div>

<!-- ui-dialog -->
<div id="dialog_simple" title="" style="font-size: 20px;color: #FF0000;">
    <p></p>
</div>

<script type="text/javascript">
    $(function () {
        // 简单对话框
        $('#dialog_simple').dialog({
            autoOpen: false,
            //modal: true,
            width: 500,
            buttons: {
                "取 消": function () {
                    $(this).dialog("close");
                    $('.btn-check').removeClass('disabled');
                    $('.btn-check').attr("disabled", false);
                    return;
                },
                "确认通过": function () {
                    $(this).dialog("close");
                    ajax_request(
                        $('.btn-check').data('url'),
                        {
                            task_type: $('.btn-check').data('task-type'),
                            task_id: $('.btn-check').data('id'),
                            buyer_id: $('.btn-check').data('buyer-id'),
                            conclusion: $('.btn-check').data('conclusion'),
                        },
                        function (e) {
                            if (e.code == CODE_SUCCESS) {
                                location.reload();
                            } else {
                                show_error_message(e.msg);
                                $('.btn-check').removeClass('disabled');
                                $('.btn-check').attr("disabled", false);
                            }
                        });
                }
            }
        });

        // 赋值
        <?php echo 'const SELLER_CONCLUSION_TASK_BAD=' . SELLER_CONCLUSION_TASK_BAD . ';'; ?>
        <?php echo 'const SELLER_CONCLUSION_REVIEW_BAD=' . SELLER_CONCLUSION_REVIEW_BAD . ';'; ?>
        <?php
            if ($data->real_task_capital > $data->single_task_capital){
                $warnType = 2; // 警告提示，允许通过
            } else {
                $warnType = 1; // 正常情况
            }
            echo 'const SELLER_CONCLUSION_TASK_OK=' . SELLER_CONCLUSION_TASK_OK . ';';
        ?>
        // 警告类型
        var warnType = <?php echo $warnType?>;
        var realAmount = <?php echo $data->real_task_capital?>;

        // 审核操作按钮（通过\不通过）
        $('.btn-check').click(function (e) {

            e.preventDefault();
            var that = $(this);

            that.addClass('disabled');
            that.attr("disabled", true);

            if ($(this).data('conclusion') == SELLER_CONCLUSION_TASK_OK) {
                // 实付金额必须大于0
                if (realAmount <= 0) {
                    show_error_message('实付金额有误，请检查！');
                    that.removeClass('disabled');
                    that.attr("disabled", false);
                    return;
                }

                // 实付金额超出任务本金
                if (warnType == 2) {
                    // 唤起对话框
                    $('#dialog_simple').html("<p>【实付金额】大于【任务本金】</p> <p>&nbsp;&nbsp;确认通过后系统将会多从您账户里直接扣去差额！</p>");
                    $('#dialog_simple').dialog('open');
                } else {
                    // 金额正常的情况
                    ajax_request(
                        $(this).data('url'),
                        {
                            task_type: $(this).data('task-type'),
                            task_id: $(this).data('id'),
                            buyer_id: $(this).data('buyer-id'),
                            conclusion: $(this).data('conclusion'),
                        },
                        function (e) {
                            if (e.code == CODE_SUCCESS) {
                                location.reload();
                            } else {
                                show_error_message(e.msg);
                                that.removeClass('disabled');
                                that.attr("disabled", false);
                            }
                        }
                    );
                }
            } else {
                // 审核不通过
                if ($(this).data('conclusion') == SELLER_CONCLUSION_TASK_BAD || $(this).data('conclusion') == SELLER_CONCLUSION_REVIEW_BAD) {
                    var reject_reason = prompt("请输入审核不通过的原因");
                    if (reject_reason == null || reject_reason == '') {
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                        return;
                    }
                }
                ajax_request(
                    $(this).data('url'),
                    {
                        task_type: $(this).data('task-type'),
                        task_id: $(this).data('id'),
                        buyer_id: $(this).data('buyer-id'),
                        conclusion: $(this).data('conclusion'),
                        reject_reason: reject_reason
                    },
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            show_error_message(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    }
                );
            }
        });

        $(".fancybox").fancybox();
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
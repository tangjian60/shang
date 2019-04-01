<?php if (!empty($audit_cnt)): ?>
    <div class="row row-0">
        <div id="audit-needed-msg" class="alert alert-danger hand-icon" data-target="<?php echo base_url('task/audit?task_type=');
        if (!empty($task_type)) echo $task_type; ?>">
            您有<?php echo $audit_cnt; ?>笔待审核订单，请及时审核并返款。
        </div>
    </div>
<?php endif; ?>
<div class="row">
    <div class="col-md-12">
        <div class="col-lg-12">
            <div class="bootstrap-admin-back-to-parent panel panel-default">
                <form id="form-filter" class="form-inline" action="<?php echo base_url('task/browse'); ?>" method="GET">
                    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
                    <input type="hidden" id="exportexcel" name="exportexcel" value="0">
                    <div class="row row-0">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>任务类型: </label>
                                <select class="form-control" name="task_type">
                                    <?php
                                    $task_array = array(
                                        TASK_TYPE_LL => '流量单',
                                        TASK_TYPE_DF => '垫付单',
                                        TASK_TYPE_DT => '多天垫付单',
                                        TASK_TYPE_PDD => '拼多多'
                                    );

                                    foreach ($task_array as $k => $v) {
                                        echo '<option value="' . $k . '"';
                                        if (isset($task_type) && $k == $task_type) {
                                            echo ' selected';
                                        }
                                        echo '>' . $v . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>接单时间: </label>
                                <input placeholder="开始时间" class="form-control format_datetime" name="jd_start_time" value="<?php if (!empty($jd_start_time)) echo $jd_start_time; ?>">
                                -
                                <input placeholder="结束时间" class="form-control format_datetime" name="jd_end_time" value="<?php if (!empty($jd_end_time)) echo $jd_end_time; ?>">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-0">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>订单状态: </label>
                                <select name="task_status" class="form-control">
                                    <option value="">全部</option>
                                    <?php
                                    foreach (Taskengine::get_all_status() as $k => $v) {
                                        if ($k == Taskengine::TASK_STATUS_DZF || $k == Taskengine::TASK_STATUS_PTSH || $k == Taskengine::TASK_STATUS_PTSH_BTG) continue;
                                        echo '<option value="' . $k . '"';
                                        if (isset($task_status) && $k == $task_status) {
                                            echo ' selected';
                                        }
                                        echo '>' . $v . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>关联店铺: </label>
                                <select class="form-control" name="bind_shop">
                                    <option value="">全部店铺</option>
                                    <?php
                                    foreach ($shop_data as $v) {
                                        echo '<option value="' . $v->id . '"';
                                        if (!empty($bind_shop) && $bind_shop == $v->id) {
                                            echo ' selected';
                                        }
                                        echo '>';
                                        switch ($v->shop_type) {
                                            case SHOP_TYPE_TAOBAO:
                                                echo '[淘宝]';
                                                break;
                                            case SHOP_TYPE_TMALL:
                                                echo '[天猫]';
                                                break;
                                        }
                                        echo $v->shop_name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-0">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>父订单编号 : </label>
                                <input placeholder="父订单编号" class="form-control" name="parent_order_id" value="<?php if (!empty($parent_order_id)) echo $parent_order_id; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>任务编号 : </label>
                                <input placeholder="任务编号" class="form-control" name="task_id" value="<?php if (!empty($task_id)) echo $task_id; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>买手淘宝号 : </label>
                                <input placeholder="买手淘宝号" class="form-control" name="buyer_taobao_nick" value="<?php if (!empty($buyer_taobao_nick)) echo $buyer_taobao_nick; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row row-0" style="margin-top:30px;">
                        <div class="col-md-6">
                            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">提交查询</a>
                            <a id="btn-export-excel" href="javascript:;" class="btn btn-success" style="margin-left:8px;">订单导出</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($data)):?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务条件</div>
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
                        <td><?php echo $data[0]->is_blacklist==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data[0]->is_collection==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data[0]->is_add_cart==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data[0]->is_fake_chat==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data[0]->is_compete_collection==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data[0]->is_compete_add_cart==1 ? '是' : '否' ;?></td>
                        <td><?php echo $data[0]->is_preferred==1 ? '优先模式，优先派送给会员接单' : '普通模式' ;?></td>
                        <td><?php echo $data[0]->is_huabei==1 ? '只允许开通花呗的会员接单' : '不限制' ;?></td>
                        <td><?php echo $data[0]->sex_limit=='na' ? '不限' : $data[0]->sex_limit ;?></td>
                        <td><?php
                                switch ($data[0]->age_limit) {
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
                            switch ($data[0]->tb_rate_limit) {
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
                        <td><?php echo $data[0]->express_type == 'na' ? '商家快递' : '平台快递' ;?></td>
                        <td><?php
                            switch ($data[0]->comment_type) {
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
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">订单明细</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="5%"><input id="ckx-select-all" type="checkbox"></th>
                        <th>父订单编号</th>
                        <th>任务编号</th>
                        <th width="20%">宝贝</th>
                        <th>订单起止时间</th>
                        <th>接单淘宝账号</th>
                        <th>接单时间</th>
                        <th>任务做单提交时间</th>
                        <th>快递单号</th>
                        <th>订单状态</th>
                        <?php if ($task_type == 'DUOTIAN'):?>
                            <th>任务进度（天）</th>
                            <th>下次做单时间</th>
                        <?php endif; ?>
                        <th width="15%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="3">无记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $v): ?>
                            <tr>
                                <td>
                                    <?php if ($v->status == Taskengine::TASK_STATUS_DJD || $v->status == Taskengine::TASK_STATUS_MJSH): ?>
                                        <input class="ckx-task-item" type="checkbox" value="<?php echo $v->id; ?>">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo encode_id($v->parent_order_id); ?></td>
                                <td><?php echo encode_id($v->id); ?></td>

                                <td>
                                    <a href="<?php echo CDN_DOMAIN . $v->item_pic; ?>" class="fancybox"><img class="item-pic-box" src="<?php echo CDN_DOMAIN . $v->item_pic; ?>"></a>
                                    <a href="<?php echo $v->item_url; ?>" title="<?php echo $v->item_title; ?>" target="_blank"><?php echo beauty_display($v->item_title, 6); ?></a>
                                </td>
                                <td><?php echo $v->start_time . ' - ' . $v->end_time; ?></td>
                                <td><?php echo $v->buyer_tb_nick; ?></td>
                                <td><?php echo $v->gmt_taking_task; ?></td>
                                <td><?php echo $v->task_submit_time; ?></td>
                                <td>
                                    <?php if (!empty($v->express_type) && $v->express_type == '圆通快递'){?>
                                            圆通快递- <?php echo$v->express_number;?>
                                            <?php if ($v->express_reason){?>"
                                                (<?php echo $v->express_reason;}?>)
                                        <?php }else{?>
                                            商家快递
                                        <?php }?>
                                </td>
                                <td><?php echo Taskengine::get_status_name($v->status); ?></td>

                                <?php if ($task_type == 'DUOTIAN'):?>
                                    <td><?php echo $v->cur_task_day , ' / ' , $v->task_days ?></td>
                                    <td><?php echo $v->next_start_time ?></td>
                                <?php endif; ?>


                                <td>
                                    <?php if ($v->status == Taskengine::TASK_STATUS_MJSH || $v->status == Taskengine::TASK_STATUS_HPSH) : ?>
                                        <a href="<?php echo base_url('task/details?task_id=' . encode_id($v->id) . '&task_type=' . $task_type); ?>" class="btn btn-sm btn-info">审核</a>
                                    <?php else: ?>
                                        <a href="<?php echo base_url('task/details?task_id=' . encode_id($v->id) . '&task_type=' . $task_type); ?>" class="btn btn-sm btn-success">详情</a>
                                    <?php endif; ?>
                                    <?php if ($v->status == Taskengine::TASK_STATUS_DJD): ?>
                                        <a href="javascript;" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('requests/cancel_task_handle'); ?>" data-task-type="<?php if (!empty($task_type)) echo $task_type; ?>" class="btn btn-sm btn-danger btn-cancel-task">撤销</a>
                                    <?php endif; ?>
                                    <?php if ($v->status == Taskengine::TASK_STATUS_DPJ && $v->express_number == '异常未产生单号'): ?>
                                        <a href="javascript;" data-id="<?php echo $v->id; ?>" data-url="<?php echo base_url('requests/yto'); ?>" data-task-type="<?php if (!empty($task_type)) echo $task_type; ?>" class="btn btn-sm btn-danger btn-yto">快递单号申请</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
                <a id="btn-batch-cancel-task" href="javascript;" data-id="" data-url="<?php echo base_url('requests/cancel_task_handle'); ?>" data-task-type="<?php if (!empty($task_type)) echo $task_type; ?>" class="btn btn-sm btn-danger">批量撤销</a>
                <a id="btn-batch-auditing-task" href="javascript;" data-id="" data-url="<?php echo base_url('requests/auditing_task'); ?>" data-task-type="<?php if (!empty($task_type)) echo $task_type; ?>" class="btn btn-sm btn-info">批量审核</a>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('fragment_pagination'); ?>
<script type="text/javascript">
    $(function () {
        function get_task_ids() {
            var o = new Array();
            $('.ckx-task-item').each(function () {
                if ($(this).prop("checked")) {
                    o.push($(this).val());
                }
            });
            return o;
        }

        function canel_task(u, i, t) {
            ajax_request(u
                , {task_id: i, task_type: t}
                , function (data) {
                    alert(data.msg);
                    location.reload();
                });
        }

        $('#btn-commit-filter').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $('#btn-export-excel').click(function (e) {
            e.preventDefault();
            $('#exportexcel').val(1);
            $('#form-filter').submit();
            $('#exportexcel').val(0);
        });

        $('#audit-needed-msg').click(function (e) {
            e.preventDefault();
            goto_url($(this).data('target'), 30);
        });

        $('#ckx-select-all').click(function (e) {
            $('.ckx-task-item').prop("checked", $(this).prop("checked"));
        });
        
        $('#btn-batch-cancel-task').click(function (e) {
            e.preventDefault();
            var ids = get_task_ids();
            if (ids.length == 0) {
                alert('请先选择要取消的做单任务');
                return;
            }
            if (confirm("确定要撤销" + ids.length + "个任务吗")) {
                canel_task($(this).data('url'), ids, $(this).data('task-type'));
            }
        });

        $('#btn-batch-auditing-task').click(function (e) {
            e.preventDefault();
            var ids = get_task_ids();
            if (ids.length == 0) {
                alert('请先选择要审核的做单任务');
                return;
            }
            if (confirm("确定要审核" + ids.length + "个任务吗")) {
                canel_task($(this).data('url'), ids, $(this).data('task-type'));
            }
        });

        $('.btn-cancel-task').click(function (e) {
            e.preventDefault();
            if (confirm("确定要撤销该任务吗")) {
                canel_task($(this).data('url'), $(this).data('id'), $(this).data('task-type'));
            }
        });

        $('.btn-yto').click(function (e) {
            e.preventDefault();
            if (confirm("确定要重新申请快递单号吗")) {
                yto($(this).data('url'), $(this).data('id'), $(this).data('task-type'));
            }
        });

        function yto(u,id, t)
        {
            ajax_request(u
                , {task_id: id, task_type: t}
                , function (data) {
                    alert(data.msg);
                    location.reload();
                });
        }

        $(".format_datetime").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd hh:ii:00',
            autoclose: true
        });

        $(".fancybox").fancybox();
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
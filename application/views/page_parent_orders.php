<div class="row">
    <div class="col-md-12">
        <div class="col-lg-12">
            <div class="bootstrap-admin-back-to-parent panel panel-default">
                <form id="form-filter" class="form-inline" method="GET">
                    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
                    <div class="row row-0">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>订单创建时间: </label>
                                <input placeholder="开始时间" class="form-control format_datetime" name="start_time" value="<?php if (!empty($start_time)) echo $start_time; ?>">
                                -
                                <input placeholder="结束时间" class="form-control format_datetime" name="end_time" value="<?php if (!empty($end_time)) echo $end_time; ?>">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-0">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>店铺: </label>
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
                                            case SHOP_TYPE_PINDUODUO:
                                                echo '[拼多多]';
                                                break;
                                        }
                                        echo $v->shop_name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>订单状态: </label>
                                <select name="order_status" class="form-control">
                                    <option value="">全部</option>
                                    <?php
                                    $task_array = array(
                                        Taskengine::TASK_STATUS_DZF => '待支付',
                                        Taskengine::TASK_STATUS_DJD => '已支付'
                                    );

                                    foreach ($task_array as $k => $v) {
                                        echo '<option value="' . $k . '"';
                                        if (isset($order_status) && $k == $order_status) {
                                            echo ' selected';
                                        }
                                        echo '>' . $v . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">提交查询</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">订单列表</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>订单编号</th>
                        <th>放单时间</th>
                        <th>任务起止时间</th>
                        <th width="20%">宝贝</th>
                        <th>任务类型</th>
                        <th>任务已接单量<span style="font-weight: bold;">/</span>任务总单量</th>
                        <th>放单时间间隔</th>
                        <th>任务金额</th>
                        <th width="15%">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><a href="<?php echo base_url('task/browse?parent_order_id=' . encode_id($v->id) . '&task_type=' . $v->task_type); ?>"><?php echo encode_id($v->id); ?></a></td>
                            <td><?php echo $v->gmt_create; ?></td>
                            <td><?php echo $v->start_time . ' ~ ' . $v->end_time; ?></td>
                            <td>
                                <a href="<?php echo CDN_DOMAIN . $v->item_pic; ?>" class="fancybox"><img class="item-pic-box" src="<?php echo CDN_DOMAIN . $v->item_pic; ?>"></a>
                                <a href="<?php echo $v->item_url; ?>" title="<?php echo $v->item_title; ?>" target="_blank"><?php echo beauty_display($v->item_title, 6); ?></a>
                            </td>
                            <td>
                                <?php
                                switch ($v->task_type) {
                                    case TASK_TYPE_LL:
                                        echo '流量单';
                                        break;
                                    case TASK_TYPE_DF:
                                        echo '垫付单';
                                        break;
                                    case TASK_TYPE_PDD:
                                        echo '拼多多';
                                        break;
                                    case TASK_TYPE_DT:
                                        echo '多天垫付单';
                                        break;
                                    default:
                                        echo '未知';
                                        break;
                                }
                                ?>
                            </td>
                            <td style="padding-left: 50px;"><?php echo $v->task_yijie."/".$v->task_cnt; ?></td>
                            <td>
                                <?php
                                if ($v->hand_out_interval > 0) {
                                    echo $v->hand_out_interval . '分钟';
                                } else {
                                    echo '无间隔';
                                }
                                ?>
                            </td>
                            <td><?php echo '本金' . $v->fee_order_total_capital . '<br>佣金' . $v->fee_order_total_commission . '<br>快递费' . $v->fee_order_total_express; ?></td>
                            <td>
                                <?php if ($v->status == Taskengine::TASK_STATUS_DZF) : ?>
                                    <a href="<?php echo base_url('task/pay?order_id=' . $v->id); ?>" class="btn btn-sm btn-danger">去支付</a>
                                <?php else : ?>
                                    <a href="<?php echo base_url('task/browse?parent_order_id=' . encode_id($v->id) . '&task_type=' . $v->task_type); ?>" class="btn btn-sm btn-success">详情</a>
                                <?php endif ?>
                                    <a href="<?php echo base_url('task/pub?parent_order_id=' . encode_id($v->id) . '&task_type=' . $v->task_type); ?>" class="btn btn-sm btn-success">一键重发</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('fragment_pagination'); ?>
<script type="text/javascript">
    $(function () {
        $('#btn-commit-filter').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

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
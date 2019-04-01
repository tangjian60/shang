<div class="row">
    <div class="col-md-12">
        <div class="col-lg-12">
            <div class="bootstrap-admin-back-to-parent panel panel-default">
                <form id="form-filter" class="form-inline" action="<?php echo base_url('task/appeal_list'); ?>" method="GET">
                    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
                    <input type="hidden" id="exportexcel" name="exportexcel" value="0">
                    <div class="row row-0">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>任务类型：</label>
                                <select class="form-control" name="task_type">
                                    <?php
                                    $task_array = array(
                                        TASK_TYPE_DF => '垫付单',
                                        TASK_TYPE_DT => '多天垫付单',
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
                                <label>申诉日期：</label>
                                <input type="text" class="form-control filter-control format_date" name="createDate" value="<?php if (!empty($createDate)) echo $createDate; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row row-0">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>申诉状态：</label>
                                <select class="form-control filter-control" name="status">
                                    <option value="">不限</option>
                                    <?php
                                    $options = array(
                                        1 => '申诉中',
                                        2 => '已处理-订单继续',
                                        3 => '已处理-关闭订单',
                                        4 => '放弃申诉'
                                    );

                                    foreach ($options as $k => $v) {
                                        echo '<option value="' . $k . '"';
                                        if (isset($status) && $status != '' && $k == $status) {
                                            echo ' selected';
                                        }
                                        echo '>' . $v . '</option>';
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
                    </div>
                    <div class="row row-0" style="margin-top:30px;">
                        <div class="col-md-6">
                            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">提交查询</a>
                            <!--<a id="btn-export-excel" href="javascript:;" class="btn btn-success" style="margin-left:8px;">订单导出</a>-->
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
                <div class="text-muted bootstrap-admin-box-title">申诉单列表</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>子任务编号</th>
                        <th>父任务编号</th>
                        <th>商家手机</th>
                        <th>买手手机</th>
                        <th>需垫付金额</th>
                        <th>佣金</th>
                        <th>发起时间</th>
                        <th>申诉理由</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (empty($data)): ?>
                        <tr>
                            <td colspan="3">无记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $v): ?>
                            <tr>
                                <td><?php echo $v->id; ?></td>
                                <td><?php echo encode_id($v->task_id); ?></td>
                                <td><?php echo encode_id($v->task_pid); ?></td>
                                <td><?php echo $v->seller_mobile; ?></td>
                                <td><?php echo $v->buyer_mobile; ?></td>
                                <td><?php echo $v->task_capital; ?></td>
                                <td><?php echo $v->task_commission; ?></td>
                                <td><?php echo $v->gmt_create; ?></td>
                                <td><?php echo $v->reject_reason_txt; ?></td>
                                <td><?php echo isset($options[$v->state]) ? $options[$v->state] : '未知状态'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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


        $(".format_date").datetimepicker({
            minView: 'month',
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
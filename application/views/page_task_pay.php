<form class="form-horizontal">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">订单支付</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;font-size:18px;color:red;">
            <div class="form-group" style="height:20px;"></div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    单号：&nbsp;&nbsp;<?php echo encode_id($order_info->id); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    单量：&nbsp;&nbsp;<?php echo $order_info->task_cnt; ?>&nbsp;&nbsp;单
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    本金：&nbsp;&nbsp;<?php echo $order_info->fee_order_total_capital; ?>&nbsp;&nbsp;元
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    佣金：&nbsp;&nbsp;<?php echo $order_info->fee_order_total_commission; ?>&nbsp;&nbsp;元
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    快递：&nbsp;&nbsp;<?php echo $order_info->fee_order_total_express; ?>&nbsp;&nbsp;元
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    总计：&nbsp;&nbsp;<?php echo $order_info->fee_order_total_capital + $order_info->fee_order_total_commission + $order_info->fee_order_total_express; ?>&nbsp;&nbsp;元
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-2"></div>
                <div class="col-md-7">
                    余额：&nbsp;&nbsp;<?php echo $balance; ?>&nbsp;&nbsp;元
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-1 control-label"></label>
                <div class="col-md-9">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
        </div>
    </div>
</form>
<div style="text-align:center;margin:50px auto;">
    <a class="btn btn-lg btn-primary" href="<?php echo base_url('pages/top_up'); ?>">充值</a>
    <button id="btn-task-pay" type="button" class="btn btn-lg btn-success" data-url="<?php echo base_url('requests/task_pay_handle'); ?>" data-target="<?php echo base_url('task/parent_orders'); ?>" data-order-id="<?php echo $order_info->id; ?>">付款
    </button>
</div>
<script type="text/javascript">
    $(function () {
        <?php
        echo 'const total_amount=' . ($order_info->fee_order_total_capital + $order_info->fee_order_total_commission + $order_info->fee_order_total_express) . ';';
        echo 'const balance=' . $balance . ';';
        ?>

        if (total_amount > balance) {
            var that = $('#btn-task-pay');
            that.addClass('disabled');
            that.attr("disabled", true);
            that.html('余额不足');
        }

        $('#btn-task-pay').click(function (e) {
            e.preventDefault();
            var that = $(this);
            hide_error_message();
            that.addClass('disabled');
            that.attr("disabled", true);
            that.html('支付中……');
            ajax_request(
                that.data('url'),
                {'order_id':that.data('order-id')},
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        that.html('支付完成');
                        show_success_message('支付成功，正在生成做单任务，请稍后……');
                        goto_url(that.data('target'), 3000);
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                        that.html('付款');
                    }
                });
        });
    });
</script>
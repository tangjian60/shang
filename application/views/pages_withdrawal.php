<?php if (empty($bank_info)) : ?>
    <div class="container">
        <div class="row">
            <h2 class="form-signin form-signin-heading">很抱歉，您还没有绑定银行卡，无法取现</h2>
        </div>
        <div class="row">
            <a href="<?php echo base_url('bank/add_bank'); ?>" class="btn btn-lg btn-primary btn-block" type="button" style="margin:20px auto;width:300px;">去绑定银行卡</a>
        </div>
    </div>
<?php else: ?>
<div data-page="seller-withdrawal" class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">取现</h3>
    </div>
    <div class="panel-body" style="padding:50px 0;">
        <form class="form-horizontal form-withdrawal">
            <div class="form-group">
                <label class="col-md-3 control-label">
                    手机号码/用户名
                </label>
                <div class="col-md-8">
                    <input class="form-control" type="text" name="user_name" id="user_name" value="<?php echo $user_info->user_name; ?>" disabled="disabled">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    手机验证码<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <input placeholder="请输入验证码" class="form-control" type="text" name="prov_code" style="display: inline; width: 30%">
                    <a href="#"class="btn btn-sm btn-danger navbar-btn" id="CheckMobilePhone" data-url="<?php echo base_url('withdrawal/send_sms_code'); ?>" style="background: #ff9500;line-height:0px;color:white; border: none; height: 30px; text-align: center; padding-top: 15px">获取验证码</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    银行账户<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <select class="form-control" name="bank_card_id">
                        <option value="" selected>请选择开户银行账号</option>
                        <?php
                        foreach ($bank_info as $bank){
                            echo '<option value='. $bank['id'] .'>'. $bank['bank_card_num'] .'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    提现金额<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <input type="number" id="input-user-withdraw" class="form-control" placeholder="请输入提现金额" name="withdrawal_amount" onmousewheel="return false;" min="0">
                    <span style="font-size:13px;color:black;margin-left:10px;margin-top: 10px">
                        余额<span style="color: red;font-weight:bold; font-size: 15px; margin:0 3px"><?php echo $user_info->balance; ?></span>元，
                        <?php
                        if ($user_info->freezing_amount > 0) {
                            echo '冻结金额<span class="important-numbers">' . $user_info->freezing_amount . '</span>元，';
                        }
                        ?>
                        最少提现金额<span style="color: red;font-weight:bold; font-size: 15px; margin:0 3px"><?php echo MIN_WITHDRAW_AMOUNT; ?></span>元
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-8">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
        </form>
        <div id="dialog-withdrawal-success" class="modal fade" tabindex="-1" role="dialog" data-target="<?php echo base_url('withdrawal/withdrawal_list'); ?>">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">提现申请成功</h4>
                    </div>
                    <div class="modal-body">
                        <p>恭喜，提现成功，等待审核！</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-primary" data-dismiss="modal">查看</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="text-align:center;margin:50px auto;">
    <button id="btn_submit_withdrawal" type="button" class="btn btn-lg btn-success"  data-min-amount="<?php echo MIN_WITHDRAW_AMOUNT; ?>" data-balance="<?php echo $user_info->balance; ?>" data-freezing-amount="<?php echo $user_info->freezing_amount; ?>" data-url="<?php echo base_url('requests/withdrawal_handle'); ?>">提交取现</button>
</div>
<script type="text/javascript">
    $(function () {
        $('#CheckMobilePhone').click(function (e) {
            var that = $(this);
            hide_error_message();
            ajax_request(
                that.data('url'),
                {'user_name':$('#user_name').val()},
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        that.addClass('disabled');
                        that.attr("disabled", true);
                        alert('验证码已发送，请查收');

                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
            });
        });


        $('#btn_submit_withdrawal').click(function (e) {
            hide_error_message();
            e.preventDefault();
            var form_data = $('.form-withdrawal').formToJSON();
            var that = $(this);
            var number = /^\d{1,}$/;
            var min_amount = that.data('min-amount');
            var balance = that.data('balance');
            var freezing_amount = that.data('freezing-amount');

            if (invalid_parameter(form_data)) {
                show_error_message('所有字段均不能为空，请填写所有字段');
                return;
            }

            if (!number.test(form_data.withdrawal_amount)) {
                show_error_message('请填写正确的提现金额');
                return;
            }

            if (form_data.withdrawal_amount < min_amount) {
                show_error_message('提现金额不能低于' + that.data('min-amount') + '元');
                return;
            }

            if (form_data.withdrawal_amount > balance - freezing_amount) {
                show_error_message('提现金额不能超出余额减去冻结金额');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        $('#dialog-withdrawal-success').modal('show');
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });

        $('#dialog-withdrawal-success').on('hidden.bs.modal', function () {
            goto_url($(this).data('target'), 50);
        });
    });
</script>
<?php endif; ?>
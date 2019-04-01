<?php if (empty($bank_info)) : ?>
    <div class="container">
        <div class="row">
            <h2 class="form-signin form-signin-heading">很抱歉，您还没有绑定银行卡，无法提现</h2>
        </div>
        <div class="row">
            <a href="<?php echo base_url('pages/add_bankcard'); ?>" class="btn btn-lg btn-primary btn-block" type="button" style="margin:20px auto;width:300px;">去绑定银行卡</a>
        </div>
    </div>
<?php else: ?>
    <div data-page="seller-withdrawal" class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">提现</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <form class="form-horizontal form-seller-withdraw">
                <div class="form-group">
                    <label class="col-md-3 control-label">请选择要提现到的银行卡</label>
                    <div class="col-md-8">
                        <select class="form-control" name="bankcard_id">
                            <?php
                            foreach ($bank_info as $v) {
                                echo '<option value=' . $v->id . '>' . $v->bank_name . '(' . $v->bank_card_num . ')</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">提现金额</label>
                    <div class="col-md-8">
                        <input type="number" id="input-seller-withdraw" class="form-control" placeholder="请输入提现金额" name="withdraw_amount" onmousewheel="return false;" min="0">
                        <span style="font-size:13px;color:black;">提现手续费<?php echo SELLER_WITHDRAW_SERVICE_FEE * 100; ?>%，余额<span style="color: red;font-weight:bold; font-size: 15px; margin:0 3px"><?php echo $user_info->balance; ?></span>元，最少提现金额<span style="color: red;font-weight:bold; font-size: 15px; margin:0 3px"><?php echo MIN_WITHDRAW_AMOUNT; ?></span>元</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">短信验证码</label>
                    <div class="col-md-8">
                        <input placeholder="请输入验证码" class="form-control" type="text" name="prov_code" style="display: inline; width: 30%">
                        <a href="javascript:;" class="btn btn-sm" id="send_sms_code" data-url="<?php echo base_url('requests/send_check_SMS_code'); ?>" style="background:#ff9500;line-height:0px;color:white; border:none;height:30px;text-align:center;padding-top:15px">获取验证码</a>
                        <br>
                        <span style="font-size:13px;color:black;">短信验证码接收手机<?php echo desensitization($user_info->user_name); ?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8">
                        <div id="error_display" class="alert"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div style="text-align:center;margin:50px auto;">
        <button id="btn-submit-withdraw" type="button" class="btn btn-lg btn-success" data-min-amount="<?php echo MIN_WITHDRAW_AMOUNT; ?>" data-url="<?php echo base_url('requests/withdraw_handle'); ?>">提交申请</button>
    </div>
    <script type="text/javascript">
        $(function () {
            $('#send_sms_code').click(function (e) {
                e.preventDefault();
                var that = $(this);
                hide_error_message();

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    {},
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            show_success_message('验证码已发送，请查收');
                        } else {
                            show_error_message(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            });


            $('#btn-submit-withdraw').click(function (e) {
                e.preventDefault();
                var form_data = $('.form-seller-withdraw').formToJSON();
                var that = $(this);

                hide_error_message();

                var min_amount = that.data('min-amount');

                if (invalid_parameter(form_data)) {
                    show_error_message('所有字段均不能为空，请填写所有字段');
                    return;
                }

                if (!number.test(form_data.withdraw_amount)) {
                    show_error_message('请填写正确的提现金额');
                    return;
                }

                if (form_data.withdraw_amount < min_amount) {
                    show_error_message('提现金额不能低于' + that.data('min-amount') + '元');
                    return;
                }


                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    form_data,
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            show_success_message('提现申请成功');
                        } else {
                            show_error_message(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            });
        });
    </script>
<?php endif; ?>
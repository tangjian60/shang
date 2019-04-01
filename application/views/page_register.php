<div class="container">
    <form class="form-signin" action="" method="post">
        <h2 class="form-signin-heading">注册商家账号</h2>

        <div class="row">
            <input type="text" id="user_account" class="form-control" name="user_account" placeholder="手机号码" required
                   autofocus>
        </div>
        <div class="row">
            <div class="col-md-7" style="margin:0;padding:0;">
                <input type="text" name="prove_code" class="form-control" placeholder="短信验证码" required>
            </div>
            <div class="col-md-5" style="margin:0;padding:0 0 0 5px;">
                <a href="javascript:;" id="btn-get-sms-code" class="btn btn-lg btn-info btn-block form-control" data-url="<?php echo base_url('user/send_sms_code'); ?>" type="button">获取短信验证码</a>
            </div>
        </div>
        <div class="row">
            <input type="password" name="user_passwd" class="form-control" placeholder="请输入密码" required>
        </div>
        <div class="row">
            <input type="password" name="confirm_passwd" class="form-control" placeholder="请再次输入密码" required>
        </div>
        <div class="row">
            <input type="hidden" name="recommend" class="form-control" placeholder="推荐人"
                   value="<?php if (isset($recommend)) echo $recommend; else echo '0'; ?>">
        </div>
        <div class="row">
            <div id="error_display" class="alert"></div>
        </div>
        <div class="row">
            <div class="btn-group btn-group-justified" role="group">
                <div class="btn-group" role="group">
                    <a href="javascript:;" id="btn-registry-submit" data-url="<?php echo base_url('user/registry_handle'); ?>" data-target="<?php echo base_url('user'); ?>" class="btn btn-lg btn-primary">提交注册</a>
                </div>
                <div class="btn-group" role="group">
                    <a href="<?php echo base_url('user'); ?>" class="btn btn-lg btn-danger">返回</a>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function () {
        $('#btn-get-sms-code').click(function (e) {
            e.preventDefault();

            var that = $(this);
            var cell_num = $('#user_account').val();

            hide_error_message();

            if (!mobile_num_pattern.test(cell_num)) {
                show_error_message('请填写正确的手机号码');
                return;
            }

            ajax_request(
                that.data('url'),
                {reg_phone_no: cell_num},
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        that.html('验证码已发送');
                        that.addClass('disabled');
                        that.attr("disabled", true);
                        alert('验证码已发送，请查收');
                    } else {
                        show_error_message(e.msg);
                    }
                });
        });

        $('#btn-registry-submit').click(function (e) {
            e.preventDefault();

            var form_data = $('.form-signin').formToJSON();
            var that = $(this);

            hide_error_message();

            if (invalid_parameter(form_data)) {
                show_error_message('所有字段均不能为空，请填写所有字段');
                return;
            }

            if (!mobile_num_pattern.test(form_data.user_account)) {
                show_error_message('请填写正确的手机号码');
                return;
            }

            if (form_data.confirm_passwd == "" || form_data.confirm_passwd != form_data.user_passwd) {
                show_error_message('两次密码输入不一致');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);
            show_success_message('提交中...');

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        show_success_message('注册成功');
                        goto_url(that.data('target'), 1000);
                    } else {
                        show_error_message(e.msg);
                        that.attr("disabled", false);
                        that.removeClass('disabled');
                    }
                });
        });

        $("#user_account").focus();
    });
</script>
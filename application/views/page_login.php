<div class="container">
    <form class="form-signin" action="" method="post">
        <h2 class="form-signin-heading">商家后台</h2>
        <input type="text" class="form-control" name="seller_account" placeholder="手机号码" required autofocus>
        <input type="password" name="seller_passwd" class="form-control" placeholder="密码" required>
        <div id="error_display" class="alert"></div>
        <button id="btn-seller-login" class="btn btn-lg btn-primary btn-block" type="button"
                data-url="<?php echo base_url('user/login_handler'); ?>" data-target="<?php echo base_url('task/pub'); ?>"
                style="margin:12px 0;">登录
        </button>
        <div class="btn-group btn-group-justified" role="group" aria-label="...">
            <div class="btn-group" role="group">
                <a href="<?php echo base_url('user/register'); ?>" class="btn btn-lg btn-success">注册</a>
            </div>
            <div class="btn-group" role="group">
                <a href="<?php echo base_url('user/forget_passwd'); ?>" class="btn btn-lg btn-warning">重置密码</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(function () {
        $('#btn-seller-login').click(function (e) {
            e.preventDefault();

            var form_data = $('.form-signin').formToJSON();
            var that = $(this);

            hide_error_message();

            if (invalid_parameter(form_data)) {
                show_error_message('所有字段均不能为空，请填写所有字段');
                return;
            }

            if (!mobile_num_pattern.test(form_data.seller_account)) {
                show_error_message('请填写正确的手机号码');
                return;
            }

            that.addClass('disabled');
            that.attr("disabled", true);
            show_success_message('正在登录，请稍等...');

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        show_success_message('登录成功');
                        goto_url(that.data('target'), 1000);
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
    });
</script>
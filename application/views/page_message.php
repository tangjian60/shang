<div class="container">
    <div class="row">
        <h2 class="form-signin form-signin-heading">
            <?php if (isset($message)) {
                echo $message;
            } ?>
        </h2>
    </div>
    <?php if (isset($btn_type) && $btn_type == BTN_TYPE_BACK) : ?>
        <div class="row">
            <a href="<?php echo base_url(); ?>" class="btn btn-lg btn-primary btn-block" type="button" style="margin:12px 0;">返回主页</a>
        </div>
    <?php endif; ?>
</div>
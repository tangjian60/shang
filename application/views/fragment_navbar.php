<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="<?php echo base_url(); ?>" style="font-size:30px;"><?php echo HILTON_NAME; ?>商家端</a>
        </div>
        <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="javascript:;" style="line-height:28px;">欢迎你，<?php echo $UserName; ?></a>
            </li>
            <li>
                <a class="btn-show-balance" href="javascript:;" data-url="<?php echo base_url('requests/get_seller_balance'); ?>" style="line-height:28px;">显示余额</a>
            </li>
            <li>
                <a class="btn btn-sm btn-success navbar-btn" href="<?php echo base_url('pages/top_up'); ?>" style="line-height:0px;color:white;margin-right:10px;">充值</a>
            </li>
            <li>
                <a class="btn btn-sm btn-danger navbar-btn" href="<?php echo base_url('user/log_out'); ?>" style="line-height:0px;color:white;">退出</a>
            </li>
        </ul>
    </div>
</nav>
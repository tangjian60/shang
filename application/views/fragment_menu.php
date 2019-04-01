<?php
$menus = array(
    "home" => "数据大屏",
    "task/pub" => "发布新任务",
    "task/parent_orders" => "父任务订单列表",
    "task/zi_task_list" => "子任务列表查询",
    "task/audit" => "待审核的子任务列表",
    "task/templates" => "任务模板",
    "pages/bills" => "账单",
    "pages/add_shop" => "绑定新店铺",
    "pages/shop_list" => "已绑定的店铺",
    "pages/add_bankcard" => "绑定银行卡",
    "pages/binded_bankcard" => "已绑定的银行卡",
    "pages/withdraw_application" => "提现",
    "pages/withdraw_records" => "提现记录",
    "pages/top_up_records" => "充值记录",
    "pages/promote" => "推广赚金",
    "task/record"=>"买手取消任务单记录",
    "task/appeal_list"=>"申诉任务单列表",
);
?>
<div class="col-sm-3 col-md-2 sidebar bootstrap-admin-col-left">
    <ul class="nav left-ul-a bootstrap-admin-navbar-side"
        style="text-align:center;line-height:26px;clear:none;">
        <?php
        foreach ($menus as $key => $value) {
            $current_url = uri_string() == '' ? 'home' : uri_string();
            $display_key = str_replace('/', '\/', $key);
            echo '<li';
            if (preg_match('/' . $display_key . '/', $current_url)) {
                echo ' class="active"';
            }
            echo '><a href="' . base_url($key) . '">' . $value . '</a></li>';
        }
        ?>
    </ul>
</div>

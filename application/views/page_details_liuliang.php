<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务信息</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>任务单号</th>
                        <th>父订单号</th>
                        <th>接单时间</th>
                        <th>关键字</th>
                        <th>接单淘宝账号</th>
                        <th>任务状态</th>
                        <th>最后一次操作时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo encode_id($data->id); ?></td>
                        <td><?php echo encode_id($data->parent_order_id); ?></td>
                        <td><?php if (empty($data->gmt_taking_task)) echo '还未接单'; else echo $data->gmt_taking_task; ?></td>
                        <td><?php echo $data->task_method_details; ?></td>
                        <td><?php if (empty($data->buyer_tb_nick)) echo '还未接单'; else echo $data->buyer_tb_nick; ?></td>
                        <td><?php echo Taskengine::get_status_name($data->status); ?></td>
                        <td><?php if (!empty($data->gmt_update)) echo $data->gmt_update; ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">任务进展</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>操作内容</th>
                        <th>操作者</th>
                        <th>结果</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>主搜页面</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->zhusou_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>商品核对结果</td>
                        <td>买家</td>
                        <td><?php if ($data->item_check_status == STATUS_ENABLE) echo '已核对成功'; else echo '还未核对'; ?></td>
                    </tr>
                    <tr>
                        <td>主宝贝详情</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->zhubaobei_prove_pic); ?></td>
                    </tr>
                    <tr>
                        <td>副宝贝详情</td>
                        <td>买家</td>
                        <td><?php echo get_prov_pic_ele($data->fubaobei_prove_pic); ?></td>
                    </tr>
                    <?php if (!empty($data->favorite_shop) && $data->favorite_shop != NOT_AVAILABLE): ?>
                        <tr>
                            <td>收藏店铺</td>
                            <td>买家</td>
                            <td><?php echo get_prov_pic_ele($data->favorite_shop_prove_pic); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($data->favorite_item) && $data->favorite_item != NOT_AVAILABLE): ?>
                        <tr>
                            <td>收藏宝贝</td>
                            <td>买家</td>
                            <td><?php echo get_prov_pic_ele($data->favorite_item_prove_pic); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($data->add_cart) && $data->add_cart != NOT_AVAILABLE): ?>
                        <tr>
                            <td>加购物车</td>
                            <td>买家</td>
                            <td><?php echo get_prov_pic_ele($data->add_cart_prove_pic); ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($data->status == Taskengine::TASK_STATUS_MJSH): ?>
                        <tr>
                            <td>商家审核</td>
                            <td>商家</td>
                            <td>
                                <a href="javascript:;" data-buyer-id="<?php echo $data->buyer_id;?>" data-id="<?php echo $data->id; ?>" data-url="<?php echo base_url('requests/task_operation_handle'); ?>" data-task-type="<?php echo $task_type; ?>" data-conclusion="<?php echo SELLER_CONCLUSION_TASK_OK; ?>" class="btn btn-sm btn-primary">审核通过</a>
                                <a href="javascript:;" data-buyer-id="<?php echo $data->buyer_id;?>" data-id="<?php echo $data->id; ?>" data-url="<?php echo base_url('requests/task_operation_handle'); ?>" data-task-type="<?php echo $task_type; ?>" data-conclusion="<?php echo SELLER_CONCLUSION_TASK_BAD; ?>" class="btn btn-sm btn-danger">审核不通过</a>

                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        <?php echo 'const SELLER_CONCLUSION_TASK_BAD=' . SELLER_CONCLUSION_TASK_BAD . ';'; ?>
        <?php echo 'const SELLER_CONCLUSION_REVIEW_BAD=' . SELLER_CONCLUSION_REVIEW_BAD . ';'; ?>

        $('.btn').click(function (e) {
            e.preventDefault();

            $('.btn').addClass('disabled');
            $('.btn').attr("disabled", true);

            if ($(this).data('conclusion') == SELLER_CONCLUSION_TASK_BAD || $(this).data('conclusion') == SELLER_CONCLUSION_REVIEW_BAD) {
                var reject_reason = prompt("请输入审核不通过的原因");
                if (reject_reason == null || reject_reason == '') {
                    $('.btn').removeClass('disabled');
                    $('.btn').attr("disabled", false);
                    return;
                }
            }

            ajax_request(
                $(this).data('url'),
                {
                    task_type: $(this).data('task-type'),
                    task_id: $(this).data('id'),
                    buyer_id:$(this).data('buyer-id'),
                    conclusion: $(this).data('conclusion'),
                    reject_reason: reject_reason
                },
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        location.reload();
                    } else {
                        alert(e.msg);
                        $('.btn').removeClass('disabled');
                        $('.btn').attr("disabled", false);
                    }
                });
        });

        $(".fancybox").fancybox();
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
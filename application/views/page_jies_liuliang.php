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
                        <th>ID</th>
                        <th>姓名</th>
                        <th>银行卡号</th>
                        <th>金额</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $task->id;?></td>
                            <td><?php echo $nick_info->true_name;?></td>
                            <td><?php echo $nick_info->bank_card_num;?></td>
                            <td><?php echo $single_task_commission;?></td>
                            <td>
                                <?php if ($task->status == 16): ?>
                                <a href="javascript:;" data-taskid="<?php echo $task->id;?>" data-single="<?php echo $single_task_commission;?>" data-url="<?php echo base_url('task/jies'); ?>"  class="btn btn-sm btn-primary">确认</a>
                                <?php endif; ?>
                            </td>
                        </tr>
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
            ajax_request(
                $(this).data('url'),
                {
                    task_id: $(this).data('taskid'),
                    single: $(this).data('single'),
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
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
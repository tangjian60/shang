<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a class="btn btn-lg btn-success" href="<?php echo base_url('task/edit_template'); ?>">新建任务模板</a>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>时间</th>
                        <th>电商平台</th>
                        <th>模板名称</th>
                        <th>宝贝</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo substr($v->gmt_create, 0, 10); ?></td>
                            <td>
                                <?php
                                    switch ($v->platform_type){
                                        case PLATFORM_TYPE_TAOBAO:
                                            echo '淘宝';
                                            break;
                                        case PLATFORM_TYPE_PINDUODUO:
                                            echo '拼多多';
                                            break;
                                        default:
                                            echo '未知平台';
                                    }
                                ?>
                            </td>
                            <td><?php echo $v->template_name; ?></td>
                            <td><?php echo '<a href="' . $v->item_url . '" target="_blank">' . $v->item_title . '</a>'; ?></td>
                            <td>
                                <a href="<?php echo base_url('task/edit_template?id=' . $v->id); ?>"><span class="tieyu-back-green tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">编辑</span></a>
                                <a class="btn-delete-task-template" data-url="<?php echo base_url('requests/delete_template_handle'); ?>" data-id="<?php echo $v->id; ?>" href="javascript:;"><span class="tieyu-back-red tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">删除</span></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('.btn-delete-task-template').click(function (e) {
            e.preventDefault();
            var that = $(this);

            if (confirm('确认要删除这个任务模板吗？')) {
                ajax_request(
                    that.data('url'),
                    {template_id: that.data('id')},
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        }
                    });
            }
        });
    });
</script>


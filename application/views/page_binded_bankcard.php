<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>绑定时间</th>
                        <th>真实姓名</th>
                        <th>开户银行</th>
                        <th>银行卡号</th>
                        <th>开户支行</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo $v->gmt_create; ?></td>
                            <td><?php echo $v->true_name; ?></td>
                            <td><?php echo $v->bank_name; ?></td>
                            <td><?php echo $v->bank_card_num; ?></td>
                            <td><?php echo $v->bank_province . $v->bank_city . $v->bank_county . $v->bank_branch; ?></td>
                            <td>
                                <a class="btn btn-danger btn-delete-bankcard" data-url="<?php echo base_url('requests/delete_binded_bankcard'); ?>" data-id="<?php echo $v->id; ?>" href="javascript:;">删除</a>
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
        $('.btn-delete-bankcard').click(function (e) {
            e.preventDefault();
            var that = $(this);
            if (confirm('确定要删除这张银行卡吗?')) {
                ajax_request(
                    that.data('url'),
                    {bankcard_id: that.data('id')},
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        } else {
                            alert(e.msg);
                        }
                    });
            }
        })
    })
</script>
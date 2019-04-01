<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>真实姓名</th>
                        <th>银行卡号</th>
                        <th>开户银行</th>
                        <th>开户地址</th>
                        <th>绑定时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo $v->true_name; ?></td>
                            <td><?php echo $v->bank_card_num; ?></td>
                            <td><?php echo $v->bank_name; ?></td>
                            <td><?php echo $v->bank_province."--".$v->bank_city."--".$v->bank_county."--".$v->bank_branch."支行"; ?></td>
                            <td><?php echo $v->gmt_create; ?></td>
                            <td><a class="btn-delete-task-template" data-url="<?php echo base_url('bank/bank_delete'); ?>" data-id="<?php echo $v->id; ?>" href="javascript:;"><span class="tieyu-back-red tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">删除</span></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<form id="form-filter" class="form-inline" method="GET">
    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
</form>
<?php $this->load->view('fragment_pagination'); ?>
<script type="text/javascript">
    $(function () {
        $('.btn-delete-task-template').click(function(e){
            e.preventDefault();
            var that=$(this);
            if(confirm('确定要删除这张银行卡吗?')){
                ajax_request(
                    that.data('url'),
                    {id: that.data('id')},
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            location.reload();
                        }
                    });
            }
        })
    })
</script>

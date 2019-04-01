<div class="row">
    <div class="col-md-12">
        <div class="col-lg-12">
            <div class="bootstrap-admin-back-to-parent panel panel-default">
                    <form id="form-filter" class="form-inline" method="GET">
                    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
                        <input type="hidden" id="exportexcel" name="exportexcel" value="0">
                    <input type="hidden" id="exportcvs" name="exportcvs" value="0">

                    <div class="row row-0">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>子任务编号 : </label>
                                <input placeholder="点击输入" class="form-control" name="task_id" value="<?php if (!empty($task_id)) echo encode_id($task_id); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>会员id : </label>
                                <input placeholder="点击输入" class="form-control" name="buyer_id" value="<?php if (!empty($buyer_id)) echo encode_id($buyer_id); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>商品id : </label>
                                <input placeholder="点击输入" class="form-control" name="item_id" value="<?php if (!empty($item_id)) echo $item_id; ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>取消日期: </label>
                                <input placeholder="-" class="form-control format_datetime" name="gmt_cancelled" value="<?php if (!empty($gmt_cancelled)) echo $gmt_cancelled; ?>">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row row-0" style="margin-top:30px;">
                        <div class="col-md-6">
                            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">搜索</a>
                            <a id="btn-export-excel" href="javascript:;" class="btn btn-success" style="margin-left:8px;">订单导出</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">账单明细</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="5%">序</th>
                        <th>取消时间</th>
                        <th>子任务编号</th>
                        <th width="20%">宝贝id</th>
                        <th>宝贝标题</th>
                        <th>会员id</th>
                        <th>取消原因</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="3">无记录</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($data as $key=>$v): ?>
                            <tr>
                                <td><?php echo $key+1; ?></td>
                                <td><?php echo $v->gmt_cancelled; ?></td>
                                <td><?php echo encode_id($v->task_id); ?></td>
                                <td><?php echo $v->item_id; ?></td>
                                <td><?php echo $v->item_title; ?></td>
                                <td><?php echo encode_id($v->buyer_id); ?></td>
                                <td><?php echo $v->cancel_reason; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('fragment_pagination'); ?>
<script type="text/javascript">
    $(function () {
        function get_task_ids() {
            var o = new Array();
            $('.ckx-task-item').each(function () {
                if ($(this).prop("checked")) {
                    o.push($(this).val());
                }
            });
            return o;
        }

        function canel_task(u, i, t) {
            ajax_request(u
                , {task_id: i, task_type: t}
                , function (data) {
                    alert(data.msg);
                    location.reload();
                });
        }

        $('#btn-commit-filter').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });

        $('#btn-export-excel').click(function (e) {
            e.preventDefault();
            $('#exportexcel').val(1);
            $('#form-filter').submit();
            $('#exportexcel').val(0);
        });


        $(".format_datetime").datetimepicker({
            language: 'zh-CN',
            format: 'yyyy-mm-dd',
            autoclose: true
        });

        $(".fancybox").fancybox();
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>
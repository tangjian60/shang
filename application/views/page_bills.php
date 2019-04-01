<div class="row">
    <div class="col-md-12">
        <div class="col-lg-12">
            <div class="bootstrap-admin-back-to-parent panel panel-default">
                <form id="form-filter" class="form-inline" method="GET">
                    <input type="hidden" id="i_page" name="i_page" value="<?php echo isset($i_page) ? $i_page : 1; ?>">
                    <div class="row" style="margin:10px 20px;">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>账单类型: </label>
                                <select name="bill_type" style="width:160px;margin-left:15px;" class="form-control">
                                    <option value="">全部</option>
                                    <?php foreach (Paycore::get_bill_type() as $key => $value) {
                                        echo '<option value="' . $key . '"';
                                        if (isset($b_type) && $key == $b_type) {
                                            echo ' selected';
                                        }
                                        echo '>' . $value . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <a id="btn-commit-filter" href="javascript:;" class="btn btn-primary">提交查询</a>
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
                        <th>时间</th>
                        <th>账单类型</th>
                        <th>详情</th>
                        <th>金额</th>
                        <th>余额</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo $v->gmt_pay; ?></td>
                            <td><?php echo Paycore::get_bill_type_name($v->bill_type); ?></td>
                            <td>
                                <?php echo $v->memo; ?></td>
                            <td>
                                <?php if ($v->amount >= 0) : ?>
                                    <div class="item-after" style="font-size:20px;color:green;">+&nbsp;<?php echo number_format(abs($v->amount), 2, '.', ','); ?></div>
                                <?php else : ?>
                                    <div class="item-after" style="font-size:20px;color:red;">-&nbsp;<?php echo number_format(abs($v->amount), 2, '.', ','); ?></div>
                                <?php endif ?>
                            </td>
                            <td><?php echo $v->balance; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('fragment_pagination'); ?>
<script type="text/javascript">
    $(function () {
        $('#btn-commit-filter').click(function (e) {
            e.preventDefault();
            $('#i_page').val(1);
            $('#form-filter').submit();
        });
    });
</script>
<link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a class="btn btn-lg btn-success" href="<?php echo base_url('pages/withdraw_application'); ?>">提现</a>
            </div>
            <?php
            $options = array(
                STATUS_CHECKING => '提现处理中',
                STATUS_REMITING => '打款处理中',
                //STATUS_REMITED => '已打款',
                STATUS_PASSED => '提现成功',
                STATUS_CANCELING => '待退款',
                STATUS_FAILED => '提现失败',

            );
            ?>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>时间</th>
                        <th>银行名称</th>
                        <th>银行卡号</th>
                        <th>提现金额</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo substr($v->create_time, 0, 10); ?></td>
                            <td><?php echo $v->bank_name; ?></td>
                            <td><?php echo $v->bank_card_num; ?></td>
                            <td><?php echo $v->amount; ?></td>
                            <td>
                                <span class="tieyu-back-darkmagenta tieyu-max-font tieyu-icon-radius" style="margin-right:14px;"> <?php echo $options[$v->status];?> </span>
                            </td>
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
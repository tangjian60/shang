<?php
/**
 * Created by PhpStorm.
 * User: xiaoliu
 * Date: 2018/7/27
 * Time: 13:55
 */
?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <a class="btn btn-lg btn-success" href="<?php echo base_url('withdrawal/index'); ?>">取现</a>
                </div>
                <div class="bootstrap-admin-panel-content">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>时间</th>
                            <th>银行名称</th>
                            <th>银行卡号</th>
                            <th>取现金额</th>
                            <th>状态</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $v): ?>
                            <tr>
                                <td><?php echo $v->create_time; ?></td>
                                <td><?php echo $v->bank_name; ?></td>
                                <td><?php echo $v->bank_card_num; ?></td>
                                <td><?php echo $v->amount; ?></td>
                                <td>
                                    <?php if ($v->status == STATUS_CHECKING) {
                                        echo '<span class="tieyu-back-darkmagenta tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">平台处理中</span>';
                                    } else if ($v->status == STATUS_PASSED) {
                                        echo '<span class="tieyu-back-green tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">取现成功</span>';
                                    } else if ($v->status == STATUS_FAILED) {
                                        echo '<span class="tieyu-back-red tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">取现失败</span>';
                                    }
                                    ?>
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
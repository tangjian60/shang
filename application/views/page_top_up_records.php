<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a class="btn btn-lg btn-success" href="<?php echo base_url('pages/top_up'); ?>">充值</a>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>时间</th>
                        <th>转账截图</th>
                        <th>汇款银行</th>
                        <th>汇款人姓名</th>
                        <th>充值金额</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo substr($v->create_time, 0, 10); ?></td>
                            <td>
                                <?php
                                if ($v->chongzhi_img == "") {
                                    echo $v->zhuanru_bank_name;
                                } else if ($v->chongzhi_img != ""){
                                    echo '<a href="'.CDN_DOMAIN . $v->chongzhi_img.'" class="fancybox"><img class="item-pic-box" src="'.CDN_DOMAIN . $v->chongzhi_img.'"></a>';
                                }
                               ?>
                            </td>
                            <td><?php echo $v->huikuan_bank_name; ?></td>
                            <td><?php echo $v->transfer_person; ?></td>
                            <td><?php echo $v->transfer_amount; ?></td>
                            <td>
                                <?php if ($v->status == STATUS_CHECKING) {
                                    echo '<span class="tieyu-back-darkmagenta tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">平台处理中</span>';
                                } else if ($v->status == STATUS_PASSED) {
                                    echo '<span class="tieyu-back-green tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">充值成功</span>';
                                } else if ($v->status == STATUS_FAILED) {
                                    echo '<span class="tieyu-back-red tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">充值失败</span>';
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
<link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
<link href="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>jquery.fancybox.min.js"></script>


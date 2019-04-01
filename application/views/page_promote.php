<div class="row">
    <div class="col-lg-12">
        <div class="bootstrap-admin-back-to-parent panel panel-default">
            <div class="invk-index" style="width:753px;margin: 0 auto;">
                <div class="f-cb">
                    即刻起，邀请好友注册，您将获得被邀请人充值流水的千分之<?php echo PROMOTION_TOP_UP_BONUS * 1000; ?>（<?php echo PROMOTION_TOP_UP_BONUS * 1000; ?>‰）作为奖励，奖励机制永久有效无上限。
                    <br>
                    注：充值金额如需提现，需要支付<?php echo SELLER_WITHDRAW_FEE * 1000; ?>‰的提现手续费。
                </div>
                <h4 class="page-header heif" style="border-bottom: 3px solid #eee;margin-top:30px;">推广链接</h4>
                <div class="f-cb">
                    <input class="clipboard f-fl" type="text" readonly="readonly" value="<?php echo SELLER_PROMOTE_LINK . encode_id($r); ?>">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="display: <?php if(count($data) == 0){ echo 'none';} ?>;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">推广明细</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>用户名</th>
                        <th>充值总金额</th>
                        <th>已完成的放单数量</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo $v['username']; ?></td>
                            <td><?php echo $v['bills']; ?></td>
                            <td><?php echo $v['task']; ?></td>
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
        $(".clipboard").click(function () {
            $(this).select();
        });
    })
</script>

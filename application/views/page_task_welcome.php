<?php if (isset($url_format)) : ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">请选择任务类型</h3>
    </div>
    <div class="panel-body" style="padding:50px 0;">
        <div class="row">
            <div class="col-md-4 task-hall">
                <a href="<?php echo base_url(sprintf($url_format, TASK_TYPE_LL)); ?>">
                    <div class="task-hall-icon">
                        <img src="<?php echo CDN_BINARY_URL; ?>task_ll.png">
                    </div>
                    <div class="task-hall-title">流量单</div>
                    <div class="task-hall-desc">无需垫钱，快速方便</div>
                </a>
            </div>
            <div class="col-md-4 task-hall">
                <a href="<?php echo base_url(sprintf($url_format, TASK_TYPE_DF)); ?>">
                    <div class="task-hall-icon">
                        <img src="<?php echo CDN_BINARY_URL; ?>task_df.png">
                    </div>
                    <div class="task-hall-title">垫付单</div>
                    <div class="task-hall-desc">需要垫钱，收益更高</div>
                </a>
            </div>
            <div class="col-md-4 task-hall">
                <a href="<?php echo base_url('task/pub_dt'); ?>">
                <!--<a href="javascript:;">-->
                    <div class="task-hall-icon">
                        <img src="<?php echo CDN_BINARY_URL; ?>task_df.png">
                    </div>
                    <div class="task-hall-title">多天垫付单</div>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 task-hall">
                <a href="javascript:;">
                    <div class="task-hall-icon">
                        <img src="<?php echo CDN_BINARY_URL; ?>task_jd.jpg">
                    </div>
                    <div class="task-hall-title">京东单</div>
                    <div class="task-hall-desc">即将上线</div>
                </a>
            </div>
            <div class="col-md-4 task-hall">
                <a href="javascript:;">
                    <div class="task-hall-icon">
                        <img src="<?php echo CDN_BINARY_URL; ?>task_pdd.png">
                    </div>
                    <div class="task-hall-title">拼多多单</div>
                    <div class="task-hall-desc">即将上线</div>
                </a>
            </div>
        </div>
    </div>
</div>

<!--<script>
    //alert('重要通知！！请各位商家朋友往新银行卡充值转账，不要再往老卡转账了，望悉知！');
</script>-->

<?php endif; ?>
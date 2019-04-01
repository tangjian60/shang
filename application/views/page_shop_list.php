<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="text-muted bootstrap-admin-box-title">店铺明细</div>
            </div>
            <div class="bootstrap-admin-panel-content">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>时间</th>
                        <th>店铺类型</th>
                        <th>店铺名称</th>
                        <th>店主ID</th>
                        <th>地区</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $v): ?>
                        <tr>
                            <td><?php echo substr($v->gmt_create, 0, 10); ?></td>
                            <td>
                                <?php
                                switch ($v->shop_type) {
                                    case SHOP_TYPE_TAOBAO:
                                        echo '淘宝';
                                        break;
                                    case SHOP_TYPE_TMALL:
                                        echo '天猫';
                                        break;
                                    case SHOP_TYPE_PINDUODUO:
                                        echo '拼多多';
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo '<a href="' . $v->shop_url . '" target="_blank">' . $v->shop_name . '</a>'; ?></td>
                            <td>
                                <?php echo $v->shop_ww; ?></td>
                            <td>
                                <?php echo $v->shop_province . $v->shop_city . $v->shop_county . $v->shop_address; ?>
                            </td>
                            <td>
                                <?php
                                switch ($v->status) {
                                    case STATUS_CHECKING :
                                        echo '<span class="tieyu-back-darkmagenta tieyu-max-font tieyu-icon-radius unbind-account">等待审核</span>';
                                        break;
                                    case STATUS_PASSED :
                                        echo '<span class="tieyu-back-green tieyu-max-font tieyu-icon-radius unbind-account">已绑定</span>';
                                        break;
                                    case STATUS_FAILED :
                                        echo '<span class="tieyu-back-darkgray tieyu-max-font tieyu-icon-radius unbind-account">审核未通过</span>';
                                        break;
                                    case STATUS_CANCEL :
                                        echo '<span class="tieyu-back-darkgray tieyu-max-font tieyu-icon-radius unbind-account">已解除绑定</span>';
                                        break;
                                    default :
                                        echo '<span class="tieyu-back-darkmagenta tieyu-max-font tieyu-icon-radius unbind-account">未提交审核</span>';
                                        break;
                                }
                                if ($v->status == STATUS_PASSED) {
                                ?>
                                    <a href="<?php echo base_url('pages/edit_shop?id=' . $v->id); ?>"><span class="tieyu-back-green tieyu-max-font tieyu-icon-radius" style="margin-right:14px;">编辑</span></a>
                                <?php
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
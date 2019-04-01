<?php if (empty($templates_data)) : ?>
    <div class="container">
        <div class="row">
            <h2 class="form-signin form-signin-heading">很抱歉，您还没有任务模板，无法发布任务</h2>
        </div>
        <div class="row">
            <a href="<?php echo base_url('task/edit_template'); ?>" class="btn btn-lg btn-primary btn-block"
               type="button" style="margin:20px auto;width:300px;">去添加任务模板</a>
        </div>
    </div>
<?php elseif ($shopsWithAddress > 0): ?>
    <div class="container">
        <div class="row">
            <h2 class="form-signin form-signin-heading">很抱歉，您有店铺信息地址没有完善，无法发布任务</h2>
        </div>
        <div class="row">
            <a href="<?php echo base_url('pages/shop_list'); ?>" class="btn btn-lg btn-primary btn-block"
               type="button" style="margin:20px auto;width:300px;">去完善店铺信息</a>
        </div>
    </div>
<?php else: ?>
    <form class="form-horizontal form-task-pinduoduo">
        <input type="hidden" name="task_type" value="<?php echo $task_type; ?>">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">拼多多任务详情</h3>
            </div>
            <div class="panel-body" style="padding:50px 0;">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        请选择一个任务模板<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <select class="form-control" name="template_id" id="template_id">
                            <option value="">请选择任务模板</option>
                            <?php
                            foreach ($templates_data as $v) {
                                echo '<option value="' . $v->id . '">';
                                echo $v->template_name . '</option>';
                            }
                            ?>
                        </select>
                        <p class="help-block">此拼多多任务将使用该模板的设置</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务入口模式<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                            $sort_type = ['App搜索模式', 'Pid模式',];
                            foreach ($sort_type as $v) {
                                echo '<label class="radio-inline"><input class="task_method" type="radio" name="task_method" value="' . $v . '">' . $v . '</label>';
                            }
                        ?>
                        <p class="help-block">请选择任务入口方式</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8 task-key-word"></div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务单数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4">
                        <input id="task-cnt-input" placeholder="任务单数" class="form-control" type="number" name="task_cnt" onmousewheel="return false;" min="0">
                        <p class="color-red">指定内容和指定图片好评，单数由好评条目自动计算</p>
                    </div>
                </div>
                <!-- <div class="form-group">
                    <label class="col-md-3 control-label">
                        入口详情<span class="label label-default">必填</span>
                    </label>
                    <div id="task_method_details" class="col-md-8"></div>
                </div> -->
                <div class="form-group" id="sort_type" style="display: none;">
                    <label class="col-md-3 control-label">
                        排序方式<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                            $sort_type = ['综合排序', '销量排序', '价格排序'];
                            foreach ($sort_type as $v) {
                                echo '<label class="radio-inline"><input class="sort_type" type="radio" name="sort_type" value="' . $v . '">' . $v . '</label>';
                            }
                        ?>
                        <p class="help-block">综合排序宝贝位置不稳定，推荐使用销量排序</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        已拼人数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="人数" class="form-control" type="text" name="buyer_cnt">
                        <p class="help-block">此处为手机拼多多搜索列表页显示的拼单人数</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        下单类型<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php $order_type = ['有团参团/无团再开', '开团', '单买', '必须参团']; //拼多多下单类型 ?>
                        <?php foreach ($order_type as $item): ?>
                            <label class="radio-inline"><input class="order_type" type="radio" name="order_type" value="<?php echo $item; ?>"><?php echo $item; ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">尺码规格</label>
                    <div class="col-md-8">
                        <input placeholder="尺码规格" class="form-control" type="text" name="sku">
                        <p class="help-block">商品的SKU，不指定可填写“任意”</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        单品购买售价<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input id="input-task-capital" placeholder="单品购买售价" class="form-control" type="number" name="task_capital" onmousewheel="return false;" min="0">
                        <p class="help-block">垫付任务本金</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        每单拍<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input id="input-num-of-pkg" placeholder="件数" class="form-control" type="number" name="num_of_pkg" onmousewheel="return false;" min="0">
                        <p class="help-block">每个订单拍的商品件数</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        是否使用优惠券<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        $sort_type = array(
                            '使用优惠券',
                            '不使用优惠券'
                        );
                        foreach ($sort_type as $v) {
                            echo '<label class="radio-inline"><input type="radio" name="is_coupon" value="' . $v . '">' . $v . '</label>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">返款模式和千人千面设置</h3>
            </div>
            <div class="panel-body" style="padding:50px 0;">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        黑号处理<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input type="radio" name="is_blacklist" value="<?php echo STATUS_ENABLE; ?>" checked="checked">过滤黑号</label>
                        <p class="color-red">每单+ <?php echo Paycore::get_fee_by_code('CNY_HEIHAO'); ?> 元</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        收藏商品<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_collection" type="radio" name="is_collection" value="<?php echo STATUS_ENABLE; ?>">是</label>
                        <label class="radio-inline"><input class="is_collection" type="radio" name="is_collection" value="<?php echo NOT_AVAILABLE; ?>">否</label>
                        <p id="tips-is-collection" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        分享到朋友圈<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_wechat_share" type="radio" name="is_wechat_share" value="<?php echo STATUS_ENABLE; ?>">是</label>
                        <label class="radio-inline"><input class="is_wechat_share" type="radio" name="is_wechat_share" value="<?php echo NOT_AVAILABLE; ?>">否</label>
                        <p id="tips-is-wechat-share" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        假聊<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_fake_chat" type="radio" name="is_fake_chat" value="<?php echo STATUS_ENABLE; ?>">是</label>
                        <label class="radio-inline"><input class="is_fake_chat" type="radio" name="is_fake_chat" value="<?php echo NOT_AVAILABLE; ?>">否</label>
                        <p id="tips-is-fake-chat" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        竞品收藏<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_compete_collection" type="radio" name="is_compete_collection" value="<?php echo STATUS_ENABLE; ?>">是</label>
                        <label class="radio-inline"><input class="is_compete_collection" type="radio" name="is_compete_collection" value="<?php echo NOT_AVAILABLE; ?>">否</label>
                        <p id="tips-is-compete-collection" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">放单模式</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_preferred" type="radio" name="is_preferred" value="<?php echo NOT_AVAILABLE; ?>" checked>普通模式</label>
                        <label class="radio-inline"><input class="is_preferred" type="radio" name="is_preferred" value="<?php echo STATUS_ENABLE; ?>">优先模式，优先派送给会员接单</label>
                        <p id="tips-is-preferred" class="color-red"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">发布时间和评论</h3>
            </div>
            <div class="panel-body" style="padding:50px 0;">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务时间范围<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4">
                        <input class="form-control format_datetime" type="text" name="start_time">
                        <p class="help-block">任务开始时间</p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control format_datetime" type="text" name="end_time">
                        <p class="help-block">任务结束时间</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">放单时间间隔（分钟）</label>
                    <div class="col-md-8">
                        <input placeholder="任务放单时间间隔" class="form-control" type="number" name="hand_out_interval" onmousewheel="return false;" min="0" max="500">
                        <p class="help-block">放任务时两单之间的时间间隔分钟数，留空或填“0”则任务到开始时间后一次性发放</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        选择快递<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="任务放单时间间隔" class="form-control" type="number" name="hand_out_interval" onmousewheel="return false;" min="0">
                        <p class="help-block">放任务时两单之间的时间间隔分钟数，留空或填“0”则任务到开始时间后一次性发放</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        评价方式<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        $comment_type = array(
                            COMMENT_TYPE_NORMAL => '普通好评',
                            COMMENT_TYPE_TEXT => '指定内容',
                            COMMENT_TYPE_PICTURE => '指定图片'
                        );

                        foreach ($comment_type as $k => $v) {
                            echo '<label class="radio-inline"><input class="comment_type" type="radio" name="comment_type" value="' . $k . '">' . $v . '</label>';
                        }
                        ?>
                        <p id="tips-comment-type" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8 task-comment-area"></div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">费用明细</h3>
            </div>
            <div class="panel-body" style="padding:50px 0;">
                <?php if (isset($commission_discount) && $commission_discount < 100): ?>
                    <div class="form-group">
                        <div class="col-md-1"></div>
                        <div class="col-md-9">
                            <span class="tieyu-back-blue tieyu-max-font tieyu-icon-radius" style="margin-left:30px;font-size:22px;">您已享受VIP会员待遇，基础佣金<?php echo $commission_discount / 10; ?>折优惠</span>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-7 price-span">
                        本金<span id="task_capital_amount">0</span>元<span>&times;</span><span class="task_cnt">0</span>单<span>=</span><span id="all_task_capital_amount">0.00</span>元
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-7 price-span">
                        佣金和服务费<span id="task_commission_amount">0</span>元<span>&times;</span><span class="task_cnt">0</span>单<span>=</span><span id="all_task_commission_amount">0.00</span>元
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-7 price-span">
                        快递费用<span id="task_express_amount">0</span>元<span>&times;</span><span class="task_cnt">0</span>单<span>=</span><span id="all_task_express_amount">0.00</span>元
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2"></div>
                    <div class="col-md-7 price-span">
                        共需支付金额<span id="total_amount">0.00</span>元
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-1 control-label"></label>
                    <div class="col-md-9">
                        <div id="error_display" class="alert"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div style="text-align:center;margin:50px auto;">
        <button id="btn-task-go" type="button" class="btn btn-lg btn-success" data-url="<?php echo base_url('requests/create_task_handle'); ?>" data-target="<?php echo base_url('task/pay?order_id='); ?>">确认放单，去支付</button>
    </div>
    <link href="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        <?php
        foreach (Paycore::get_fee_scale() as $v) {
            echo 'const ' . $v['fee_code'] . '=' . $v['fee_amount'] . ';';
        }
        echo 'const COMMENT_TYPE_NORMAL=' . COMMENT_TYPE_NORMAL . ';';
        echo 'const COMMENT_TYPE_TEXT=' . COMMENT_TYPE_TEXT . ';';
        echo 'const COMMENT_TYPE_PICTURE=' . COMMENT_TYPE_PICTURE . ';';
        echo 'const CDN_BINARY_URL="' . CDN_BINARY_URL . '";';
        echo 'const COMMISSION_DISCOUNT="' . $commission_discount . '";';

        if (!empty($parent_order_data_attribute)) {
            echo 'var parent_order_data_attribute=' . $parent_order_data_attribute . ';';
        }else{
            echo 'var parent_order_data_attribute=null;';
        }
        ?>
    </script>
    <script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>seller-task-pinduoduo.js?t=2018101611"></script>
<?php endif; ?>

<?php if (empty($templates_data)) : ?>
    <div class="container">
        <div class="row">
            <h2 class="form-signin form-signin-heading">很抱歉，您还没有任务模板，无法发布任务</h2>
        </div>
        <div class="row">
            <a href="<?php echo base_url('task/edit_template'); ?>" class="btn btn-lg btn-primary btn-block" type="button" style="margin:20px auto;width:300px;">去添加任务模板</a>
        </div>
    </div>
<?php else: ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">流量任务详情</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <form class="form-horizontal form-task-liuliang">
                <input type="hidden" name="task_type" value="<?php echo $task_type; ?>">
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
                        <p class="help-block">此流量任务将使用该模板的设置</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务入口<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                            $sort_type = ['搜索', '分享成交', '淘口令', '聚划算', '淘抢购', '天天特价'];
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
                <!-- <div class="form-group">
                    <label class="col-md-3 control-label">
                        入口详情<span class="label label-default">必填</span>
                    </label>
                    <div id="task_method_details" class="col-md-8"></div>
                </div> -->
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务单数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input id="input_order_cnt" placeholder="任务单数" class="form-control" type="number" name="task_cnt" onmousewheel="return false;" min="0">
                        <p class="help-block">请在此处填写要发放的任务单数</p>
                    </div>
                </div>
                <div class="form-group" id="sort_type" style="display: none;">
                    <label class="col-md-3 control-label">
                        排序方式<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                            $sort_type = ['销量', '综合', '综合直通车'];
                            foreach ($sort_type as $v) {
                                echo '<label class="radio-inline"><input class="sort_type" type="radio" name="sort_type" value="' . $v . '">' . $v . '</label>';
                            }
                        ?>
                        <p class="help-block">综合排序宝贝位置不稳定，推荐使用销量排序</p>
                    </div>
                </div>
                 <div class="form-group">
                    <label class="col-md-3 control-label">
                        现有<span class="buyer_count_label color-red">收货</span>人数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="人数" class="form-control" type="text" name="receipt_cnt">
                        <p class="help-block">此处为手机淘宝搜索列表页显示的人数</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        现有<span class="buyer_count_label color-red">付款</span>人数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="人数" class="form-control" type="text" name="buyer_cnt">
                        <p class="help-block">此处为手机淘宝搜索列表页显示的人数</p>
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
                    <label class="col-md-3 control-label">放单模式</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_preferred" type="radio" name="is_preferred" value="<?php echo NOT_AVAILABLE; ?>" checked>普通模式</label>
                        <label class="radio-inline"><input class="is_preferred" type="radio" name="is_preferred" value="<?php echo STATUS_ENABLE; ?>">优先模式，优先派送给会员接单</label>
                        <p id="tips-is-preferred" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">收藏店铺</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="favorite_shop" type="radio" name="favorite_shop" value="<?php echo STATUS_ENABLE; ?>">人肉收藏</label>
                        <label class="radio-inline"><input class="favorite_shop" type="radio" name="favorite_shop" value="<?php echo NOT_AVAILABLE; ?>">不收藏</label>
                        <p id="tips-favorite-shop" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">收藏宝贝</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="favorite_item" type="radio" name="favorite_item" value="<?php echo STATUS_ENABLE; ?>">人肉收藏</label>
                        <label class="radio-inline"><input class="favorite_item" type="radio" name="favorite_item" value="<?php echo NOT_AVAILABLE; ?>">不收藏</label>
                        <p id="tips-favorite-item" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">加购物车</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="add_cart" type="radio" name="add_cart" value="<?php echo STATUS_ENABLE; ?>">真实加购</label>
                        <label class="radio-inline"><input class="add_cart" type="radio" name="add_cart" value="<?php echo NOT_AVAILABLE; ?>">不加购</label>
                        <p id="tips-add-cart" class="color-red"></p>
                    </div>
                </div>
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
                    <label class="col-md-3 control-label">收费明细</label>
                    <div class="col-md-8 price-span">
                        佣金<span id="commission_amount">0</span>元<span>&times;</span><span id="task_cnt">0</span>单<span>=</span><span id="total_amount">0.00</span>元
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label"></label>
                    <div class="col-md-8">
                        <div id="error_display" class="alert"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
            echo 'const CDN_BINARY_URL="' . CDN_BINARY_URL . '";';
            echo 'const COMMISSION_DISCOUNT="' . $commission_discount . '";';

            if (!empty($parent_order_data_attribute)) {
                echo 'var parent_order_data_attribute=' . $parent_order_data_attribute . ';';
            }else{
                echo 'var parent_order_data_attribute=null;';
            }
        ?>
    </script>
    <script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>seller-task-ll.js?t=2018112009"></script>
<?php endif; ?>

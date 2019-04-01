<style type="text/css">
    .container_area {position:absolute; display:none; padding-left:120px;}
    .shadow {float:left;}
    .frame_area {position:relative; background:#fff; padding:6px; display:block;
        -moz-box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.6);
        -webkit-box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.6);
        box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.6);
        overflow: hidden;
        width:510px
    }
    .frame_area {font-size:13px;color:#4f6b72;}
    div.frame_area div {margin-bottom:5px; float:left}
    div.frame_area div.foot1 {margin-top:15px; text-agiln:right; margin-right:20px; float:right}
    div.frame_area div.foot1 a{
        margin-right: 20px;
        background: lightseagreen;
        width: 80px;
        display: inline-block;
        text-align: center;
        height: 24px;
        line-height: 24px;
        color:  	#000000;
    }
    div.frame_area label {margin: 0 10px 0 5px;}
    div.frame_area a:link,div.frame_area span a:visited {
        text-decoration:none; margin:0 20px;
</style>

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
    <form class="form-horizontal form-task-dianfu">
        <input type="hidden" name="task_type" value="<?php echo $task_type; ?>">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">设置做单轨迹</h3>
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
                        <p class="help-block">此垫付任务将使用该模板的设置</p>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务下单类型<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        $sort_type = ['2' => '次日下单', '3' => '第三天下单', '4' => '第四天下单', '5' => '第五天下单'];
                        foreach ($sort_type as $k => $v) {
                            echo '<label class="radio-inline"><input class="task_days" type="radio" name="task_days" value="' . $k . '">' . $v . '</label>';
                        }
                        ?>
                    </div>
                </div>

                <div class="day_1 hidden">
                        <h5 class="page-header"></h5>
                        <div class="form-group">
                            <label class="col-md-3 control-label font-18">【 第 1 天 】</label>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">操作入口</label>
                            <div class="col-md-8">
                                <label class="radio-inline"><input class="task_way" type="radio" name="task_way_1" value="1" checked>手淘搜索</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">填写搜索流程</label>
                            <div class="col-md-8">
                                <textarea class="form-control" name="op_flow_1" rows="3" style="min-width: 90%"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">浏览动作<br><font style="color: #ff0000">*所有选项皆可调整</font></label>
                            <div class="col-md-8">
                                <div class="col-md-1">店外</div>
                                <div class="col-md-7">
                                    <?php
                                    foreach ($task_behaviors as $k => $v) {
                                        if ($k > $sp) break;
                                        echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_1" value="' . ($k+1) . '">' . $v . '</label>';
                                    }
                                    ?>
                                </div>
                                <div class="col-md-4 " style="color: #ff0000;font-size: 14px;font-weight: bold;">*每日至少选择一个截图，每个截图1元/单</div>
                            </div>

                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label"></label>
                            <div class="col-md-9" >
                                <div class="col-md-1">店内</div>
                                <div class="col-md-6">
                                    <?php
                                    foreach ($task_behaviors as $k => $v) {
                                        if ($k < 5) continue;
                                        echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_1" value="' . ($k+1) . '">' . $v . '</label>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <div class="form-group"><label class="col-md-3 control-label extra-day1 color-red"></label></div>
                </div>

                <div class="day_2 hidden">
                    <h5 class="page-header"></h5>
                    <div class="form-group">
                        <label class="col-md-3 control-label font-18">【 第 2 天 】</label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">操作入口</label>
                        <div class="col-md-8">
                            <?php
                            foreach ($task_ways as $k => $v) {
                                echo '<label class="radio-inline"><input class="task_way" type="radio" name="task_way_2" value="' . ($k+1) . '">' . $v . '</label>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">填写搜索流程</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="op_flow_2" rows="3" style="min-width: 90%"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">浏览动作</label>
                        <div class="col-md-9">
                            <div class="col-md-1">店外</div>
                            <div class="col-md-11">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k > $sp) break;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_2" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9" >
                            <div class="col-md-1">店内</div>
                            <div class="col-md-6">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k <= $sp) continue;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_2" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"><label class="col-md-3 control-label extra-day2 color-red"></label></div>
                </div>

                <div class="day_3 hidden">
                    <h5 class="page-header"></h5>
                    <div class="form-group">
                        <label class="col-md-3 control-label font-18">【 第 3 天 】</label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">操作入口</label>
                        <div class="col-md-8">
                            <label class="radio-inline">
                                <?php
                                foreach ($task_ways as $k => $v) {
                                    echo '<label class="radio-inline"><input class="task_way" type="radio" name="task_way_3" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">填写搜索流程</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="op_flow_3" rows="3" style="min-width: 90%"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">浏览动作</label>
                        <div class="col-md-9">
                            <div class="col-md-1">店外</div>
                            <div class="col-md-11">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k > $sp) break;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_3" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9" >
                            <div class="col-md-1">店内</div>
                            <div class="col-md-6">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k <= $sp) continue;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_3" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"><label class="col-md-3 control-label extra-day3 color-red"></label></div>
                </div>

                <div class="day_4 hidden">
                    <h5 class="page-header"></h5>
                    <div class="form-group">
                        <label class="col-md-3 control-label font-18">【 第 4 天 】</label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">操作入口</label>
                        <div class="col-md-8">
                            <label class="radio-inline">
                                <?php
                                foreach ($task_ways as $k => $v) {
                                    echo '<label class="radio-inline"><input class="task_way" type="radio" name="task_way_4" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">填写搜索流程</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="op_flow_4" rows="3" style="min-width: 90%"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">浏览动作</label>
                        <div class="col-md-9">
                            <div class="col-md-1">店外</div>
                            <div class="col-md-11">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k > $sp) break;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_4" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9" >
                            <div class="col-md-1">店内</div>
                            <div class="col-md-6">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k <= $sp) continue;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_4" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"><label class="col-md-3 control-label extra-day4 color-red"></label></div>
                </div>

                <div class="day_5 hidden">
                    <h5 class="page-header"></h5>
                    <div class="form-group">
                        <label class="col-md-3 control-label font-18">【 第 5 天 】</label>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">操作入口</label>
                        <div class="col-md-8">
                            <label class="radio-inline">
                                <?php
                                foreach ($task_ways as $k => $v) {
                                    echo '<label class="radio-inline"><input class="task_way" type="radio" name="task_way_5" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">填写搜索流程</label>
                        <div class="col-md-8">
                            <textarea class="form-control" name="op_flow_5" rows="3" style="min-width: 90%"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">浏览动作</label>
                        <div class="col-md-9">
                            <div class="col-md-1">店外</div>
                            <div class="col-md-11">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k > $sp) break;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_5" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"></label>
                        <div class="col-md-9" >
                            <div class="col-md-1">店内</div>
                            <div class="col-md-6">
                                <?php
                                foreach ($task_behaviors as $k => $v) {
                                    if ($k <= $sp) continue;
                                    echo '<label class="radio-inline"><input class="method_outer" type="checkbox" name="method_outer_5" value="' . ($k+1) . '">' . $v . '</label>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"><label class="col-md-3 control-label extra-day5 color-red"></label></div>
                </div>

            </div>
        </div>



        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">下单说明</h3>
            </div>
            <div class="panel-body" style="padding:50px 0;">

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

                <div class="form-group">
                    <label class="col-md-3 control-label">
                        下单入口<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4" >
                        <select class="form-control" name="order_place" id="order_place">
                            <option value="">请选择下单入口</option>
                            <option value="1">购物车</option>
                            <option value="2">收藏夹</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">尺码规格</label>
                    <div class="col-md-4">
                        <input placeholder="尺码规格" class="form-control" type="text" name="sku" value="任意">
                        <p class="help-block">商品的SKU，不指定可填写“任意”</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        商品单价<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4">
                        <input id="input-task-capital" placeholder="单品购买售价" class="form-control" type="number" name="task_capital" onmousewheel="return false;" min="0">
                        <p class="help-block">垫付任务本金</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        每单拍<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4">
                        <input id="input-num-of-pkg" placeholder="件数" class="form-control" type="number" name="num_of_pkg" onmousewheel="return false;" min="0">
                        <p class="help-block">每个订单拍的商品件数</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-md-3 control-label">
                        任务单数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4" >
                        <input id="task-cnt-input" placeholder="任务单数" class="form-control" type="number" name="task_cnt" onmousewheel="return false;" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        现有<span class="buyer_count_label color-red">付款</span>人数<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-4">
                        <input placeholder="人数" class="form-control" type="text" name="buyer_cnt">
                        <p class="help-block">此处为手机淘宝搜索列表页显示的人数</p>
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
                    <label class="col-md-3 control-label">放单模式</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_preferred" type="radio" name="is_preferred" value="<?php echo NOT_AVAILABLE; ?>" checked>普通模式</label>
                        <label class="radio-inline"><input class="is_preferred" type="radio" name="is_preferred" value="<?php echo STATUS_ENABLE; ?>">优先模式，优先派送给会员接单</label>
                        <p id="tips-is-preferred" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">花呗设置</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="is_huabei" type="radio" name="is_huabei" value="<?php echo NOT_AVAILABLE; ?>" checked>不限制</label>
                        <label class="radio-inline"><input class="is_huabei" type="radio" name="is_huabei" value="<?php echo STATUS_ENABLE; ?>">只允许开通花呗的会员接单</label>
                        <p id="tips-is-huabei" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">性别限制</label>
                    <div class="col-md-8">
                        <label class="radio-inline"><input class="sex_limit" type="radio" name="sex_limit" value="<?php echo NOT_AVAILABLE; ?>" checked>不限</label>
                        <label class="radio-inline"><input class="sex_limit" type="radio" name="sex_limit" value="男">男</label>
                        <label class="radio-inline"><input class="sex_limit" type="radio" name="sex_limit" value="女">女</label>
                        <p id="tips-sex-limit" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        年龄限制
                    </label>
                    <div class="col-md-8">
                        <select class="form-control age_limit" name="age_limit">
                            <option value="<?php echo NOT_AVAILABLE; ?>">不限制</option>
                            <option value="15">15-25岁</option>
                            <option value="26">26-35岁</option>
                            <option value="36">36-45岁</option>
                            <option value="46">46-55岁</option>
                            <option value="56">56岁以上</option>
                        </select>
                        <p id="tips-age-limit" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        等级限制
                    </label>
                    <div class="col-md-8">
                        <select class="form-control tb_rate_limit" name="tb_rate_limit">
                            <option value="<?php echo NOT_AVAILABLE; ?>">不限制</option>
                            <?php foreach (Tbrateenum::get_display_list() as $v) {
                                echo '<option value="' . $v['id'] . '">' . $v['rate_name'] . '</option>';
                            } ?>
                        </select>
                        <p id="tips-tb-rate-limit" class="color-red"></p>
                    </div>
                </div>

                <div class="container_area">
                    <div class="shadow">
                        <div class="frame_area">
                            <div><input type="checkbox" id="location01" name="demo1"/><label for="location01">北京市</label></div>
                            <div><input type="checkbox" id="location02" name="demo2" /><label for="location02">天津市</label></div>
                            <div><input type="checkbox" id="location03" name="demo3"/><label for="location03">上海市</label></div>
                            <div><input type="checkbox" id="location04" name="demo4"/><label for="location04">重庆市</label></div>
                            <div><input type="checkbox" id="location05" name="demo5"/><label for="location05">河北省</label></div>
                            <div><input type="checkbox" id="location06" name="demo6"/><label for="location06">山西省</label></div>
                            <div><input type="checkbox" id="location07" name="demo7"/><label for="location07">内蒙古</label></div>
                            <div><input type="checkbox" id="location08" name="demo8"/><label for="location08">辽宁省</label></div>
                            <div><input type="checkbox" id="location09" name="demo9"/><label for="location09">吉林省</label></div>
                            <div><input type="checkbox" id="location10" name="demo10"/><label for="location10">黑龙江省</label></div>
                            <div><input type="checkbox" id="location11" name="demo11"/><label for="location11">江苏省</label></div>
                            <div><input type="checkbox" id="location12" name="demo12"/><label for="location12">浙江省</label></div>
                            <div><input type="checkbox" id="location13" name="demo13"/><label for="location13">安徽省</label></div>
                            <div><input type="checkbox" id="location14" name="demo14"/><label for="location14">福建省</label></div>
                            <div><input type="checkbox" id="location15" name="demo15"/><label for="location15">江西省</label></div>
                            <div><input type="checkbox" id="location16" name="demo16"/><label for="location16">山东省</label></div>
                            <div><input type="checkbox" id="location17" name="demo17"/><label for="location17">河南省</label></div>
                            <div><input type="checkbox" id="location18" name="demo18"/><label for="location18">湖北省</label></div>
                            <div><input type="checkbox" id="location19" name="demo19"/><label for="location19">湖南省</label></div>
                            <div><input type="checkbox" id="location20" name="demo20"/><label for="location20">广东省</label></div>
                            <div><input type="checkbox" id="location21" name="demo21"/><label for="location21">广西</label></div>
                            <div><input type="checkbox" id="location22" name="demo22"/><label for="location22">海南省</label></div>
                            <div><input type="checkbox" id="location23" name="demo23"/><label for="location23">四川省</label></div>
                            <div><input type="checkbox" id="location24" name="demo24"/><label for="location24">贵州省</label></div>
                            <div><input type="checkbox" id="location25" name="demo25"/><label for="location25">云南省</label></div>
                            <div><input type="checkbox" id="location26" name="demo26"/><label for="location26">西藏</label></div>
                            <div><input type="checkbox" id="location27" name="demo27"/><label for="location27">陕西省</label></div>
                            <div><input type="checkbox" id="location28" name="demo28"/><label for="location28">甘肃省</label></div>
                            <div><input type="checkbox" id="location29" name="demo29"/><label for="location29">青海省</label></div>
                            <div><input type="checkbox" id="location30" name="demo30"/><label for="location30">宁夏</label></div>
                            <div><input type="checkbox" id="location31" name="demo31"/><label for="location31">新疆</label></div>
                            <div><input type="checkbox" id="location32" name="demo32"/><label for="location32">不限制</label></div>
                            <div class="foot1" style="text-agiln:right" style="margin:0 8px">
                                <a href="javascript:void(0)" id="submit_area">确定</a>
                                <a href="javascript:void(0)" id="close_area">取消</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">区域限制</label>
                    <div class="col-md-8">
                        <input id="taskPlanDay" name="taskPlanDay" type="text" class="form-control" readonly="readonly" datatype="*" value="不限制">
                        <p class="help-block">温馨提示：区域限制可多选，被选中区域内的买手将无法接到该订单！</p>
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
                        <input class="form-control format_datetime" type="text" name="start_time" placeholder="任务开始时间">
                        <p class="help-block" style="color: #ff0000;font-weight: bold">
                            注：起止时间表示接单有效期，即买手可以接此时间范围内状态为“待接单”的任务
                        </p>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control format_datetime" type="text" name="end_time" placeholder="任务结束时间">
                        <p class="help-block"></p>
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
                        <label class="radio-inline"><input class="express_type" type="radio" name="express_type" value="<?php echo NOT_AVAILABLE; ?>">商家快递</label>
                        <label class="radio-inline"><input class="express_type" type="radio" name="express_type" value="圆通快递">平台快递</label>
                        <p id="tips-express-type" class="color-red"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        评价方式<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        $comment_type = array(
                            COMMENT_TYPE_NORMAL     => '普通好评',
                            COMMENT_TYPE_TEXT       => '指定内容',
                            COMMENT_TYPE_PICTURE    => '指定图片'
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
    <style type="text/css">
        .hidden {
            display: none;}
        .font-18 {font-size: 18px}
    </style>
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
    <script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>seller-task-duotian.js?t=1812221730"></script>
<?php endif; ?>

<script language="javascript">
    $('#taskPlanDay').bind('focus', function() {
        var offset = $(this).offset(), container_area = $('div.container_area');
        var top = offset.top - 67
        var left = offset.left - 46
        //console.log(top)
        container_area.css({top: top, left: left, zIndex: 99}).show(100);

//        container.css({top:offset.top+Number($(this).css('height').replace('px', '')), left:offset.left, zIndex: 99}).show(100);
    });
    $(document).bind('click', function(){
        var targ;
        if (event.target) targ = event.target
        else if (event.srcElement) targ = event.srcElement
        if (targ.nodeType == 3) // defeat Safari bug
            targ = targ.parentNode;
        if (targ.id !='taskPlanDay' && !$(targ).parents('div.container_area').attr('class'))
            $('div.container_area').hide(100);
    });
    $('#submit_area').bind('click', function(){
        var vals = '', length;
        $('div.frame_area input[type=checkbox]:checked').each(function(){
            vals += ($(this).next().text() + ',');
        });
        if ((length = vals.length) > 0) vals = vals.substr(0, length -1);
        $('#taskPlanDay').val(vals);
        $('div.container_area').hide(100);
    });
    $('#close_area').bind('click', function(){
        $('div.container_area').hide(100);
    });
</script>

<?php if (empty($shop_data)) : ?>
    <div class="container">
        <div class="row">
            <h2 class="form-signin form-signin-heading">很抱歉，您至少需要绑定一个店铺，才能添加任务模板</h2>
        </div>
        <div class="row">
            <a href="<?php echo base_url('pages/add_shop'); ?>" class="btn btn-lg btn-primary btn-block" type="button" style="margin:20px auto;width:300px;">去绑定店铺</a>
        </div>
    </div>
<?php else: ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">任务详情</h3>
        </div>
        <div class="panel-body" style="padding:50px 0;">
            <form class="form-horizontal form-task-template">
                <input type="hidden" name="template_id" value="<?php echo empty($template_info) ? NOT_AVAILABLE : $template_info->id; ?>">
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        模板名称<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="模板名称" class="form-control" type="text" name="template_name" value="<?php echo empty($template_info) ? '' : $template_info->template_name; ?>">
                        <p class="help-block">请为当前的模板确定一个名字</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        电商平台<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <label class="radio-inline">
                            <?php if (empty($template_info) || $template_info->platform_type == PLATFORM_TYPE_TAOBAO): ?>
                                <input type="radio" name="platform" class="platform" value="<?php echo PLATFORM_TYPE_TAOBAO; ?>" checked="checked" >淘宝
                            <?php else: ?>
                                <input type="radio" name="platform" class="platform" value="<?php echo PLATFORM_TYPE_TAOBAO; ?>" >淘宝
                            <?php endif; ?>
                        </label>
                        <label class="radio-inline">
                            <?php if (!empty($template_info) && $template_info->platform_type == PLATFORM_TYPE_PINDUODUO): ?>
                                <input type="radio" name="platform" class="platform" value="<?php echo PLATFORM_TYPE_PINDUODUO;?>" checked="checked" >拼多多
                            <?php else: ?>
                                <input type="radio" name="platform" class="platform" value="<?php echo PLATFORM_TYPE_PINDUODUO;?>">拼多多
                            <?php endif; ?>
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="platform" value="" disabled="disabled">京东
                        </label>
                        <p class="help-block">其它平台即将上线</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        请选择一个店铺<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <select class="form-control" name="bind_shop" id="bind_shop">
                            <option value="">请选择店铺</option>
                            <?php
                            // Load the shop data of Taobao First
                            foreach ($shop_data as $shop){
                                if (empty($template_info) || $template_info->platform_type == PLATFORM_TYPE_TAOBAO){
                                    if ($shop->platform_type != PLATFORM_TYPE_TAOBAO){
                                        continue;
                                    }else{
                                        echo '<option value="' . $shop->id . '"';
                                        if (!empty($template_info)){
                                            if ($shop->id == $template_info->shop_id){
                                                echo ' selected';
                                            }
                                        }
                                        echo '>';
                                        switch ($shop->shop_type){
                                            case SHOP_TYPE_TAOBAO:
                                                echo ' [淘宝] ';
                                                break;
                                            case SHOP_TYPE_TMALL:
                                                echo ' [天猫] ';
                                                break;
                                        }
                                        echo $shop->shop_name . '</option>';
                                    }
                                }elseif(!empty($template_info) && $template_info->platform_type == PLATFORM_TYPE_PINDUODUO){
                                    if ($shop->platform_type != PLATFORM_TYPE_PINDUODUO){
                                        continue;
                                    }else{
                                        echo '<option value="' . $shop->id . '"';
                                        if ($shop->id == $template_info->shop_id){
                                            echo ' selected';
                                        }
                                        echo '>';
                                        if ($shop->platform_type != PLATFORM_TYPE_PINDUODUO){
                                            continue;
                                        }
                                        switch ($shop->shop_type){
                                            case SHOP_TYPE_PINDUODUO:
                                                echo ' [拼多多] ';
                                                break;
                                            default:
                                                echo ' [未知] ';
                                                break;
                                        }
                                        echo $shop->shop_name . '</option>';
                                    }
                                }
                            }

                            // old - show all shop
//                            foreach ($shop_data as $v) {
//                                echo '<option value="' . $v->id . '"';
//                                if (!empty($template_info) && $template_info->shop_id == $v->id) {
//                                    echo ' selected';
//                                }
//                                echo '>';
//                                switch ($v->shop_type) {
//                                    case SHOP_TYPE_TAOBAO:
//                                        echo '[淘宝]';
//                                        break;
//                                    case SHOP_TYPE_TMALL:
//                                        echo '[天猫]';
//                                        break;
//                                }
//                                echo $v->shop_name . '</option>';
//                            }
                            ?>
                        </select>
                        <p class="help-block">任务将关联到此店铺</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        使用设备<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <?php
                        $task_types = array(
                            DEVICE_TYPE_MOBILE => "手机",
                            DEVICE_TYPE_COMPUTER => "电脑"
                        );

                        foreach ($task_types as $key => $value) {
                            echo '<label class="radio-inline"><input type="radio" name="device_type" value="' . $key . '"';
                            if (!empty($template_info) && $template_info->device_type == $key) {
                                echo ' checked="checked"';
                            }
                            echo '>' . $value . '</label>';
                        }
                        ?>
                        <p class="help-block">请选择要求刷手使用的设备</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        宝贝链接<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="宝贝链接" class="form-control" type="text" name="item_url" value="<?php echo empty($template_info) ? '' : $template_info->item_url; ?>">
                        <p class="help-block">请将完整的网址粘贴在此栏中</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        宝贝标题<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="宝贝标题" class="form-control" type="text" name="item_title" value="<?php echo empty($template_info) ? '' : $template_info->item_title; ?>">
                        <p class="help-block">请准确填写当前宝贝的标题</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        宝贝展示价格<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <input placeholder="宝贝展示价格" class="form-control" type="text" name="item_display_price" value="<?php echo empty($template_info) ? '' : $template_info->item_display_price; ?>">
                        <p class="help-block">宝贝在搜索页面展示的价格，方便找商品，请务必准确填写</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">
                        宝贝主图<span class="label label-default">必填</span>
                    </label>
                    <div class="col-md-8">
                        <div class="tieyu-icon image-upload-container">
                            <img data-input-name="item_pic" class="image-upload" src="<?php echo empty($template_info) ? CDN_BINARY_URL . 'cross.png' : CDN_DOMAIN . $template_info->item_pic; ?>">
                            <input type="hidden" name="item_pic" value="<?php echo empty($template_info) ? '' : $template_info->item_pic; ?>">
                        </div>
                        <p class="help-block">请确保与搜索页面展示的图片一致</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">额外要求</label>
                    <div class="col-md-8">
                        <textarea class="form-control" rows="8" name="note"><?php echo empty($template_info) ? '' : $template_info->template_note; ?></textarea>
                        <p class="help-block">注意：如果对买手有特别的要求，请在备注里注明，我们会把要求传达给买家，但无法强制买家执行，最多不能超过100字</p>
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
        <button id="btn-submit-template" type="button" class="btn btn-lg btn-success" data-url="<?php echo base_url('requests/edit_template_handle'); ?>">提交</button>
    </div>
    <div id="dialog-template-edit-success" class="modal fade" tabindex="-1" role="dialog" data-target="<?php echo base_url('task/templates'); ?>">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">任务模板</h4>
                </div>
                <div class="modal-body">
                    <p>任务模板已经成功保存。</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">好</button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function () {

            $(".platform").change(
                function() {
                    var platformValue   = $("input[name='platform']:checked").val(); //String
                    var shopData        = <?php echo json_encode($shop_data); ?>;
                    var selDom          = $("#bind_shop");//根据id获取radio的jquery对象
                    var options         = "<option value='' selected>请选择店铺</option>";
                    for ( var i = 0; i <shopData.length; i++){
                        if(parseInt(shopData[i].platform_type) != parseInt(platformValue)){
                            continue;
                        }
                        var shopTypeText = '';
                        switch (parseInt(shopData[i].shop_type)) {
                            case <?php echo SHOP_TYPE_TAOBAO; ?>:
                                shopTypeText = ' [淘宝] ';
                                break;
                            case <?php echo SHOP_TYPE_TMALL; ?>:
                                shopTypeText = ' [天猫] ';
                                break;
                            case <?php echo SHOP_TYPE_PINDUODUO; ?>:
                                shopTypeText = ' [拼多多] ';
                                break;
                        }
                        options += "<option value=" + shopData[i].id + ">" + shopTypeText + shopData[i].shop_name + "</option>";
                    }

                    selDom.html(options);
                    // bind_shop
                    $("#bind_shop option:first").prop("selected", 'selected');
                }
            );

            $('#btn-submit-template').click(function (e) {
                e.preventDefault();
                var form_data = $('.form-task-template').formToJSON();
                var that = $(this);

                hide_error_message();

                if (form_data.note == "") {
                    form_data.note = "无"
                }

                if (invalid_parameter(form_data)) {
                    show_error_message('所有字段均不能为空，请填写所有字段');
                    return;
                }

                that.addClass('disabled');
                that.attr("disabled", true);

                ajax_request(
                    that.data('url'),
                    form_data,
                    function (e) {
                        if (e.code == CODE_SUCCESS) {
                            $('#dialog-template-edit-success').modal('show');
                        } else {
                            show_error_message(e.msg);
                            that.removeClass('disabled');
                            that.attr("disabled", false);
                        }
                    });
            });

            $('#dialog-template-edit-success').on('hidden.bs.modal', function () {
                goto_url($(this).data('target'), 50);
            });

            bind_image_upload_event(true);
        });
    </script>
<?php endif; ?>

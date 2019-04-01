<form class="form-horizontal form-edit-shop">
	<input type="hidden" name="shop_id" value="<?php echo $shop_info->id; ?>">
    <div class="control-group">
        <label class="control-label">电商平台</label>
        <div class="controls">
            <label class="radio-inline">
                <?php 
                	if ($shop_info->platform_type == 1) {
                		echo "<h4>淘宝</h4>";
                	} elseif ($shop_info->platform_type == 3) {
                		echo "<h4>拼多多</h4>";
                	}else{
                	}
                ?>
            </label>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">店铺名称</label>
        <div class="controls">
            <input placeholder="店铺名称" class="form-control" type="text" name="shop_title" value="<?php echo $shop_info->shop_name ; ?>" disabled='disabled'>
            <p class="help-block">务必跟手机端宝贝页显示的店铺名一致</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">店铺网址</label>
        <div class="controls">
            <input placeholder="店铺首页地址" class="form-control" type="text" name="shop_url" value="<?php echo $shop_info->shop_url ; ?>" disabled='disabled'>
            <p class="help-block">店铺地址绑定后无法修改</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" id="shop_ww">店主淘宝旺旺ID</label>
        <div class="controls">
            <input placeholder="店主淘宝旺旺ID" class="form-control" type="text" name="shop_ww" value="<?php echo $shop_info->shop_ww ; ?>" disabled='disabled'>
            <p class="help-block">店主ID绑定后无法修改</p>
        </div>
    </div>
    <div class="control-group  form-inline">
        <label class="control-label">店铺地区</label>
        <div class="controls">
            <div class="form-group" style="margin:0;">
               <?php echo $shop_info->shop_province . $shop_info->shop_city . $shop_info->shop_county ; ?>
            </div>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <input placeholder="店铺详细地址" class="form-control" type="text" name="address" value="<?php echo $shop_info->shop_address ; ?>">
            <p class="help-block">请填写平时正常的发货地址</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">店铺后台截图</label>
        <div class="controls">
            <div class="tieyu-icon image-upload-container">
                <img data-input-name="shop_pic" class="image-upload-diasble" src="<?php echo CDN_DOMAIN . $shop_info->shop_pic; ?>">
                <!-- <input type="hidden" name="shop_pic"> -->
            </div>
            <p class="help-block">请提供包含店铺名和旺旺ID的店铺后台截图</p>
        </div>
    </div>
    <div class="control-group" style="margin:20px 0;">
        <div class="controls">
            <div id="error_display" class="alert"></div>
        </div>
    </div>
    <div class="control-group" style="margin:20px 0;">
        <div class="controls">
            <button id="btn-submit-shop" type="button" class="btn btn-lg btn-success" data-url="<?php echo base_url('requests/edit_shop_handle'); ?>">提交</button>
        </div>
    </div>
</form>
<div id="dialog-shop-edit-success" class="modal fade" tabindex="-1" role="dialog" data-target="<?php echo base_url('pages/shop_list'); ?>">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">店铺</h4>
            </div>
            <div class="modal-body">
                <p>店铺已经成功保存。</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">好</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>area.js?v=201810221530"></script>
<script type="text/javascript">
$(function () {

    $('#btn-submit-shop').click(function (e) {
        e.preventDefault();
        var form_data = $('.form-edit-shop').formToJSON();
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
                    $('#dialog-shop-edit-success').modal('show');
                } else {
                    show_error_message(e.msg);
                    that.removeClass('disabled');
                    that.attr("disabled", false);
                }
            });
    });

    $('#dialog-shop-edit-success').on('hidden.bs.modal', function () {
        goto_url($(this).data('target'), 50);
    });

    bind_image_upload_event(true);
    });
</script>
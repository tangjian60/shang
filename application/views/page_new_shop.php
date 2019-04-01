<form class="form-horizontal form-new-shop">
    <div class="control-group">
        <label class="control-label">电商平台</label>
        <div class="controls">
            <label class="radio-inline">
                <input type="radio" name="platform" class="platform" value="<?php echo PLATFORM_TYPE_TAOBAO; ?>" checked="checked">淘宝
            </label>
            <label class="radio-inline">
                <input type="radio" name="platform" class="platform" value="<?php echo PLATFORM_TYPE_PINDUODUO; ?>">拼多多
            </label>
            <label class="radio-inline">
                <input type="radio" name="platform" class="platform" value="" disabled="disabled">京东
            </label>
            <p class="help-block">其它平台即将上线</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">店铺名称</label>
        <div class="controls">
            <input placeholder="店铺名称" class="form-control" type="text" name="shop_title">
            <p class="help-block" style="color: red;font-weight: bold;">*务必跟手机端宝贝页显示的店铺名一致</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">店铺网址</label>
        <div class="controls">
            <input placeholder="店铺首页地址" class="form-control" type="text" name="shop_url">
            <p class="help-block">店铺地址绑定后无法修改</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" id="shop_ww">店主淘宝旺旺ID</label>
        <div class="controls">
            <input placeholder="店主淘宝旺旺ID" class="form-control" type="text" name="shop_ww">
            <p class="help-block">店主ID绑定后无法修改</p>
        </div>
    </div>
    <div class="control-group  form-inline">
        <label class="control-label">店铺地区</label>
        <div class="controls">
            <div class="form-group" style="margin:0;">
                <select class="form-control" name="province" id="province">
                    <option value="">请选择省份</option>
                </select>
                <select class="form-control" name="city" id="city">
                    <option value="">请选择城市</option>
                </select>
                <select class="form-control" name="county" id="county">
                    <option value="">请选择地区</option>
                </select>
            </div>
        </div>
    </div>
    <div class="control-group">
        <div class="controls">
            <input placeholder="店铺详细地址" class="form-control" type="text" name="address">
            <p class="help-block">请填写平时正常的发货地址</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">店铺后台截图</label>
        <div class="controls">
            <div class="tieyu-icon image-upload-container">
                <img data-input-name="shop_pic" class="image-upload" src="<?php echo CDN_BINARY_URL; ?>cross.png">
                <input type="hidden" name="shop_pic">
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
            <a id="btn-new-shop" class="btn btn-primary" data-url="<?php echo base_url('requests/shop_bind_handle'); ?>">提交审核</a>
        </div>
    </div>
</form>
<div id="dialog-shop-add-success" class="modal fade" tabindex="-1" role="dialog" data-target="<?php echo base_url('pages/shop_list'); ?>">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">绑定新店铺成功</h4>
            </div>
            <div class="modal-body">
                <p>恭喜，您新的店铺已经绑定成功，工作人员将会在 2 个工作日内完成审核，请耐心等待。</p>
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
    window.s = ["province", "city", "county"];
    window.def_select_name = ["请选择省份", "请选择城市", "请选择区县"];
    _init_area();

    $(".platform").change(
        function() {
            var selectedValue = $("input[name='platform']:checked").val(); //String
            var textID = '';
            switch(parseInt(selectedValue)) {
                case <?php echo PLATFORM_TYPE_TAOBAO; ?>:
                    textID = '店主淘宝旺旺ID';
                    break;
                case <?php echo PLATFORM_TYPE_PINDUODUO; ?>:
                    textID = '店主拼多多ID';
                    break;
                default:
                    textID = '店主淘宝旺旺ID';
            }
            document.getElementById("shop_ww").innerHTML = textID; //JS
            $("input[name='shop_ww']").attr('placeholder', textID); //jQuery

        }
    );

    $('#btn-new-shop').click(function (e) {
            e.preventDefault();
            var form_data = $('.form-new-shop').formToJSON();
            var that = $(this);

            hide_error_message();

            if (invalid_parameter(form_data) || form_data.city == "请选择城市" || form_data.county == "请选择区县" || form_data.province == "请选择省份") {
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
                        $('#dialog-shop-add-success').modal('show');
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });

        $('#dialog-shop-add-success').on('hidden.bs.modal', function () {
            goto_url($(this).data('target'), 50);
        });

        bind_image_upload_event();
    });
</script>
<?php if (!is_working_hour()) : ?>
    <div class="well">
        <p>尊敬的用户：</p>
        <p>现在是非工作时间，您的充值申请不会马上被处理，我们会在下一个工作日依次按顺序处理，给您带来的不便敬请谅解。</p>
        <p style="color:red;">平台工作时间：</p>
        <p style="color:red;margin-left:40px;">周一 ~ 周六 09:00 ~ 12:00 13:00~18:00（法定节假日除外）</p>
    </div>
<?php endif; ?>
<div class="well" style="font-size: 18px;">
    <!--<p style="color: red;">请不要往老卡信息上转账充值，不然全部审核无效退回！</p>-->
    <p>转账到<!--<font style="color: red;">新的</font>-->吉娃娃官方账号：</p>
    <p>中国民生银行&nbsp;&nbsp;<span style="color: red;"><?php echo MY_BANK_CARD;?></span>&nbsp;&nbsp;(户名：<span style="color: red;"> <?php echo MY_BANK_NAME;?></span>&nbsp;&nbsp;开户行：<?php echo MY_BANK_OPEN_LINE;?>) </p>
</div>
<form class="form-horizontal form-top-up">
<!--    <div class="control-group">-->
<!--        <label class="control-label">请输入转入银行账号</label>-->
<!--        <div class="controls">-->
<!--            <input placeholder="银行名称" class="form-control" type="text" name="zhuanru_bank_name">-->
<!--            <p class="help-block">请输入转入银行账号</p>-->
<!--        </div>-->
<!--    </div>-->

    <div class="control-group">
        <label class="control-label">请选择汇款账号（如您是支付宝充值，可不选择汇款账号）:</label>
            <select class="form-control" name="huikuan_bank_name">
                <option value="" selected>请选择汇款账号</option>
                <?php
                foreach ($Banklist as $val){
                    echo '<option value='. $val->bank_name."-".$val->bank_province ."-".$val->bank_city."-".$val->bank_county."-".$val->bank_card_num.'>'. $val->bank_name."-".$val->bank_province ."-".$val->bank_city."-".$val->bank_county."-".$val->bank_card_num.'</option>';
                }
                ?>
            </select>

    </div>
    <div class="control-group">
        <label class="control-label">您的姓名</label>
        <div class="controls">
            <input placeholder="姓名" class="form-control" type="text" name="transfer_person">
            <p class="help-block">请输入您綁定的银行卡开户人姓名</p>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">联系电话</label>
        <div class="controls">
            <input placeholder="联系电话" class="form-control" type="text" name="transfer_contact"
                   value="<?php echo $SellerName; ?>">
            <p class="help-block">当充值出现问题时，我们将通过此号码来联络您</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">充值金额</label>
        <div class="controls">
            <input id="transfer_amount" placeholder="充值金额(保留小数点后2位)" class="form-control" type="number" name="transfer_amount" onmousewheel="return false;" min="0">
            <p class="help-block">请输入充值金额，保留小数点后2位</p>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label">转账备注</label>
        <div class="controls">
            <input id="transfer_memo" class="form-control" type="text" readonly="readonly" name="transfer_memo" value="<?php echo $SellerName; ?>充值">
            <p class="help-block">请在转账时备注填写此栏显示的内容</p>
        </div>
    </div>
    <div class="form-group" style="align-content: left;">
        <label class="col-md-3 control-label" style="margin-left: -270px;">
            转账截图<span class="label label-default">必填</span>
        </label>
        <div class="col-md-8">
            <div class="tieyu-icon image-upload-container">
                <img data-input-name="chongzhi_img" class="image-upload" src="<?php echo empty($template_info) ? CDN_BINARY_URL . 'cross.png' : CDN_DOMAIN . $template_info->chongzhi_img; ?>">
                <input type="hidden" name="chongzhi_img" value="<?php echo empty($template_info) ? '' : $template_info->chongzhi_img; ?>">
            </div>
            <p class="help-block">转账截图务必真实有效</p>
        </div>
    </div>
    <div class="control-group" style="margin:20px 0;">
        <div class="controls">
            <div id="error_display" class="alert"></div>
        </div>
    </div>
    <div class="control-group" style="margin:20px 0;">
        <div class="controls">
            <a id="btn-top-up" class="btn btn-primary" data-url="<?php echo base_url('requests/top_up_handle'); ?>">提交</a>
        </div>
    </div>
</form>
<div id="dialog-top-up-success" class="modal fade" tabindex="-1" role="dialog"
     data-target="<?php echo base_url('pages/top_up_records'); ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">提交充值申请成功</h4>
            </div>
            <div class="modal-body">
                <?php
                if (is_working_hour()) {
                    echo '<p>恭喜，您的充值申请平台已经受理，请耐心等待。</p>';
                } else {
                    echo '<p>尊敬的用户：</p>';
                    echo '<p>现在是非工作时间，您的充值申请将会顺延到下一工作日处理，请您耐心等待，感谢你的理解与配合。</p>';
                    echo '<p style="color:red;">平台工作时间：</p>';
                    echo '<p style="color:red;">周一 ~ 周六 09:00 ~ 12:00 13:00~18:00（法定节假日除外）</p>';
                }
                ?>
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
        $('#btn-top-up').click(function (e) {
            e.preventDefault();
            var form_data = $('.form-top-up').formToJSON();
            var number_decimal_pattern = /^\d+(\.\d+)?$/;
            var that = $(this);

            hide_error_message();
            form_data.huikuan_bank_name = (form_data.huikuan_bank_name == "") ? '支付宝' :  form_data.huikuan_bank_name;
            if (invalid_parameter(form_data)) {
                show_error_message('所有字段均不能为空，请填写所有字段');
                return;
            }

            if (!mobile_num_pattern.test(form_data.transfer_contact)) {
                show_error_message('请填写正确的手机号码');
                return;
            }
            if (!number_decimal_pattern.test(form_data.transfer_amount)) {
                show_error_message('请填写正确的充值金额(保留小数点后2位)');
                return;
            }

            var num = new Number(form_data.transfer_amount);
            form_data.transfer_amount = num.toFixed(2);
            
            that.addClass('disabled');
            that.attr("disabled", true);

            ajax_request(
                that.data('url'),
                form_data,
                function (e) {
                    if (e.code == CODE_SUCCESS) {
                        $('#dialog-top-up-success').modal('show');
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });

        $('#dialog-top-up-success').on('hidden.bs.modal', function (e) {
            goto_url($(this).data('target'), 50);
        })
        bind_image_upload_event(true);
    });
</script>
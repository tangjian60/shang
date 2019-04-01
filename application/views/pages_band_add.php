<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">绑定银行卡</h3>
    </div>
    <div class="panel-body" style="padding:50px 0;">
        <form class="form-horizontal form-bind-bank">
            <div class="form-group">
                <label class="col-md-3 control-label">
                    真实姓名<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <input placeholder="请填写真实姓名" class="form-control" type="text" name="true_name">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    银行卡号<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <input placeholder="请填写本人的银行卡" class="form-control" type="text" name="bank_card_num">
                    <p style="font-size:8px;color:red;" class="help-block">必须为本人银行卡</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">
                    开户银行<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <select class="form-control" name="bank_name">
                        <option value="" selected>请选择开户银行</option>
                        <?php
                        foreach ($BanksList as $bank){
                            echo '<option value='. $bank .'>'. $bank .'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group form-inline">
                <label class="col-md-3 control-label">
                    开户地区<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
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
            <div class="form-group">
                <label class="col-md-3 control-label">
                    开户支行<span class="label label-default">必填</span>
                </label>
                <div class="col-md-8">
                    <input placeholder="请填写开户支行" class="form-control" type="text" name="bank_branch">
                    <p style="font-size:8px;color:red;" class="help-block">可联系发卡银行咨询</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label"></label>
                <div class="col-md-8">
                    <div id="error_display" class="alert"></div>
                </div>
            </div>
        </form>
        <div id="dialog-bank-add-success" class="modal fade" tabindex="-1" role="dialog" data-target="<?php echo base_url('bank/bank_list'); ?>">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">绑定银行卡成功</h4>
                    </div>
                    <div class="modal-body">
                        <p>恭喜，银行卡成功</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-primary" data-dismiss="modal">查看</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="text-align:center;margin:50px auto;">
    <button id="btn_submit_cert" type="button" class="btn btn-lg btn-success" data-url="<?php echo base_url('bank/bank_handle'); ?>">提交绑定</button>
</div>
<script type="text/javascript" src="<?php echo CDN_BINARY_URL; ?>area.js?v=201810221530"></script>
<script type="text/javascript">
    $(function () {
        window.s = ["province", "city", "county"];
        window.def_select_name = ["请选择省份", "请选择城市", "请选择区县"];
        _init_area();


        $('#btn_submit_cert').click(function (e) {
            e.preventDefault();
            var form_data = $('.form-bind-bank').formToJSON();
            var that = $(this);

            hide_error_message();

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
                        $('#dialog-bank-add-success').modal('show');
                    } else {
                        show_error_message(e.msg);
                        that.removeClass('disabled');
                        that.attr("disabled", false);
                    }
                });
        });
        $('#dialog-bank-add-success').on('hidden.bs.modal', function () {
            goto_url($(this).data('target'), 50);
        });
    });
</script>

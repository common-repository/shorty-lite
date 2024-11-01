<div class="page-header">
    <h2><i class="fa fa-upload"></i> Import / Add Conversions</h2>
    <p>Manually add conversions or upload conversion reports in Microsoft Excel or CSV from third-party sites and affiliate networks. Items marked <i class="fa fa-exclamation-circle text-silver"></i>  are required.</p>
</div>
<p>&nbsp;</p>
<div class="form">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"> Add Conversion</div>
                </div>
                <div class="panel-body">
                    <form method="POST" name="frmNewConversion">
                        <div class="form-group">
                            <label class="control-label" for="conversion_date"><i class="fa fa-exclamation-circle text-silver"></i> Date &amp; Time: </label>
                            <div class="controls">
                                <input required name="conversion_date" type="text" class="form-control datetimepicker" id="conversion_date" placeholder="Select Date & Time">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_name"><i class="fa fa-exclamation-circle text-silver"></i> Conversion Name: </label>
                            <div class="controls">
                                <input required name="goal_name" type="text" class="form-control" id="goal_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_type"><i class="fa fa-exclamation-circle text-silver"></i> Goal Type: </label>
                            <div class="controls">
                                <select required name="goal_type" id="goal_type" class="form-control">
                                    <option value="<?php echo GOAL_TYPE_LEAD; ?>">Lead</option>
                                    <option value="<?php echo GOAL_TYPE_SALE; ?>">Sale</option>
                                </select>
                                <p class="help-block">A lead and sale is treated differently based on your conversion settings.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_value"><i class="fa fa-exclamation-circle text-silver"></i> Goal Value: </label>
                            <div class="controls">
                                <div class="input-group">
                                    <input required type="text" name="goal_value" class="form-control" id="goal_value" placeholder="Example: 9.90">
                                    <span class="input-group-addon"><?php echo get_option(SH_PREFIX . 'settings_currency'); ?></span></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_reference">Unique ID: </label>
                            <div class="controls">
                                <input type="text" name="goal_reference" class="form-control" id="goal_reference" placeholder="Example: 0123456">
                                <p class="help-block">Enter a unique ID so we can tell when to recognize a goal conversion, and when to ignore it. If you have no unique ID to pass, leave this blank. <a href="#" rel="tooltip" data-original-title="This is normally your order ID, customer email address or transaction ID. You can automatically replace this unique id in your shopping cart process."><i class="fa fa-question-circle text-silver"></i></a></p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="click_id">CTID: </label>
                            <div class="controls">
                                <input name="click_id" type="text" class="form-control" id="click_id">
                                <p class="help-block">You should have passed the CTID variable to your affiliate network in the format of a Sub ID. Refer to your network's report and enter that Sub ID here.</p>
                            </div>
                        </div>
                        <div class="form-actions"><button name="btnAdd" type="submit" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add Conversion</button></div>
                    </form>
                </div>
            </div>
        </div>        
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">IMPORT FILE</div>
                </div>
                <div class="panel-body">
                    <div class="alert alert-block alert-info">
                        <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

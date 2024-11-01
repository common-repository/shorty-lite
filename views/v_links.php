<div class="modal fade" id="campaigns" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Custom Tracking</h4>
            </div>
            <form class="modal-body form" style="padding:20px;">
                <fieldset>
                    <legend> Source &amp; Cost</legend>
                    <div class="form-group">
                        <label class="control-label" for="label10">Customizeable Link: </label>
                        <div class="controls">
                            <input tabindex="1" name="campaign_link" type="text" class="form-control input-lg" id="campaign_link" value="?source=kreydle&amp;medium=referral&amp;campaign=blogs&amp;content=footer_ads&amp;term=keyword&amp;cpc=0.10" size="">
                            <p class="help-block"> The full campaign link is useful if you need to override the variables manually for each ad and track multiple parameters.</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="label17"><i class="fa fa-exclamation-circle text-silver"></i> Source: </label>
                            <div class="controls">
                                <input tabindex="2" name="campaign_source" type="text" class="campaign_properties form-control" id="campaign_source">
                                <p class="help-block">The referring source. E.g: google, facebook, citysearch, soloads, newsletter4.</p>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="label28">Medium: </label>
                            <div class="controls">
                                <input tabindex="3" name="campaign_medium" type="text" class="campaign_properties form-control" id="campaign_medium">
                                <p class="help-block">Marketing medium. E.g: cpc, banner, email. <a href="https://support.google.com/analytics/answer/1191184?hl=en" target="_blank">Read more about MCF Channels</a>.</p>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="label19">Cost Per Click: </label>
                            <div class="controls">
                                <div class="input-group">
                                    <input tabindex="4" name="campaign_cpc" type="text" class="campaign_properties form-control" id="campaign_cpc" placeholder="0.00">
                                    <span class="input-group-addon"><?php echo get_option(SH_PREFIX . 'settings_currency'); ?></span></div>
                                <p class="help-block">Enter the cost per click here. The currency is defined in the <a href="http://wp.local/wp-admin/admin.php?page=sh_settings_page">settings</a> page.</p>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="row" id="parameters_add">
                    <div class="form-group col-md-4">
                        <label for="label17">Campaign: </label>
                        <div class="controls">
                            <input tabindex="5" id="campaign_campaign" type="text" class="campaign_properties form-control" id="campaign_campaign">
                            <p class="help-block">Product, promo code, or slogan. E.g: christmas sale, exit offer, launch email.</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="label17">Content: </label>
                        <div class="controls">
                            <input tabindex="6" name="campaign_content" type="text" class="campaign_properties form-control" id="campaign_content">
                            <p class="help-block">Use to differentiate ads. E.g: 250x250 banner, followup2, autoresponder6.</p>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="label7">Term: </label>
                        <div class="controls">
                            <input tabindex="7"  name="campaign_term" type="text" class="campaign_properties form-control" id="campaign_term">
                            <p class="help-block">Identify the paid keywords used.</p>
                        </div>
                    </div>
                </div>
                <fieldset>
                </fieldset>
            </form>
            <div class="modal-footer">
                <div class="btn-group pull-left">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-header">
    <h2><i class="fa fa-list-alt"></i> Trackers
        <a href="?page=<?php echo 'sh_trackers'; ?>&action=add" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
    </h2>
    <p>Create a tracking link to mask or cloak a link, and redirect it to the actual destination URL. </p>
</div>
<table id="v_links" class="table table-hover" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th class="col-sm-1"><input type="checkbox" name="checkbox" class="cbAll"></th>
            <th>Tracker Name</th>
            <th>Cloaked Link</th>
            <th>Tags</th>
            <th class="col-sm-1">Ads & CPC</th>
            <th class="col-sm-2">Actions</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5">Loading</td>
        </tr>
    </tbody>
</table>


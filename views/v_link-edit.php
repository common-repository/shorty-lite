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
<div id="v_links_edit" class="container-fluid">
    <div class="row">
        <div class="ltbody">
            <div class="page-header">
                <h2><i class="fa fa-list-alt"></i> Edit Tracker <small>| <a target="_blank" href="<?php echo $this->current_domain(TRUE, FALSE, TRUE) . $link->tracking_link; ?>"><?php echo $this->current_domain(TRUE, FALSE, TRUE) . $link->tracking_link; ?></a> <span data-original-title="Copy to Clipboard" data-text="<?php echo $this->current_domain(TRUE, FALSE, TRUE) . $link->tracking_link; ?>" class="clippy btn btn-xs btn-default" rel="tooltip" ><i class="fa fa-copy"></i> Copy URL</span> <button class="btncustomize btn btn-xs btn-default" data-text="<?php echo $this->current_domain(TRUE, FALSE, TRUE) . $row->tracking_link; ?>"><i class="fa fa-random"></i> Customize</button></small></h2>
                Create a new tracking link to track clicks and visitors. Items marked <i class="fa fa-exclamation-circle text-silver"></i> are required.
            </div>
            <div class="form">
                <form method="POST" name="frmEditLink">
                    <?php wp_nonce_field('frmEditLink-' . $link->id); ?>
                    <div class="row">
                        <div class="col-md-6">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="panel-title">Tracker Details</div>
                                </div>
                                <div class="panel-body">
                                    <fieldset>
                                        <div class="form-group <?php echo $this->form_error_class('link_name'); ?>">
                                            <label class="control-label" for="link_name"><i class="fa fa-exclamation-circle text-silver"></i> Tracker Name: </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" id="link_name" name="link_name" placeholder="Facebook Link" value="<?php echo stripslashes($this->set_value('link_name', $link->link_name)); ?>">
                                                <?php echo $this->form_error_message('link_name'); ?>
                                                <p class="help-block">A reference name for your link. Make it easy to remember so you can find it later.</p>
                                            </div>
                                        </div>
                                        <div class="form-group <?php echo $this->form_error_class('primary_url'); ?>">
                                            <label class="control-label" for="primary_url"><i class="fa fa-exclamation-circle text-silver"></i> Primary URL: </label>
                                            <div class="controls  has-feedback">
                                                <input type="text" class="form-control" id="primary_url" name="primary_url" placeholder="http://" value="<?php echo $this->set_value('primary_url', $link->destination_url); ?>">
                                                <span id="url_availability" class="form-control-feedback" aria-hidden="true"></span>
                                                <?php echo $this->form_error_message('primary_url'); ?>
                                                <p class="help-block">The actual URL of the link you want to track. This could be a URL to your own website, or an affiliate link.</p>
                                            </div>
                                        </div>
                                        <div class="form-group <?php echo $this->form_error_class('mobile_url'); ?>">
                                            <label class="control-label" for="mobile_url"> Mobile URL: </label>
                                            <div class="controls has-feedback">
                                                <input name="mobile_url" type="text" class="form-control" id="mobile_url" placeholder="http://" value="<?php echo $this->set_value('mobile_url', $link->mobile_url); ?>">
                                                <span id="url_availability3" class="form-control-feedback" aria-hidden="true"></span>
                                                <?php echo $this->form_error_message('mobile_url'); ?>
                                                <p class="help-block">The mobile URL is used instead of your Primary URL for visitors using mobile devices.</p>
                                            </div>
                                        </div>
                                        <div class="form-group <?php echo $this->form_error_class('tracking_link'); ?>">
                                            <label class="control-label" for="tracking_link"><i class="fa fa-exclamation-circle text-silver"></i> Cloaked Link: </label>
                                            <div class="controls">
                                                <div class="input-group"><span class="input-group-addon"><?php echo $this->current_domain(FALSE, FALSE, TRUE); ?></span>
                                                    <input type="text" class="form-control" id="tracking_link" name="tracking_link" placeholder="something" value="<?php echo $this->set_value('tracking_link', $link->tracking_link); ?>">
                                                </div>
                                                <?php echo $this->form_error_message('tracking_link'); ?>
                                                <p class="help-block">This is a redirect link used to mask and track the Destination URL. Make it short and easy to remember.</p>
                                            </div>
                                        </div>
                                        <div id="reference_tags" class="form-group">
                                            <label class="control-label" for="reference_tags">Reference Tags: </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" name="reference_tags" value="<?php echo $this->set_value('reference_tags', $link->reference_tags); ?>" placeholder="keyword1, keyword 2, keyword 3">
                                                <p class="help-block">Use tags instead of groups to organize your links and find them easily later.</p>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <div class="control-group">
                                            <label for="cloaking_status_enable" >Cloaking: </label>
                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('cloaking_status_enable', $link->cloaking_status_enable) == 1 ? 'active' : ''; ?>" data-target="#ct">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('cloaking_status_enable', $link->cloaking_status_enable) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">You can choose to cloak this link and display a viral bar. <span class="text-error"><strong>NOTE:</strong></span> Some websites block iframes, so our cloaking feature will not work on their pages. Please also check with your affiliate network to confirm that cloaking is not against their terms.</p>
                                            </div>
                                            <input type="hidden" id="cloaking_status_enable" name="cloaking_status_enable" class="switch-data" value="<?php echo $link->cloaking_status_enable; ?>"/>
                                        </div>
                                        <!--collapse-->
                                        <div class="collapse out form-group" id="ct">
                                            <label class="control-label" for="cloaking_type">Cloaking Type: </label>
                                            <div class="controls">
                                                <select id="cloaking_type" name="cloaking_type" class="form-control">
                                                    <option value="<?php echo SHORTLY_CLOAKING_TYPE_BASIC; ?>" <?php echo selected($this->set_value('cloaking_type', $link->cloaking_type), SHORTLY_CLOAKING_TYPE_BASIC, true); ?>><?php echo SHORTLY_CLOAKING_TYPE_BASIC; ?></option>
                                                    <option value="<?php echo SHORTLY_CLOAKING_TYPE_VIRAL; ?>" <?php echo selected($this->set_value('cloaking_type', $link->cloaking_type), SHORTLY_CLOAKING_TYPE_VIRAL, true); ?>><?php echo SHORTLY_CLOAKING_TYPE_VIRAL; ?></option>
                                                </select>
                                                <p class="help-block">You can opt to display or hide the viral bar.</p>
                                            </div>
                                            <fieldset class="cloaking_type_child collapse out ">
                                                <label class="control-label" for="bar_position">Bar Position: </label>
                                                <div class="controls">
                                                    <select id="bar_position" name="bar_position" class="form-control">
                                                        <option value="<?php echo SHORTLY_BAR_POSITION_TOP; ?>" <?php echo selected($this->set_value('bar_position', $link->bar_position), SHORTLY_BAR_POSITION_TOP, true); ?>><?php echo SHORTLY_BAR_POSITION_TOP; ?></option>
                                                        <option value="<?php echo SHORTLY_BAR_POSITION_BOTTOM; ?>" <?php echo selected($this->set_value('bar_position', $link->bar_position), SHORTLY_BAR_POSITION_BOTTOM, true); ?>><?php echo SHORTLY_BAR_POSITION_BOTTOM; ?></option>
                                                    </select>
                                                    <p class="help-block">You can opt to display viral bar on top or bottom.</p>
                                                </div>
                                            </fieldset>
                                            <label class="control-label" for="frame_content">Frame Content: </label>
                                            <div class="controls">
                                                <select id="frame_content" name="frame_content" class="form-control">
                                                    <option value="<?php echo SHORTLY_FRAME_CONTENT_VISIBLE; ?>" <?php echo selected($this->set_value('frame_content', $link->frame_content), SHORTLY_FRAME_CONTENT_VISIBLE, true); ?>>Visible to search engines (index)</option>
                                                    <option value="<?php echo SHORTLY_FRAME_CONTENT_HIDDEN; ?>" <?php echo selected($this->set_value('frame_content', $link->frame_content), SHORTLY_FRAME_CONTENT_HIDDEN, true); ?>>Hidden from search engines (noindex)</option>
                                                </select>
                                                <p class="help-block">You can choose to hide the content of the cloaked link from search engines (recommended) or make it visible.</p>
                                            </div>
                                            <fieldset class="">
                                                <div class="form-group <?php echo $this->form_error_class('meta_title'); ?>">
                                                    <label class="control-label" for="meta_title">Meta Title: </label>
                                                    <div class="controls">
                                                        <!--<div class="input-group">-->
                                                        <input type="text" class="form-control" name="meta_title" value="<?php echo $this->set_value('meta_title', stripslashes($link->meta_title)); ?>">
                                                        <!--<div class="input-group-btn"><button data-meta="title" class="btn btn-default get_meta">Get</button></div>-->
                                                        <!--</div>-->
                                                        <?php echo $this->form_error_message('meta_title'); ?>
                                                        <p class="help-block">Create a compelling and interesting title. This title will be used when posting your link to social media services, and in all public places.</p>
                                                    </div>
                                                </div>
                                                <div class="form-group <?php echo $this->form_error_class('meta_description'); ?>">
                                                    <label class="control-label" for="meta_description">Meta Description: </label>
                                                    <div class="controls">
                                                        <!--<div class="input-group">-->
                                                        <input type="text" class="form-control" name="meta_description" value="<?php echo $this->set_value('meta_description', stripslashes($link->meta_description)); ?>">
                                                        <!--<div class="input-group-btn"><button data-meta="description" class="btn btn-default get_meta">Get</button></div>-->
                                                        <!--</div>-->
                                                        <?php echo $this->form_error_message('meta_description'); ?>
                                                        <p class="help-block">Type in a brief description about the link. This description will be used when posting your link to social media services, and in all public places.</p>
                                                    </div>
                                                </div>
                                                <div class="form-group <?php echo $this->form_error_class('meta_image'); ?>">
                                                    <label class="control-label" for="label47">Meta Image: </label>
                                                    <div class="controls">
                                                        <input name="meta_image" type="text" class="form-control" id="meta_image" value="<?php echo $this->set_value('meta_image', $link->meta_image); ?>">
                                                        <?php echo $this->form_error_message('meta_image'); ?>
                                                        <p class="help-block">The image will be automatically picked up and displayed by social media services when someone shares your cloaked link.</p>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <div class="form-group">
                                                <label class="control-label" for="retargeting_code">Custom Codes: </label>
                                                <div class="controls">
                                                    <textarea name="retargeting_code" type="text" class="form-control" ><?php echo $this->set_value('retargeting_code', stripslashes($link->retargeting_code)); ?></textarea>
                                                    <p class="help-block">You can enter other codes in here, for example an optin popup.</p>
                                                </div>
                                            </div>

                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <div class="control-group">
                                            <label for="param_tag_affiliate_tracking">Affiliate Tracking: </label>

                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1"
                                                            class="btn btn-small <?php echo $this->set_value('param_tag_affiliate_tracking', $link->param_tag_affiliate_tracking) == 1 ? 'active' : ''; ?>"
                                                            data-target="#ast">On
                                                    </button>
                                                    <button type="button" data-value="0"
                                                            class="btn btn-small <?php echo $this->set_value('param_tag_affiliate_tracking', $link->param_tag_affiliate_tracking) == 0 ? 'active' : ''; ?>"
                                                            data-target="#">Off
                                                    </button>
                                                </div>
                                                <p class="help-block">Turn this on to automatically pass any tag within your campaigns to your affiliate network's SubID parameters, as specified below.</p>
                                            </div>
                                            <input id="param_tag_affiliate_tracking" name="param_tag_affiliate_tracking" class="switch-data" type="hidden" value="<?php echo $link->param_tag_affiliate_tracking; ?>"/>
                                        </div>

                                        <div class="collapse out form-group" id="ast">
                                            <label class="control-label" for="param_tag_affiliate_network">SubID Format: </label>
                                            <div class="controls">
                                                <select id="param_tag_affiliate_network" name="param_tag_affiliate_network" class="form-control">
                                                    <option value="<?php echo AFFILIATE_NETWORK_AFF_SUB; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_AFF_SUB, true); ?>><?php echo AFFILIATE_NETWORK_AFF_SUB; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_AFFTRACK; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_AFFTRACK, true); ?>><?php echo AFFILIATE_NETWORK_AFFTRACK; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_TID; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_TID, true); ?>><?php echo AFFILIATE_NETWORK_TID; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_SID; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_SID, true); ?>><?php echo AFFILIATE_NETWORK_SID; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_S1; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_S1, true); ?>><?php echo AFFILIATE_NETWORK_S1; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_U1; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_U1, true); ?>><?php echo AFFILIATE_NETWORK_U1; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_C1; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_C1, true); ?>><?php echo AFFILIATE_NETWORK_C1; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_SUBID; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_SUBID, true); ?>><?php echo AFFILIATE_NETWORK_SUBID; ?></option>
                                                    <option value="<?php echo AFFILIATE_NETWORK_SUBID1; ?>" <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), AFFILIATE_NETWORK_SUBID1, true); ?>><?php echo AFFILIATE_NETWORK_SUBID1; ?></option>
                                                    <option <?php echo selected($this->set_value('param_tag_affiliate_network', $link->param_tag_affiliate_network), 'Custom', true); ?>>Custom</option>
                                                </select>
                                                <p class="help-block">If you're not sure which SubID tag to use, <a href="#subid_types" data-toggle="collapse">refer to this chart</a> for most popular affiliate networks and systems.</p>
                                                <div id="subid_types" class="out collapse" style="margin-top: 10px;">
                                                    <table class="table table-condensed">
                                                        <thead>
                                                            <tr>
                                                                <th>SubID</th>
                                                                <th>Network</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td><code>aff_sub</code></td>
                                                                <td>HasOffers, Adsimilis</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>afftrack</code></td>
                                                                <td>Shareasale</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>tid</code></td>
                                                                <td>ClickBank, JVZoo</td>
                                                            </tr>

                                                            <tr>
                                                                <td><code>sid</code></td>
                                                                <td>CJ, PepperJam, ClickPromise, CAKE, LinkTrust</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>s1</code></td>
                                                                <td>PeerFly, A4D, Above All Offers, C2M, Cash Network, Clickbooth, GlobalWide, MaxBounty, NeverBlue, XY7</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>u1</code></td>
                                                                <td>LinkShare</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>c1</code></td>
                                                                <td>HitPath, W4</td>
                                                            </tr>
                                                            <tr>
                                                                <td><code>subid</code></td>
                                                                <td>CPAWay</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <p class="alert alert-info">
                                                        Some networks may allow more than one SubID to be passed along with your affiliate link. However you only need to use one.
                                                    </p>
                                                </div>
                                            </div>

                                            <div
                                                class="collapse out param_tag_affiliate_network_custom control-group <?php echo $this->form_error_class('param_tag_affiliate_network_custom'); ?>">
                                                <label class="control-label" for="sub_id_custom"><i class="fa fa-exclamation-circle text-silver"></i> Custom SubID: </label>
                                                <div class="controls">
                                                    <input name="param_tag_affiliate_network_custom" maxlength="12" class="form-control" type="text" value="<?php echo $this->set_value('param_tag_affiliate_network_custom', $link->param_tag_affiliate_network_custom); ?>"/>
                                                    <?php echo $this->form_error_message('param_tag_affiliate_network_custom'); ?>
                                                    <p class="help-block">Specify your own <code>SubID</code> format. Make sure  it allows up to 12 characters.</p>
                                                </div>
                                            </div>

                                            <div class="control-group">
                                                <label class="control-label" for="blank_referrer">Blank Referrers: </label>
                                                <div class="controls">
                                                    <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                        <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('blank_referrer', $link->blank_referrer) == 1 ? 'active' : ''; ?>" data-target="#">On</button>
                                                        <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('blank_referrer', $link->blank_referrer) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                    </div>
                                                    <p class="help-block">Hide the referring URL and wipe all source data to protect your traffic source from being identified.</p>
                                                </div>
                                                <input id="blank_referrer" name="blank_referrer" class="switch-data" type="hidden"/>
                                            </div>

                                            <div class="control-group">
                                                <label for="param_tag_forward_param">Forward Parameters: </label>
                                                <div class="controls">
                                                    <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                        <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('param_tag_forward_param', $link->param_tag_forward_param) == 1 ? 'active' : ''; ?>" data-target="#">On</button>
                                                        <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('param_tag_forward_param', $link->param_tag_forward_param) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                    </div>
                                                    <p class="help-block">Turn this on to automatically forward all parameters from tracking link to destination URL</p>
                                                </div>
                                                <input id="param_tag_forward_param" name="param_tag_forward_param" class="switch-data" type="hidden" value="<?php echo $link->param_tag_forward_param; ?>"/>
                                            </div>

                                            <div class="control-group">
                                                <label for="param_tag_forward_campaign">Forward Campaign Tag: </label>
                                                <div class="controls">
                                                    <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                        <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('param_tag_forward_campaign', $link->param_tag_forward_campaign) == 1 ? 'active' : ''; ?>" data-target="#">On</button>
                                                        <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('param_tag_forward_campaign', $link->param_tag_forward_campaign) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                    </div>
                                                    <p class="help-block">Turn this on to automatically pass any campaign tag within your campaigns to the destination URL using the correct <code>utm_</code> format. Do not enable if you are not promoting your own website.</p>
                                                </div>
                                                <input id="param_tag_forward_campaign" name="param_tag_forward_campaign" class="switch-data" type="hidden" value="<?php echo $link->param_tag_forward_campaign; ?>"/>
                                            </div>
                                        </div>

                                    </fieldset>

                                    <fieldset>
                                        <div class="control-group">
                                            <label for="param_tag_retargeting">Retargeting: </label>

                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('param_tag_retargeting', $link->param_tag_retargeting) == 1 ? 'active' : ''; ?>" data-target="#rc">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('param_tag_retargeting', $link->param_tag_retargeting) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">Enable retargeting to buy ads for people who have clicked on this link. No iFrames are used.</p>
                                            </div>

                                            <input id="param_tag_retargeting" name="param_tag_retargeting" class="switch-data" type="hidden" value="<?php echo $link->param_tag_retargeting; ?>"/>
                                        </div>

                                        <div class="collapse out form-group" id="rc">
                                            <div class="alert alert-block alert-info">
                                                <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                                            </div>
                                        </div>
                                    </fieldset>

                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="panel-title">Link Automation</div>
                                </div>
                                <div class="panel-body">
                                    <fieldset>
                                        <div class="control-group">
                                            <label class="control-label" for="label45">Uptime monitoring: </label>
                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('uptime_monitoring_enabled', $link->uptime_monitoring_enabled) == 1 ? 'active' : ''; ?>" data-target="#uptime">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('uptime_monitoring_enabled', $link->uptime_monitoring_enabled) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">Monitor the primary destination URL and alert by email if there is a downtime. Turning this one may cause require more work for your server, so do it only if its critical!</p>
                                            </div>
                                            <input id="uptime_monitoring_enabled" name="uptime_monitoring_enabled" class="switch-data" type="hidden" />
                                        </div>
                                        <section id="uptime" class="collapse out">
                                            <div class="alert alert-block alert-info">
                                                <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                                            </div>
                                        </section>
                                    </fieldset>

                                    <fieldset>
                                        <!--not yet done-->
                                        <div class="control-group">
                                            <label class="control-label" for="auto_keyword_linking_enable">Auto Keyword Linking: </label>
                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('auto_keyword_linking_enable', $link->auto_keyword_linking_enable) == 1 ? 'active' : ''; ?>" data-target="#akl">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('auto_keyword_linking_enable', $link->auto_keyword_linking_enable) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">Turn on automatic keyword linking to get instant, targeted traffic from all your blog posts and pages. You can <a href="?page=sh_settings_page">control options here</a>.</p>
                                            </div>
                                            <input id="auto_keyword_linking_enable" name="auto_keyword_linking_enable" class="switch-data" type="hidden" value="<?php echo $link->auto_keyword_linking_enable; ?>"/>
                                        </div>

                                        <div class="collapse out form-group <?php echo $this->form_error_class('meta_keyword'); ?>" id="akl">
                                            <label class="control-label" for="meta_keyword">Meta Keywords: </label>
                                            <div class="controls">
                                                <!--<div class="input-group">-->
                                                <input type="text" class="form-control" name="meta_keyword" placeholder="keyword1, keyword 2, keyword 3" value="<?php echo $this->set_value('meta_keyword', stripslashes($link->meta_keyword)); ?>">
                                                <!--<div class="input-group-btn"><button data-meta="keywords" class="btn btn-default get_meta">Get</button></div>-->
                                                <!--</div>-->
                                                <?php echo $this->form_error_message('meta_keyword'); ?>
                                                <p class="help-block">Type in keywords most relevant to the link. Separate each keyword with a comma. If you do not enter any keywords, this tracking link will not be used in automatic keyword conversions.</p>
                                            </div>
                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <div class="control-group">
                                            <label class="control-label" for="geo_redirect_enable">Automatic Geo Redirect: </label>
                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('geo_redirect_enable', $link->geo_redirect_enable) == 1 ? 'active' : ''; ?>" data-target="#geo_redirect">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('geo_redirect_enable', $link->geo_redirect_enable) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">Turn on automatic geographical redirects to send users to a different landing page based on country.</p>
                                            </div>
                                            <input id="geo_redirect_enable" name="geo_redirect_enable" class="switch-data" type="hidden" value="<?php echo $link->geo_redirect_enable; ?>"/>
                                        </div>
                                        <div class="collapse out form-group" id="geo_redirect">
                                            <div class="alert alert-block alert-info">
                                                <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div class="control-group">
                                            <label class="control-label" for="click_limiter_enable">Click Limiter: </label>
                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('click_limiter_enable', $link->click_limiter_enable) == 1 ? 'active' : ''; ?>" data-target="#click_limiter">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('click_limiter_enable', $link->click_limiter_enable) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">You can choose to send traffic to a different URL once you've reach a certain click limit.</p>
                                            </div>
                                            <input id="geo_redirect_enable" name="click_limiter_enable" class="switch-data" type="hidden" />
                                        </div>
                                        <fieldset class="collapse out " id="click_limiter">
                                            <div class="alert alert-block alert-info">
                                                <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                                            </div>
                                        </fieldset>
                                    </fieldset>
                                    <fieldset>
                                        <div class="control-group">
                                            <label class="control-label" for="link_expired_enable">Expiration Timer: </label>
                                            <div class="controls">
                                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio">
                                                    <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('link_expired_enable', $link->link_expired_enable) == 1 ? 'active' : ''; ?>" data-target="#link-expired">On</button>
                                                    <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('link_expired_enable', $link->link_expired_enable) == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                                </div>
                                                <p class="help-block">You can choose to expire this link automatically based on a date and time, and redirect clicks to another URL.</p>
                                            </div>
                                            <input type="hidden" id="link_expired_enable" name="link_expired_enable" class="switch-data"/>
                                        </div>
                                        <div class="collapse out form-group" id="link-expired">
                                            <div class="alert alert-block alert-info">
                                                <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="panel-title">Testing &amp; Debug</div>
                                </div>
                                <div class="panel-body">
                                    <fieldset>
                                        <div class="form-group">
                                            <label class="control-label" for="label6">Test URL: </label>
                                            <div class="controls">
                                                <input type="text" class="form-control" id="label6" placeholder="http://" value="<?php echo $this->current_domain(TRUE, FALSE, TRUE) . $link->tracking_link; ?>?mode=test">
                                                <p class="help-block">Use this URL to for testing and debugging purposes, for example to determine if a postback connection is working.</p>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="form-actions"><button name="btnEdit" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
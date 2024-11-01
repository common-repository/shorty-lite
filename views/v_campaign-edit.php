<div class="container-fluid">
    <div class="row">
        <div class="ltbody">
            <div class="page-header">
                <h2><i class="fa fa-random"></i> Edit Campaign <small>|  <a target="_blank" href="<?php echo $this->current_domain() . 'c/' . $campaign->tracking_campaign; ?>"><?php echo $this->current_domain() . 'c/' . $campaign->tracking_campaign; ?></a> <span data-original-title="Copy to Clipboard" data-text="<?php echo $this->current_domain() . 'c/' . $campaign->tracking_campaign; ?>" class="clippy btn btn-xs btn-default" rel="tooltip" ><i class="fa fa-copy"></i></span></small></h2>
                Update campaign. Items marked <i class="fa fa-exclamation-circle text-silver"></i> are required. 
            </div>
            <form method="POST" name="frmEditCampaign">
                <?php wp_nonce_field('frmEditCampaign-' . $campaign->id); ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">Campaign Details</div>
                            </div>
                            <div class="panel-body">
                                <fieldset>
                                    <div class="form-group <?php echo $this->form_error_class('link_id'); ?> <?php echo $this->form_error_class('tracking_link'); ?>">
                                        <label for="input3">   Choose Tracking Link: </label>
                                        <div class="controls">
                                            <input type="text" name="tracking_link" class="form-control typeahead" placeholder="Type keyword to choose.." value="<?php echo $this->set_value('tracking_link', $this->current_domain(TRUE) . $campaign->tracking_link); ?>" >
                                            <input id="link_id" name="link_id" type="hidden" value="<?php echo $this->set_value('link_id', $campaign->link_id); ?>" />
                                            <?php echo $this->form_error_message('link_id'); ?> <?php echo $this->form_error_message('tracking_link'); ?>
                                            <p class="help-block">Choose the tracking link you want to use for your campaign.</p>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset><legend>Add Campaign Parameters</legend>
                                    <div class="form-group <?php echo $this->form_error_class('source'); ?>">
                                        <label for="source"><i class="fa fa-exclamation-circle text-silver"></i> Source: </label>
                                        <div class="controls">
                                            <input  type="text" class="form-control" name="source" value="<?php echo $this->set_value('source', stripslashes($campaign->source)); ?>">
                                            <?php echo $this->form_error_message('source'); ?>
                                            <p class="help-block">The referring source. E.g: google, facebook, citysearch, soloads, newsletter4.</p>
                                        </div>
                                    </div>
                                    <div class="form-group <?php echo $this->form_error_class('medium'); ?>">
                                        <label for="medium"><i class="fa fa-exclamation-circle text-silver"></i> Medium: </label>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="medium" value="<?php echo $this->set_value('medium', stripslashes($campaign->medium)); ?>">
                                            <?php echo $this->form_error_message('source'); ?>
                                            <p class="help-block">Marketing medium. E.g: cpc, banner, email. <a href="https://support.google.com/analytics/answer/1191184?hl=en" target="_blank">Read more about MCF Channels</a>.</p>
                                        </div>
                                    </div>
                                    <div class="form-group <?php echo $this->form_error_class('campaign'); ?>">
                                        <label for="campaign"><i class="fa fa-exclamation-circle text-silver"></i> Campaign: </label>
                                        <div class="controls">
                                            <input required type="text" class="form-control" name="campaign" value="<?php echo $this->set_value('campaign', stripslashes($campaign->campaign)); ?>">
                                            <?php echo $this->form_error_message('campaign'); ?>
                                            <p class="help-block">Product, promo code, or slogan. E.g: christmas sale, exit offer, launch email.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="content">Content: </label>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="content" value="<?php echo $this->set_value('content', stripslashes($campaign->content)); ?>">
                                            <p class="help-block">Use to differentiate ads. E.g: 250x250 banner, followup2, autoresponder6.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="term">Term: </label>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="term" value="<?php echo $this->set_value('term', stripslashes($campaign->term)); ?>">
                                            <p class="help-block">Identify the paid keywords used.</p>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">Campaign Cost</div>
                            </div>
                            <div class="panel-body">
                                <div class="alert alert-block alert-info"><i class="fa fa-info-circle"></i> PPC and cost tracking is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&utm_medium=referral&utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and full support.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions"><button name="btnEditCampaign" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> Save Changes</button></div>
            </form>
        </div>
    </div>
</div>
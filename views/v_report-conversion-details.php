<div class="page-header">
    <h2><i class="fa fa-signal"></i> Conversion Details</h2>
    <p> Timeline and full details of your conversion.</p>
</div>
<div class="btn-toolbar">
    <div class="btn-group pull-left">
        <a href="?page=sh_conversions_page&action=report-conversions" class="btn btn-default">&laquo; Back to Conversions</a>
    </div>
</div>
<p>&nbsp;</p>
<div class="row">

    <div class="col-md-8">
        <ul class="timeline">
            <li class="timeline-inverted">
                <?php
                if ($conversion->status == 'Accepted') {
                    ?>
                    <div class="timeline-badge success"><i class="fa fa-check"></i></div>
                    <?php
                } else {
                    ?>
                    <div class="timeline-badge danger"><i class="fa fa-close"></i></div>
                    <?php
                }
                ?>
                <div class="timeline-panel">
                    <div class="timeline-heading">
                        <h4 class="timeline-title">Conversion Recorded</h4>
                        <p><small class="text-concrete"><i class="fa fa-clock-o"></i> <?php echo $this->date_format($conversion->conversion_date, 'jS M Y \a\t g:iA'); ?> </small></p>
                    </div>
                    <div class="timeline-body" style=" padding:20px; margin: 20px -20px -20px -20px; background-color:#fafafa; border-top:1px solid #e7e7e7;">
                        <ul class="fa-ul">
                            <li><strong><i class="fa-li fa fa-info-circle text-muted"></i> Name:</strong> <?php echo $conversion->goal_name; ?></li>
                            <li><strong><i class="fa-li fa fa-circle-o text-muted"></i> Type:</strong> <?php echo $conversion->goal_type; ?></li>
                            <li><strong><i class="fa-li fa fa-shopping-cart text-muted"></i> Amount:</strong> <?php echo $conversion->goal_value; ?></li>
                            <li><strong><i class="fa-li fa fa-link text-muted"></i> Trigger URL:</strong> <?php echo $conversion->referrer_url; ?></li>
                            <li><strong><i class="fa-li fa fa-paperclip text-muted"></i> Reference ID:</strong> <?php echo $conversion->goal_reference; ?></li>
                            <li><strong><i class="fa-li fa fa-random text-muted"></i> Campaign:</strong> <?php echo (isset($campaign->source) && (trim($campaign->source) != '')) ? $campaign->source : ''; ?> <?php echo (isset($campaign->medium) && (trim($campaign->medium) != '')) ? '&gt; ' . $campaign->medium : ''; ?> <?php echo (isset($campaign->campaign) && (trim($campaign->campaign) != '')) ? '&gt; ' . $campaign->campaign : ''; ?> <?php echo (isset($campaign->content) && (trim($campaign->content) != '')) ? '&gt; ' . $campaign->content : ''; ?> <?php echo (isset($campaign->term) && (trim($campaign->term) != '')) ? '&gt; ' . $campaign->term : ''; ?></li>
                            <li><strong><i class="fa-li fa fa-map-marker text-muted"></i> Country &amp; City:</strong> <?php echo $conversion->ip_country_name; ?> <?php echo trim($conversion->ip_city_name) != '' ? '» ' . $conversion->ip_city_name : ''; ?></li>
                            <li><strong><i class="fa-li fa fa-location-arrow text-muted"></i> IP Address:</strong> <?php echo $conversion->ip_address; ?></li>
                            <?php
                            if (trim($conversion->message) != '') {
                                ?>
                                <li><strong><i class="fa-li fa fa-comment-o text-muted"></i> Notes:</strong> <?php echo implode(', ', explode(PHP_EOL, $conversion->message)); ?></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div style="background-color:#f4f4f4; padding:5px; margin: 20px -20px -20px -20px;">
                        <div class="embed-responsive embed-responsive-16by9"><iframe src="https://maps.google.com/maps?q=<?php echo $conversion->ip_latitude; ?>,<?php echo $conversion->ip_longitude; ?>&z=7&output=embed" width="800" height="600" frameborder="0" style="border:0" allowfullscreen class="embed-responsive-item"></iframe></div>
                    </div>
                </div>
            </li>
            <?php
            foreach ($timelines as $timeline) {
                if ($timeline->datatype == 'CV') {
                    ?>
                    <li class="timeline-inverted">
                        <?php
                        if ($timeline->status == 'Accepted') {
                            ?>
                            <div class="timeline-badge success"><i class="fa fa-check"></i></div>
                            <?php
                        } else {
                            ?>
                            <div class="timeline-badge danger"><i class="fa fa-close"></i></div>
                            <?php
                        }
                        ?>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">Conversion Recorded</h4>
                                <p><small class="text-concrete"><i class="fa fa-clock-o"></i> <?php echo $this->date_format($timeline->timeline_date, 'jS M Y \a\t g:iA'); ?> </small></p>
                            </div>
                            <div class="timeline-body"> <small>
                                    <ul>
                                        <li>Conv. Name: <?php echo $timeline->goal_name; ?> </li>
                                        <li>Conv. Amount: <?php echo $timeline->goal_value; ?> </li>
                                        <li>Conv. URL: <?php echo $timeline->referrer_url; ?></li>
                                        <li>Reference ID: <?php echo $timeline->goal_reference; ?></li>
                                        <?php
                                        if (trim($timeline->message) != '') {
                                            ?>
                                            <li>Message: <?php echo $timeline->message; ?></li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </li>
                    <?php
                } else {
                    ?>

                    <li class="timeline-inverted">
                        <div class="timeline-badge info"><i class="fa fa-link"></i></div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">Tracking Link Clicked</h4>
                                <p><small class="text-concrete"><i class="fa fa-clock-o"></i> <?php echo $this->date_format($timeline->timeline_date, 'jS M Y \a\t g:iA'); ?> </small></p>
                            </div>
                            <div class="timeline-body"><small> Visitor <a href="#"> <?php echo $timeline->visitor_id; ?></a> clicked on tracking link <a href="?page=sh_links_page&action=edit&id=<?php echo $timeline->link_id; ?>"> <?php echo $this->current_domain(TRUE) . $timeline->tracking_link; ?> </a> from <?php echo $timeline->referrer_url != NULL ? 'the url' : 'direct visit'; ?> <a href="<?php echo $timeline->referrer_url; ?>" target="_blank"> <?php echo $timeline->referrer_url; ?> </a></small></div>
                        </div>
                    </li>
                    <?php
                    if (isset($timeline->campaign_source)) {
                        if ($timeline->tracking_campaign == 'Auto Keyword Linking') {
                            ?>
                            <li class="timeline-inverted">
                                <div class="timeline-badge info"><i class="fa fa-random"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title">Campaign Link Clicked</h4>
                                        <p><small class="text-concrete"><i class="fa fa-clock-o"></i> <?php echo $this->date_format($timeline->timeline_date, 'jS M Y \a\t g:iA'); ?> </small></p>
                                    </div>
                                    <div class="timeline-body"><small> Visitor <a href="#"> <?php echo $timeline->visitor_id; ?></a> clicked on an keyword conversion campaign from the page <?php echo $timeline->referrer_url != NULL ? 'the url' : 'direct visit'; ?> <a href="<?php echo $timeline->referrer_url; ?>" target="_blank"> <?php echo $timeline->referrer_url; ?> </a></small></div>
                                </div>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="timeline-inverted">
                                <div class="timeline-badge info"><i class="fa fa-random"></i></div>
                                <div class="timeline-panel">
                                    <div class="timeline-heading">
                                        <h4 class="timeline-title">Campaign Link Clicked</h4>
                                        <p><small class="text-concrete"><i class="fa fa-clock-o"></i> <?php echo $this->date_format($timeline->timeline_date, 'jS M Y \a\t g:iA'); ?> </small></p>
                                    </div>
                                    <div class="timeline-body"><small> Visitor <a href="#"> <?php echo $timeline->visitor_id; ?></a> clicked on campaign link <a href="?page=sh_campaigns_page&action=edit&id=<?php echo $timeline->campaign_id; ?>"> <?php echo $this->current_domain(TRUE) . 'c/' . $timeline->tracking_campaign; ?> </a> from <?php echo $timeline->referrer_url != NULL ? 'the url' : 'direct visit'; ?> <a href="<?php echo $timeline->referrer_url; ?>" target="_blank"> <?php echo $timeline->referrer_url; ?> </a></small></div>
                                </div>
                            </li>
                            <?php
                        }
                    }
                }
            }
            ?>
        </ul>
    </div>

    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">Conversion Details</div>
            </div>
            <form method="POST" class="form">
                <div class="panel-body">
                    <fieldset>
                        <div class="form-group">
                            <label class="control-label" for="conversion_date"><i class="fa fa-exclamation-circle text-silver"></i> Date &amp; Time: </label>
                            <div class="controls">
                                <input name="conversion_date" type="text" class="form-control datetimepicker" value="<?php echo $conversion->conversion_date; ?>" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_name"><i class="fa fa-exclamation-circle text-silver"></i> Conversion Name: </label>
                            <div class="controls">
                                <input type="text" class="form-control" id="goal_name" name="goal_name" value="<?php echo $conversion->goal_name; ?>" required="required">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_type"><i class="fa fa-exclamation-circle text-silver"></i> Conversion Type: </label>
                            <div class="controls">
                                <select name="goal_type" id="goal_type" class="form-control">
                                    <option <?php echo selected($conversion->goal_type, GOAL_TYPE_LEAD, true); ?> value="<?php echo GOAL_TYPE_LEAD; ?>">Lead</option>
                                    <option <?php echo selected($conversion->goal_type, GOAL_TYPE_SALE, true); ?> value="<?php echo GOAL_TYPE_SALE; ?>">Sale</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_value"><i class="fa fa-exclamation-circle text-silver"></i> Conversion Value: </label>
                            <div class="controls">
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo get_option(SH_PREFIX . 'settings_currency'); ?></span>
                                    <input type="text" class="form-control" id="goal_value" name="goal_value" placeholder="Example: 0.00" value="<?php echo $conversion->goal_value; ?>" required="required">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="goal_reference"><i class="fa text-silver"></i> Unique ID: </label>
                            <div class="controls">
                                <input type="text" class="form-control" id="goal_reference" name="goal_reference" value="<?php echo $conversion->goal_reference; ?>" placeholder="Example: 123456">
                                <p class="help-block">Enter a unique ID so we can tell when to recognize a goal conversion, and when to ignore it. If you have no unique ID to pass, leave this blank. <a href="#" rel="tooltip" data-original-title="This is normally your order ID, customer email address or transaction ID. You can automatically replace this unique id in your shopping cart process."><i class="fa fa-question-circle text-silver"></i></a></p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="visits_log_id">CTID: </label>
                            <div class="controls">
                                <input name="visits_log_id" type="text" class="form-control" value="<?php echo $conversion->visits_log_id; ?>">
                                <p class="help-block">Enter the <code>CTID</code> here, which you can get from your network's subid report. If you do not enter anything here the conversion will be added but will only show up in your overall poerformance report. </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <div class="controls">
                                <select id="status" name="status" class="form-control">
                                    <option <?php echo selected($conversion->status, STATUS_ACCEPTED, true); ?> value="<?php echo STATUS_ACCEPTED; ?>"> Accepted</option>
                                    <option <?php echo selected($conversion->status, STATUS_REJECTED, true); ?> value="<?php echo STATUS_REJECTED; ?>"> Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message">Notes:</label>
                            <div class="controls">
                                <textarea name="message" class="form-control"><?php echo $conversion->message; ?></textarea>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="panel-footer clearfix">
                    <button name="btnUpdate" type="submit" class="btn btn-default pull-left"><i class="fa fa-check-circle"></i> Update</button>
                    <button name="btnDelete" type="submit" class="confirm btn btn-danger pull-right">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
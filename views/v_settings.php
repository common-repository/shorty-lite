<div class="page-header">
    <h2><i class="fa fa-cogs"></i> Settings</h2>
    Modify settings for automatic keyword linking and cloaking.
</div>
<div class="form">
    <form method="POST" name="frmSettings">
        <div class="row">
            <div class="col-md-6">

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Tracking  &amp; Reporting Options</div>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <div class="form-group <?php echo $this->form_error_class('settings_tracking_domain'); ?>">
                                <label for="settings_tracking_domain">Tracking Domain:</label>
                                <div class="controls">
                                    <div class="input-group"> <span class="input-group-addon"><?php echo $this->current_domain(FALSE, TRUE); ?></span>
                                        <input type="text" class="form-control" id="settings_tracking_domain" name="settings_tracking_domain" placeholder="x" value="<?php echo $this->set_value('settings_tracking_domain', get_option(SH_PREFIX . 'settings_tracking_domain')); ?>">
                                        <span class="input-group-addon">/</span></div>
                                    <?php echo $this->form_error_message('settings_tracking_domain'); ?>
                                    <p class="help-block">Specify a prefix (recommended to avoid conflicts) for or leave it blank to track links and campaigns from the root domain, i.e <code><?php echo $this->current_domain(TRUE, TRUE); ?></code></p>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="settings_enable_custom_domain">Use Custom Domain:</label>
                                <div class="controls">
                                    <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                        <button type="button" data-value="1" class="btn btn-small <?php echo $this->set_value('settings_enable_custom_domain', get_option(SH_PREFIX . 'settings_enable_custom_domain')) == 1 ? 'active' : ''; ?>"  data-target="#custom_domain">On</button>
                                        <button type="button" data-value="0" class="btn btn-small <?php echo $this->set_value('settings_enable_custom_domain', get_option(SH_PREFIX . 'settings_enable_custom_domain')) == 0 ? 'active' : ''; ?>"  data-target="#">Off</button>
                                    </div>
                                    <p class="help-block"> </p>
                                </div>
                                <input id="settings_enable_custom_domain" name="settings_enable_custom_domain" class="switch-data" type="hidden" value="<?php echo get_option(SH_PREFIX . 'settings_akl_status'); ?>" />
                                <p class="help-block">Use a different domain name you own in all links and campaigns. You must use CNAME to map your domain to the default tracking domain above.</p>
                            </div>
                            <div class="form-group collapse out <?php echo $this->form_error_class('settings_custom_domain'); ?>" id="custom_domain">
                                <label for="exampleInputEmail9">Custom Domain:</label>
                                <div class="controls">
                                    <div class="input-group"> <span class="input-group-addon">http://</span>
                                        <input type="text" class="form-control"  name="settings_custom_domain" value="<?php echo $this->set_value('settings_custom_domain', get_option(SH_PREFIX . 'settings_custom_domain')); ?>">
                                        <span class="input-group-addon">/</span>
                                    </div>
                                    <p class="help-block">Make sure that you have set the correct CNAME properties in your registrar, and allow up to 24 hours for the domain name mapping to work. <a href="http://www.shortywp.com/tutorials/">Refer to the tutorials</a> on how to do this.</p>
                                    <?php echo $this->form_error_message('settings_custom_domain'); ?>
                                </div>
                            </div>
                            <div class="form-group <?php echo $this->form_error_class('downtime_alert_threshold'); ?>">
                                <label for="exampleInputEmail7">Downtime alert threshold:</label>
                                <div class="controls">
                                    <div class="input-group">
                                        <input name="downtime_alert_threshold" type="text" class="form-control" id="downtime_alert_threshold" placeholder="10" value="<?php echo $this->set_value('downtime_alert_threshold', get_option(SH_PREFIX . 'downtime_alert_threshold')); ?>">
                                        <span class="input-group-addon">Minutes</span> </div>
                                    <p class="help-block">How long should your tracked URLs be offline before receiving an alert? Setting this value too low may result in a slow server, we recommend leaving it as the default.</p>
                                </div>
                                <?php echo $this->form_error_message('downtime_alert_threshold'); ?>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer">Modify the cookie settings and traffic filtering. Specify the tracking domain name for all link and campaigns on this blog.</div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Goals &amp; Currency</div>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <div class="form-group">
                                <label class="control-label" for="settings_duplicate_handling">Duplicate Handling: </label>
                                <div class="controls">
                                    <select name="settings_duplicate_handling" class="form-control" id="settings_duplicate_handling">
                                        <option value="<?php echo SHORTLY_DUPLICATE_HANDLING_IGNORE; ?>"<?php selected(get_option(SH_PREFIX . 'settings_duplicate_handling'), SHORTLY_DUPLICATE_HANDLING_IGNORE, true); ?>>Ignore conversion with duplicate reference ID</option>
                                        <option value="<?php echo SHORTLY_DUPLICATE_HANDLING_COUNT; ?>"<?php selected(get_option(SH_PREFIX . 'settings_duplicate_handling'), SHORTLY_DUPLICATE_HANDLING_COUNT, true); ?>>Count duplicate reference Ids as a conversion</option>
                                    </select>
                                    <p class="help-block"> This setting only applies when you have specified a unique identifier for each conversion. </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="settings_currency">Currency:</label>
                                <div class="controls">
                                    <select name="settings_currency" id="settings_currency" class="form-control">
                                        <?php
                                        foreach ($currency_dropdown as $key => $row) {
                                            echo '<option value="' . $key . '" ' . ($key == get_option(SH_PREFIX . 'settings_currency') ? 'selected="selected"' : '') . '>' . $row . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="help-block">Changing currency only replaces the display text with your selection, which is<code><?php echo get_option(SH_PREFIX . 'settings_currency'); ?></code>. <?php echo SH_MENU_DISPLAY; ?> does not convert amounts from one currency to another.</p>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer">Control how <?php echo SH_MENU_DISPLAY; ?> recognizes valid conversions, and the currency used in reports.</div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Access &amp; Permissions</div>
                    </div>
                    <div class="panel-body">
                        <div class="control-group">
                            <label for="settings_enable_roles">Status:</label>
                            <div class="controls">
                                <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                    <button type="button" data-value="1" class="btn btn-small <?php echo get_option(SH_PREFIX . 'settings_enable_roles') == 1 ? 'active' : ''; ?>" data-target="#enable_role">On</button>
                                    <button type="button" data-value="0" class="btn btn-small <?php echo get_option(SH_PREFIX . 'settings_enable_roles') == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                </div>
                            </div>
                            <input id="settings_enable_roles" name="settings_enable_roles" class="switch-data" type="hidden" value="<?php echo get_option(SH_PREFIX . 'settings_enable_roles'); ?>" />
                        </div>
                        <fieldset class="collapse out form-group" id="enable_role">
                            <div class="alert alert-block alert-info">
                                <p><i class="fa fa-info-circle"></i> This feature is not available in Shorty Lite. <a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer">Control the availability of <?php echo SH_MENU_DISPLAY; ?> to the various user levels. The role &quot;Administrator&quot; has access to everything. Only the administrator can set permissions.</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Automatic Keyword Links</div>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <div class="control-group">
                                <label for="settings_akl_status" >Status:</label>
                                <div class="controls">
                                    <div class="switch btn-group btn-group-sm" data-toggle="buttons-radio" style="margin-bottom:10px;">
                                        <button type="button" data-value="1" class="btn btn-small <?php echo get_option(SH_PREFIX . 'settings_akl_status') == 1 ? 'active' : ''; ?>" data-target="#akl">On</button>
                                        <button type="button" data-value="0" class="btn btn-small <?php echo get_option(SH_PREFIX . 'settings_akl_status') == 0 ? 'active' : ''; ?>" data-target="#">Off</button>
                                    </div>
                                    <p class="help-block"> </p>
                                </div>
                                <input id="settings_akl_status" name="settings_akl_status" class="switch-data" type="hidden" value="<?php echo get_option(SH_PREFIX . 'settings_akl_status'); ?>" />
                            </div>
                            <fieldset class="collapse out form-group" id="akl">
                                <div class="form-group">
                                    <label for="settings_akl_on_homepage">Convert On:</label>
                                    <div class="controls">
                                        <div class="checkbox">
                                            <label for="settings_akl_on_homepage">
                                                <input type="checkbox" id="settings_akl_on_homepage" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_on_homepage'), '1', true); ?> name="settings_akl_on_homepage">
                                                Home page</label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="settings_akl_on_singlepost">
                                                <input type="checkbox" id="settings_akl_on_singlepost" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_on_singlepost'), '1', true); ?> name="settings_akl_on_singlepost" >
                                                Single post</label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="settings_akl_on_singlepage">
                                                <input type="checkbox" id="settings_akl_on_singlepage" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_on_singlepage'), '1', true); ?> name="settings_akl_on_singlepage">
                                                Single page</label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="settings_akl_on_comments">
                                                <input type="checkbox" id="settings_akl_on_comments" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_on_comments'), '1', true); ?> name="settings_akl_on_comments">
                                                Comments</label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="settings_akl_on_archives">
                                                <input type="checkbox" id="settings_akl_on_archives" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_on_archives'), '1', true); ?> name="settings_akl_on_archives">
                                                Archives</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group <?php echo $this->form_error_class('settings_akl_max_per_page'); ?>">
                                    <label for="settings_akl_max_per_page">Max Per Page:</label>
                                    <div class="controls">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="settings_akl_max_per_page" name="settings_akl_max_per_page" value="<?php echo $this->set_value('settings_akl_max_per_page', get_option(SH_PREFIX . 'settings_akl_max_per_page')); ?>">
                                            <span class="input-group-addon">links per page</span></div>
                                    </div>
                                    <?php echo $this->form_error_message('settings_akl_max_per_page'); ?>
                                </div>
                                <div class="form-group <?php echo $this->form_error_class('settings_akl_max_per_keyword'); ?>">
                                    <label for="settings_akl_max_per_keyword">Max Per Keyword:</label>
                                    <div class="controls">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="settings_akl_max_per_keyword" name="settings_akl_max_per_keyword" value="<?php echo $this->set_value('settings_akl_max_per_keyword', get_option(SH_PREFIX . 'settings_akl_max_per_keyword')); ?>">
                                            <span class="input-group-addon">links per keyword</span></div>
                                    </div>
                                    <?php echo $this->form_error_message('settings_akl_max_per_keyword'); ?>
                                </div>
                                <div class="form-group">
                                    <label for="checkbox">Link Options:</label>
                                    <div class="controls">
                                        <div class="checkbox">
                                            <label for="settings_akl_new_window">
                                                <input id="settings_akl_new_window" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_new_window'), '1', true); ?> name="settings_akl_new_window" type="checkbox">
                                                Open links in new window</label>
                                        </div>
                                        <div class="checkbox">
                                            <label for="settings_akl_no_follow">
                                                <input id="settings_akl_no_follow" value="1" <?php checked(get_option(SH_PREFIX . 'settings_akl_no_follow'), '1', true); ?> name="settings_akl_no_follow" type="checkbox">
                                                Add no-follow to links</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </fieldset>
                    </div>
                    <div class="panel-footer"> <?php echo SH_MENU_DISPLAY; ?> can automatically convert keywords on your blog's posts and pages into tracking links.</div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Viral Bar</div>
                    </div>
                    <div class="panel-body">
                        <fieldset>
                            <div class="form-group">
                                <label for="settings_bar_theme">Bar Theme:</label>
                                <select id="settings_bar_theme" name="settings_bar_theme"  class="form-control" >
                                    <option value="<?php echo SHORTLY_BAR_THEME_GREY; ?>" <?php selected(get_option(SH_PREFIX . 'settings_bar_theme'), SHORTLY_BAR_THEME_GREY, true); ?>><?php echo SHORTLY_BAR_THEME_GREY; ?></option>
                                    <option value="<?php echo SHORTLY_BAR_THEME_BLACK; ?>"<?php selected(get_option(SH_PREFIX . 'settings_bar_theme'), SHORTLY_BAR_THEME_BLACK, true); ?>><?php echo SHORTLY_BAR_THEME_BLACK; ?></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail50">Social Buttons:</label>
                                <div class="controls">
                                    <div class="checkbox">
                                        <label for="settings_socialButtons_facebook">
                                            <input type="checkbox" name="settings_socialButtons_facebook" class="viral_retweet" id="settings_socialButtons_facebook" value="1" <?php checked(get_option(SH_PREFIX . 'settings_socialButtons_facebook'), '1', true); ?>>
                                            FaceBook</label>
                                    </div>
                                    <div class="checkbox">
                                        <label for="settings_socialButtons_twitter">
                                            <input type="checkbox" name="settings_socialButtons_twitter" class="cbSosial" id="settings_socialButtons_twitter" value="1" <?php checked(get_option(SH_PREFIX . 'settings_socialButtons_twitter'), '1', true); ?>>
                                            Twitter</label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer">The <?php echo SH_MENU_DISPLAY; ?> viral bar for WordPress displays a floating bar with sharing options on cloaked links. </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title">Cookies</div>
                    </div>
                    <div class="panel-body">
                        <fieldset >
                            <div class="form-group <?php echo $this->form_error_class('session_timeout'); ?>">
                                <label for="session_timeout">Session Timeout:</label>
                                <div class="controls">
                                    <div class="input-group"> 
                                        <input type="text" class="form-control" id="session_timeout" name="session_timeout" placeholder="30" value="<?php echo $this->set_value('session_timeout', get_option(SH_PREFIX . 'session_timeout')); ?>">
                                        <span class="input-group-addon">Minutes</span></div>
                                    <p class="help-block">After a visitor has been idle for the time specified above, he will be considered a new visit. Cannot be less than 1 minute or more than 4 hours.</p>

                                    <?php echo $this->form_error_message('session_timeout'); ?>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group <?php echo $this->form_error_class('cookie_window'); ?>">
                                <label for="cookie_window">Cookie Window:</label>
                                <div class="controls">
                                    <div class="input-group"> 
                                        <input type="text" class="form-control" id="cookie_window" name="cookie_window" placeholder="30" value="<?php echo $this->set_value('cookie_window', get_option(SH_PREFIX . 'cookie_window')); ?>">
                                        <span class="input-group-addon">Days</span></div>
                                    <p class="help-block">This determines your cookie settings for recording goals and conversions.</p>
                                </div>
                                <?php echo $this->form_error_message('cookie_window'); ?>
                            </div>
                        </fieldset>
                    </div>
                    <div class="panel-footer">Customize advanced tracking rules to match Google Analytics or other tracking tools. Do not modify these settings is you do not know what they mean.</div>
                </div>

            </div>
        </div>
        <?php wp_nonce_field( 'change_settings'); ?>
        <div class="form-actions"><button name="btnSave" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-check-circle"></i> Save Changes</button></div>
    </form>
</div>

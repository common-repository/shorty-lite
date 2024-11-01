<!DOCTYPE HTML>
<html>
    <head>
        <title><?php _e('Insert Shorty Link', 'srty'); ?></title>
        <link rel="stylesheet" href="<?php echo SH_CSS_URL . '/bootstrap.css'; ?>" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo SH_CSS_URL . '/dataTables.bootstrap.css'; ?>" type="text/css" media="all" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo SH_CSS_URL . '/bootstrap-datetimepicker.min.css'; ?>" type="text/css" media="all" />
        <link rel="stylesheet" href="<?php echo SH_CSS_URL . '/style.css'; ?>" type="text/css" media="all" />
        <script language="javascript" type="text/javascript" src="<?php echo SH_JS_URL . '/jquery-1.11.3.min.js'; ?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php echo SH_JS_URL . '/bootstrap.min.js'; ?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php echo SH_JS_URL . '/typeahead.bundle.js'; ?>" ></script>
        <script language="javascript" type="text/javascript" src="<?php echo SH_JS_URL . '/handlebars-v3.0.3.js'; ?>" ></script>
    </head>
    <body class="shorty" style="background-color: #fff;">
        <div class="container">
            <form id="srty_tinymce" action="" method="POST" class="modal-body">
                <ul id="srty_tab_selection" class="nav nav-tabs" style="margin-bottom:20px;">
                    <li class="active"><a href="#existing" data-toggle="tab">Existing Link</a></li>
                    <li><a href="#addlink" data-toggle="tab">Add New Link</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active fade in" id="existing">
                        <fieldset>
                            <legend class="text-muted small">SEARCH EXISTING</legend>
                            <div class="form-group">
                                <label for="srty_link_search">Search</label>
                                <input id="srty_link_search" type="text" class="typeahead form-control" placeholder="Type to search..">
                                <p class="help-block">Destination URL: <span id="srty_dest_url">http://www.kreydle.com/?source=campaignname</span></p>
                            </div>
                            <div class="form-group">
                                <label for="srty_link_text">Link Text</label>
                                <input type="text" class="form-control" id="srty_link_text" placeholder="">
                                <p class="help-block">The display text for the hyperlink.</p>
                            </div>
                        </fieldset>
                    </div>
                    <div class="tab-pane fade" id="addlink">
                        <fieldset>
                            <div id="msg"></div>
                            <legend class="text-muted small">ADD NEW</legend>
                            <div class="form-group">
                                <label class="control-label" for="srty_link_name"><i class="fa fa-exclamation-circle text-silver"></i> Link Name: </label>
                                <div class="controls">
                                    <input type="text" class="form-control" id="srty_link_name">
                                    <p class="help-block">A reference name for your link. Make it easy to remember so you can find it later.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for=""><i class="fa fa-exclamation-circle text-silver"></i> Destination URL: </label>
                                <div class="controls">
                                    <input type="url" class="form-control" id="srty_destination_url" placeholder="http://">
                                    <p class="help-block">The actual URL of the link you want to track. This could be a URL to your own website, or an affiliate link.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="srty_tracking_link"><i class="fa fa-exclamation-circle text-silver"></i> Tracking Link: </label>
                                <div class="controls">
                                    <div class="input-group"><span class="input-group-addon"><?php echo $this->current_domain(FALSE, FALSE, TRUE); ?></span>
                                        <input type="text" class="form-control" id="srty_tracking_link" placeholder="something" value="<?php echo $this->generate_random_letters(); ?>">
                                    </div>
                                    <p class="help-block">This is a redirect link used to mask and track the Destination URL. Enter just one word, no spaces or dashes. Make it short and easy to remember.</p>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>                
                <div class="modal-footer">
                    <button id="btnInsert" type="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> Insert Link</button>
                </div>
            </form>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                var tracking_domain = '<?php echo $this->current_domain(TRUE, FALSE, TRUE); ?>';
                var settings_akl_new_window = '<?php echo ((get_option(SH_PREFIX . 'settings_akl_statusget_option') == 1) && (get_option(SH_PREFIX . 'settings_akl_new_window') == 1)) ? 'target="_blank"' : ''; ?>';
                var settings_akl_no_follow = '<?php echo ((get_option(SH_PREFIX . 'settings_akl_statusget_option') == 1) && (get_option(SH_PREFIX . 'settings_akl_no_follow') == 1)) ? 'rel="nofollow"' : ''; ?>';

                function prepare(query, settings) {
                    settings.url = ajaxurl + '?action=srty_campaigns_typeahead&q=' + query;
                    settings.type = 'POST';
                    return settings;
                }
                var engine = new Bloodhound({
                    name: 'wp_page',
                    remote: {
                        url: ajaxurl,
                        prepare: prepare
                    },
                    datumTokenizer: function (d) {
                        return Bloodhound.tokenizers.whitespace(d.val);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                engine.initialize();
                engine.get('');


                jQuery('.typeahead').typeahead({
                    hint: false,
                    highlight: false,
                    limit: 20,
                    minLength: 0,
                }, {
                    name: 'link_tracking_url',
                    displayKey: 'url',
                    templates: {
                        suggestion: Handlebars.compile('<p style="font-size:14px;"><strong>{{link_name}}</strong> – {{url}}</p>')
                    },
                    // `ttAdapter` wraps the suggestion engine in an adapter that
                    // is compatible with the typeahead jQuery plugin
                    source: engine.ttAdapter(),
                }).bind('typeahead:selected', function (eventObject, suggestionObject, suggestionDataset) {
                    jQuery('#srty_dest_url').html(suggestionObject.destination_url);
                }).bind('focus', function (e, b) {
                    clear_error();
                    jQuery('#srty_dest_url').html('');
                });

                jQuery('#btnInsert').click(function (e) {
                    e.preventDefault();
                    var active_tab = jQuery('.tab-pane.active').attr('id');

                    clear_error();
                    if (active_tab == 'existing') {
                        srty_existing_link();
                    } else {
                        srty_new_link();
                    }
                    return false;
                });

                function srty_existing_link() {

                    var url = jQuery('#srty_link_search').val();
                    var n = url.lastIndexOf('/');
                    var tracking_name = '';
                    if (n > -1) {
                        var this_tracking_domain = url.substring(0, n + 1);
                        if (tracking_domain == this_tracking_domain) {
                            tracking_name = url.substring(n + 1).trim();
                        }
                    }

                    var text = jQuery('#srty_link_text').val().toString().trim();
                    if ((tracking_name != '') && (text != '')) {
                        jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {tracking_name: tracking_name, action: 'srty_shorty_get_existing_link'},
                        }).done(function (data) {
                            if (data.result == true) {
                                parent.tinymce.activeEditor.insertContent('<a href="' + url + '" ' + settings_akl_new_window + '  ' + settings_akl_no_follow + ' > ' + text + '</a>');
                                parent.tinymce.activeEditor.windowManager.close();
                            } else {
                                jQuery('#srty_link_search').parent().parent().addClass('has-error');
                                jQuery('#srty_link_search').parent().append('<p class="txterror text-danger">Link not found</p>');
                            }
                        });
                    } else {
                        if (tracking_name == '') {
                            jQuery('#srty_link_search').parent().parent().addClass('has-error');
                            jQuery('#srty_link_search').parent().append('<p class="txterror text-danger">Link is required</p>');
                        }
                        if (text == '') {
                            jQuery('#srty_link_text').parent().addClass('has-error');
                            jQuery('#srty_link_text').parent().find('input').after('<p class="txterror text-danger">Link Text is required</p>');
                        }
                    }
                }

                function srty_new_link() {

                    var link_name = jQuery('#srty_link_name').val().toString().trim();
                    var destination_url = jQuery('#srty_destination_url').val().toString().trim();
                    var tracking_link = jQuery('#srty_tracking_link').val().toString().trim();
                    if ((link_name != '') && (destination_url != '') && (tracking_link != '') && (isUrl(destination_url))) {
                        jQuery.ajax({
                            url: ajaxurl,
                            method: "POST",
                            data: {
                                link_name: link_name,
                                destination_url: destination_url,
                                tracking_link: tracking_link,
                                action: 'srty_shorty_add_link'
                            },
                        }).done(function (data) {
                            if (data.result == true) {
                                parent.tinymce.activeEditor.insertContent('<a href="' + data.url + '" ' + settings_akl_new_window + '  ' + settings_akl_no_follow + ' > ' + link_name + '</a>');
                                parent.tinymce.activeEditor.windowManager.close();
                            } else {
                                jQuery('#msg').addClass('has-error');
                                jQuery('#msg').append('<p class="txterror text-danger"><strong>Opps!</strong> Can\'t add new link</p>');
                            }
                        });
                    } else {
                        if (link_name == '') {
                            jQuery('#srty_link_name').parent().parent().addClass('has-error');
                            jQuery('#srty_link_name').parent().append('<p class="txterror text-danger">Link Name is required</p>');
                        }
                        if (destination_url == '') {
                            jQuery('#srty_destination_url').parent().addClass('has-error');
                            jQuery('#srty_destination_url').parent().find('input').after('<p class="txterror text-danger">Destination URL is required</p>');
                        }

                        if (!isUrl(destination_url)) {
                            jQuery('#srty_destination_url').parent().addClass('has-error');
                            jQuery('#srty_destination_url').parent().find('input').after('<p class="txterror text-danger">Invalid URL format</p>');
                        }

                        if (tracking_link == '') {
                            jQuery('#srty_tracking_link').parent().parent().addClass('has-error');
                            jQuery('#srty_tracking_link').parent().parent().after('<p class="txterror text-danger">Tracking Link is required</p>');
                        }
                    }
                }

                function isUrl(s) {
                    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
                    return regexp.test(s);
                }

                function clear_error() {
                    jQuery('.txterror').remove();
                    jQuery('.has-error').removeClass('has-error');
                }
            });
        </script>
    </body>
</html>

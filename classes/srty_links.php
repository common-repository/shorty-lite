<?php

class Srty_links extends Srty_core {

    public function __construct() {
        parent::__construct();
        $this->table_name = $this->wpdb->prefix . SH_PREFIX . 'links';
    }

    public function init() {
        add_action('wp_ajax_srty_datatable', array($this, 'ajax_datatable'));
        add_action('wp_ajax_srty_delete', array($this, 'ajax_delete'));
        add_action('wp_ajax_srty_get_meta', array($this, 'srty_get_meta'));
        add_action('wp_ajax_srty_checkurl', array($this, 'srty_checkurl'));
    }

    public function display() {
        add_action('admin_footer', array($this, 'page_js'));
        wp_cache_delete(SH_PREFIX . 'cache_the_content');
        $this->view('v_links', $this->view_data);
    }

    public function add() {


        add_action('admin_footer', array($this, 'page_js'));
        add_action('admin_footer', array($this, 'js_typeahead_links'));
        $this->view_data['countries'] = new Srty_countries();
        if (!empty($_POST) && check_admin_referer('frmNewLink')) {
            if (isset($_POST['btnAdd'])) {
                wp_cache_delete(SH_PREFIX . 'cache_the_content');

                /**
                 * Link Details
                 */
                $rules = array(
                    'link_name' => 'required',
                    'primary_url' => 'required|valid_url',
                    'backup_url' => 'valid_url',
                    'mobile_url' => 'valid_url',
                    'tracking_link' => 'required|alpha_dash_slash|tracking_link',
                );

                /**
                 * Cloaking & Retargeting
                 */
                if ($this->_post('cloaking_status_enable') == 1 && $this->_post('frame_content') == 'INDEX') {
                    $rules = array_merge($rules, array(
                        'meta_title' => 'required',
                        'meta_description' => 'required',
                        'meta_image' => 'valid_url',
                    ));
                }

                /**
                 * Parameter Tagging
                 */
                if ($this->_post('param_tag_affiliate_network') == 'Custom' && $this->_post('param_tag_affiliate_tracking') == 1) {
                    $rules = array_merge($rules, array(
                        'param_tag_affiliate_network_custom' => 'required',
                    ));
                }

                /**
                 * Keyword Linking
                 */
                if ($this->_post('auto_keyword_linking_enable') == 1 && $this->_post('frame_content') == 'INDEX') {
                    $rules = array_merge($rules, array(
                        'meta_keyword' => 'required',
                    ));
                }




                $this->gump->validation_rules($rules);

                /**
                 * Filter & Sanitize data
                 */
                $this->gump->filter_rules(array(
                    'link_name' => 'trim|sanitize_string',
                    'primary_url' => 'trim|sanitize_url',
                    'meta_image' => 'trim|sanitize_url',
                    'tracking_link' => 'trim',
                    'meta_keyword' => 'trim',
                    'param_tag_affiliate_network_custom' => 'trim',
                    'link_expired_url' => 'trim|sanitize_url',
                    'geo_redirect_destination_url' => 'trim|sanitize_url',
                ));

                $validated_data = $this->gump->run($_POST);
                if ($validated_data !== FALSE) {

                    $link_name = $this->_post('link_name');
                    $destination_url = $this->add_http($this->_post('primary_url'));
                    $tracking_link = $this->_post('tracking_link');

                    $cloaking_status_enable = $this->_post('cloaking_status_enable', 0);
                    $cloaking_type = $this->_post('cloaking_type', SHORTLY_CLOAKING_TYPE_BASIC);
                    $bar_position = $this->_post('bar_position', SHORTLY_BAR_POSITION_TOP);
                    $frame_content = $this->_post('frame_content', SHORTLY_FRAME_CONTENT_HIDDEN);
                    $meta_title = $this->_post('meta_title');
                    $meta_description = $this->_post('meta_description');
                    $retargeting_code = $this->_post('retargeting_code');

                    $param_tag_forward_param = $this->_post('param_tag_forward_param', 0);
                    $param_tag_forward_campaign = $this->_post('param_tag_forward_campaign', 0);
                    $param_tag_affiliate_tracking = $this->_post('param_tag_affiliate_tracking', 0);
                    $param_tag_affiliate_network = $this->_post('param_tag_affiliate_network', AFFILIATE_NETWORK_TID);
                    $param_tag_affiliate_network_custom = $this->_post('param_tag_affiliate_network_custom');

                    $auto_keyword_linking_enable = $this->_post('auto_keyword_linking_enable', 0);
                    $meta_keyword = $this->_post('meta_keyword');

                    $geo_redirect_countries = '';
                    if (is_array($this->_post('geo_redirect_countries'))) {
                        $geo_redirect_countries = implode(',', $this->_post('geo_redirect_countries'));
                    }

                    $this->wpdb->insert(
                            $this->table_name, array(
                        'link_name' => $link_name,
                        'destination_url' => $destination_url,
                        'backup_url' => $this->_post('backup_url'),
                        'mobile_url' => $this->_post('mobile_url'),
                        'tracking_link' => $tracking_link,
                        'cloaking_status_enable' => $cloaking_status_enable,
                        'cloaking_type' => $cloaking_type,
                        'bar_position' => $bar_position,
                        'frame_content' => $frame_content,
                        'meta_title' => $meta_title,
                        'meta_description' => $meta_description,
                        'meta_image' => $this->_post('meta_image'),
                        'retargeting_code' => $retargeting_code,
                        'param_tag_forward_param' => $param_tag_forward_param,
                        'param_tag_forward_campaign' => $param_tag_forward_campaign,
                        'param_tag_affiliate_tracking' => $param_tag_affiliate_tracking,
                        'param_tag_affiliate_network' => $param_tag_affiliate_network,
                        'param_tag_affiliate_network_custom' => $param_tag_affiliate_network_custom,
                        'auto_keyword_linking_enable' => $auto_keyword_linking_enable,
                        'meta_keyword' => $meta_keyword,
                        'uptime_is_online' => 1,
                        'blank_referrer' => $this->_post('blank_referrer', 0),
                        'reference_tags' => $this->_post('reference_tags'),
                            )
                    );

                    $link_id = $this->wpdb->insert_id;
                    if ($link_id > 0) {
                        $this->view_data['msg'] = array(
                            'status' => 'alert-success',
                            'text' => SRTY_MSG_LINK_ADDED
                        );
                        $this->set_top_message($this->view_data['msg']);
                        wp_redirect('?page=' . 'sh_trackers' . '&action=edit&id=' . $link_id);
                        exit();
                    } else {
                        $this->view_data['error'] = $this->gump->get_errors_array();
                        $this->view_data['msg'] = array(
                            'status' => 'alert-danger',
                            'text' => SRTY_MSG_LINK_TOP_ERROR_MESSAGE
                        );
                    }
                } else {
                    $this->view_data['error'] = $this->gump->get_errors_array();
                    $this->view_data['msg'] = array(
                        'status' => 'alert-danger',
                        'text' => SRTY_MSG_LINK_TOP_ERROR_MESSAGE
                    );
                }
            }
        }

        $this->view_data['tracking_link'] = $this->generate_random_letters();
        $this->view('v_link-new', $this->view_data);
    }

    public function edit($link_id = FALSE) {
        add_action('admin_footer', array($this, 'page_js'));
        add_action('admin_footer', array($this, 'js_typeahead_links'));
        $id = $_GET['id'];

        $this->view_data['countries'] = new Srty_countries();
        $id = $_GET['id'];
        if (!empty($_POST) && check_admin_referer('frmEditLink-' . $id)) {
            if (isset($_POST['btnEdit'])) {
                wp_cache_delete(SH_PREFIX . 'cache_the_content');

                /**
                 * Link Details
                 */
                $rules = array(
                    'link_name' => 'required',
                    'primary_url' => 'required|valid_url',
                    'backup_url' => 'valid_url',
                    'mobile_url' => 'valid_url',
                    'tracking_link' => 'required|alpha_dash_slash|tracking_link,' . $id,
                );


                /**
                 * Cloaking & Retargeting
                 */
                if ($this->_post('cloaking_status_enable') == 1 && $this->_post('frame_content') == 'INDEX') {
                    $rules = array_merge($rules, array(
                        'meta_title' => 'required',
                        'meta_description' => 'required',
                        'meta_image' => 'valid_url',
                    ));
                }

                /**
                 * Parameter Tagging
                 */
                if ($this->_post('param_tag_affiliate_network') == 'Custom' && $this->_post('param_tag_affiliate_tracking') == 1) {
                    $rules = array_merge($rules, array(
                        'param_tag_affiliate_network_custom' => 'required',
                    ));
                }

                /**
                 * Keyword Linking
                 */
                if ($this->_post('auto_keyword_linking_enable') == 1 && $this->_post('frame_content') == 'INDEX') {
                    $rules = array_merge($rules, array(
                        'meta_keyword' => 'required',
                    ));
                }


                $this->gump->validation_rules($rules);

                /**
                 * Filter & Sanitize data
                 */
                $this->gump->filter_rules(array(
                    'link_name' => 'trim|sanitize_string',
                    'primary_url' => 'trim|sanitize_url',
                    'backup_url' => 'trim|sanitize_url',
                    'tracking_link' => 'trim',
                    'meta_keyword' => 'trim',
                    'param_tag_affiliate_network_custom' => 'trim',
                    'link_expired_url' => 'trim|sanitize_url',
                    'geo_redirect_destination_url' => 'trim|sanitize_url',
                ));

                $validated_data = $this->gump->run($_POST);
                if ($validated_data !== FALSE) {
                    $geo_redirect_countries = '';
                    if (is_array($this->_post('geo_redirect_countries'))) {
                        $geo_redirect_countries = implode(',', $this->_post('geo_redirect_countries'));
                    }

                    $this->wpdb->update(
                            $this->table_name, array(
                        'link_name' => $this->_post('link_name'),
                        'destination_url' => $this->add_http($this->_post('primary_url')),
                        'backup_url' => $this->_post('backup_url'),
                        'mobile_url' => $this->_post('mobile_url'),
                        'tracking_link' => $this->_post('tracking_link'),
                        'cloaking_status_enable' => $this->_post('cloaking_status_enable', 0),
                        'cloaking_type' => $this->_post('cloaking_type', SHORTLY_CLOAKING_TYPE_BASIC),
                        'bar_position' => $this->_post('bar_position', SHORTLY_BAR_POSITION_TOP),
                        'frame_content' => $this->_post('frame_content', SHORTLY_FRAME_CONTENT_VISIBLE),
                        'meta_title' => $this->_post('meta_title'),
                        'meta_description' => $this->_post('meta_description'),
                        'meta_image' => $this->_post('meta_image'),
                        'retargeting_code' => $this->_post('retargeting_code'),
                        'param_tag_forward_param' => $this->_post('param_tag_forward_param', 0),
                        'param_tag_forward_campaign' => $this->_post('param_tag_forward_campaign', 0),
                        'param_tag_affiliate_tracking' => $this->_post('param_tag_affiliate_tracking', 0),
                        'param_tag_affiliate_network' => $this->_post('param_tag_affiliate_network', AFFILIATE_NETWORK_TID),
                        'param_tag_affiliate_network_custom' => $this->_post('param_tag_affiliate_network_custom'),
                        'auto_keyword_linking_enable' => $this->_post('auto_keyword_linking_enable', 0),
                        'meta_keyword' => $this->_post('meta_keyword'),
                        'uptime_is_online' => 1,
                        'blank_referrer' => $this->_post('blank_referrer', 0),
                        'reference_tags' => $this->_post('reference_tags'),
                            ), array('id' => $id)
                    );

                    $this->view_data['msg'] = array(
                        'status' => 'alert-success',
                        'text' => SRTY_MSG_LINK_EDITED
                    );
                } else {
                    $this->view_data['error'] = $this->gump->get_errors_array();
                    $this->view_data['msg'] = array(
                        'status' => 'alert-danger',
                        'text' => SRTY_MSG_LINK_TOP_ERROR_MESSAGE
                    );
                }
            }
        }
        $this->view_data['link'] = $this->_by_id();
        $this->view('v_link-edit', $this->view_data);
    }

    public function clone_page() {
        add_action('admin_footer', array($this, 'page_js'));
        $id = $_GET['id'];

        /**
         * get the existing data
         */
        $sql = "SELECT * FROM {$this->table_name} WHERE id = %d";
        $link = $this->wpdb->get_row($this->wpdb->prepare($sql, array($id)), OBJECT);

        $this->wpdb->insert(
                $this->table_name, array(
            'link_name' => $link->link_name,
            'destination_url' => $link->destination_url,
            'backup_url' => $link->backup_url,
            'mobile_url' => $link->mobile_url,
            'tracking_link' => $this->generate_random_letters(),
//            'link_redirect_type' => $link->link_redirect_type,
            'cloaking_status_enable' => $link->cloaking_status_enable,
            'cloaking_type' => $link->cloaking_type,
            'bar_position' => $link->bar_position,
            'frame_content' => $link->frame_content,
            'meta_title' => $link->meta_title,
            'meta_description' => $link->meta_description,
            'meta_image' => $link->meta_image,
            'retargeting_code' => $link->retargeting_code,
            'param_tag_forward_param' => $link->param_tag_forward_param,
            'param_tag_forward_campaign' => $link->param_tag_forward_campaign,
            'param_tag_affiliate_tracking' => $link->param_tag_affiliate_tracking,
            'param_tag_affiliate_network' => $link->param_tag_affiliate_network,
            'param_tag_affiliate_network_custom' => $link->param_tag_affiliate_network_custom,
            'auto_keyword_linking_enable' => $link->auto_keyword_linking_enable,
            'meta_keyword' => $link->meta_keyword,
            'geo_redirect_enable' => $link->geo_redirect_enable,
            'geo_redirect_option' => $link->geo_redirect_option,
            'geo_redirect_countries' => $link->geo_redirect_countries,
            'geo_redirect_destination_url' => $link->geo_redirect_destination_url,
            'uptime_monitoring_enabled' => $link->uptime_monitoring_enabled,
            'uptime_is_online' => 1,
            'link_expired_enable' => $link->link_expired_enable,
            'link_expired_date' => $link->link_expired_date,
            'link_expired_url' => $link->link_expired_url,
            'click_limiter_enable' => $link->click_limiter_enable,
            'click_limiter_max_clicks' => $link->click_limiter_max_clicks,
            'click_limiter_url' => $link->click_limiter_url,
            'blank_referrer' => $link->blank_referrer,
            'param_tag_retargeting' => $link->param_tag_retargeting,
            'retargeting_adwords' => $link->retargeting_adwords,
            'retargeting_fb' => $link->retargeting_fb,
            'retargeting_adroll' => $link->retargeting_adroll,
            'retargeting_perfect' => $link->retargeting_perfect,
            'retargeter_code' => $link->retargeter_code,
                )
        );
        $cloned_id = $this->wpdb->insert_id;
        $this->view_data['msg'] = array(
            'status' => 'alert-success',
            'text' => SRTY_MSG_CLONE_LINK
        );
        $this->set_top_message($this->view_data['msg']);
        wp_redirect('?page=' . 'sh_trackers' . '&action=edit&id=' . $cloned_id);
        exit();
    }

    public function page_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var network;

                ZeroClipboard.config({swfPath: "<?php echo SH_JS_URL; ?>/ZeroClipboard.swf"});
                var oTable = jQuery('#v_links').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form form-inline clearfix"<"pull-left"<"#bulk_action.form-group">><"pull-right"<"form-group"f><"form-group"l>>>> <"table-responsive"t><"panel-footer clearfix"<"pull-left"<"form-group text-muted"<"form-control-static"i>>><"pull-right"p>>>',
                    "language": {
                        "search": '',
                        "lengthMenu": '&nbsp;_MENU_',
                        "paginate": {
                            "previous": "«",
                            "next": "»"
                        },
                        "searchPlaceholder": 'Type to Search...',
                    },
                    "serverSide": true,
                    "ajax": {
                        "url": ajaxurl,
                        "type": "POST",
                        "data": function (d) {
                            return jQuery.extend({}, d, {
                                "action": 'srty_datatable'
                            });
                        },
                    },
                    "order": [[0, "desc"]],
                    "columnDefs": [
                        {
                            "targets": 0,
                            "orderable": false
                        },
                        {
                            "targets": 4,
                            "orderable": false
                        },
                        {
                            "targets": 5,
                            "orderable": false
                        },
                    ]
                });
                jQuery('#bulk_action').html('<select name="slcAction" class="form-control input-sm slcAction"><option>Bulk Actions</option><option>Delete</option></select> <button data-action="srty_delete" class="btnAction btn btn-default btn-sm" type="submit">Apply</button>');
                jQuery('#v_links').on('draw.dt', function () {
                    var client = new ZeroClipboard(jQuery(".clippy"));
                    client.on("ready", function (readyEvent) {
                        client.on("copy", function (event) {
                            var clipboard = event.clipboardData;
                            clipboard.setData("text/plain", event.target.dataset.text);
                        });
                        client.on("aftercopy", function (event) {
                            jQuery(event.target)
                                    .attr('data-original-title', 'Copied!')
                                    .tooltip('show');
                            jQuery(event.target).on('hidden.bs.tooltip', function () {
                                jQuery(event.target).attr('data-original-title', 'Copy to Clipboard');
                            });
                        });
                    });
                });

                jQuery('body').tooltip({selector: '[rel="tooltip"]', placement: 'top', title: 'Copy to Clipboard'});

                jQuery('.get_meta').click(function (event) {
                    event.preventDefault();
                    var obj = jQuery(this);
                    var url = jQuery('#destination_url').val();
                    get_meta('srty_get_meta', url, obj);
                    //                    jQuery(this).parent().parent().children('input').val(result);
                    //                    console.log(result);
                });

                jQuery("#primary_url").focusout(function () {
                    var pUrl = jQuery('#primary_url').val();
                    jQuery("#url_availability").removeClass("glyphicon glyphicon-ok text-emerald glyphicon-remove text-alizarin").addClass("fa fa-spinner fa-spin text-river");

                    var request = jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {action: 'srty_checkurl', purl: pUrl},
                        dataType: "text"

                    });
                    request.done(function (msg) {
                        //alert( "Data Saved: " + msg );
                        if (msg) {
                            jQuery("#url_availability").removeClass("glyphicon glyphicon-remove text-alizarin fa fa-spinner fa-spin text-river").addClass("glyphicon glyphicon-ok text-emerald");
                        } else {
                            jQuery("#url_availability").removeClass("glyphicon glyphicon-ok text-emerald fa fa-spinner fa-spin text-river").addClass("glyphicon glyphicon-remove text-alizarin");
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        console.log("Request failed: " + textStatus);
                    });
                });

                jQuery("#backup_url").focusout(function () {
                    var bUrl = jQuery('#backup_url').val();
                    jQuery("#url_availability2").removeClass("glyphicon glyphicon-ok text-emerald glyphicon-remove text-alizarin").addClass("fa fa-spinner fa-spin text-river");

                    var request = jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {action: 'srty_checkurl', purl: bUrl},
                        dataType: "text"

                    });
                    request.done(function (msg) {
                        //alert( "Data Saved: " + msg );
                        if (msg) {
                            jQuery("#url_availability2").removeClass("glyphicon glyphicon-remove text-alizarin fa fa-spinner fa-spin text-river").addClass("glyphicon glyphicon-ok text-emerald");
                        } else {
                            jQuery("#url_availability2").removeClass("glyphicon glyphicon-ok text-emerald fa fa-spinner fa-spin text-river").addClass("glyphicon glyphicon-remove text-alizarin");
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        console.log("Request failed: " + textStatus);
                    });
                });

                jQuery("#mobile_url").focusout(function () {
                    var bUrl = jQuery('#mobile_url').val();
                    jQuery("#url_availability3").removeClass("glyphicon glyphicon-ok text-emerald glyphicon-remove text-alizarin").addClass("fa fa-spinner fa-spin text-river");

                    var request = jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {action: 'srty_checkurl', purl: bUrl},
                        dataType: "text"

                    });
                    request.done(function (msg) {
                        //alert( "Data Saved: " + msg );
                        if (msg) {
                            jQuery("#url_availability3").removeClass("glyphicon glyphicon-remove text-alizarin fa fa-spinner fa-spin text-river").addClass("glyphicon glyphicon-ok text-emerald");
                        } else {
                            jQuery("#url_availability3").removeClass("glyphicon glyphicon-ok text-emerald fa fa-spinner fa-spin text-river").addClass("glyphicon glyphicon-remove text-alizarin");
                        }

                    });
                    request.fail(function (jqXHR, textStatus) {
                        console.log("Request failed: " + textStatus);
                    });
                });

                var client = new ZeroClipboard(jQuery(".clippy"));
                client.on("ready", function (readyEvent) {
                    client.on("copy", function (event) {
                        var clipboard = event.clipboardData;
                        clipboard.setData("text/plain", event.target.dataset.text);
                    });
                    client.on("aftercopy", function (event) {
                        jQuery(event.target)
                                .attr('data-original-title', 'Copied!')
                                .tooltip('show');
                        jQuery(event.target).on('hidden.bs.tooltip', function () {
                            jQuery(event.target).attr('data-original-title', 'Copy to Clipboard');
                        });
                    });
                });

                jQuery(".chosen-select").chosen({
                    no_results_text: "Oops, nothing found!",
                    width: "100%"
                });

                jQuery('#frame_content').change(function () {
                    jQuery('.frame_content_child').collapse('hide');
                    if (jQuery(this).val() == '<?php echo SHORTLY_FRAME_CONTENT_VISIBLE; ?>') {
                        jQuery('.frame_content_child').collapse('show');
                    }
                }).trigger('change');

                jQuery('#cloaking_type').change(function () {
                    jQuery('.cloaking_type_child').collapse('hide');
                    if (jQuery(this).val() == '<?php echo SHORTLY_CLOAKING_TYPE_VIRAL; ?>') {
                        jQuery('.cloaking_type_child').collapse('show');
                    }
                }).trigger('change');

                jQuery('#v_links,#v_links_edit').on('click', '.btncustomize', function () {
                    network = jQuery(this).data('text');
                    jQuery('#campaigns').modal('show');
                    jQuery('#campaign_link').val(network);

                });

                jQuery('.campaign_properties').keyup(function () {
                    /**
                     * only show when there is got data
                     */
                    url = '';
                    if (jQuery('#campaign_source').val() != '') {
                        url += '&source=' + jQuery('#campaign_source').val();
                    }
                    if (jQuery('#campaign_medium').val() != '') {
                        url += '&medium=' + jQuery('#campaign_medium').val();
                    }
                    if (jQuery('#campaign_campaign').val() != '') {
                        url += '&campaign=' + jQuery('#campaign_campaign').val();
                    }
                    if (jQuery('#campaign_content').val() != '') {
                        url += '&content=' + jQuery('#campaign_content').val();
                    }
                    if (jQuery('#campaign_term').val() != '') {
                        url += '&term=' + jQuery('#campaign_term').val();
                    }
                    if (jQuery('#campaign_cpc').val() != '') {
                        url += '&cpc=' + jQuery('#campaign_cpc').val();
                    }
                    url = network + url.replace(/^&/, "?");
                    jQuery('#campaign_link').val(url);

                })



            });

        </script>
        <?php
    }

    public function js_typeahead_links() {
        if (wp_script_is('jquery', 'done')) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
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

                    jQuery('.typeahead').on('added', function () {
                        jQuery('.typeahead').typeahead('destroy');
                        jQuery('.typeahead').typeahead({
                            autoselect: true,
                            hint: false,
                            highlight: false,
                            limit: 40,
                            minLength: 0,
                        }
                        , {
                            name: 'link_tracking_url',
                            displayKey: 'url',
                            templates: {
                                suggestion: Handlebars.compile('<p style="font-size:14px;"><strong>{{link_name}}</strong> – {{url}}</p>')
                            },
                            // `ttAdapter` wraps the suggestion engine in an adapter that
                            // is compatible with the typeahead jQuery plugin
                            source: engine.ttAdapter(),
                        });
                    }).trigger('added');

                    var client = new ZeroClipboard(jQuery(".clippy"));
                    client.on("ready", function (readyEvent) {
                        client.on("copy", function (event) {
                            var clipboard = event.clipboardData;
                            clipboard.setData("text/plain", event.target.dataset.text);
                        });
                        client.on("aftercopy", function (event) {
                            jQuery(event.target)
                                    .attr('data-original-title', 'Copied!')
                                    .tooltip('show');
                            jQuery(event.target).on('hidden.bs.tooltip', function () {
                                jQuery(event.target).attr('data-original-title', 'Copy to Clipboard');
                            });
                        });
                    });
                });
            </script>
            <?php
        }
    }

    /**
     * ajax call
     */
    public function ajax_datatable() {
        wp_send_json($this->_datatable_get_all());
        wp_die();
    }

    public function ajax_delete() {

        $this->_delete_batch($this->explode_trim($_POST['ids']));
        $this->view_data['msg'] = array(
            'status' => 'alert-success',
            'text' => SRTY_MSG_LINK_DELETED
        );
        $this->set_top_message($this->view_data['msg']);
        wp_send_json(array('result' => 1));
        wp_die();
    }

    /**
     * DB section
     */

    /**
     * 
     * @global type $wpdb
     * @return type
     */
    private function _by_id($id = FALSE) {
        if ($id === FALSE) {
            $id = isset($_GET['id']) ? $_GET['id'] : -1;
        }

        return $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM $this->table_name WHERE id=%d", $id));
    }

    private function _get_all() {
        return $this->wpdb->get_results("SELECT * FROM $this->table_name ");
    }

    private function _datatable_get_all() {

        $params = array();


        $where = '';
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $where = " WHERE ( link_name LIKE %s OR tracking_link LIKE %s OR reference_tags LIKE %s )";
                $params = array_merge($params, array('%%' . $_search['value'] . '%%', '%%' . $_search['value'] . '%%', '%%' . $_search['value'] . '%%'));
            }
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);

        $results = $this->wpdb->get_results($this->wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS id,link_name,tracking_link,reference_tags FROM {$this->table_name} {$where} {$order} {$limit}", $params));

        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");
        $record = $this->wpdb->get_row("SELECT COUNT(id) AS total FROM $this->table_name;");

        $data = array();
        foreach ($results as $row) {
            $tags = '<span class="label label-default">' . implode('</span> <span class="label label-default">', explode(',', $row->reference_tags)) . '</span>';
            $data[] = array(
                '<input type="checkbox" name="cbAction[]" value="' . $row->id . '">',
                $row->link_name,
                '<span id="clip_' . $row->id . '" data-text="' . $this->current_domain(TRUE, FALSE, TRUE) . $row->tracking_link . '" class="clippy btn btn-xs btn-default" rel="tooltip" ><i class="fa fa-copy"></i></span> ' . '<a href=' . $this->current_domain(TRUE, FALSE, TRUE) . $row->tracking_link . ' class="linked" target="_blank"> ' . $this->ellipsize($this->current_domain(TRUE, FALSE, TRUE) . $row->tracking_link) . '</a>',
                $tags,
                '<button class="btncustomize btn btn-xs btn-default" data-text="' . $this->current_domain(TRUE, FALSE, TRUE) . $row->tracking_link . '"><i class="fa fa-random"></i> Customize</button>',
                '<div class="btn-group btn-group-xs">' .
                '<a href="?page=' . 'sh_trackers' . '&action=edit&id=' . $row->id . '" class="btn btn-default">edit</a>' .
                '<a href="?page=' . 'sh_trackers' . '&action=clone&id=' . $row->id . '" class="btn btn-default">clone</a>' .
                '<a href="' . wp_nonce_url('?page=' . 'sh_trackers' . '&action=delete&id=' . $row->id, 'delete') . '" class="btn btn-default confirm">delete</a>' .
                '</div>',
            );
        }

        return array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $data
        );
    }

    private function _filter($request, $columns) {
        $where = '';
        return $where;
    }

    /* private function validate_form($link_name, $destination_url, $tracking_link, $link_redirect_type, $cloaking_status_enable, $cloaking_type, $bar_position, $frame_content, $meta_title, $meta_title, $meta_description, $param_tag_affiliate_tracking, $param_tag_affiliate_network, $auto_keyword_linking_enable, $meta_keyword) {
      // Make the WP_Error object global
      global $form_error;

      // instantiate the class
      $form_error = new WP_Error;

      // If any field is left empty, add the error message to the error object
      if (empty($link_name) || empty($destination_url) || empty($tracking_link)) {
      $form_error->add('field', 'No field should be left empty');
      }

      // if the name field isn't alphabetic, add the error message
      if (!ctype_alnum($tracking_link)) {
      $form_error->add('invalid_link', 'Invalid tracking link entered');
      }

      // Check if the email is valid
      if (!$this->is_valid_url($destination_url)) {
      $form_error->add('invalid_url', 'URL is not valid');
      }

      // if $form_error is WordPress Error, loop through the error object
      // and echo the error
      if (is_wp_error($form_error)) {
      foreach ($form_error->get_error_messages() as $error) {
      //                echo '<div>';
      //                echo '<strong>ERROR</strong>:';
      //                echo $error . '<br/>';
      //                echo '</div>';
      return TRUE;
      }
      }
      } */

    public function srty_get_meta() {
        $meta_type = $this->_post('meta_type');
        $target_url = $this->_post('url');
        $result = $this->curl($target_url);
        $meta = $this->get_preg_meta_tags($result);
        wp_send_json(array('data' => isset($meta[$meta_type]) ? $meta[$meta_type] : ''));
        wp_die();
    }

    public function srty_checkurl() {
        $url = $_POST["purl"];

        $pattern = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';
        if (!preg_match($pattern, $url)) { // Check for valid URL Format
            echo FALSE;
        } else {
            $c = curl_init();
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_HEADER, 1); //get the header
            curl_setopt($c, CURLOPT_NOBODY, 1); //and *only* get the header
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); //get the response as a string from curl_exec(), rather than echoing it
            curl_setopt($c, CURLOPT_FRESH_CONNECT, 1); //don't use a cached version of the url
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($c, CURLOPT_USERAGENT, 'shortywp.com');
            curl_exec($c);
            $httpcode = curl_getinfo($c, CURLINFO_HTTP_CODE);
            if ($httpcode > 0) {
                echo TRUE;
            } else {
                echo FALSE;
            }
        }
        wp_die();
    }

}

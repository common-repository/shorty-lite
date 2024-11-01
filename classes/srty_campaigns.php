<?php

class Srty_campaigns extends Srty_core {

    public $tbl_links;

    public function __construct() {
        parent::__construct();
        $this->table_name = $this->wpdb->prefix . SH_PREFIX . 'campaigns';
        $this->tbl_links = $this->wpdb->prefix . SH_PREFIX . 'links';
    }

    public function init() {
        add_action('wp_ajax_srty_campaigns_datatable', array($this, 'ajax_datatable'));
        add_action('wp_ajax_srty_campaigns_delete', array($this, 'ajax_delete'));
        add_action('wp_ajax_srty_campaigns_typeahead', array($this, 'ajax_typeahead'));
//        add_action('wp_custom_route', array($this, 'custom_route'));
    }

    public function routes() {
        if (isset($_GET['action']) && $_GET['action'] == 'add') {
            $this->add();
        } else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $this->edit();
        } else if (isset($_GET['action']) && $_GET['action'] == 'delete') {
            $this->delete();
        } else if (isset($_GET['action']) && $_GET['action'] == 'code') {
            $this->code();
        } else {
            $this->display();
        }
    }

    public function display() {
        add_action('admin_footer', array($this, 'page_js'));
        wp_cache_delete(SH_PREFIX . 'cache_the_content');
        $this->view('v_campaigns', $this->view_data);
    }

    public function add() {
        if (!empty($_POST) && check_admin_referer('frmNewCampaign')) {
            if (isset($_POST['btnCreateCampaign'])) {
                wp_cache_delete(SH_PREFIX . 'cache_the_content');
                $this->gump->validation_rules(array(
                    'link_id' => 'required',
                    'tracking_link' => 'required|tracking_link_exist',
                    'source' => 'required',
                    'medium' => 'required',
                    'campaign' => 'required'
                ));

                $this->gump->filter_rules(array(
                    'link_id' => 'trim|sanitize_string',
                    'source' => 'trim|sanitize_string',
                    'medium' => 'trim|sanitize_string',
                    'campaign' => 'trim|sanitize_string',
                ));

                $validated_data = $this->gump->run($_POST);
                if ($validated_data !== FALSE) {

                    $link_id = $this->_post('link_id');
                    $source = $this->_post('source');
                    $medium = $this->_post('medium');
                    $campaign = $this->_post('campaign');
                    $content = $this->_post('content');
                    $term = $this->_post('term');
                    $this->wpdb->insert(
                            $this->table_name, array(
                        'tracking_campaign' => $this->generate_random_letters(),
                        'link_id' => $link_id,
                        'source' => $source,
                        'medium' => $medium,
                        'campaign' => $campaign,
                        'content' => $content,
                        'term' => $term,
                            )
                    );
                    $link_id = $this->wpdb->insert_id;
                    $this->view_data['msg'] = array(
                        'status' => 'alert-success',
                        'text' => SRTY_MSG_CAMPAIGN_ADDED
                    );
                    $this->set_top_message($this->view_data['msg']);
                    wp_redirect('?page=sh_campaigns_page&action=edit&id=' . $link_id);
                    exit();
                } else {
                    $this->view_data['error'] = $this->gump->get_errors_array();
                    $this->view_data['msg'] = array(
                        'status' => 'alert-danger',
                        'text' => SRTY_MSG_CAMPAIGN_TOP_ERROR_MESSAGE
                    );
                }
            }
        }

        add_action('admin_footer', array($this, 'js_typeahead_campaigns'));
        $this->view_data['tracking_link'] = $this->generate_random_letters();
        $this->view('v_campaign-new', $this->view_data);
    }

    public function edit($campaign_id = FALSE) {
        $id = $_GET['id'];

        if (!empty($_POST) && check_admin_referer('frmEditCampaign-' . $id)) {
            if (isset($_POST['btnEditCampaign'])) {
                wp_cache_delete(SH_PREFIX . 'cache_the_content');

                $this->gump->validation_rules(array(
                    'link_id' => 'required',
                    'tracking_link' => 'required|tracking_link_exist',
                    'source' => 'required',
                    'medium' => 'required',
                    'campaign' => 'required'
                ));

                $this->gump->filter_rules(array(
                    'link_id' => 'trim|sanitize_string',
                    'source' => 'trim|sanitize_string',
                    'medium' => 'trim|sanitize_string',
                    'campaign' => 'trim|sanitize_string',
                ));

                $validated_data = $this->gump->run($_POST);
                if ($validated_data !== FALSE) {

                    $link_id = $this->_post('link_id');
                    $source = $this->_post('source');
                    $medium = $this->_post('medium');
                    $campaign = $this->_post('campaign');
                    $content = $this->_post('content');
                    $term = $this->_post('term');

                    $this->wpdb->update(
                            $this->table_name, array(
                        'link_id' => $link_id,
                        'source' => $source,
                        'medium' => $medium,
                        'campaign' => $campaign,
                        'content' => $content,
                        'term' => $term,
                            ), array('id' => $id)
                    );

                    $this->view_data['msg'] = array(
                        'status' => 'alert-success',
                        'text' => SRTY_MSG_CAMPAIGN_EDITED
                    );
                } else {
                    $this->view_data['error'] = $this->gump->get_errors_array();
                    $this->view_data['msg'] = array(
                        'status' => 'alert-danger',
                        'text' => SRTY_MSG_CAMPAIGN_TOP_ERROR_MESSAGE
                    );
                }
            }
        }

        add_action('admin_footer', array($this, 'js_typeahead_campaigns'));
        $this->view_data['campaign'] = $this->_by_id();

        $this->view('v_campaign-edit', $this->view_data);
    }

    public function code() {
        $id = $_GET['id'];
        $this->view_data['campaign'] = $this->_by_id();
        $this->view('v_campaign-code', $this->view_data);
    }

    public function page_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                ZeroClipboard.config({swfPath: "<?php echo SH_JS_URL; ?>/ZeroClipboard.swf"});
                var oTable = jQuery('#v_campaigns').DataTable({
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
                                "action": 'srty_campaigns_datatable'
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
                            "targets": 3,
                            "orderable": false
                        },
                    ]
                });
                jQuery('#bulk_action').html('<select name="slcAction" class="form-control input-sm slcAction"><option>Bulk Actions</option><option>Delete</option></select> <button data-action="srty_campaigns_delete" class="btnAction btn btn-default btn-sm" type="submit">Apply</button>');
                jQuery('#v_campaigns').on('draw.dt', function () {
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
            });

        </script>
        <?php
    }

    public function js_typeahead_campaigns() {
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


                    jQuery('.typeahead').typeahead({
                        autoselect: true,
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
                    });

                    jQuery('.typeahead').bind('paste', function (e) {
                        query = e.originalEvent.clipboardData.getData('text');
                        jQuery.post(ajaxurl + '?action=srty_campaigns_typeahead&q=' + query, function (data) {
                            if (typeof (data[0]) != "undefined" && data[0] !== null) {
                                jQuery('#link_id').val(data[0].id);
                            }
                        });
                    });

                    jQuery('.typeahead').bind('keyup', function (e) {
                        query = this.value;
                        jQuery.post(ajaxurl + '?action=srty_campaigns_typeahead&q=' + query, function (data) {
                            console.log(data);
                            if (typeof (data[0]) != "undefined" && data[0] !== null) {
                                jQuery('#link_id').val(data[0].id);
                            }
                        });
                    });

                    var idSelectedHandler = function (eventObject, suggestionObject, suggestionDataset) {
                        console.log(suggestionObject.id);
                        jQuery('#link_id').val(suggestionObject.id);
                    };
                    jQuery('.typeahead').on('typeahead:selected', idSelectedHandler);

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

    public function ajax_typeahead() {

        $search = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
        $search = str_replace($this->current_domain(TRUE), '', $search);
        if ($search == '') {
            $sql = "SELECT * FROM {$this->tbl_links} LIMIT 30;";
            $links = $this->wpdb->get_results($sql, OBJECT);
        } else {
            $sql = "SELECT * FROM {$this->tbl_links} WHERE link_name LIKE %s OR tracking_link LIKE %s;";
            $links = $this->wpdb->get_results($this->wpdb->prepare($sql, array('%' . $search . '%', '%' . $search . '%')), OBJECT);
        }

        $result = array();
        foreach ($links as $link) {
            $result[] = array(
                'id' => $link->id,
                'link_name' => $link->link_name,
                'url' => $this->current_domain(TRUE) . $link->tracking_link,
                'destination_url' => $link->destination_url,
            );
        }
        wp_send_json($result);
        wp_die();
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
            'text' => SRTY_MSG_CAMPAIGN_DELETED
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

        return $this->wpdb->get_row($this->wpdb->prepare("SELECT a.*, b.tracking_link FROM {$this->table_name} a INNER JOIN {$this->tbl_links} b ON b.id = a.link_id WHERE a.id=%d", $id));
    }

    private function _datatable_get_all() {

        $params = array();

        $where = '';
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $where = " WHERE ( a.source LIKE %s OR a.tracking_campaign LIKE %s )";
                $params = array_merge($params, array('%%' . $_search['value'] . '%%', '%%' . $_search['value'] . '%%'));
            }
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);

        $results = $this->wpdb->get_results($this->wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS a.*,b.link_name,b.tracking_link FROM {$this->table_name} a LEFT JOIN {$this->tbl_links} b ON a.link_id = b.id {$where} {$order} {$limit}", $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $data = array();
        foreach ($results as $row) {
            $data[] = array(
                '<input type="checkbox" name="cbAction[]" value="' . $row->id . '">',
                '<a href="?page=sh_links_page&action=edit&id=' . $row->link_id . '">' . $row->link_name . '</a> from ' . $row->source . ' via ' . $row->medium . '<br/>'
                . '<small class="text-muted">' . $row->source . ' &gt; ' . $row->medium . ' &gt;  ' . $row->campaign . ((trim($row->content) != '') ? ' &gt; ' . $row->content : '') . ((trim($row->term) != '') ? ' &gt; ' . $row->term : '') . '</small>',
                '<span id="clip_' . $row->id . '" data-text="' . $this->current_domain(TRUE) . 'c/' . $row->tracking_campaign . '" class="clippy btn btn-xs btn-default" rel="tooltip" ><i class="fa fa-copy"></i></span> ' . '<a href=' . $this->current_domain(TRUE) . 'c/' . $row->tracking_campaign . ' class="linked" target="_blank"> ' . $this->current_domain(TRUE) . 'c/' . $row->tracking_campaign . '</a>',
                '<div class="btn-group btn-group-xs">'
                . '<a href="?page=sh_campaigns_page&action=code&id=' . $row->id . '" class="btn btn-default">codes</a>'
                . '<a href="?page=sh_campaigns_page&action=edit&id=' . $row->id . '" class="btn btn-default">edit</a>'
                . '<a href="' . wp_nonce_url('?page=sh_campaigns_page&action=delete&id=' . $row->id, 'delete') . '" class="btn btn-default confirm">delete</a>'
                . '</div>',
            );
        }

        return array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $data
        );
    }

}

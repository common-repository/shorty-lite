<?php

class Srty_conversions extends Srty_core {

    public function __construct() {
        parent::__construct();
        $this->view_data['date_selection'] = $this->date_selection();

        $this->tbl_import_temp = $this->wpdb->prefix . SH_PREFIX . 'import_temp';
        $this->table_name = $this->wpdb->prefix . SH_PREFIX . 'conversions_log';
        $this->tbl_conversions_log = $this->wpdb->prefix . SH_PREFIX . 'conversions_log';
        $this->tbl_visits_log = $this->wpdb->prefix . SH_PREFIX . 'visits_log';

        $this->view_data['currency'] = new Srty_currency();
    }

    public function init() {
        add_action('wp_ajax_srty_report_conversions_datatable', array($this, 'srty_report_conversions_datatable'));
        add_action('wp_ajax_srty_cbforceall', array($this, 'srty_cbforceall'));
        add_action('wp_ajax_srty_conversion_delete', array($this, 'ajax_conversion_delete'));
        add_action('wp_ajax_srty_imports_datatable', array($this, 'ajax_datatable'));
    }

    public function routes() {
        if (isset($_GET['action']) && $_GET['action'] == 'import' ) {
            if (isset($_GET['sub']) && $_GET['sub'] == 'add') {
                $this->add();
            } else if (isset($_GET['sub']) && $_GET['sub'] == 'edit') {
                $this->edit();
            } else if (isset($_GET['sub']) && $_GET['sub'] == 'import') {
                $this->import();
            } else if (isset($_GET['sub']) && $_GET['sub'] == 'cancel_import') {
                $this->cancel_import();
            } else if (isset($_GET['sub']) && $_GET['sub'] == 'confirm_import') {
                $this->confirm_import();
            } else if (isset($_GET['sub']) && $_GET['sub'] == 'delete') {
                $this->delete();
            } else {
                $this->import_display();
            }
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-conversions-details') {
            $this->_conversion_details();
            $this->view('v_report-conversion-details', $this->view_data);
        } else {
            $this->display();
            add_action('admin_footer', array($this, 'conversions_report_js'));
            $this->view('v_report-conversions', $this->view_data);
        }
    }

    public function display() {
        if ((bool) $this->_get('download', FALSE) === TRUE ) {

            $params = array();
            $where = ' WHERE 1 ';
            $search = $this->_get('search', FALSE);
            if ($search !== FALSE) {
                if (trim($search) != '') {
                    $where .= " AND ( goal_name LIKE %s OR goal_type LIKE %s OR goal_value LIKE %s OR goal_reference LIKE %s OR status LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $search . '%%',
                        '%%' . $search . '%%',
                        '%%' . $search . '%%',
                        '%%' . $search . '%%',
                        '%%' . $search . '%%'
                    ));
                }
            }

            $order = $this->_order($params);

            $results = $this->wpdb->get_results($this->wpdb->prepare("SELECT conversion_date,goal_name,goal_type,goal_value,goal_reference,status,id FROM {$this->tbl_conversions_log} {$where} {$order}", $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'conversion' . current_time("YmdHis") . '.csv';
            $output_handle = @fopen('php://output', 'w');

            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Description: File Transfer');
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename=' . $output_filename);
            header('Expires: 0');
            header('Pragma: public');

            $first = true;
            foreach ($results as $row) {
                if ($first) {
                    $titles = array();
                    foreach ($row as $key => $val) {
                        $titles[] = $key;
                    }
                    fputcsv($output_handle, $titles);
                    $first = false;
                }

                $leadArray = (array) $row; // Cast the Object to an array
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    public function conversions_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var oTable = jQuery('#report_conversions').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"<"#bulk_action.form-group"><"form-group"f>|><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_conversions_datatable'
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "columnDefs": [
                        {
                            "targets": 0,
                            "orderable": false,
                        },
                        {
                            "targets": 7,
                            "orderable": false
                        },
                    ]
                });
                jQuery('#bulk_action').html('<select name="slcAction" class="form-control input-sm slcAction"><option>Bulk Actions</option><option>Delete</option></select> <button data-action="srty_conversion_delete" class="btnAction btn btn-default btn-sm" type="submit">Apply</button>');
        
                    jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                    download_excel('.download-excel', '?page=sh_conversions_page&action=report-conversions');
           
            });
        </script>
        <?php
    }

    public function srty_report_conversions_datatable() {

        $params = array();
        $where = ' WHERE 1 ';
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $where .= " AND ( goal_name LIKE %s OR goal_type LIKE %s OR goal_value LIKE %s OR goal_reference LIKE %s OR status LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%'
                ));
            }
        }
        $_order = $_POST['order'];
        $_order[0]['column'] = $_order[0]['column'] - 1;

        /**
         * correcting order column
         */
        $_POST['order'] = $_order;
        $order = $this->_order($params);
        $limit = $this->_limit($params);

        $results = $this->wpdb->get_results($this->wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS conversion_date,goal_name,goal_type,goal_value,goal_reference,status,id FROM {$this->tbl_conversions_log} {$where} {$order} {$limit}", $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");
        $record = $this->wpdb->get_row("SELECT COUNT(id) AS total FROM $this->tbl_conversions_log;");

        $data = array();
        foreach ($results as $row) {
            $data[] = array(
                '<input type="checkbox" name="cbAction[]" value="' . $row->id . '">',
                $row->conversion_date,
                $row->goal_name,
                $row->goal_type,
                $row->goal_value,
                $row->goal_reference,
                ($row->status == 'Accepted') ? '<span class="label label-success">' . $row->status . '</span>' : '<span class="label label-danger">' . $row->status . '</span>',
                '<a href="?page=sh_conversions_page&action=report-conversions-details&id=' . $row->id . '" class="btn btn-default btn-xs">view &amp; edit</a>',
            );
        }

        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $data
        ));

        wp_die();
    }

    public function srty_cbforceall() {
        if ($id = $this->_post('id', FALSE)) {
            $this->wpdb->update(
                    $this->tbl_import_temp, array(
                'force_import' => $this->_post('status') == 'true' ? 1 : 0,
                    ), array(
                'status' => 'Rejected',
                'id' => $id
                    ), array('%d'), array('%s', '%d')
            );
        } else {
            $this->wpdb->update(
                    $this->tbl_import_temp, array(
                'force_import' => $this->_post('status') == 'true' ? 1 : 0,
                    ), array(
                'status' => 'Rejected'
                    ), array(
                '%d',
                    ), array('%s')
            );
        }
    }

    private function _conversion_details() {
        $id = $this->_get('id', FALSE);

        if ($id === FALSE) {
            $this->view_data['msg'] = array(
                'status' => 'alert-danger',
                'text' => SRTY_MSG_GENERAL_RECORD_NOT_FOUND
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=sh_conversions_page&action=report-conversions');
            exit();
        }

        if ($this->_post('btnDelete', FALSE) !== FALSE) {
            $this->wpdb->delete($this->tbl_conversions_log, array('id' => $id), array('%d'));
            $this->view_data['msg'] = array(
                'status' => 'alert-success',
                'text' => SRTY_MSG_REPORT_GOAL_DELETED
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=sh_conversions_page&action=report-conversions');
            exit();
        }

        if ($this->_post('btnUpdate', FALSE) !== FALSE) {
            $this->wpdb->update(
                    $this->tbl_conversions_log, array(
                'conversion_date' => $this->_post('conversion_date'),
                'goal_name' => $this->_post('goal_name'),
                'goal_type' => $this->_post('goal_type'),
                'goal_value' => $this->_post('goal_value'),
                'goal_reference' => $this->_post('goal_reference'),
                'status' => $this->_post('status'),
                'visits_log_id' => $this->_post('visits_log_id'),
                'message' => $this->_post('message'),
                    ), array(
                'id' => $id
                    ), array(
                '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%s',
                    ), array('%d')
            );

            $this->view_data['msg'] = array(
                'status' => 'alert-success',
                'text' => SRTY_MSG_REPORT_GOAL_UPDATED
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=sh_conversions_page&action=report-conversions');
            exit();
        }
        /**
         * GET CONVERSION
         */
        $this->view_data['conversion'] = $this->wpdb->get_row($this->wpdb->prepare("SELECT a.*,b.ip_address,b.ip_country_code,b.ip_country_name,b.ip_city_name,b.ip_latitude,b.ip_longitude FROM {$this->tbl_conversions_log} a LEFT JOIN {$this->tbl_visits_log} b ON a.visits_log_id = b.id WHERE a.id=%d", array($id)));

        if (!isset($this->view_data['conversion']->id)) {
            $this->view_data['msg'] = array(
                'status' => 'alert-danger',
                'text' => SRTY_MSG_GENERAL_RECORD_NOT_FOUND
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=sh_conversions_page&action=report-conversions');
            exit();
        }
        $this->view_data['campaign'] = $this->wpdb->get_row($this->wpdb->prepare("SELECT * FROM {$this->tbl_visits_log} WHERE campaign!='' AND id=%d", array($this->view_data['conversion']->visits_log_id)));

        /**
         * GET TIMELINE
         */
        $sql = " SELECT * FROM 
                (
                    SELECT 
                        'CV' AS datatype,
                        id,
                        link_id,
                        conversion_date AS timeline_date,
                        goal_name,
                        goal_value,
                        goal_reference,
                        referrer_url,
                        status,
                        NULL AS tracking_link,
                        NULL AS tracking_campaign,
                        NULL AS campaign_id,
                        NULL AS campaign_source,
                        NULL AS visitor_id,
                        message
                    FROM {$this->tbl_conversions_log} WHERE conversion_date < %s and visits_log_id=%d
                    UNION
                    SELECT 
                        'CLICK' AS datatype,
                        id,
                        link_id,
                        created_date AS timeline_date,
                        '' AS goal_name,
                        '' AS goal_value,
                        '' AS goal_reference,
                        referrer_url,
                        '' AS status,
                        tracking_link,
                        tracking_campaign,
                        campaign_id,
                        source AS campaign_source,
                        visitor_id,
                        NULL AS message
                    FROM {$this->tbl_visits_log} WHERE id = %d
                ) AS t ORDER BY timeline_date DESC";
        $this->view_data['timelines'] = $this->wpdb->get_results($this->wpdb->prepare($sql, array(
                    $this->view_data['conversion']->conversion_date,
                    $this->view_data['conversion']->visits_log_id,
                    $this->view_data['conversion']->visits_log_id
        )));
    }

    public function ajax_conversion_delete() {
        
        $ids = $this->explode_trim($_POST['ids']);
        foreach ($ids as $id) {
            $this->wpdb->delete($this->tbl_conversions_log, array('id' => $id), array('%d'));
        }
        $this->view_data['msg'] = array(
            'status' => 'alert-success',
            'text' => SRTY_MSG_GOAL_DELETED
        );
        $this->set_top_message($this->view_data['msg']);
        wp_send_json(array('result' => 1));
        wp_die();
    }

    public function page_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var error_count = 0;
                var oTable = jQuery('#import_verification').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading tbltitle"> <"table-responsive"t><"panel-footer form-actions clearfix thebutton"p>>',
                    "language": {
                        "search": '',
                        "lengthMenu": '&nbsp;_MENU_',
                        "paginate": {
                            "previous": "«",
                            "next": "»"
                        }
                    },
                    "serverSide": true,
                    "ajax": {
                        "url": ajaxurl,
                        "type": "POST",
                        "data": function (d) {
                            return jQuery.extend({}, d, {
                                "action": 'srty_imports_datatable'
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
                            "targets": 5,
                            "orderable": false
                        },
                        {
                            "targets": 7,
                            "orderable": false
                        },
                    ],
                    "fnRowCallback": function (nRow, aData, iDisplayIndex) {
                        if (aData[8] == 'Rejected') {
                            nRow.className = "text-danger";
                            error_count++;
                        } else {
                            nRow.className = "";
                        }
                        jQuery('#error_count').html(error_count);
                        return nRow;

                    },
                    'iDisplayLength': 100
                });
                jQuery('.tbltitle').html('The file you\'ve uploaded contains <span id="error_count" class="label label-danger">0</span> invalid rows. Tick the checkbox if you want to ignore all errors and import anyway.');
                jQuery('#bulk_action').html('<select name="slcAction" class="form-control input-sm slcAction"><option>Bulk Actions</option><option>Delete</option></select> <button data-action="srty_delete" class="btnAction btn btn-default btn-sm" type="submit">Apply</button>');
                var btn = '<a href="?page=sh_conversions_page&action=import&sub=cancel_import" class="btn btn-default pull-left"><i class="fa fa-trash-o"></i> Cancel Import</a>';
                btn += '<a style="margin-left:10px;"  href="?page=sh_conversions_page&action=import&sub=confirm_import" class="btn btn-primary pull-right"><i class="fa fa-check-circle"></i> Confirm Import</a>';
                jQuery('.thebutton').prepend(btn);
                jQuery('#import_option').change(function () {
                    if (jQuery(this).val() == 'custom') {
                        jQuery('#custom_import_option').show();
                    } else {
                        jQuery('#custom_import_option').hide();
                    }
                });

                jQuery('.cbForce').change(function () {
                    console.log(jQuery(this).is(':checked'));
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {action: "srty_cbforceall", status: jQuery(this).is(':checked'), ci_csrf_token: ''},
                        dataType: "json"

                    });
                });

                jQuery('#import_verification').on('click', '.cbForceSingle', function () {
                    console.log(jQuery(this).is(':checked'));
                    console.log(jQuery(this).val());
                    var request = jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        data: {action: "srty_cbforceall", status: jQuery(this).is(':checked'), id: jQuery(this).val(), ci_csrf_token: ''},
                        dataType: "json"

                    });
                });
            });

        </script>
        <?php
    }

    private function _valid_data($data = array(), $affiliate_network = 'custom') {
        $return = array();
        $return['status'] = STATUS_ACCEPTED;
        $return['message'] = '';

        /**
         * custom csv data set
         */
        switch ($affiliate_network) {
            case 'jvzoo':
                $total_fields = 8;
                $conversion_date = $data[0];
                $goal_name = $data[3];
                $goal_type = ($data[4] == 'COMPLETED' ? 'SALE' : $data[4]);
                $goal_value = $data[5];
                $goal_reference = $data[6];
                $ctid = $data[1];

                /**
                 * check if goaltype is REFUNDED, we make sure the goal value is -ve value
                 */
                if ($goal_type == 'REFUNDED') {
                    $goal_value = 0 - abs($goal_value);
                    //map REFUNDED to SALE
                    $goal_type = 'SALE';
                    $return['message'] .= 'Sale refunded' . PHP_EOL;
                }
                break;
            case 'cj':
                $total_fields = 20;
                $conversion_date = date('Y-m-d H:i:s', date_timestamp_get(date_create_from_format('j-M-Y H:i:s', $data[12]))); //4-Feb-2015 07:00:26
                $goal_name = $data[9];
                if (strpos(strtolower($data[2]), 'sale') !== false) {
                    $goal_type = 'SALE';
                } else {
                    $goal_type = 'LEAD';
                }
                $goal_value = $data[3];
                $goal_reference = $data[17];
                $ctid = $data[14];
                break;
            case 'shareasale':
                $total_fields = 24;
                $conversion_date = date('Y-m-d H:i:s', date_timestamp_get(date_create_from_format('m/d/Y h:i:s A', $data[4])));
                $goal_name = $data[3];
                $goal_type = 'SALE';
                $goal_value = $data[6];
                $goal_reference = $data[0];
                $ctid = filter_var($data[11], FILTER_SANITIZE_NUMBER_INT);
                break;
            default:
                $total_fields = 6;
                $conversion_date = $data[0];
                $goal_name = $data[1];
                $goal_type = $data[2];
                $goal_value = $data[3];
                $goal_reference = $data[4];
                $ctid = $data[5];
                break;
        }

        if (count($data) < $total_fields) {
            $return['status'] = STATUS_REJECTED;
            $return['message'] .= 'Field count not match' . PHP_EOL;
        } elseif (!in_array(strtoupper($goal_type), array('SALE', 'LEAD'))) {
            $return['status'] = STATUS_REJECTED;
            $return['message'] .= 'Invalid Type' . PHP_EOL;
        } elseif (!is_numeric($goal_value)) {
            $return['status'] = STATUS_REJECTED;
            $return['message'] .= 'GV does not valid' . PHP_EOL;
//        } elseif ($this->_post('time_format') == '24' && !$this->_is_24_hour($data[1])) {
//            $return['status'] = STATUS_REJECTED;
//            $return['message'] .= 'Invalid time format' . PHP_EOL;
//        } elseif ($this->_post('time_format') == '12' && !$this->_is_12_hour($data[1])) {
//            $return['status'] = STATUS_REJECTED;
//            $return['message'] .= 'Invalid time format' . PHP_EOL;
        } elseif (!$this->_is_valid_date($conversion_date, 'Y-m-d H:i:s')) {
            $return['status'] = STATUS_REJECTED;
            $return['message'] .= 'Invalid datetime format' . PHP_EOL;
        }
        $click_id = trim($ctid);
        if ($click_id == 0 || trim($click_id) == '') {
            $return['status'] = STATUS_REJECTED;
            $return['message'] .= 'CTID does not exist' . PHP_EOL;
            $click_id = '';
        } else {

            $sql = "SELECT id,link_id FROM {$this->tbl_visits_log} WHERE id = %d";
            $visitor_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id)), OBJECT);
            if (isset($visitor_log->id)) {

                /**
                 * check if same CTID already imported. if exist, we should ignore that
                 */
                $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE visits_log_id = %d AND goal_name = %s AND goal_type = %s AND goal_value = %.2f";
                $ctid_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id, $goal_name, $goal_type, $goal_value)), OBJECT);
                if ($ctid_log->total > 0) {
                    $return['status'] = STATUS_REJECTED;
                    $return['message'] .= 'Duplicate CTID ' . $click_id . PHP_EOL;
                } else {
                    $return['message'] .= 'CTID Found' . PHP_EOL;
                }
            } else {
                $return['status'] = STATUS_REJECTED;
                $return['message'] .= 'CTID Not Found' . PHP_EOL;
            }


            if (get_option(SH_PREFIX . 'settings_duplicate_handling') == SHORTLY_DUPLICATE_HANDLING_IGNORE) {
                if (trim($goal_reference) != '') {
                    $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = %s";
                    $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($goal_reference)), OBJECT);
                    if ($conversion_log->total > 0) {
                        $return['status'] = STATUS_REJECTED;
                        $return['message'] .= 'Duplicate Goal Reference ID ' . $goal_reference . PHP_EOL;
                    }
                }
            }
        }
        /**
         * disable this as we did not use it for the import
          $country = $this->ip_info();
         * 
         */
        $return['link_id'] = isset($visitor_log->link_id) ? $visitor_log->link_id : 0;
        $return['goal_id'] = 0;
        $return['visits_log_id'] = isset($visitor_log->id) ? $visitor_log->id : $ctid;
        $return['goal_name'] = $goal_name;
        $return['goal_type'] = $goal_type;
        $return['goal_value'] = $goal_value;
        $return['goal_reference'] = $goal_reference;
        $return['referrer_url'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $return['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $return['ip_country_code'] = isset($country['country_code']) ? $country['country_code'] : '';
        $return['ip_country_name'] = isset($country['country']) ? $country['country'] : '';
        $return['ip_city_name'] = isset($country['city']) ? $country['city'] : '';
        $return['ip_latitude'] = isset($country['latitude']) ? $country['latitude'] : '';
        $return['ip_longitude'] = isset($country['longitude']) ? $country['longitude'] : '';
        $return['user_agent_string'] = $_SERVER['HTTP_USER_AGENT'];

        $return['conversion_date'] = $conversion_date;
        $return['created_date'] = current_time("Y-m-d H:i:s");

        return $return;
    }

    private function _is_24_hour($time) {
        return preg_match("/^(2[0-3]|[01][0-9]):([0-5][0-9]|[0-5][0-9]:[0-5][0-9])$/", $time);
    }

    private function _is_12_hour($time) {
        return preg_match("/^(1[012]|0[0-9]):([0-5][0-9]|[0-5][0-9]:[0-5][0-9]) (AM|PM)$/", $time);
    }

    private function _is_valid_date($date, $date_format) {
        $date = trim($date);
        $datetime = date_create_from_format($date_format, $date);
        if ($datetime == FALSE) {
            return FALSE;
        }
        return date($date_format, date_timestamp_get($datetime)) == $date;
    }

    private function _format_mysql_date($date, $time, $date_format, $time_format) {
        $is_valid_time = FALSE;
        if ($time_format == '12') {
            $is_valid_time = $this->_is_12_hour($time);
        } elseif ($time_format == '24') {
            $is_valid_time = $this->_is_24_hour($time);
        }
        if ($this->_is_valid_date($date, $date_format) && $is_valid_time) {
            $datetime = date_create_from_format($date_format, $date);
            return date('Y-m-d H:i:s', strtotime(date('Y-m-d', date_timestamp_get($datetime)) . ' ' . $time));
        }
    }

    public function ajax_datatable() {
        wp_send_json($this->_datatable_get_all());
        wp_die();
    }

    private function _datatable_get_all() {

        $params = array();


        $where = '';
//        if (isset($_POST['search'])) {
//            $_search = $_POST['search'];
//            if (trim($_search['value']) != '') {
//                $where = " WHERE ( link_name LIKE %s OR tracking_link LIKE %s )";
//                $params = array_merge($params, array('%%' . $_search['value'] . '%%', '%%' . $_search['value'] . '%%'));
//            }
//        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);

        $results = $this->wpdb->get_results($this->wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM {$this->tbl_import_temp} {$where} {$order} {$limit}", $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");
        $record = $this->wpdb->get_row("SELECT COUNT(id) AS total FROM $this->tbl_import_temp;");

        $data = array();
        foreach ($results as $row) {
            $messages = explode(PHP_EOL, $row->message);
            $message = '';
            foreach ($messages as $msg) {
                if ($msg == 'CTID Found') {
                    if (trim($msg) != '') {
                        $message .= '<small class="label label-success"><i class="fa fa-check-circle"></i> ' . $msg . '</small><br/>';
                    }
                } else {
                    if (trim($msg) != '') {
                        $message .= '<small class="label label-danger"><i class="fa fa-times-circle"></i> ' . $msg . '</small><br/>';
                    }
                }
            }

            $data[] = array(
                $row->conversion_date,
                $row->goal_name,
                $row->goal_type,
                $row->goal_value,
                $row->goal_reference,
                $row->visits_log_id,
                $message,
                ($row->status == 'Rejected') ? '<input class="cbForceSingle" ' . ($row->force_import == 1 ? 'checked="checked"' : '') . ' type="checkbox" name="cbAction[]" value="' . $row->id . '">' : '',
                $row->status,
            );
        }

        return array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $data
        );
    }

    /**
     * Import
     */
    public function import_display() {
        add_action('admin_footer', array($this, 'page_js'));
        if (isset($_POST['btnAdd'])) {
            $conversion_date = $this->_post('conversion_date');
            $goal_name = $this->_post('goal_name');
            $goal_type = $this->_post('goal_type');
            $goal_value = $this->_post('goal_value');
            $goal_reference = $this->_post('goal_reference');
            $click_id = $this->_post('click_id', 0);



            if (TRUE) {
                $status = STATUS_ACCEPTED;
                $message = '';

                $sql = "SELECT id,link_id FROM {$this->tbl_visits_log} WHERE id = %d";
                $visitor_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id)), OBJECT);
                if (isset($visitor_log->id)) {
                    $message .= 'CTID Found' . PHP_EOL;
                } else {
                    $status = STATUS_REJECTED;
                    $message .= 'CTID Not Found' . PHP_EOL;
                }


                if (get_option(SH_PREFIX . 'settings_duplicate_handling') == SHORTLY_DUPLICATE_HANDLING_IGNORE) {
                    if (trim($goal_reference) != '') {
                        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = %s";
                        $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($goal_reference)), OBJECT);
                        if ($conversion_log->total > 0) {
                            $status = STATUS_REJECTED;
                            $message .= 'Duplicate Goal Reference ID ' . $goal_reference . PHP_EOL;
                        }
                    }
                }
                $country = $this->ip_info();
                $this->wpdb->insert(
                        $this->tbl_import_temp, array(
                    'link_id' => isset($visitor_log->link_id) ? $visitor_log->link_id : 0,
                    'goal_id' => 0,
                    'visits_log_id' => isset($visitor_log->id) ? $visitor_log->id : 0,
                    'goal_name' => $goal_name,
                    'goal_value' => $goal_value,
                    'goal_type' => $goal_type,
                    'goal_reference' => $goal_reference,
                    'referrer_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'ip_country_code' => isset($country['country_code']) ? $country['country_code'] : '',
                    'ip_country_name' => isset($country['country']) ? $country['country'] : '',
                    'ip_city_name' => isset($country['city']) ? $country['city'] : '',
                    'ip_latitude' => isset($country['latitude']) ? $country['latitude'] : '',
                    'ip_longitude' => isset($country['longitude']) ? $country['longitude'] : '',
                    'user_agent_string' => $_SERVER['HTTP_USER_AGENT'],
                    'status' => $status,
                    'message' => $message,
                    'conversion_date' => $conversion_date,
                    'created_date' => current_time("Y-m-d H:i:s"),
                        )
                );

                $conversion_id = $this->wpdb->insert_id;
                wp_redirect('?page=sh_conversions_page&action=import&sub=import');
                exit();
            } else {
                $this->view_data['msg'] = array(
                    'status' => 'alert-danger',
                    'text' => SRTY_MSG_IMPORT_TOP_ERROR_MESSAGE
                );
            }
        }

        $this->view('v_import', $this->view_data);
    }

    public function import() {
        add_action('admin_footer', array($this, 'page_js'));
        $this->view('v_import-verification', array('data' => 'hello world'));
    }

    public function cancel_import() {
        $delete = $this->wpdb->query("TRUNCATE TABLE {$this->tbl_import_temp}");
        $this->view_data['msg'] = array(
            'status' => 'alert-success',
            'text' => SRTY_MSG_IMPORT_CANCELLED
        );
        $this->set_top_message($this->view_data['msg']);
        wp_redirect('?page=sh_conversions_page&action=import');
    }

    public function confirm_import() {
        $this->wpdb->query("INSERT INTO {$this->tbl_conversions_log} ("
                . "link_id,"
                . "goal_id,"
                . "visits_log_id,"
                . "goal_name,"
                . "goal_value,"
                . "goal_type,"
                . "goal_reference,"
                . "referrer_url,"
                . "ip_address,"
                . "ip_country_code,"
                . "ip_country_name,"
                . "ip_city_name,"
                . "ip_latitude,"
                . "ip_longitude,"
                . "user_agent_string,"
                . "status,"
                . "message,"
                . "conversion_date,"
                . "created_date"
                . ") SELECT "
                . "link_id,"
                . "goal_id,"
                . "visits_log_id,"
                . "goal_name,"
                . "goal_value,"
                . "goal_type,"
                . "goal_reference,"
                . "referrer_url,"
                . "ip_address,"
                . "ip_country_code,"
                . "ip_country_name,"
                . "ip_city_name,"
                . "ip_latitude,"
                . "ip_longitude,"
                . "user_agent_string,"
                . "'Accepted',"
                . "CONCAT('Manual Import\n', message),"
                . "conversion_date,"
                . "created_date "
                . "FROM {$this->tbl_import_temp} WHERE status='Accepted' OR force_import=1 ");

        /**
         * done. we clean up temp table
         */
        $delete = $this->wpdb->query("TRUNCATE TABLE {$this->tbl_import_temp}");

        $this->view_data['msg'] = array(
            'status' => 'alert-success',
            'text' => SRTY_MSG_IMPORT_IMPORTED
        );
        $this->set_top_message($this->view_data['msg']);
        wp_redirect('?page=sh_conversions_page&action=import');
    }

}

<?php

class Srty_reports extends Srty_core {

    public $tbl_links;
    public $tbl_import_temp;
    public $tbl_visits_log;
    public $tbl_campaigns;
    public $tbl_conversions_log;

    public function __construct() {
        parent::__construct();
        $this->view_data['date_selection'] = $this->date_selection();

        $this->tbl_import_temp = $this->wpdb->prefix . SH_PREFIX . 'import_temp';
        $this->tbl_links = $this->wpdb->prefix . SH_PREFIX . 'links';
        $this->tbl_visits_log = $this->wpdb->prefix . SH_PREFIX . 'visits_log';
        $this->tbl_campaigns = $this->wpdb->prefix . SH_PREFIX . 'campaigns';
        $this->tbl_conversions_log = $this->wpdb->prefix . SH_PREFIX . 'conversions_log';
        $this->view_data['currency'] = new Srty_currency();
    }

    public function init() {
        add_action('wp_ajax_srty_ajax_overview_report', array($this, 'srty_ajax_overview_report'));
        add_action('wp_ajax_srty_report_referrers_datatable', array($this, 'srty_report_referrers_datatable'));
        add_action('wp_ajax_srty_report_countries_datatable', array($this, 'srty_report_countries_datatable'));
        add_action('wp_ajax_srty_report_links_datatable', array($this, 'srty_report_links_datatable'));
        add_action('wp_ajax_srty_report_visitors_datatable', array($this, 'srty_report_visitors_datatable'));
        add_action('wp_ajax_srty_report_source_datatable', array($this, 'srty_report_source_datatable'));
        add_action('wp_ajax_srty_report_medium_datatable', array($this, 'srty_report_medium_datatable'));
        add_action('wp_ajax_srty_report_campaign_datatable', array($this, 'srty_report_campaign_datatable'));
        add_action('wp_ajax_srty_report_content_datatable', array($this, 'srty_report_content_datatable'));
        add_action('wp_ajax_srty_report_term_datatable', array($this, 'srty_report_term_datatable'));
//        add_action('wp_ajax_srty_report_conversions_datatable', array($this, 'srty_report_conversions_datatable'));
//        add_action('wp_ajax_srty_cbforceall', array($this, 'srty_cbforceall'));

        add_action('wp_ajax_srty_delete', array($this, 'ajax_delete'));
//        add_action('wp_ajax_srty_conversion_delete', array($this, 'ajax_conversion_delete'));
//        add_action('wp_ajax_srty_imports_datatable', array($this, 'ajax_datatable'));
//        add_action('wp_custom_route', array($this, 'custom_route'));
    }

    public function display() {
        $this->view('v_reports', $this->view_data);
    }

    public function routes() {
        if (isset($_GET['action']) && $_GET['action'] == 'report-links') {
            $this->_links();
            add_action('admin_footer', array($this, 'links_report_js'));
            $this->view('v_report-links', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-referrers') {
            $this->_referrers();
            add_action('admin_footer', array($this, 'referrers_report_js'));
            $this->view('v_report-referrers', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-visitors') {
            $this->_visitors();
            add_action('admin_footer', array($this, 'visitors_report_js'));
            $this->view('v_report-visitors', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-countries') {
            $this->_countries();
            add_action('admin_footer', array($this, 'countries_report_js'));
            $this->view('v_report-countries', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-conversions') {
            $this->_conversions();
            add_action('admin_footer', array($this, 'conversions_report_js'));
            $this->view('v_report-conversions', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-conversions-details') {
            $this->_conversion_details();
            $this->view('v_report-conversion-details', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-source') {
            $this->_source();
            add_action('admin_footer', array($this, 'source_report_js'));
            $this->view('v_report-source', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-medium' && isset($_GET['source']) && $_GET['source'] != '') {
            $this->_medium();
            add_action('admin_footer', array($this, 'medium_report_js'));
            $this->view('v_report-medium', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-campaign' && isset($_GET['source']) && isset($_GET['medium'])) {
            $this->_campaign();
            add_action('admin_footer', array($this, 'campaign_report_js'));
            $this->view('v_report-campaigns', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-content' && isset($_GET['source']) && isset($_GET['medium']) && isset($_GET['campaign'])) {
            $this->_content();
            add_action('admin_footer', array($this, 'content_report_js'));
            $this->view('v_report-content', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'report-term' && isset($_GET['source']) && isset($_GET['medium']) && isset($_GET['campaign']) && isset($_GET['content'])) {
            $this->_term();
            add_action('admin_footer', array($this, 'term_report_js'));
            $this->view('v_report-term', $this->view_data);
        } else if (isset($_GET['action']) && $_GET['action'] == 'import') {
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
        } else {
            add_action('admin_footer', array($this, 'overview_report_js'));
            $this->display();
        }
    }

    /**
     * Overview Report
     */
    public function overview_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var obj = jQuery(this);
                jQuery.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    type: "POST",
                    data: {"action": 'srty_ajax_overview_report'},
                }).success(function (json) {
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([item.date_label * 1000, parseInt(item.visits)]);
                            series2.push([item.date_label * 1000, parseInt(item.cv)]);
                        });
                    }

                    report_chart('#chart', series1, series2, 230);

                    obj.find('#summary_total_visits').html(json.summary.total_visits);
                    obj.find('#summary_total_visitors').html(json.summary.total_visitors);
                    obj.find('#summary_conversions').html(json.summary.conversions);
                    obj.find('#summary_cost').html(json.summary.cost);
                    obj.find('#summary_conversions_percent').html(json.summary.convpercent);
                    obj.find('#summary_revenue').html(json.summary.revenue);
                    obj.find('#summary_rpv').html(json.summary.rpv);
                    obj.find('#summary_cpa').html(json.summary.cpa);
                    obj.find('#summary_cpc').html(json.summary.cpc);
                    obj.find('#summary_profit').html(json.summary.profit);
                    obj.find('#summary_roi').html(json.summary.roi);
                    obj.find('#summary_cost_per_day').html(json.summary.cpd);

                    if (json.summary.profit.match(/^-/)) {
                        obj.find('#summary_profit').addClass('text-danger');
                    }
                    if (json.summary.roi.match(/^-/)) {
                        obj.find('#summary_roi').addClass('text-danger');
                    }
                });
            });
        </script>
        <?php
    }

    public function srty_ajax_overview_report() {
        /**
         * process summary
         */
        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency_symbol = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        /**
         * summaries section
         */
        $sql = "SELECT
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit,
                    IFNULL(((SUM(revenue)-SUM(cost))/IF(SUM(cost)>0,SUM(cost),1))*100,0) AS roi
                FROM (
                    SELECT 
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                    GROUP BY a.source

                    UNION

                    SELECT
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = %s
                 ) AS t";
        $params = array($date_from, $date_to, $date_from, $date_to, STATUS_ACCEPTED);
        $summary = $this->wpdb->get_row($this->wpdb->prepare($sql, $params));
        $this->wpdb->flush();
        $output = array(
            'summary' => array(
                'total_visits' => 0,
                'conversions' => 0,
                'convpercent' => 0,
                'revenue' => $currency_symbol . '0.00',
                'cost' => $currency_symbol . '0.00',
                'cpa' => $currency_symbol . '0.00',
                'cpc' => $currency_symbol . '0.00',
                'rpv' => $currency_symbol . '0.00',
                'profit' => $currency_symbol . '0.00',
                'roi' => $currency_symbol . '0.00',
                'cpd' => 0,
            ),
            'chart_data' => array()
        );

        $output['summary']['total_visits'] = number_format($summary->visits);
        $output['summary']['total_visitors'] = number_format($summary->visitor);
        $output['summary']['conversions'] = number_format($summary->conversion);
        $output['summary']['convpercent'] = number_format($summary->conversion_rate, 2) . "%";
        $output['summary']['revenue'] = $currency_symbol . number_format($summary->revenue, 2);
        $output['summary']['cost'] = $currency_symbol . number_format($summary->cost, 2);
        $output['summary']['cpa'] = $currency_symbol . number_format($summary->cpa, 2);
        $output['summary']['cpc'] = $currency_symbol . number_format($summary->cpc, 2);
        $output['summary']['rpv'] = $currency_symbol . number_format($summary->rpv, 2);
        $output['summary']['profit'] = $currency_symbol . number_format($summary->profit, 2);
        $output['summary']['roi'] = number_format($summary->roi, 2) . '%';
        $output['summary']['cpd'] = $currency_symbol . number_format(($summary->cost / $difference), 2);
        /**
         * EO: summary section
         */
        /**
         * chart
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00')

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted'    
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00')
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }
        $output['chart_data'] = $pre_populate;
        /**
         * EO: chart
         */
        wp_send_json($output);
        wp_die();
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
                download_excel('.download-excel', '?page=sh_reports_page&action=report-conversions');

            });
        </script>
        <?php
    }

//    public function srty_report_conversions_datatable() {
//        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
//        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];
//
//        $params = array();
//        $where = ' WHERE conversion_date BETWEEN %s AND %s ';
//        $params = array($date_from, $date_to);
//        if (isset($_POST['search'])) {
//            $_search = $_POST['search'];
//            if (trim($_search['value']) != '') {
//                $where .= " AND ( goal_name LIKE %s OR goal_type LIKE %s OR goal_value LIKE %s OR goal_reference LIKE %s OR status LIKE %s )";
//                $params = array_merge($params, array(
//                    '%%' . $_search['value'] . '%%',
//                    '%%' . $_search['value'] . '%%',
//                    '%%' . $_search['value'] . '%%',
//                    '%%' . $_search['value'] . '%%',
//                    '%%' . $_search['value'] . '%%'
//                ));
//            }
//        }
//
//        $order = $this->_order($params);
//        $limit = $this->_limit($params);
//
//        $results = $this->wpdb->get_results($this->wpdb->prepare("SELECT SQL_CALC_FOUND_ROWS conversion_date,goal_name,goal_type,goal_value,goal_reference,status,id FROM {$this->tbl_conversions_log} {$where} {$order} {$limit}", $params));
//        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");
//        $record = $this->wpdb->get_row("SELECT COUNT(id) AS total FROM $this->tbl_conversions_log;");
//
//        $data = array();
//        foreach ($results as $row) {
//            $data[] = array(
//                '<input type="checkbox" name="cbAction[]" value="' . $row->id . '">',
//                $row->conversion_date,
//                $row->goal_name,
//                $row->goal_type,
//                $row->goal_value,
//                $row->goal_reference,
//                ($row->status == 'Accepted') ? '<span class="label label-success">' . $row->status . '</span>' : '<span class="label label-danger">' . $row->status . '</span>',
//                '<a href="?page=sh_reports_page&action=report-conversions-details&id=' . $row->id . '" class="btn btn-default btn-xs">view &amp; edit</a>',
//            );
//        }
//
//        wp_send_json(array(
//            "draw" => intval($_POST['draw']),
//            "recordsTotal" => intval($found->total),
//            "recordsFiltered" => intval($found->total),
//            "data" => $data
//        ));
//
//        wp_die();
//    }
//
//    public function srty_cbforceall() {
//        if ($id = $this->_post('id', FALSE)) {
//            $this->wpdb->update(
//                    $this->tbl_import_temp, array(
//                'force_import' => $this->_post('status') == 'true' ? 1 : 0,
//                    ), array(
//                'status' => 'Rejected',
//                'id' => $id
//                    ), array('%d'), array('%s', '%d')
//            );
//        } else {
//            $this->wpdb->update(
//                    $this->tbl_import_temp, array(
//                'force_import' => $this->_post('status') == 'true' ? 1 : 0,
//                    ), array(
//                'status' => 'Rejected'
//                    ), array(
//                '%d',
//                    ), array('%s')
//            );
//        }
//    }

    private function _conversion_details() {
        $id = $this->_get('id', FALSE);
        if ($id === FALSE) {
            $this->view_data['msg'] = array(
                'status' => 'alert-danger',
                'text' => SRTY_MSG_GENERAL_RECORD_NOT_FOUND
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=sh_reports_page&action=report-conversions');
            exit();
        }

        if ($this->_post('btnDelete', FALSE) !== FALSE) {
            $this->wpdb->delete($this->tbl_conversions_log, array('id' => $id), array('%d'));
            $this->view_data['msg'] = array(
                'status' => 'alert-success',
                'text' => SRTY_MSG_REPORT_GOAL_DELETED
            );
            $this->set_top_message($this->view_data['msg']);
            wp_redirect('?page=sh_reports_page&action=report-conversions');
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
            wp_redirect('?page=sh_reports_page&action=report-conversions');
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
            wp_redirect('?page=sh_reports_page&action=report-conversions');
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

    /**
     * Referrers Report
     */
    public function referrers_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var oTable = jQuery('#report_links').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_referrers_datatable'
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');

                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-referrers');

            });
        </script>
        <?php
    }

    /**
     * Countries Report
     */
    public function countries_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var oTable = jQuery('#report_links').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_countries_datatable'
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');

                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-countries');

            });
        </script>
        <?php
    }

    /**
     * Links Report
     */
    public function links_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var oTable = jQuery('#report_links').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_links_datatable'
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');

                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');

                download_excel('.download-excel', '?page=sh_reports_page&action=report-links');

            });
        </script>
        <?php
    }

    public function visitors_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var oTable = jQuery('#report_visitors').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_visitors_datatable'
                            });
                        },
                    },
                    "order": [[0, "desc"]],
                });

                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');

                download_excel('.download-excel', '?page=sh_reports_page&action=report-visitors');

            });
        </script>
        <?php
    }

    public function srty_report_referrers_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    IF(referrer_url!='',referrer_url,'[ Direct visit ]') AS referrer_url,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.referrer_url AS referrer_url,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s  ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.referrer_url LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY CRC32(a.referrer_url)

                    UNION

                    SELECT
                        b.referrer_url AS referrer_url,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted' ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.referrer_url LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY CRC32(b.referrer_url)
                ) AS t 
                GROUP BY CRC32(referrer_url) ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                (trim($row->referrer_url) != '' && trim($row->referrer_url) != '[ Direct visit ]') ? '<a href="' . $row->referrer_url . '">' . $this->ellipsize($row->referrer_url, 30) . '</a>' : $row->referrer_url,
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.referrer_url)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.referrer_url LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.referrer_url)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.referrer_url)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.referrer_url LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'),CRC32(b.referrer_url)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
//        echo $this->wpdb->last_query;
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    public function srty_report_countries_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    IF(ip_country_name!='',ip_country_name,'[ Unknown ]') AS ip_country_name,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.ip_country_name AS ip_country_name,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s  ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.ip_country_name LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY CRC32(a.ip_country_name)

                    UNION

                    SELECT
                        b.ip_country_name AS ip_country_name,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted' ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.ip_country_name LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY CRC32(b.ip_country_name)
                ) AS t 
                GROUP BY CRC32(ip_country_name) ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                $row->ip_country_name,
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.ip_country_name)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.ip_country_name LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.ip_country_name)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.ip_country_name)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.ip_country_name LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'),CRC32(b.ip_country_name)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
//        echo $this->wpdb->last_query;
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    public function srty_report_links_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    tracking_link,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.tracking_link AS tracking_link,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.tracking_link IS NOT NULL ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.tracking_link LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY a.tracking_link

                    UNION

                    SELECT
                        b.tracking_link AS tracking_link,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted'
                        AND b.tracking_link IS NOT NULL ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.tracking_link LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY b.tracking_link
                ) AS t 
                GROUP BY tracking_link ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                $row->tracking_link,
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.tracking_link)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.tracking_link IS NOT NULL ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.tracking_link LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.tracking_link)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.tracking_link)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.tracking_link IS NOT NULL ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.tracking_link LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.tracking_link)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    public function srty_report_visitors_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "
                    SELECT 
                        SQL_CALC_FOUND_ROWS
                        id,ip_address,ip_country_name,ip_city_name,referrer_url,tracking_link,destination_url,
                        source,medium,campaign,content,term,cpc,
                        created_date
                    FROM {$this->tbl_visits_log} 
                    WHERE 
                        created_date BETWEEN %s AND %s
                        AND tracking_link IS NOT NULL ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ("
                        . " ip_address LIKE %s "
                        . " OR ip_country_name LIKE %s "
                        . " OR ip_city_name LIKE %s "
                        . " OR referrer_url LIKE %s "
                        . " OR tracking_link LIKE %s "
                        . " OR destination_url LIKE %s "
                        . ")";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }


        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'];
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $campaign = '';
            if (trim($row->source) != '') {
                $campaign .= '&source=' . $row->source;
            }
            if (trim($row->medium) != '') {
                $campaign .= '&medium=' . $row->medium;
            }
            if (trim($row->campaign) != '') {
                $campaign .= '&campaign=' . $row->campaign;
            }
            if (trim($row->content) != '') {
                $campaign .= '&content=' . $row->content;
            }
            if (trim($row->term) != '') {
                $campaign .= '&term=' . $row->term;
            }
            if (trim($row->cpc) != '') {
                $campaign .= '&cpc=' . $row->cpc;
            }
            $campaign = preg_replace("/^&/", "?", $campaign);
            $table_data[] = array(
                $row->id,
                $row->ip_address,
                $row->ip_country_name,
                $row->ip_city_name,
                (trim($row->referrer_url) != '') ? '<a href="' . $row->referrer_url . '">' . $this->ellipsize($row->referrer_url, 30) . '</a>' : '',
                '/' . $row->tracking_link,
                (trim($row->destination_url) != '') ? '<a href="' . $row->destination_url . '">' . $this->ellipsize($row->destination_url, 30) . '</a>' : '',
                $campaign,
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.tracking_link)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.tracking_link IS NOT NULL ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.tracking_link LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.tracking_link)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.tracking_link)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.tracking_link IS NOT NULL ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.tracking_link LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.tracking_link)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    private function _referrers() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];


            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    IF(referrers!='',referrers,'[ Direct Visit ]') AS referrers,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.referrer_url AS referrers,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.referrer_url LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.referrer_url

                    UNION

                    SELECT
                        b.referrer_url AS referrers,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.referrer_url LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.referrer_url
                ) AS t 
                GROUP BY referrers ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'tracking_referrers' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->referrers,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    private function _countries() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];


            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    IF(countries!='',countries,'[ Unknown ]') AS countries,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.ip_country_name AS countries,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.ip_country_name LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.ip_country_name

                    UNION

                    SELECT
                        b.ip_country_name AS countries,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.ip_country_name LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.ip_country_name
                ) AS t 
                GROUP BY countries ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'tracking_referrers' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->countries,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    private function _links() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];


            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    tracking_link,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.tracking_link AS tracking_link,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.tracking_link IS NOT NULL ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.tracking_link LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.tracking_link

                    UNION

                    SELECT
                        b.tracking_link AS tracking_link,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND b.tracking_link IS NOT NULL ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.tracking_link LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.tracking_link
                ) AS t 
                GROUP BY tracking_link ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'tracking_links' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->tracking_link,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    private function _visitors() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];


            $params = array();
            $where = '';

            $sql = "
                    SELECT 
                        id,ip_address,ip_country_name,ip_city_name,referrer_url,tracking_link,destination_url,
                        source,medium,campaign,content,term,cpc,
                        created_date
                    FROM {$this->tbl_visits_log} 
                    WHERE 
                        created_date BETWEEN %s AND %s
                        AND tracking_link IS NOT NULL ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_POST['search'])) {
                $_search = $_POST['search'];
                if (trim($_search['value']) != '') {
//                $sql .= " AND ( a.tracking_link LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'visitors_report' . current_time("YmdHis") . '.csv';
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

                $campaign = '';
                if (trim($row->source) != '') {
                    $campaign .= '&source=' . $row->source;
                }
                if (trim($row->medium) != '') {
                    $campaign .= '&medium=' . $row->medium;
                }
                if (trim($row->campaign) != '') {
                    $campaign .= '&campaign=' . $row->campaign;
                }
                if (trim($row->content) != '') {
                    $campaign .= '&content=' . $row->content;
                }
                if (trim($row->term) != '') {
                    $campaign .= '&term=' . $row->term;
                }
                if (trim($row->cpc) != '') {
                    $campaign .= '&cpc=' . $row->cpc;
                }
                $campaign = preg_replace("/^&/", "?", $campaign);

                $leadArray = array(
                    $row->id,
                    $row->ip_address,
                    $row->ip_country_name,
                    $row->ip_city_name,
                    (trim($row->referrer_url) != '') ? '<a href="' . $row->referrer_url . '">' . $this->ellipsize($row->referrer_url, 30) . '</a>' : '',
                    '/' . $row->tracking_link,
                    (trim($row->destination_url) != '') ? '<a href="' . $row->destination_url . '">' . $this->ellipsize($row->destination_url, 30) . '</a>' : '',
                    $campaign,
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    /**
     * Source Report
     */
    public function source_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var oTable = jQuery('#report_source').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_source_datatable'
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');

                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-source');

            });
        </script>
        <?php
    }

    public function srty_report_source_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_source,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.source AS cpg_source,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.source IS NOT NULL ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.source LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY a.source

                    UNION

                    SELECT
                        b.source AS cpg_source,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted'
                        AND b.source IS NOT NULL ";
        $params = array_merge($params, array($date_from, $date_to));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.source LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY b.source
                ) AS t 
                GROUP BY cpg_source ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                '<a href=' . str_replace(' ', '+', '?page=sh_reports_page&action=report-medium&source=' . $row->cpg_source) . '>' . $row->cpg_source . '</a>',
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.source)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.source IS NOT NULL ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.source LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.source)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.source)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.source IS NOT NULL ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.source LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.source)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    private function _source() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];


            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_source,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.source AS cpg_source,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.source IS NOT NULL ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.source LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.source

                    UNION

                    SELECT
                        b.source AS cpg_source,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND b.source IS NOT NULL ";
            $params = array_merge($params, array($date_from, $date_to));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.source LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.source
                ) AS t 
                GROUP BY cpg_source ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'source' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->cpg_source,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    /**
     * Medium Report
     */
    public function medium_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var source = '<?php echo $_GET['source']; ?>';
                var oTable = jQuery('#report_medium').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_medium_datatable',
                                "source": "<?php echo $this->_get('source'); ?>"
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('#breadcrumb').html('<div class="" style="padding:10px;"><ul class="breadcrumb"><li class="active"><a href="?page=sh_reports_page&action=report-source">' + source + '</a></li></ul></div>');
                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-medium&source=' + source);
            });
        </script>
        <?php
    }

    public function srty_report_medium_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $source = $this->_post('source');
//        echo $source;

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_medium,
                    cpg_source,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.medium AS cpg_medium,
                        a.source AS cpg_source,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.medium IS NOT NULL
                        AND a.source LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.medium LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY a.medium

                    UNION

                    SELECT
                        b.medium AS cpg_medium,
                        b.source AS cpg_source,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted'
                        AND b.medium IS NOT NULL
                        AND b.source LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.medium LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY b.medium
                ) AS t 
                GROUP BY cpg_medium ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                '<a href=' . str_replace(' ', '+', '?page=sh_reports_page&action=report-campaign&source=' . $row->cpg_source . '&medium=' . $row->cpg_medium) . '>' . $row->cpg_medium . '</a>',
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.medium)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.medium IS NOT NULL
                            AND a.source LIKE '{$source}'";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.medium LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.medium)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.medium)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.medium IS NOT NULL
                            AND b.source LIKE '{$source}' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.medium LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.medium)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    private function _medium() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

            $source = $_GET['source'];

            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_medium,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.medium AS cpg_medium,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.medium IS NOT NULL AND a.source LIKE %s";
            $params = array_merge($params, array($date_from, $date_to, $source));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.medium LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.medium

                    UNION

                    SELECT
                        b.medium AS cpg_medium,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND b.medium IS NOT NULL AND b.source LIKE %s ";
            $params = array_merge($params, array($date_from, $date_to, $source));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.medium LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.medium
                ) AS t 
                GROUP BY cpg_medium ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'medium' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->cpg_medium,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    /**
     * Campaign Report
     */
    public function campaign_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var source = '<?php echo $_GET['source']; ?>';
                var medium = '<?php echo $_GET['medium']; ?>';
                var oTable = jQuery('#report_campaign').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_campaign_datatable',
                                "source": "<?php echo $this->_get('source'); ?>",
                                "medium": "<?php echo $this->_get('medium'); ?>"
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('#breadcrumb').html('<div class="" style="padding:10px;"><ul class="breadcrumb"><li class="active"><a href="?page=sh_reports_page&action=report-source">' + source + '</a> &gt; <a href="?page=sh_reports_page&action=report-medium&source=' + source + '">' + medium + '</a></li></ul></div>');
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');


                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-campaign&source=' + source + '&medium=' + medium);

            });
        </script>
        <?php
    }

    public function srty_report_campaign_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $source = $this->_post('source');
        $medium = $this->_post('medium');
//        echo $source;

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_campaign,
                    cpg_medium,
                    cpg_source,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.campaign AS cpg_campaign,
                        a.medium AS cpg_medium,
                        a.source AS cpg_source,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.campaign IS NOT NULL
                        AND a.source LIKE %s 
                        AND a.medium LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source, $medium));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.campaign LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY a.campaign

                    UNION

                    SELECT
                        b.campaign AS cpg_campaign,
                        b.medium AS cpg_medium,
                        b.source AS cpg_source,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted'
                        AND b.campaign IS NOT NULL
                        AND b.source LIKE %s 
                        AND b.medium LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source, $medium));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.campaign LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY b.campaign
                ) AS t 
                GROUP BY cpg_campaign ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                '<a href=' . str_replace(' ', '+', '?page=sh_reports_page&action=report-content&source=' . $row->cpg_source . '&medium=' . $row->cpg_medium . '&campaign=' . $row->cpg_campaign) . '>' . $row->cpg_campaign . '</a>',
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.campaign)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.campaign IS NOT NULL
                            AND a.source LIKE '{$source}'
                            AND a.medium LIKE '{$medium}'";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.campaign LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.campaign)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.campaign)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.campaign IS NOT NULL
                            AND b.source LIKE '{$source}'
                            AND b.medium LIKE '{$medium}' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.campaign LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.campaign)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    private function _campaign() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

            $medium = $_GET['medium'];
            $source = $_GET['source'];

            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_campaign,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.campaign AS cpg_campaign,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.campaign IS NOT NULL
                        AND a.medium LIKE %s
                        AND a.source LIKE %s ";
            $params = array_merge($params, array($date_from, $date_to, $medium, $source));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.campaign LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.campaign

                    UNION

                    SELECT
                        b.campaign AS cpg_campaign,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND b.campaign IS NOT NULL
                        AND b.medium LIKE %s
                        AND b.source LIKE %s";
            $params = array_merge($params, array($date_from, $date_to, $medium, $source));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.campaign LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.campaign
                ) AS t 
                GROUP BY cpg_campaign ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'campaign' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->cpg_campaign,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    /**
     * Content Report
     */
    public function content_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var source = '<?php echo $_GET['source']; ?>';
                var medium = '<?php echo $_GET['medium']; ?>';
                var campaign = '<?php echo $_GET['campaign']; ?>';
                var oTable = jQuery('#report_content').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_content_datatable',
                                "source": "<?php echo $this->_get('source'); ?>",
                                "medium": "<?php echo $this->_get('medium'); ?>",
                                "campaign": "<?php echo $this->_get('campaign'); ?>"
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-content&source=' + source + '&medium=' + medium + '&campaign=' + campaign);
            });
        </script>
        <?php
    }

    public function srty_report_content_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $source = $this->_post('source');
        $medium = $this->_post('medium');
        $campaign = $this->_post('campaign');
//        echo $source;

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_content,
                    cpg_campaign,
                    cpg_medium,
                    cpg_source,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.content AS cpg_content,
                        a.campaign AS cpg_campaign,
                        a.medium AS cpg_medium,
                        a.source AS cpg_source,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.content IS NOT NULL
                        AND a.source LIKE %s 
                        AND a.medium LIKE %s
                        AND a.campaign LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source, $medium, $campaign));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.content LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY a.content

                    UNION

                    SELECT
                        b.content AS cpg_content,
                        b.campaign AS cpg_campaign,
                        b.medium AS cpg_medium,
                        b.source AS cpg_source,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted'
                        AND b.content IS NOT NULL
                        AND b.source LIKE %s 
                        AND b.medium LIKE %s
                        AND b.campaign LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source, $medium, $campaign));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.content LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY b.content
                ) AS t 
                GROUP BY cpg_content ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                '<a href=' . str_replace(' ', '+', '?page=sh_reports_page&action=report-term&source=' . $row->cpg_source . '&medium=' . $row->cpg_medium . '&campaign=' . $row->cpg_campaign . '&content=' . $row->cpg_content) . '>' . $row->cpg_content . '</a>',
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.content)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.content IS NOT NULL
                            AND a.source LIKE '{$source}'
                            AND a.medium LIKE '{$medium}'
                            AND a.campaign LIKE '{$campaign}'";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.content LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.content)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.content)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.content IS NOT NULL
                            AND b.source LIKE '{$source}'
                            AND b.medium LIKE '{$medium}'
                            AND b.campaign LIKE '{$campaign}' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.content LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.content)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    private function _content() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

            $medium = $_GET['medium'];
            $source = $_GET['source'];
            $campaign = $_GET['campaign'];

            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_content,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.content AS cpg_content,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.content IS NOT NULL
                        AND a.medium LIKE %s
                        AND a.source LIKE %s
                        AND a.campaign LIKE %s ";
            $params = array_merge($params, array($date_from, $date_to, $medium, $source, $campaign));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.content LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.content

                    UNION

                    SELECT
                        b.content AS cpg_content,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND b.content IS NOT NULL
                        AND b.medium LIKE %s
                        AND b.source LIKE %s
                        AND b.campaign LIKE %s";
            $params = array_merge($params, array($date_from, $date_to, $medium, $source, $campaign));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.content LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.content
                ) AS t 
                GROUP BY cpg_content ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'content' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->cpg_content,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    /**
     * Term Report
     */
    public function term_report_js() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var currency = '<?php echo $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency')); ?>';
                var source = '<?php echo $_GET['source']; ?>';
                var medium = '<?php echo $_GET['medium']; ?>';
                var campaign = '<?php echo $_GET['campaign']; ?>';
                var content = '<?php echo $_GET['content']; ?>';
                var oTable = jQuery('#report_term').DataTable({
                    "dom": '<"report panel panel-default"<"panel-heading"<"form-inline clearfix"<"pull-left"f><"pull-right"<"form-group text-muted"<"form-control-static"i>><"form-group"l>>>><"stats"<"#chart">><"table-responsive"t><"panel-footer clearfix"<"pull-left btndownload"><"pull-right"p>>>',
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
                                "action": 'srty_report_term_datatable',
                                "source": "<?php echo $this->_get('source'); ?>",
                                "medium": "<?php echo $this->_get('medium'); ?>",
                                "campaign": "<?php echo $this->_get('campaign'); ?>",
                                "content": "<?php echo $this->_get('content'); ?>"
                            });
                        },
                    },
                    "order": [[1, "desc"]],
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                        var intVal = function (i) {
                            return typeof i === 'string' ?
                                    //only get number
                                    i.replace(/[^\d\.-]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                        };
                        jQuery(api.column(1).footer()).html(0);
                        jQuery(api.column(2).footer()).html(0);
                        jQuery(api.column(3).footer()).html(0);
                        jQuery(api.column(4).footer()).html(0);
                        jQuery(api.column(5).footer()).html(0);
                        jQuery(api.column(6).footer()).html(0);
                        jQuery(api.column(7).footer()).html(0);
                        jQuery(api.column(8).footer()).html(0);
                        jQuery(api.column(9).footer()).html(0);
                        jQuery(api.column(10).footer()).html(0);
                        if (data.length > 0) {
                            jQuery(api.column(1).footer()).html(
                                    api.column(1).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(2).footer()).html(
                                    api.column(2).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(3).footer()).html(
                                    api.column(3).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(4).footer()).html(
                                    api.column(4).data().reduce(function (a, b) {
                                return number_format(intVal(a) + intVal(b));
                            }));

                            jQuery(api.column(5).footer()).html(
                                    api.column(5).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(6).footer()).html(
                                    api.column(6).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(7).footer()).html(
                                    api.column(7).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(8).footer()).html(
                                    api.column(8).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                            jQuery(api.column(9).footer()).html(
                                    api.column(9).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));
                            jQuery(api.column(10).footer()).html(
                                    api.column(10).data().reduce(function (a, b) {
                                return currency + number_format(intVal(a) + intVal(b), 2);
                            }));

                        }
                    },
                });
                oTable.on('xhr', function () {
                    var json = oTable.ajax.json();
                    var series1 = [];
                    var series2 = [];
                    if (json.hasOwnProperty('chart_data')) {
                        jQuery.each(json.chart_data, function (i, item) {
                            series1.push([(item.date_label * 1000), parseInt(item.visits)]);
                            series2.push([(item.date_label * 1000), parseInt(item.cv)]);
                        });
                    }
                    report_chart('#chart', series1, series2);
                });
                jQuery('.btndownload').append('<button type="button" class="btn btn-default download-excel"><i class="fa fa-calendar"></i> Download in CSV</button>');
                jQuery('#chart').attr('style', 'height: 270px;padding:20px 0 20px 0;');
                download_excel('.download-excel', '?page=sh_reports_page&action=report-term&source=' + source + '&medium=' + medium + '&campaign=' + campaign + '&content=' + content);
            });
        </script>
        <?php
    }

    public function srty_report_term_datatable() {

        $dtselect = $this->view_data['date_selection']['date_selection']['dtselect'];
        $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
        $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

        $source = $this->_post('source');
        $medium = $this->_post('medium');
        $campaign = $this->_post('campaign');
        $content = $this->_post('content');
//        echo $source;

        $start_day = date_create($date_from);
        $last_day = date_create($date_to);
        $days = date_diff($start_day, $last_day);
        $difference = $days->format('%a');
        $difference = $difference > 0 ? $difference : 1;

        $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));

        $params = array();
        $where = '';

        $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_term,
                    cpg_content,
                    cpg_campaign,
                    cpg_medium,
                    cpg_source,
                    SUM(visitor) AS visitor,
                    SUM(visits) AS visits,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.term AS cpg_term,
                        a.content AS cpg_content,
                        a.campaign AS cpg_campaign,
                        a.medium AS cpg_medium,
                        a.source AS cpg_source,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.term IS NOT NULL
                        AND a.source LIKE %s 
                        AND a.medium LIKE %s
                        AND a.campaign LIKE %s
                        AND a.content LIKE %s";
        $params = array_merge($params, array($date_from, $date_to, $source, $medium, $campaign, $content));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.term LIKE %s )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .=" GROUP BY a.term

                    UNION

                    SELECT
                        b.term AS cpg_term,
                        b.content AS cpg_content,
                        b.campaign AS cpg_campaign,
                        b.medium AS cpg_medium,
                        b.source AS cpg_source,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND a.status = 'Accepted'
                        AND b.term IS NOT NULL
                        AND b.source LIKE %s 
                        AND b.medium LIKE %s
                        AND b.campaign LIKE %s 
                        AND b.content LIKE %s ";
        $params = array_merge($params, array($date_from, $date_to, $source, $medium, $campaign, $content));
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.term LIKE %s  )";
                $params = array_merge($params, array(
                    '%%' . $_search['value'] . '%%',
                ));
            }
        }

        $sql .="
                    GROUP BY b.term
                ) AS t 
                GROUP BY cpg_term ";

        if (isset($_POST['order'])) {
            $_order = $_POST['order'];
            $_order[0]['column'] = $_order[0]['column'] + 1;
            $_POST['order'] = $_order;
        }

        $order = $this->_order($params);
        $limit = $this->_limit($params);
        $sql .= " {$order} {$limit}";
        $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
        $found = $this->wpdb->get_row("SELECT FOUND_ROWS() AS total;");

        $table_data = array();
        foreach ($results as $row) {
            $table_data[] = array(
                $row->cpg_term,
                $row->visits,
                $row->visitor,
                $row->conversion,
                number_format($row->conversion_rate, 2),
                $currency . number_format($row->cost, 2),
                $currency . number_format($row->cpa, 2),
                $currency . number_format($row->cpc, 2),
                $currency . number_format($row->revenue, 2),
                $currency . number_format($row->rpv, 2),
                $currency . number_format($row->profit, 2),
            );
        }

        /**
         * CHART DATA
         */
        $pre_populate = array();
        $date_label = '';
        switch ($dtselect) {
            case 'last24hours':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 23; $i >= 0; $i--) {
                    $timestamp = strtotime(date('Y-m-d H:00', strtotime(current_time("Y-m-d H:i:s") . " -{$i} hours")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'today':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'yesterday':
                $group_date = ' CRC32(TIME(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                for ($i = 0; $i <= 23; $i++) {
                    $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-d $h:00", strtotime(current_time("Y-m-d") . " -1 day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thismonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = current_time('t');
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'lastmonth':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                $last_day = date('t', strtotime(current_time("Y-m-d") . " -1 month"));
                for ($i = 1; $i <= $last_day; $i++) {
                    $d = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-m-{$d}", strtotime(current_time("Y-m-d") . " -1 month")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'last7days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 6; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'thisyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';


                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d"))));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'lastyear':
                $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m" ))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-01" ) AS date_label';

                for ($i = 1; $i <= 12; $i++) {
                    $m = str_pad($i, 2, '0', STR_PAD_LEFT);
                    $timestamp = strtotime(date("Y-{$m}-01", strtotime(current_time("Y-m-d") . " -1 year")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }

                break;
            case 'last30days':
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
            case 'custom':
                if (date("Y-m-d", strtotime($date_from)) == date("Y-m-d", strtotime($date_to))) {
                    $group_date = ' CRC32(DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d %H:00" ) AS date_label';

                    for ($i = 0; $i <= 23; $i++) {
                        $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                        $timestamp = strtotime(date("Y-m-d $h:00", strtotime($date_from)));
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                } else {
                    $group_date = ' CRC32(DATE(display_date))';
                    $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d") AS date_label';

                    $last_day = strtotime($date_to) - strtotime($date_from);
                    $last_day = floor($last_day / (60 * 60 * 24));
                    for ($i = 0; $i <= $last_day; $i++) {
                        $timestamp = strtotime(date("Y-m-d", strtotime($date_from)) . " +{$i} days");
                        $data = new stdClass();
                        $data->date_label = $timestamp;
                        $data->visits = 0;
                        $data->cv = 0;
                        $pre_populate[$timestamp] = $data;
                    }
                }
                break;
            default:
                $group_date = ' CRC32(DATE(display_date))';
                $date_label = ' DATE_FORMAT(display_date,"%Y-%m-%d" ) AS date_label';

                for ($i = 29; $i >= 0; $i--) {
                    $timestamp = strtotime(date("Y-m-d", strtotime(current_time("Y-m-d") . " -{$i} day")));
                    $data = new stdClass();
                    $data->date_label = $timestamp;
                    $data->visits = 0;
                    $data->cv = 0;
                    $pre_populate[$timestamp] = $data;
                }
                break;
        }


        $sql = "SELECT
                    {$date_label},
                    SUM(visits) AS visits,
                    SUM(conversion) AS cv
                    FROM (
                        SELECT
                            DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00') as display_date,
                            COUNT(a.id) AS visits,
                            0 AS conversion,
                            CRC32(a.term)
                        FROM {$this->tbl_visits_log} a 
                        WHERE 
                            a.created_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.term IS NOT NULL
                            AND a.source LIKE '{$source}'
                            AND a.medium LIKE '{$medium}'
                            AND a.campaign LIKE '{$campaign}'
                            AND a.content LIKE '{$content}'";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( a.term LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .=" GROUP BY DATE_FORMAT(a.created_date,'%Y-%m-%d %k:00:00'), CRC32(a.term)

                        UNION

                        SELECT
                            DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00') as display_date,
                            0 AS visits,
                            COUNT(a.id) AS conversion,
                            CRC32(b.term)
                        FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                        WHERE 
                            a.conversion_date BETWEEN '{$date_from}' AND '{$date_to}'
                            AND a.status = 'Accepted' 
                            AND b.term IS NOT NULL
                            AND b.source LIKE '{$source}'
                            AND b.medium LIKE '{$medium}'
                            AND b.campaign LIKE '{$campaign}'
                            AND b.content LIKE '{$content}' ";
        if (isset($_POST['search'])) {
            $_search = $_POST['search'];
            if (trim($_search['value']) != '') {
                $sql .= " AND ( b.term LIKE '%{$_search['value']}%'  )";
            }
        }
        $sql .="
                            GROUP BY DATE_FORMAT(a.conversion_date,'%Y-%m-%d %k:00:00'), CRC32(b.term)
                    ) AS t GROUP BY {$group_date}";

        $results = $this->wpdb->get_results($sql);
        foreach ($results AS $row) {
            if (isset($pre_populate[strtotime($row->date_label)])) {
                $pre_populate[strtotime($row->date_label)]->cv = $row->cv;
                $pre_populate[strtotime($row->date_label)]->visits = $row->visits;
            }
        }

        /**
         * RETURN OUTPUT
         */
        wp_send_json(array(
            "draw" => intval($_POST['draw']),
            "recordsTotal" => intval($found->total),
            "recordsFiltered" => intval($found->total),
            "data" => $table_data,
            "chart_data" => $pre_populate
        ));

        wp_die();
    }

    private function _term() {
        if ((bool) $this->_get('download', FALSE) === TRUE) {
            $currency = $this->view_data['currency']->to_currency_symbol(get_option(SH_PREFIX . 'settings_currency'));
            $date_from = $this->view_data['date_selection']['date_selection']['date_from'];
            $date_to = $this->view_data['date_selection']['date_selection']['date_to'];

            $medium = $_GET['medium'];
            $source = $_GET['source'];
            $campaign = $_GET['campaign'];
            $content = $_GET['content'];

            $params = array();
            $where = '';

            $sql = "SELECT
                    SQL_CALC_FOUND_ROWS
                    cpg_term,
                    SUM(visits) AS visits,
                    SUM(visitor) AS visitor,
                    SUM(sessions) AS sessions,
                    SUM(conversion) AS conversion,
                    IFNULL(SUM(conversion) / SUM(visits),0)*100 AS conversion_rate,
                    SUM(cost) AS cost,
                    IFNULL(SUM(cost)/SUM(conversion),0) AS cpa,
                    IFNULL(SUM(cost)/SUM(visits),0) AS cpc,
                    SUM(revenue) AS revenue,
                    IFNULL(SUM(revenue)/SUM(visitor),0) AS rpv,
                    SUM(revenue)-SUM(cost) AS profit
                FROM (
                    SELECT 
                        a.term AS cpg_term,
                        COUNT(DISTINCT(a.visitor_id)) AS visitor,
                        COUNT(a.id) AS visits,
                        COUNT(DISTINCT(a.visitor_session)) AS sessions,
                        0 AS conversion,
                        SUM(a.cpc) AS cost,
                        0 AS revenue
                    FROM {$this->tbl_visits_log} a 
                    WHERE 
                        a.created_date BETWEEN %s AND %s
                        AND a.term IS NOT NULL
                        AND a.medium LIKE %s
                        AND a.source LIKE %s
                        AND a.campaign LIKE %s
                        AND a.content LIKE %s ";
            $params = array_merge($params, array($date_from, $date_to, $medium, $source, $campaign, $content));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( a.term LIKE %s )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .=" GROUP BY a.term

                    UNION

                    SELECT
                        b.term AS cpg_term,
                        0 AS visitor,
                        0 AS visits,
                        0 AS sessions,
                        COUNT(a.id) AS conversion,
                        0 AS cost,
                        SUM(a.goal_value) AS revenue
                    FROM {$this->tbl_conversions_log} a INNER JOIN {$this->tbl_visits_log} b ON b.id = a.visits_log_id
                    WHERE 
                        a.conversion_date BETWEEN %s AND %s
                        AND b.term IS NOT NULL
                        AND b.medium LIKE %s
                        AND b.source LIKE %s
                        AND b.campaign LIKE %s
                        AND b.content LIKE %s ";
            $params = array_merge($params, array($date_from, $date_to, $medium, $source, $campaign, $content));
            if (isset($_GET['search'])) {
                $_search = $_GET['search'];
                if (trim($_search['value']) != '') {
                    $sql .= " AND ( b.term LIKE %s  )";
                    $params = array_merge($params, array(
                        '%%' . $_search['value'] . '%%',
                    ));
                }
            }

            $sql .="
                    GROUP BY b.term
                ) AS t 
                GROUP BY cpg_term ";

            if (isset($_GET['order'])) {
                $_order = $_GET['order'];
                $_order[0]['column'] = $_order[0]['column'] + 1;
                $_POST['order'] = $_order;
            }

            $order = $this->_order($params);
            $sql .= " {$order} ";
            $results = $this->wpdb->get_results($this->wpdb->prepare($sql, $params));

            /**
             * clean up output buffer. so we can save only related csv file
             */
            ob_end_clean();
            $output_filename = 'term' . current_time("YmdHis") . '.csv';
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

                $leadArray = array(
                    $row->cpg_term,
                    $row->visits,
                    $row->visitor,
                    $row->sessions,
                    $row->conversion,
                    number_format($row->conversion_rate, 2),
                    number_format($row->cost, 2),
                    number_format($row->cpa, 2),
                    number_format($row->cpc, 2),
                    number_format($row->revenue, 2),
                    number_format($row->rpv, 2),
                    number_format($row->profit, 2),
                );
                // Add row to file
                fputcsv($output_handle, $leadArray);
            }
            fclose($output_handle);
            die();
        }
    }

    /**
     * Import
     */
//    public function import_display() {
//        add_action('admin_footer', array($this, 'page_js'));
//        if (isset($_POST['btnAdd'])) {
//            $conversion_date = $this->_post('conversion_date');
//            $goal_name = $this->_post('goal_name');
//            $goal_type = $this->_post('goal_type');
//            $goal_value = $this->_post('goal_value');
//            $goal_reference = $this->_post('goal_reference');
//            $click_id = $this->_post('click_id', 0);
//
//
//
//            if (TRUE) {
//                $status = STATUS_ACCEPTED;
//                $message = '';
//
//                $sql = "SELECT id,link_id FROM {$this->tbl_visits_log} WHERE id = %d";
//                $visitor_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($click_id)), OBJECT);
//                if (isset($visitor_log->id)) {
//                    $message .= 'CTID Found' . PHP_EOL;
//                } else {
//                    $status = STATUS_REJECTED;
//                    $message .= 'CTID Not Found' . PHP_EOL;
//                }
//
//
//                if (get_option(SH_PREFIX . 'settings_duplicate_handling') == SHORTLY_DUPLICATE_HANDLING_IGNORE) {
//                    if (trim($goal_reference) != '') {
//                        $sql = "SELECT COUNT(*) AS total FROM {$this->tbl_conversions_log} WHERE goal_reference = %s";
//                        $conversion_log = $this->wpdb->get_row($this->wpdb->prepare($sql, array($goal_reference)), OBJECT);
//                        if ($conversion_log->total > 0) {
//                            $status = STATUS_REJECTED;
//                            $message .= 'Duplicate Goal Reference ID ' . $goal_reference . PHP_EOL;
//                        }
//                    }
//                }
//                $country = $this->ip_info();
//                $this->wpdb->insert(
//                        $this->tbl_import_temp, array(
//                    'link_id' => isset($visitor_log->link_id) ? $visitor_log->link_id : 0,
//                    'goal_id' => 0,
//                    'visits_log_id' => isset($visitor_log->id) ? $visitor_log->id : 0,
//                    'goal_name' => $goal_name,
//                    'goal_value' => $goal_value,
//                    'goal_type' => $goal_type,
//                    'goal_reference' => $goal_reference,
//                    'referrer_url' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
//                    'ip_address' => $_SERVER['REMOTE_ADDR'],
//                    'ip_country_code' => isset($country['country_code']) ? $country['country_code'] : '',
//                    'ip_country_name' => isset($country['country']) ? $country['country'] : '',
//                    'ip_city_name' => isset($country['city']) ? $country['city'] : '',
//                    'ip_latitude' => isset($country['latitude']) ? $country['latitude'] : '',
//                    'ip_longitude' => isset($country['longitude']) ? $country['longitude'] : '',
//                    'user_agent_string' => $_SERVER['HTTP_USER_AGENT'],
//                    'status' => $status,
//                    'message' => $message,
//                    'conversion_date' => $conversion_date,
//                    'created_date' => current_time("Y-m-d H:i:s"),
//                        )
//                );
//
//                $conversion_id = $this->wpdb->insert_id;
//                $this->view_data['msg'] = array(
//                    'status' => 'alert-success',
//                    'text' => SRTY_MSG_IMPORT_IMPORTED
//                );
//                $this->set_top_message($this->view_data['msg']);
//                wp_redirect('?page=sh_reports_page&action=import&sub=import');
//                exit();
//            } else {
//                $this->view_data['msg'] = array(
//                    'status' => 'alert-danger',
//                    'text' => SRTY_MSG_IMPORT_TOP_ERROR_MESSAGE
//                );
//            }
//        }
//
//        if (isset($_POST['btnImport'])) {
//            /**
//             * clean up existing temp data
//             */
//            $delete = $this->wpdb->query("TRUNCATE TABLE {$this->tbl_import_temp}");
//
//            $upload_file = $_FILES['upload_file'];
//
//            $upload_overrides = array('test_form' => false, 'mimes' => array('txt' => 'text/plain', 'csv' => 'text/csv'));
//            $data = wp_handle_upload($upload_file, $upload_overrides);
//            if (isset($data['error']) && (trim($data['error']) != '')) {
//                $this->view_data['msg'] = array(
//                    'status' => 'alert-danger',
//                    'text' => $data['error']
//                );
//                $this->set_top_message($this->view_data['msg']);
//                wp_redirect('?page=sh_reports_page&action=import');
//                exit();
//            } else {
//                $result = array();
//                $row = 1;
//
//                $delimiter = ",";
//                if ($this->_post('import_option', 'custom') == 'shareasale') {
//                    $delimiter = "|";
//                }
//                /*
//                 * this ini_set('auto_detect_line_endings', true);
//                 * solved csv end of line issue. if not it getting all csv row as one row
//                 */
//                ini_set('auto_detect_line_endings', true);
//                if (($handle = fopen($data['file'], "r")) !== FALSE) {
//                    while (($data = fgetcsv($handle, 0, $delimiter)) !== FALSE) {
//                        if ($row > 1) {
//
//                            /*
//                             * eo: reformat date
//                             */
//                            $result = $this->_valid_data($data, $this->_post('import_option', 'custom'));
//
//                            $this->wpdb->insert(
//                                    $this->tbl_import_temp, array(
//                                'link_id' => $result['link_id'],
//                                'goal_id' => $result['goal_id'],
//                                'visits_log_id' => $result['visits_log_id'],
//                                'goal_name' => $result['goal_name'],
//                                'goal_value' => $result['goal_value'],
//                                'goal_type' => $result['goal_type'],
//                                'goal_reference' => $result['goal_reference'],
//                                'referrer_url' => $result['referrer_url'],
//                                'ip_address' => $result['ip_address'],
//                                'ip_country_code' => $result['ip_country_code'],
//                                'ip_country_name' => $result['ip_country_name'],
//                                'ip_city_name' => $result['ip_city_name'],
//                                'ip_latitude' => $result['ip_latitude'],
//                                'ip_longitude' => $result['ip_longitude'],
//                                'user_agent_string' => $result['user_agent_string'],
//                                'status' => $result['status'],
//                                'message' => $result['message'],
//                                'conversion_date' => $result['conversion_date'],
//                                'created_date' => $result['created_date'],
//                                    )
//                            );
//                            $conversion_id = $this->wpdb->insert_id;
//                        }
//                        if (trim($data[0]) == '') {
//                            break;
//                        }
//                        $row++;
//                    }
//                    fclose($handle);
//                    $this->view_data['msg'] = array(
//                        'status' => 'alert-success',
//                        'text' => SRTY_MSG_IMPORT_IMPORTED
//                    );
//                    $this->set_top_message($this->view_data['msg']);
//                    wp_redirect('?page=sh_reports_page&action=import&sub=import');
//                    exit();
//                }
//                wp_redirect('?page=sh_reports_page&action=import');
//            }
//        }
//        $this->view('v_import', $this->view_data);
//    }
//
//    public function import() {
//        add_action('admin_footer', array($this, 'page_js'));
//        $this->view('v_import-verification', array('data' => 'hello world'));
//    }
//
//    public function cancel_import() {
//        $delete = $this->wpdb->query("TRUNCATE TABLE {$this->tbl_import_temp}");
//        $this->view_data['msg'] = array(
//            'status' => 'alert-success',
//            'text' => SRTY_MSG_IMPORT_CANCELLED
//        );
//        $this->set_top_message($this->view_data['msg']);
//        wp_redirect('?page=sh_reports_page&action=import');
//    }
//
//    public function confirm_import() {
//        $this->wpdb->query("INSERT INTO {$this->tbl_conversions_log} ("
//                . "link_id,"
//                . "goal_id,"
//                . "visits_log_id,"
//                . "goal_name,"
//                . "goal_value,"
//                . "goal_type,"
//                . "goal_reference,"
//                . "referrer_url,"
//                . "ip_address,"
//                . "ip_country_code,"
//                . "ip_country_name,"
//                . "ip_city_name,"
//                . "ip_latitude,"
//                . "ip_longitude,"
//                . "user_agent_string,"
//                . "status,"
//                . "message,"
//                . "conversion_date,"
//                . "created_date"
//                . ") SELECT "
//                . "link_id,"
//                . "goal_id,"
//                . "visits_log_id,"
//                . "goal_name,"
//                . "goal_value,"
//                . "goal_type,"
//                . "goal_reference,"
//                . "referrer_url,"
//                . "ip_address,"
//                . "ip_country_code,"
//                . "ip_country_name,"
//                . "ip_city_name,"
//                . "ip_latitude,"
//                . "ip_longitude,"
//                . "user_agent_string,"
//                . "status,"
//                . "CONCAT('Manual Import\n', message),"
//                . "conversion_date,"
//                . "created_date "
//                . "FROM {$this->tbl_import_temp} WHERE status='Accepted' OR force_import=1 ");
//
//        /**
//         * done. we clean up temp table
//         */
//        $delete = $this->wpdb->query("TRUNCATE TABLE {$this->tbl_import_temp}");
//
//        $this->view_data['msg'] = array(
//            'status' => 'alert-success',
//            'text' => SRTY_MSG_IMPORT_IMPORTED
//        );
//        $this->set_top_message($this->view_data['msg']);
//        wp_redirect('?page=sh_reports_page&action=import');
//    }

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

    public function ajax_datatable() {
        wp_send_json($this->_datatable_get_all());
        wp_die();
    }

    public function ajax_delete() {

        $this->_delete_batch($this->explode_trim($_POST['ids']));
        $this->view_data['msg'] = array(
            'status' => 'alert-success',
            'text' => SRTY_MSG_IMPORT_DELETED
        );
        $this->set_top_message($this->view_data['msg']);
        wp_send_json(array('result' => 1));
        wp_die();
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

                    }
                });
                jQuery('.tbltitle').html('Only valid rows will be imported. The file you\'ve uploaded contains <span id="error_count" class="label label-danger">0</span>  invalid ones.');
                jQuery('#bulk_action').html('<select name="slcAction" class="form-control input-sm slcAction"><option>Bulk Actions</option><option>Delete</option></select> <button data-action="srty_delete" class="btnAction btn btn-default btn-sm" type="submit">Apply</button>');
                var btn = '<a href="?page=sh_reports_page&action=import&sub=cancel_import" class="btn btn-default pull-left"><i class="fa fa-trash-o"></i> Cancel Import</a>';
                btn += '<a style="margin-left:10px;"  href="?page=sh_reports_page&action=import&sub=confirm_import" class="btn btn-primary pull-right"><i class="fa fa-check-circle"></i> Confirm Import</a>';
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

}

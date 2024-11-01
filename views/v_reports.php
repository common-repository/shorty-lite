<div class="page-header">
    <h2><i class="fa fa-signal"></i> Performance Report</h2>
    <p> Performance overview <?php echo $date_selection['display_title']; ?>. </p>
</div>
<div class="btn-toolbar">
    <div class="btn-group pull-left">
        <a href="?page=sh_reports_page" class="btn btn-primary active">Overview</a>
        <a href="?page=sh_reports_page&action=report-referrers" class="btn btn-default">Referrers</a>
        <a href="?page=sh_reports_page&action=report-countries" class="btn btn-default">Countries</a>
        <a href="?page=sh_reports_page&action=report-links" class="btn btn-default">Links</a>
        <a href="?page=sh_reports_page&action=report-source" class="btn btn-default">Campaigns</a>
        <a href="?page=sh_reports_page&action=report-visitors" class="btn btn-default">Visitors</a>
    </div>
    <div class="btn-group pull-right">
        <?php echo $date_selection['widget']; ?>
    </div>
</div>
<p>&nbsp;</p>
<div class="panel panel-default stats">
    <div class="well" style="border:none; padding:0px; margin:0px;">
        <div class="row">
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_total_visits" class="text-center" style="margin:0px;">0</h2>
                <p class="text-silver nomargins text-center">total visits <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total clicks on your tracking campaigns and links, excluding all bots and non-human clicks."></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_total_visitors" class="text-center" style="margin:0px;">0</h2>
                <p class="text-silver nomargins text-center">visitors <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total unique visitors or people who clicked on your links and campaigns. Based on sessions settings specified under the project settings page."></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_conversions" class="text-center" style="margin:0px;">0</h2>
                <p class="text-silver nomargins text-center">conversions <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total value of sale, lead or page view as specified under goals"></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_conversions_percent" class="text-center" style="margin:0px;">0.00%</h2>
                <p class="text-silver nomargins text-center">conversion rate <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total goal conversions divided by visitors and multipled by 100."></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_revenue" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">revenue <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total sales and revenue based on your goals conversion settings, as specified under Goals and tracked with your pixel or code."></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_rpv" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">rpv <a href="#" data-toggle="tooltip" data-placement="bottom" title="Revenue Per Visitor is the total revenue divided by the number of visitors."><i class="fa fa-info-circle text-silver"></i></a></p>
            </div>
        </div>
    </div>
    <div class="stats" style="border-top:1px solid #ECF0F1; border-bottom:1px solid #ECF0F1;">
        <div id="chart" style="height: 250px;padding:20px 0 20px 0;"></div>
    </div>
    <div class="well" style="border:none; padding:0px; margin:0px;">
        <div class="row">
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_cost" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">total cost <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total spent on advertising, calculated from CPC cost settings"></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_cpc" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">cost / visit <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="The cost per click or visit as specified under campaign settings"></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_cost_per_day" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">cost / day <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="The average daily spend, as specified under your campaign settings"></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_cpa" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">cpa <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Cost Per Acquisition or Cost Per Action, the amount spent to get a conversion"></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_profit" class="text-center" style="margin:0px;">0.00</h2>
                <p class="text-silver nomargins text-center">profit <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Total profits calculated as (total revenue - total cost) and displayed in your project's currency setting"></i></a></p>
            </div>
            <div class="col-md-2" style="padding:30px;">
                <h2 id="summary_roi" class="text-center" style="margin:0px;">0.00%</h2>
                <p class="text-silver nomargins text-center">roi <a href="#"><i class="fa fa-info-circle text-silver" data-toggle="tooltip" data-placement="bottom" title="Return on Investment, calculated as [(total revenue - total cost) / total cost] x 100"></i></a></p>
            </div>
        </div>
    </div>
</div>
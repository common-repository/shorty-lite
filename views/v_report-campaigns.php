<div class="page-header">
    <h2><i class="fa fa-signal"></i> Campaign Report</h2>
    <p> Campaign performance overview <?php echo $date_selection['display_title']; ?>. </p>
</div>

<div class="btn-toolbar">
    <div class="btn-group pull-left">
        <a href="?page=sh_reports_page" class="btn btn-default">Overview</a>
        <a href="?page=sh_reports_page&action=report-referrers" class="btn btn-default">Referrers</a>
        <a href="?page=sh_reports_page&action=report-countries" class="btn btn-default">Countries</a>
        <a href="?page=sh_reports_page&action=report-links" class="btn btn-default">Links</a>
        <a href="?page=sh_reports_page&action=report-source" class="btn btn-primary active">Campaigns</a>
        <a href="?page=sh_reports_page&action=report-visitors" class="btn btn-default">Visitors</a>
    </div>
    <div class="btn-group pull-right">
        <?php echo $date_selection['widget']; ?>
    </div>
</div>

<div class="header" style="padding:30px 0px; height:auto;">
    <ul class="nav nav-wizard" style="margin:0px;">
        <a href="?page=sh_reports_page&action=report-source"><li> <?php echo $_GET['source']; ?> </li></a>
        <a href="?page=sh_reports_page&action=report-medium&source=<?php echo $_GET['source']; ?>"><li class="active"> <?php echo $_GET['medium']; ?> </li></a>
    </ul>
</div>

<table id="report_campaign" class="table table-striped">
    <thead>
        <tr>
            <th>Campaigns</th>
            <th>Visits</th>
            <th>Visitors</th>
            <th>Conv.</th>
            <th>Conv. %</th>
            <th>Cost</th>
            <th>CPA</th>
            <th>CPC</th>
            <th>Revenue</th>
            <th>RPV</th>
            <th>Profit</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="11">Loading</td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th><strong>Total</strong></th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    </tfoot>
</table>


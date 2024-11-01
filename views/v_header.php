<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'Shorty';
?>
&nbsp;
<div class="shorty">
    <div class="container-fluid">
        <div class="ltbody">
            <nav class="navbar navbar-inverse" role="navigation" style="margin-top:5px;">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                        <a class="navbar-brand" href="?page=<?php echo SH_MENU_SLUG; ?>"><img src="<?php echo SH_IMAGE_URL; ?>/logotext-white.png" height="25" alt="shorty">
                            
                        </a>
                    </div>
                    <div class="collapse navbar-collapse" id="wpmenu">
                        <ul class="nav navbar-nav" style="height: 50px;">
                            <li <?php echo $page == 'sh_trackers' ? 'class="active"' : ''; ?>><a href="?page=<?php echo 'sh_trackers'; ?>"><i class="fa fa-list-alt"></i> Trackers</a></li>
                            <li <?php echo $page == 'sh_split_tests' ? 'class="active"' : ''; ?>><a href="?page=sh_split_tests"><i class="fa fa-random"></i> Split Test</a></li>
                            <li <?php echo $page == 'sh_conversions_page' ? 'class="active"' : ''; ?>><a href="?page=sh_conversions_page"><i class="fa fa-crosshairs"></i> Conversions</a></li>
                            <li <?php echo $page == 'sh_reports_page' || $page == 'sh_import_page' ? 'class="active"' : ''; ?>><a href="?page=sh_reports_page"><i class="fa fa-signal"></i> Reports</a></li>
                            <li <?php echo $page == 'sh_tools_page' ? 'class="active"' : ''; ?>><a href="?page=sh_tools_page"><i class="fa fa-gamepad"></i> Tools</a></li>
                            <li <?php echo $page == 'sh_settings_page' ? 'class="active"' : ''; ?>><a href="?page=sh_settings_page"><i class="fa fa-cogs"></i> Settings</a></li>
                            <li <?php echo $page == 'sh_help' ? 'class="active"' : ''; ?>><a href="?page=sh_help"><i class="fa fa-heart"></i> Help</a></li>
                        </ul>
                        <div class="pull-right" style="margin-top:15px; color:#FFFFFF; background-color:#e74c3c; padding:3px 10px; border-radius:8px;">LITE</div>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container-fluid -->
            </nav>
            <div class="sty-body">
                <?php $this->top_message(); ?>
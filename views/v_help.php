<div class="page-header">
    <h2><i class="fa fa-heart"></i> License &amp; Help</h2>
    Check your license key and get important data about our support.
</div>
<form method="POST" class="form" action="?page=sh_help">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">License &amp; Support</div>
                </div>
                <div class="panel-body">
                    <fieldset>
                        <div class="alert alert-block alert-info">
                            <p><i class="fa fa-info-circle"></i> Support for Shorty Lite is available through the official WordPress forum. </p>
                            <p><a href="http://www.shortywp.com/?utm_source=wordpress&amp;utm_medium=referral&amp;utm_campaign=shorty_lite" target="_blank" class="alert-link">Upgrade now</a> to get additional features and dedicated support.</p>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Backup &amp; Reset</div>
                </div>
                <div class="panel-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <a href="?page=sh_help&action=download" class="btn btn-default btn-block"><i class="fa fa-download"></i> Download Data</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 text-muted">Download complete information for your links.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <a href="?page=sh_help&action=clear_reports" class="btn btn-warning btn-block confirm" data-message="Are you sure you want to clear reports?"><i class="fa fa-history"></i> Clear Reports</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 text-muted">Safely clear your report data without losing your links, campaigns, goals or settings.</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="controls">
                                        <button id="btnNuke" type="button" class="btn btn-danger btn-block" ><i class="fa fa-trash"></i> Nuke Everything</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 text-muted">Wipe out all data and start over again. You will be required to enter your license information.</div>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Important Stuff</div>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="control-label" for="support_token">Support Token: </label>
                        <div class="controls">
                            <textarea rows="6" class="form-control" id="support_token"><?php echo $info; ?></textarea>
                            <p class="help-block">Please provide this token to our customer support team. It will help us identify important technical data about your hosting so we can help serve you faster.</p>
                        </div>
                        <p><strong>News & Updates</strong></p>
                    <script src="http://feeds.feedburner.com/shortywp?format=sigpro" type="text/javascript" ></script><noscript><p>Subscribe to RSS headline updates from: <a href="http://feeds.feedburner.com/shortywp"></a><br/>Powered by FeedBurner</p> </noscript>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</form>
<div class="modal fade" id="nuke" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Nuke Warning</h4>
            </div>
            <div class="modal-body">
                <p>Sorry dude, this button is so important that we just had to annoy you with another popup so that you don't nuke innocent civilians.</p>
                <p>Everything will be wiped clean and nothing will survive this.</p>
                <p>Are you sure? <strong>I repeat:</strong> are you sure?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-times-circle"></i> Abort Nuke</button>
                <a href="?page=sh_help&action=nuke" class="btn btn-danger"><i class="fa fa-check-circle"></i> Yes, Nuke Everything Now!</a>
            </div>
        </div>
    </div>
</div>
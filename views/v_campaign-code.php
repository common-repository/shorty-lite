<div class="container-fluid">
    <div class="row">
        <div class="ltbody">
            <div class="page-header">
                <h2><i class="fa fa-random"></i> Campaign <small>| Code </small></h2>
                <p>Use the correct campaign code when advertising with different ad sources. Some campaign code contain variable parameters that are used by specific ad networks or affiliate networks.      </p>
            </div>
            <form class="form">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">Campaign Code</div>
                            </div>
                            <div class="panel-body">
                                <fieldset>
                                    <div class="form-group">
                                        <label class="control-label" for="url">Short Link: </label>
                                        <div class="controls">
                                            <input name="url" type="text" class="form-control input-lg"  value="<?php echo $this->current_domain(TRUE) . 'c/' . $campaign->tracking_campaign; ?>">
                                            <p class="help-block"> The short link will redirect to the full link and pass your tracking variables.</p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="longurl">Full Link: </label>
                                        <div class="controls">
                                            <textarea name="longurl" cols="" rows="5" class="form-control input-lg" ><?php echo $this->current_domain(TRUE) . 'c/' . $campaign->tracking_campaign; ?>?source=<?php echo $campaign->source; ?>&medium=<?php echo $campaign->medium; ?>&campaign=<?php echo $campaign->campaign; ?>&content=<?php echo $campaign->content; ?>&term=<?php echo $campaign->term; ?></textarea>
                                            <p class="help-block"> The full campaign link is useful if you need to override the variables manually for each ad.</p>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">Campaign Details</div>
                            </div>
                            <div class="panel-body">
                                <fieldset>
                                    <div class="form-group">
                                        <label for="label3">Source: </label>
                                        <div class="controls">
                                            <?php echo $campaign->source; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="label5">Medium: </label>
                                        <div class="controls">
                                            <?php echo $campaign->medium; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="label3">Campaign: </label>
                                        <div class="controls">
                                            <?php echo $campaign->campaign; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="label3">Content: </label>
                                        <div class="controls">
                                            <?php echo $campaign->content; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="label4">Term: </label>
                                        <div class="controls">
                                            <?php echo $campaign->term; ?>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="form-actions"><a href="?page=sh_campaigns_page&action=edit&id=<?php echo $campaign->id; ?>" class="btn btn-default">Edit</a></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
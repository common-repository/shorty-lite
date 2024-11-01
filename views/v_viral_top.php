<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo stripslashes($link->link_name); ?></title>
        <meta name="title" content="<?php echo stripslashes($link->meta_title); ?>">
        <meta name="description" content="<?php echo stripslashes($link->meta_description); ?>">
        <meta name="og:title" content="<?php echo stripslashes($link->meta_title); ?>">
        <meta name="og:url" content="<?php echo stripslashes($link->destination_url); ?>">
        <meta name="og:site_name" content="<?php echo stripslashes($link->meta_title); ?>">
        <meta name="og:description" content="<?php echo stripslashes($link->meta_description); ?>">
        <meta name="og:image" content="<?php echo stripslashes($link->meta_image); ?>">
        <?php
        if ($link->frame_content == SHORTLY_FRAME_CONTENT_HIDDEN) {
            ?>
            <meta name="robots" content="noindex">
            <?php
        }
        ?>
        <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" >
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" >
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <style type="text/css">
            body {
                margin: 0;
            }
            .view{
                position:fixed; 
            }
            .brand {
                font-size: 14px;
            }
            .ltr-social {
                padding: 10px;
            }
        </style>
        <script type="text/javascript">
            /* <![CDATA[ */
            jQuery(document).ready(main);
            function main() {
                registerEvents();
                resizeIframe();

                jQuery('.popup').click(function (event) {
                    var width = 575,
                            height = 400,
                            left = (jQuery(window).width() - width) / 2,
                            top = (jQuery(window).height() - height) / 2,
                            url = this.href,
                            opts = 'status=1' +
                            ',width=' + width +
                            ',height=' + height +
                            ',top=' + top +
                            ',left=' + left;

                    window.open(url, 'Share', opts);

                    return false;
                });
            }

            function registerEvents() {
                jQuery(window).resize(function () {
                    resizeIframe();
                });
            }

            function resizeIframe() {
                jQuery("#iframe").height(WindowHeight());
            }

            function WindowHeight() {
                var de = document.documentElement;
                return self.innerHeight ||
                        (de && de.clientHeight) ||
                        document.body.clientHeight;
            }

            function getObjHeight(obj) {
                if (obj.offsetWidth)
                {
                    return obj.offsetHeight;
                }
                return obj.clientHeight;
            }

        </script>
        <style type="text/css">
            html {overflow: hidden;} /*we don't need any scrolls for our html */
            #iframe { overflow: hidden; } /*this is to remove the scroll when not needed*/

            #iframe, iframe{
                width: 100%;
                height: 100%;
                border:0;
                z-index:0;
            }
        </style>
        <?php
        if ((bool) $link->param_tag_retargeting) {
            echo stripslashes($link->retargeting_fb);
        }
        ?>
    </head>
    <body>
        <?php echo stripslashes($link->retargeting_code); ?>
        <?php
        if ((bool) $link->param_tag_retargeting) {
            echo stripslashes($link->retargeting_adwords);
            echo stripslashes($link->retargeting_adroll);
            echo stripslashes($link->retargeting_perfect);
            echo stripslashes($link->retargeter_code);
        }
        ?>
        <?php
        if ($link->cloaking_type == SHORTLY_CLOAKING_TYPE_VIRAL) {
            ?>
            <div id="fb-root"></div>
            <script>
                (function (d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id))
                        return;
                    js = d.createElement(s);
                    js.id = id;
                    js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=894950487240828";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
            </script>
            <div class="navbar navbar-fixed-top <?php echo get_option(SH_PREFIX . 'settings_bar_theme') == SHORTLY_BAR_THEME_BLACK ? 'navbar-inverse' : ''; ?>">
                <div class="navbar-inner">
                    <div class="container">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <div class="brand"><i class="icon-share"></i> Share </div>
                        <div class="nav-collapse collapse">
                            <ul class="nav pull-left">
                                <?php
                                if ((bool) get_option(SH_PREFIX . 'settings_socialButtons_facebook')) {
                                    ?>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                            <i class="icon-facebook-sign"></i> FaceBook  
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li class="ltr-social">
                                                <div class="fb-like" data-href="<?php echo $link->tracking_link; ?>" data-send="true" data-width="300" data-show-faces="true" ></div>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php
                                }

                                if ((bool) get_option(SH_PREFIX . 'settings_socialButtons_twitter')) {
                                    ?>
                                    <li class="">
                                        <a class="popup" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo $this->current_domain(TRUE) . $link->tracking_link; ?>" >
                                            <i class="icon-twitter-sign"></i> Twitter 
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>

                            </ul>
                            <?php
                            if ((bool) get_option(SH_PREFIX . 'settings_earnMoney_enable') && (trim(get_option(SH_PREFIX . 'settings_earnMoney_affiliateLink')) != '')) {
                                ?>
                                <ul class="nav pull-right">
                                    <li class="divider-vertical"></li>
                                    <li><a target="_blank" href="<?php echo get_option(SH_PREFIX . 'settings_earnMoney_affiliateLink'); ?>"><i class="icon-external-link"></i> ShortyWP</a></li>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        <div id='iframe'>
            <iframe src='<?php echo $link->destination_url; ?>' class='view'></iframe>
        </div>
    </body>
</html>

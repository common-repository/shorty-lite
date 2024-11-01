<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Redirecting..</title>
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

        if ((bool) $link->blank_referrer) {
            ?>
            <meta name="referrer" content="no-referrer" />
            <?php
        }
        ?>
        <!--<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,400italic">-->
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
              <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->
        <!-- Le fav and touch icons -->
        <!-- Le Google Analytics -->
        <?php
        if ((bool) $link->param_tag_retargeting) {
            echo stripslashes($link->retargeting_fb);
        }
        ?>
    </head>
    <body style="background-color:#fff;">
        <?php echo stripslashes($link->retargeting_code); ?>
        <?php
        if ((bool) $link->param_tag_retargeting) {
            echo stripslashes($link->retargeting_adwords);
            echo stripslashes($link->retargeting_adroll);
            echo stripslashes($link->retargeting_perfect);
            echo stripslashes($link->retargeter_code);
        }
        ?>
        <!--<p align="center" style="margin-top:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i><br/>Redirecting, hold on..</p>-->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!--<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>-->
        <script type = "text/javascript" language = "javascript">
            jQuery(document).ready(function () {
                window.location = "<?php echo $link->destination_url;?>";
            });
        </script>
    </body>
</html>

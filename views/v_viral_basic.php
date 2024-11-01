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

        <style type="text/css">
            body {
                margin: 0;
            }
            .view{
                position:fixed; 
                top:0px; 
                left:0px; 
                bottom:0px; 
                right:0px; 
                width:100%; 
                height:100%; 
                border:none; 
                margin:0; 
                padding:0; 
                overflow:hidden; 
                z-index:999999;
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
        <iframe src='<?php echo $link->destination_url; ?>' class='view'></iframe>
    </body>
</html>

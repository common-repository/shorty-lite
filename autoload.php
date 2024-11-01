<?php

spl_autoload_register('srty_autoload');

function srty_autoload($class_name)
{
    if (false !== strpos($class_name, 'Srty')) {
        $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
//        $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';
        $class_file = strtolower($class_name) . '.php';
        require_once $classes_dir . $class_file;
    }
}

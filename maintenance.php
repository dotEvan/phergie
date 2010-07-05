#!/usr/bin/env php
<?php
/**
 * Phergie
 *
 * PHP version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://phergie.org/license
 *
 * @category  Phergie
 * @package   Phergie
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie
 */
/**
 * @see Phergie_Autoload
 */
require 'Phergie/Autoload.php';
Phergie_Autoload::registerAutoloader();

if (!isset($argc)) {
    echo
    'The PHP setting register_argc_argv must be enabled for Phergie\'s ',
    'maintenance scripts to function properly.',
    PHP_EOL;
} else if ($argc > 0) {

    // Skip the current file for manual installations
    // ex: php maintenance.php Tld init
    if (realpath($argv[0]) == __FILE__) {
        array_shift($argv);
    }

    try {
        $plugin = array_shift($argv);
        $keyword = array_shift($argv);
        if ($plugin && $keyword) {
            $maintenanceClassName = "Phergie_Plugin_{$plugin}_Maintainer";
            if (!class_exists($maintenanceClassName)) {
                throw new Phergie_Db_Exception(
                    "Unable to find a maintenance class for $plugin",
                    Phergie_Db_Exception::ERR_PLUGIN_MAINTENANCE_CLASS_DOES_NOT_EXIST
                );
            }
            $maintenanceClass = new $maintenanceClassName();
            $maintenanceClass->dispatchKeyword($keyword, $argv);
        } else {
            throw new Phergie_Db_Exception(
                "Plugin and/or keyword are required.",
                Phergie_Db_Exception::ERR_PLUGIN_MAINTENANCE_PLUGIN_AND_KEYWORD_REQUIRED
            );
        }
    } catch (Phergie_Exception $e) {
        echo "{$e->getMessage()}", PHP_EOL;
    }
}


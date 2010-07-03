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
 * @package   Phergie_Plugin_Url
 * @author    Phergie Development Team <team@phergie.org>
 * @copyright 2008-2010 Phergie Development Team (http://phergie.org)
 * @license   http://phergie.org/license New BSD License
 * @link      http://pear.phergie.org/package/Phergie
 */

/**
 * Provides database maintenance functionality for the Tld plugin.
 *
 * @category Phergie
 * @package  Phergie_Plugin_Tld
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie
 *
 * @pluginDesc Provides information for a top level domain.
 */
class Phergie_Plugin_Tld_Maintainer extends Phergie_Db_Maintainer
{
    /**
     * @var string location of the Sqlite database
     */
    protected $dbFile;
    /**
     * Initializes the class and creates keyword to callback relationships
     * 
     */
    public function __construct()
    {
        $config = new Phergie_Db_MaintainerConfig();
        $config->addCommand('init', 'initializeDatabase');

        $tldPlugin = new Phergie_Plugin_Tld();
        $this->dbFile = $tldPlugin->getSqliteDbFilePath();

        parent::__construct($config);
    }

    public function initializeDatabase() {
    }
}


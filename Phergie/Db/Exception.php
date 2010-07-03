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
 * Exceptions related to handling databases for plugins.
 *
 * @category Phergie
 * @package  Phergie
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie
 */

class Phergie_Db_Exception extends Phergie_Exception
{
    /**
     * Error indicating that a directory needed to support database
     * functionality was unable to be created.
     */
    const ERR_UNABLE_TO_CREATE_DIRECTORY = 1;

    /**
     * Error indicating that a defined callback is unable to be called.
     */
    const ERR_UNABLE_TO_INVOKE_CALLBACK = 2;

    /**
     * Error indicating that a specific plugin db maintenance class does not
     * exist.
     */
    const ERR_PLUGIN_MAINTENANCE_CLASS_DOES_NOT_EXIST = 3;

    /**
     * Error indicating that a plugin name and keyword must be supplied when
     * performing database maintenance.
     */
    const ERR_PLUGIN_MAINTENANCE_PLUGIN_AND_KEYWORD_REQUIRED = 4;

    /**
     * Error indicating that a keyword does not have an associated callback.
     */
    const ERR_KEYWORD_HAS_NO_CALLBACK = 5;
}

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
 * Provides a fluent interface for configuring a Maintainer
 *
 * @category Phergie
 * @package  Phergie
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie
 */
class Phergie_Db_MaintainerConfig implements IteratorAggregate
{
    /**
     * Enumerated array of commands provided by the Maintainer
     *
     * @var array
     */
    protected $commands;

    /**
     * Initializes class data
     */
    public function __construct()
    {
        $this->commands = array();
    }

    /**
     * Adds a command to the config that calls $callback when triggered
     * by $keyword. Provides a fluent interface
     *
     * @param string $keyword  keyword to trigger the callback
     * @param string $callback method to call on receiving the keyword
     *
     * @return Phergie_Db_MaintainerConfig
     */
    public function addCommand($keyword, $callback)
    {
        $this->commands[$keyword] = $callback;

        return $this;
    }

    /**
     * Returns the callback method for the supplied keyword. Returns null
     * if the keyword is not defined.
     * 
     * @param string $keyword keyword 
     * 
     * @return string
     */
    public function getCallback($keyword)
    {
        if (!empty($this->commands[$keyword])) {
            return $this->commands[$keyword];
        }

        return null;
    }

    /**
     * Alias for getCallback
     *
     * @param string $keyword keyword
     *
     * @see getCallback
     * @return string
     */
    public function getCallbackFor($keyword)
    {
        return $this->getCallback($keyword);
    }

    /**
     * Allows iteration over this object. Keys are keywords, values are
     * callbacks.
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->commands);
    }
}

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
 * Provides a system for maintaining plugin databases.
 *
 * @category Phergie
 * @package  Phergie
 * @author   Phergie Development Team <team@phergie.org>
 * @license  http://phergie.org/license New BSD License
 * @link     http://pear.phergie.org/package/Phergie
 */
abstract class Phergie_Db_Maintainer
{
    /**
     * list of available commands provided by the maintainer
     *
     * @var Phergie_Db_MaintainerConfig
     */
    protected $config;

    public function __construct(Phergie_Db_MaintainerConfig $config)
    {
        // sanity check the callbacks
        foreach ($config as $keyword => $callback) {
            if (!is_callable(array($this, $callback))) {
                throw new Phergie_Db_Exception(
                    "Unable to invoke $callback for keyword $keyword.",
                    Phergie_Db_Exception::ERR_UNABLE_TO_INVOKE_CALLBACK
                );
            }
        }

        $this->config = $config;
    }

    public function dispatchKeyword($keyword, array $arguments = array())
    {
        $this->{$this->config->getCallback($keyword)}($arguments);
    }
    
}

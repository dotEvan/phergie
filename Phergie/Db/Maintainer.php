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

    /**
     * initializes the class, ensures defined callbacks are valid (callable)
     * 
     * @param Phergie_Db_MaintainerConfig $config configuration container
     */
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

    /**
     * If a callback is defined by the instantiating class for the keyword
     * give, then call that method and pass in the arguments as the first
     * parameter
     * 
     * @param string $keyword   keyword related to a specific callback method
     * @param array  $arguments arguments to pass to called method
     * 
     * @throws Phergie_Db_Exception
     * 
     * @return null
     */
    public function dispatchKeyword($keyword, array $arguments = array())
    {
        $callback = $this->config->getCallback($keyword);
        if ($callback) {
            $this->{$callback}($arguments);
        } else {
            throw new Phergie_Db_Exception(
                    "{$keyword} does not have an associated task.",
                    Phergie_Db_Exception::ERR_KEYWORD_HAS_NO_CALLBACK
            );
        }
    }

    /**
     * Sends a message to the console.
     * 
     * @param string $message message to output to the console
     * 
     * @todo replace with Phergie's log utility when it is written
     *
     * @return null
     */
    public function console($message)
    {
        echo $message, PHP_EOL;
    }

    /**
     * Supplies a user with a prompt and returns the user's input
     *
     * @param string $message prompt message
     *
     * @return string
     */
    public function promptConsole($message)
    {
        echo $message;
        $fp = fopen('php://stdin', 'r');
        $user_input = trim(fread($fp, 32));
        fclose($fp);
        return $user_input;
    }
}

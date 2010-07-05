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
        $config->addCommand('add', 'addFixed');
        $config->addCommand('remove', 'removeTld');

        $tldPlugin = new Phergie_Plugin_Tld();
        $this->dbFile = $tldPlugin->getSqliteDbFilePath();

        parent::__construct($config);
    }

    /**
     * Creates the Tld database and populates it with data scraped from 
     * iana.org
     * 
     * @param array $args arguments passed to the method from the command line
     *                    [0] -> 'clear' (optional)
     *
     * @return null
     */
    public function initializeDatabase($args)
    {
        $this->console('Initializing Tld database.');

        if (is_array($args)) {
            if ($args[0] == 'clear') {
                $prompt = 'Are you sure you wish to clear the Tld database? [y/N]';
                $response = $this->promptConsole($prompt);
                $response = strtolower($response);
                if ($response != 'y' && $response != 'yes') {
                    $this->console('Aborting.');
                    return;
                }
                $this->console('Clearing Tld database.');
                @unlink($this->dbFile);
            }
        }

        if (!is_dir(dirname($this->dbFile))) {
            mkdir(dirname($this->dbFile), 0755, true);
        }

        $dbManager = new Phergie_Db_Sqlite($this->dbFile);
        $this->db = $dbManager->getDb();
        if (!$dbManager->hasTable('tld')) {
            $query = 'CREATE TABLE tld ('
                . 'tld VARCHAR(20), '
                . 'type VARCHAR(20), '
                . 'description VARCHAR(255))';

            $this->db->exec($query);

            // prepare a statement to populate the table with
            // tld information
            $insert = $this->db->prepare(
                'INSERT INTO tld
                (tld, type, description)
                VALUES (:tld, :type, :description)'
            );

            // grab tld data from iana.org...
            $contents = file_get_contents(
                'http://www.iana.org/domains/root/db/'
            );

            // ...and then parse it out
            $regex = '{<tr class="iana-group[^>]*><td><a[^>]*>\s*\.?([^<]+)\s*'
                . '(?:<br/><span[^>]*>[^<]*</span>)?</a></td><td>\s*'
                . '([^<]+)\s*</td><td>\s*([^<]+)\s*}i';
            preg_match_all($regex, $contents, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                list(, $tld, $type, $description) = array_pad($match, 4, null);
                $type = trim(strtolower($type));
                if ($type != 'test') {
                    $tld = trim(strtolower($tld));
                    $description = trim($description);

                    switch ($tld) {

                    case 'com':
                        $description = 'Commercial';
                        break;

                    case 'info':
                        $description = 'Information';
                        break;

                    case 'net':
                        $description = 'Network';
                        break;

                    case 'org':
                        $description = 'Organization';
                        break;

                    case 'edu':
                        $description = 'Educational';
                        break;

                    case 'name':
                        $description = 'Individuals, by name';
                        break;
                    }

                    if (empty($tld) || empty($description)) {
                        continue;
                    }

                    $regex = '{(^(?:Reserved|Restricted)\s*(?:exclusively\s*)?'
                        . '(?:for|to)\s*(?:members of\s*)?(?:the|support)?'
                        . '\s*|\s*as advised.*$)}i';
                    $description = preg_replace($regex, '', $description);
                    $description = ucfirst(trim($description));

                    $data = array_map(
                        'html_entity_decode', array(
                        'tld' => $tld,
                        'type' => $type,
                        'description' => $description
                        )
                    );

                    $insert->execute($data);
                }
            }
        }

        $fixedTlds = array(
            'phergie' => 'You can find Phergie at http://www.phergie.org',
            'spoon' => 'Don\'t you know? There is no spoon!',
            'poo' => 'Do you really think that\'s funny?',
            'root' => 'Diagnostic marker to indicate '
            . 'a root zone load was not truncated.'
        );

        foreach ($fixedTlds as $tld => $description) {
            $data = array(
                'tld' => $tld,
                'type' => 'fixed',
                'description' => $description
            );

            $insert->execute($data);
        }
    }

    /**
     * adds a fixed tld into the database
     *
     * @param array $args arguments passed to the method from the command line
     *                    [0] -> tld (required)
     *                    [1] -> description (required)
     *
     * @return null
     */
    public function addFixed($args)
    {
        $tld = array_shift($args);
        $description = array_shift($args);
        if (empty($tld) || empty($description)) {
            throw new Phergie_Db_Exception(
                'Required: tld description',
                Phergie_Db_Exception::ERR_KEYWORD_ARGS_INCORRECT
            );
        }

        $dbManager = new Phergie_Db_Sqlite($this->dbFile);
        $this->db = $dbManager->getDb();

        $tld = trim(ltrim($tld, '. '));
        $description = trim($description);

        $this->console("Adding $tld: $description");

        $tld = $this->db->quote($tld);
        $description = $this->db->quote($description);

        // if the tld already exists, then update.
        $update = false;
        $tlds = $this->db->query("SELECT * FROM tld WHERE tld=$tld")
            ->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($tlds) && count($tlds) > 0) {
            $row = $tlds[0];
            $this->console("{$row['tld']} already exists in the database.");
            $this->console("{$row['tld']} ({$row['type']}) {$row['description']}");
            $response = $this->promptConsole(
                "Do you want to overwrite {$row['tld']}? [y/N] "
            );
            if ($response != 'y' && $response != 'yes') {
                $this->console('Aborting.');
                return;
            }

            $update = true;
        }

        if ($update) {
            $query = "UPDATE tld SET type='fixed', description=$description "
                . "WHERE tld=$tld";
        } else {
            $query = 'INSERT INTO tld (tld, type, description) VALUES ('
                . join(',', array($tld, "'fixed'", $description))
                . ')';
        }

        if ($this->db->exec($query)) {
            $this->console('Added successfully.');
        } else {
            $this->console('There was an error adding the tld.');
        }
    }

    /**
     * removes a tld from the database
     *
     * @param array $args arguments passed to the method from the command line
     *                    [0] -> tld (required)
     *
     * @return null
     */
    public function removeTld($args)
    {
        $tld = array_shift($args);
        if (empty($tld)) {
            throw new Phergie_Db_Exception(
                'Required: tld',
                Phergie_Db_Exception::ERR_KEYWORD_ARGS_INCORRECT
            );
        }

        $dbManager = new Phergie_Db_Sqlite($this->dbFile);
        $this->db = $dbManager->getDb();

        $tld = trim(ltrim($tld, '. '));

        if ($this->db->exec('DELETE FROM tld WHERE tld=' . $this->db->quote($tld))) {
            $this->console("Successfully deleted $tld");
        } else {
            $this->console("Error deleting $tld");
        }
    }

}


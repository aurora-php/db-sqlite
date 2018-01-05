<?php

/*
 * This file is part of the 'octris/db-sqlite' package.
 *
 * (c) Harald Lapp <harald@octris.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Octris\Db\Device\Sqlite;

/**
 * SQLite connection handler.
 *
 * @copyright   copyright (c) 2012-2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Connection extends \SQLite3 implements \Octris\Db\Device\ConnectionInterface
{
    /**
     * Device the connection belongs to.
     *
     * @type    \Octris\Db\Device\Sqlite
     */
    protected $device;

    /**
     * Constructor.
     *
     * @param   \Octris\Db\Device\Sqlite            $device             Device the connection belongs to.
     * @param   array                               $options            Connection options.
     */
    public function __construct(\Octris\Db\Device\Sqlite $device, array $options)
    {
        $this->device = $device;

        parent::__construct($options['file'], $options['flags'], $options['key']);
    }

    /**
     * Release connection.
     */
    public function release()
    {
        $this->device->release($this);
    }

    /**
     * Check connection.
     *
     * @return  bool                                            Returns true if the connection is alive.
     */
    public function isAlive()
    {
        return true;
    }

    /**
     * Resolve a database reference.
     *
     * @todo    Implementation.
     * @param   \Octris\Db\Type\DbRef                          $dbref      Database reference to resolve.
     * @return  \Octris\Db\Device\Sqlite\DataObject|bool                   Data object or false if reference could not he resolved.
     */
    public function resolve(\Octris\Db\Type\DbRef $dbref)
    {
        return false;
    }

    /**
     * Return list of collections.
     *
     * @return  array|bool                                      Array of names of collections or false in case
     *                                                          of an error.
     */
    public function getCollections()
    {
        $sql = 'SELECT  *
                FROM    sqlite_master
                WHERE   type="table"';

        $result = $this->query($sql);
        $return = array();

        foreach ($result as $row) {
            $return[] = $row['name'];
        }

        return $return;
    }

    /**
     * Return instance of collection object.
     *
     * @param   string          $name                               Name of collection to return instance of.
     * @return  \Octris\Db\Device\Sqlite\Collection                 Instance of sqlte collection.
     */
    public function getCollection($name)
    {
        return new \Octris\Db\Device\Sqlite\Collection(
            $this->device,
            $this,
            $name
        );
    }

    /**
     * Initialize prepared statement.
     *
     * @param   string                      $sql                SQL statement to use as prepared statement.
     * @return  \Octris\Db\Sqlite\Statement                     Instance of prepared statement.
     */
    public function prepare($sql)
    {
        $stmt = parent::prepare($sql);

        return new \Octris\Db\Device\Sqlite\Statement($this->device, $stmt);
    }

    /**
     * Execute a SQL query.
     *
     * @param   string                      $sql                SQL statement to execute.
     * @return  \Octris\Db\Sqlite\Result                        Instance of result class.
     */
    public function query($sql)
    {
        $result = parent::query($sql);

        return new \Octris\Db\Device\Sqlite\Result($this->device, $result);
    }
}

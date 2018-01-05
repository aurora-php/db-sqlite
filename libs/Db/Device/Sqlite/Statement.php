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
 * SQLite prepared statement.
 *
 * @copyright   copyright (c) 2012-2018 by Harald Lapp
 * @author      Harald Lapp <harald@octris.org>
 */
class Statement
{
    /**
     * Instance of device.
     *
     * @type    \Octris\Db\Device\Sqlite
     */
    protected $device;

    /**
     * Instance of prepared statement.
     *
     * @type    \SQLite3Stmt
     */
    protected $instance;

    /**
     * Parameter types.
     *
     * @type    array
     */
    protected static $types = array(
        'i' => SQLITE3_INTEGER,
        'd' => SQLITE3_FLOAT,
        's' => SQLITE3_TEXT,
        'b' => SQLITE3_BLOB
    );

    /**
     * Constructor.
     *
     * @param   \Octris\Db\Device\Sqlite            $device         Instance of device.
     * @param   \SQLite3                            $link           Database connection.
     */
    public function __construct(\Octris\Db\Device\Sqlite $device, \SQLite3Stmt $link)
    {
        $this->device   = $device;
        $this->instance = $link;
    }

    /**
     * Returns number of parameters in statement.
     *
     * @return  int                                 Number of parameters.
     */
    public function paramCount()
    {
        return $this->instance->paramCount();
    }

    /**
     * Bind parameters to statement.
     *
     * @param   string          $types              String of type identifiers.
     * @param   array           $values             Array of values to bind.
     */
    public function bindParam($types, array $values)
    {
        if (preg_match('/[^idsb]/', $types)) {
            throw new \Exception('unknown data type in "' . $types . '"');
        } elseif (strlen($types) != ($cnt1 = count($values))) {
            throw new \Exception('number of specified types and values does not match');
        } elseif ($cnt1 != ($cnt2 = $this->paramCount())) {
            throw new \Exception(
                sprintf(
                    'number of specified parameters (%d) does not match required parameters (%d)',
                    $cnt1,
                    $cnt2
                )
            );
        } else {
            for ($i = 0, $len = strlen($types); $i < $len; ++$i) {
                $this->instance->bindParam(
                    $i + 1,
                    $values[$i],
                    (is_null($values[$i]) ? SQLITE3_NULL : self::$types[$types[$i]])
                );
            }
        }
    }

    /**
     * Execute the prepared statement.
     *
     * @return  \Octris\Db\Device\Sqlite\Result                Instance of result class.
     */
    public function execute()
    {
        $result = $this->instance->execute();

        return new \Octris\Db\Device\Sqlite\Result($this->device, $result);
    }
}

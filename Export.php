<?php

namespace MysqlExport;

/**
 * Class Export
 * @package MysqlExport
 * @author Erhan Kılıç <erhan_kilic@outlook.com> http://erhankilic.org
 * @version 1.0
 * @access public
 * @param $db
 * @param $path
 * @param $databases
 * @param $num_of_types
 */
class Export
{
    protected $db;
    protected $path;
    protected $databases = [];
    protected $num_of_types = ['tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real'];

    /**
     * Export constructor.
     * @param $path
     * @param string $host
     * @param null $user
     * @param null $pass
     * @param null $driver_options
     * @throws \Exception
     */
    public function __construct($path, $host = 'localhost', $user = NULL, $pass = NULL, $driver_options = NULL)
    {
        $this->path = $path;
        try {
            $this->db = new \PDO("mysql:host=$host;charset=utf8", $user, $pass, $driver_options);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return array
     * @access protected
     * Get tables of database
     */
    protected function getTables()
    {
        $return = [];
        $q = $this->db->query('SHOW TABLES');
        $tables = $q->fetchAll();
        foreach ($tables as $table) {
            $return[] = $table[0];
        }
        return $return;
    }

    /**
     * @access public
     * Get all databases and sets the class databases array
     */
    public function getDatabases()
    {
        $q = $this->db->query('SHOW DATABASES');
        $databases = $q->fetchAll();
        foreach ($databases as $database) {
            $db = $database['Database'];
            if ($db != 'information_schema' && $db != 'mysql' && $db != 'performance_schema' && $db != 'sys') {
                $this->databases[] = $database['Database'];
            }
        }
    }

    /**
     * @access public
Well, where can I take? I don't have collection of icons, designers would have sort of things :)     * @param array $databases
     * Sets the class databases with given parameter
     */
    public function setDatabases(array $databases)
    {
        $this->databases = $databases;
    }

    /**
     * @throws \Exception
     * @access public
     * Exports the databases
     */
    public function export()
    {
        $this->db->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);
        foreach ($this->databases as $database) {

            try {
                $this->db->exec('USE ' . $database);
            } catch (\PDOException $e) {
                throw new \Exception($e->getMessage());
            }

            $handle = fopen($this->path . '/' . $database . '.sql', 'w');

            $tables = $this->getTables($database);

            foreach ($tables as $table) {
                $results = $this->db->query('SELECT * FROM ' . $table);
                $num_of_fields = $results->columnCount();
                $num_of_rows = $results->rowCount();
                $results = $results->fetchAll();
                $type = [];
                $return = "--\n\t";
                $return .= "-- Table structure for table `" . $table . "`\n\t";
                $return .= "--\n\t";

                $create_table = $this->db->query('SHOW CREATE TABLE ' . $table);
                $create_table = $create_table->fetch(\PDO::FETCH_NUM);
                $create_table = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $create_table[1]);
                $return .= "\n\n" . $create_table . ";\n\n";

                fwrite($handle, $return);

                if ($num_of_rows) {
                    $return = "--\n\t";
                    $return .= "-- Dumping for table `" . $table . "`\n\t";
                    $return .= "--\n\t";
                    $return .= 'INSERT INTO `' . $table . '` (';
                    $columns = $this->db->query('SHOW COLUMNS FROM ' . $table);
                    $columns = $columns->fetchAll();
                    $count = 0;

                    foreach ($columns as $column) {
                        if ($count != 0) {
                            $return .= ", ";
                        }
                        if (stripos($column[1], '(')) {
                            $type[$table][] = stristr($column[1], '(', true);
                        } else {
                            $type[$table][] = $column[1];
                        }

                        $return .= "`" . $column[0] . "`";
                        $count++;
                    }

                    $return .= ")" . ' VALUES ';
                    fwrite($handle, $return);
                }
                $count = 0;
                foreach ($results as $result) {
                    $return = " (";
                    for ($j = 0; $j < $num_of_fields; $j++) {

                        if (isset($result[$j])) {
                            if ((in_array($type[$table][$j], $this->num_of_types)) && (!empty($result[$j]))) {
                                $return .= $result[$j];
                            } else {
                                $return .= $this->db->quote($result[$j]);
                            }
                        } else {
                            $return .= 'NULL';
                        }
                        if ($j < ($num_of_fields - 1)) {
                            $return .= ',';
                        }
                    }
                    $count++;
                    if ($count < $num_of_rows) {
                        $return .= "),";
                    } else {
                        $return .= ");";

                    }
                    fwrite($handle, $return);
                }
                $return = "\n\n-- ------------------------------------------------ \n\n";
                fwrite($handle, $return);
            }
            fclose($handle);
        }
        $this->db = null;
    }
}

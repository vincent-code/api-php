<?php

namespace Database;

use PDO;
use Database\DBConnector as DBConnector;
//use DB;

class DBHelper extends DBConnector
{

    /**
     * Get all column names from table
     *
     * @param string $table
     * @return array
     */
    public static function getColumnNames($table)
    {
        $dbh = DB::connect();

        $sql = "SHOW COLUMNS FROM $table";

        try {
            $sth = $dbh->query($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $result = [];

        while ($row = $sth->fetch()) {
            array_push($result, $row['Field']);
        }

        return $result;
    }

    /**
     * Get all rows from table
     *
     * @param string $table
     * @return array
     */
    public function getList($table)
    {
        $columnList = self::getColumnNames($table);

        $sql = "SELECT * FROM $table";

        try {
            $sth = $this->dbh->query($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $result = [];

        $i = 0;
        while($row = $sth->fetch()) {
            foreach ($columnList as $column) {
                $result[$i][$column] = $row[$column];
            }
            $i++;
        }

        return $result;
    }

    /**
     * Get item from table by column value
     *
     * @param string $table
     * @param string $column
     * @param string $value
     * @return array
     */
    public function getItem($table, $column, $value)
    {
        $sql = "SELECT * FROM $table WHERE $column = :value";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':value', $value, PDO::PARAM_STR);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $sth->fetch();
    }

    /**
     * Delete item from table by column value
     *
     * @param string $table
     * @param string $column
     * @param string $value
     * @return void
     */
    public function deleteItem($table, $column, $value)
    {
        $sql = "DELETE FROM $table WHERE $column = :value";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':value', $value, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Get the total number of rows in the table
     *
     * @param string $table
     * @return int
     */
    public function count($table)
    {
        $sql = "SELECT count(*) as total FROM $table";

        try {
            $sth = $this->dbh->query($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $result = $sth->fetch();

        return $result["total"];
    }

}

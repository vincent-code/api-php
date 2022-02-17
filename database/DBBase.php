<?php

namespace Database;

use PDO;

trait DBBase
{

    /**
     * Get item from table by id value
     *
     * @param string $table
     * @param string $id
     * @return array
     */
    public function getItemById($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = :id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_STR);
            $sth->execute();
            $sth->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $sth->fetch();
    }

}
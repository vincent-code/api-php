<?php

namespace Models;

use PDO;
use Database\DBConnector as DBConnector;
use Database\DBBase as DBBase;

class Tag extends DBConnector
{
    use DBBase;

    private $table = 'tag';

    public function getTableName()
    {
        return $this->table;
    }

    public function index($data)
    {
        if ($data != '') {
            $dataArray = explode('&', $data);
            $params = [];
            foreach ($dataArray as $item) {
                $itemArray = explode('=', $item);
                $params[$itemArray[0]] = $itemArray[1];
            }
        }

        $sql = "SELECT t.*,
                    (SELECT count(*)
                    FROM xref_help_tag ht
                    WHERE ht.tag_id = t.id) as count
                FROM tag t";

        if (isset($params['name']) && $params['name'] != '') {
            $sql .= ' WHERE t.name LIKE :tag1';
        }

        $sql .= ' ORDER BY count DESC';

        try {
            $sth = $this->dbh->prepare($sql);

            if (isset($params['name']) && $params['name'] != '')
                $sth->bindValue(':tag1', '%' . $params['name'] . '%', PDO::PARAM_STR);

            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $result = [];

        $i = 0;
        while($row = $sth->fetch()) {
            $result[$i]['id'] = $row['id'];
            $result[$i]['name'] = $row['name'];
            $result[$i]['count'] = $row['count'];
            $i++;
        }

        return $result;
    }

    public function create($data)
    {
        $sql = "INSERT INTO tag (name) VALUES (:name)";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':name', $data, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this->dbh->lastInsertId();
    }

    public function checkExists($name)
    {
        $sql = "SELECT EXISTS(SELECT *
                FROM tag
                WHERE name = :name) as exist";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    public function createXrefHelpTag($help_id, $tag_name)
    {
        $sql = "INSERT INTO xref_help_tag (help_id, tag_id)
                VALUES (:help_id, (SELECT id 
                                    FROM tag 
                                    WHERE name = :tag_name)) ";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':help_id', $help_id, PDO::PARAM_INT);
            $sth->bindParam(':tag_name', $tag_name, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function deleteXrefHelpTag($help_id)
    {
        $sql = "DELETE FROM xref_help_tag WHERE help_id = :help_id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':help_id', $help_id, PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function delete($name)
    {
        $sql = "DELETE FROM tag WHERE name = :name";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':name', $name, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return 'delete success';
    }
}

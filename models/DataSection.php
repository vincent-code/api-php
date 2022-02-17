<?php

namespace Models;

use PDO;
use Database\DBConnector as DBConnector;
use Database\DBBase as DBBase;

class DataSection extends DBConnector
{
    use DBBase;

    private $table = 'help_section';

    public function getTableName()
    {
        return $this->table;
    }

    public function index()
    {
        $sql = "SELECT t.*,
                    (SELECT count(*)
                    FROM help 
                    WHERE help_section_id = t.id) as count 
                FROM help_section t 
                ORDER BY rating DESC";

        try {
            $sth = $this->dbh->query($sql);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $result = [];

        $i = 0;
        while($row = $sth->fetch()) {
            $result[$i]['id'] = $row['id'];
            $result[$i]['name'] = $row['name'];
            $result[$i]['icon'] = $row['icon'];
            $result[$i]['count'] = $row['count'];
            $result[$i]['rating'] = $row['rating'] * 1;
            $i++;
        }

        return $result;
    }

    public function view($data)
    {
        $data = json_decode($data);

        $sql = "SELECT h.*, hs.name as section
            FROM help h, help_section hs 
            WHERE h.id = :id 
            AND h.help_section_id = hs.id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':id', $data, PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $sth->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO help_section (name) VALUES (:name)";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':name', $data, PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this->dbh->lastInsertId();
    }

    public function update($data)
    {
        $data = (array)json_decode($data);

        $sql = "UPDATE help_section 
                SET name = :name,
                    icon = :icon,
                    rating = :rating
                WHERE id = :id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':id', $data['id'], PDO::PARAM_INT);
            $sth->bindParam(':name', $data['section'], PDO::PARAM_STR);
            $sth->bindParam(':icon', $data['icon'], PDO::PARAM_STR);
            $sth->bindParam(':rating', $data['rating'], PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return 'update success';
    }

    public function delete($id)
    {
        $sql = "DELETE FROM help_section WHERE id = :id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return 'delete success';
    }
}

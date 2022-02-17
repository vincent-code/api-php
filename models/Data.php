<?php

namespace Models;

use PDO;
use Database\DBConnector as DBConnector;
use Database\DBBase as DBBase;

class Data extends DBConnector
{
    use DBBase;

    private $table = 'help';

    public function getTableName()
    {
        return $this->table;
    }

    /**
     * Получаем все записи одной секции по ее названию
     * @param $data
     * @return array
     */
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

        $sql = "SELECT 
                    t1.id,
                    t1.problem, 
                    t1.solution,
                    t1.code,
                    t1.section,
                    GROUP_CONCAT(distinct t1.tag) as tags
                FROM (SELECT h.*, t.name as tag, hs.name as section
                FROM help h
                LEFT JOIN xref_help_tag ht ON h.id = ht.help_id
                LEFT JOIN tag t ON t.id = ht.tag_id
                LEFT JOIN help_section hs ON hs.id = h.help_section_id) t1
                WHERE problem IS NOT NULL";

        if (isset($params['name']) && $params['name'] != '') {
            $sql .= " AND section = :name";
        }

        if (isset($params['tag']) && $params['tag'] != '') {
            $tagArr = explode(',', $params['tag']);
            foreach ($tagArr as $tagItem) {
                $sql .= " AND EXISTS (SELECT *
                                    FROM xref_help_tag
                                    WHERE tag_id = (SELECT id
                                                    FROM tag
                                                    WHERE name = '$tagItem')
                                    AND help_id = t1.id)";
            }

        }

        if (isset($params['search']) && $params['search'] != '') {
            $params['search'] = urldecode($params['search']);
            $sql .= " AND problem LIKE :search";
        }

        $sql .= " GROUP BY id, problem, solution, code 
                  ORDER BY id DESC";

        try {
            $sth = $this->dbh->prepare($sql);

            if (isset($params['name']) && $params['name'] != '')
            $sth->bindParam(':name', $params['name'], PDO::PARAM_STR);

            if (isset($params['search']) && $params['search'] != '')
            $sth->bindValue(':search', '%' . $params['search'] . '%', PDO::PARAM_STR);

            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        $result = [];

        if ($sth->rowCount() > 0) {
            $i = 0;
            while($row = $sth->fetch()) {
                $result[$i]['id'] = $row['id'];
                $result[$i]['problem'] = $row['problem'];
                $result[$i]['solution'] = nl2br($row['solution']);
                $result[$i]['code'] = $row['code'];
                $result[$i]['section'] = $row['section'];
                $result[$i]['tags'] = $row['tags'];
                $i++;
            }
        }

        return $result;
    }

    /**
     * Получаем одну запись по id
     * @param $data
     * @return mixed
     */
    public function view($data)
    {
        $data = json_decode($data);

        $sql = "SELECT 
                    t1.id,
                    t1.problem, 
                    t1.solution,
                    t1.code,
                    t1.section,
                    t1.help_section_id,
                    GROUP_CONCAT(distinct t1.tag) as tags
                FROM (SELECT h.*, t.name as tag, hs.name as section
                FROM help h
                LEFT JOIN xref_help_tag ht ON h.id = ht.help_id
                LEFT JOIN tag t ON t.id = ht.tag_id
                LEFT JOIN help_section hs ON hs.id = h.help_section_id) t1
                WHERE id  = :id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':id', $data, PDO::PARAM_INT);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $data = (array)json_decode($data);
        $data['help_section_id'] = 1;

        $sql = "INSERT INTO help (help_section_id, problem, solution, code)
            VALUES (:section, :problem, :solution, :code)";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':section', $data['section'], PDO::PARAM_INT);
            $sth->bindParam(':problem', $data['problem'], PDO::PARAM_STR);
            $sth->bindParam(':solution', $data['solution'], PDO::PARAM_STR);
            $sth->bindParam(':code', $data['code'], PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $this->dbh->lastInsertId();
    }

    public function update($data, $id)
    {
        $data = (array)json_decode($data);

        $sql = "UPDATE help SET 
                    help_section_id = :section, 
                    problem = :problem, 
                    solution = :solution, 
                    code = :code
                WHERE id = :id";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindParam(':id', $id, PDO::PARAM_INT);
            $sth->bindParam(':section', $data['section'], PDO::PARAM_INT);
            $sth->bindParam(':problem', $data['problem'], PDO::PARAM_STR);
            $sth->bindParam(':solution', $data['solution'], PDO::PARAM_STR);
            $sth->bindParam(':code', $data['code'], PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return 'update success';
    }

    public function delete($id)
    {
        $sql = "DELETE FROM help WHERE id = :id";

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

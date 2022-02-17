<?php

namespace Models;

use PDO;
use Database\DBConnector as DBConnector;

class User extends DBConnector
{

    public function index($data)
    {
        $sql = "SELECT password, login
                FROM user
                WHERE login = :login";

        try {
            $sth = $this->dbh->prepare($sql);
            $sth->bindValue(':login', $data['login'] , PDO::PARAM_STR);
            $sth->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

        return $sth->fetch(PDO::FETCH_ASSOC);
    }

}

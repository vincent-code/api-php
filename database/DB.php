<?php

namespace Database;

use PDO;

class DB
{
    /**
     * Сreate database connection
     *
     * dsn - data source name
     * dbh - database handle
     * sth - statement handle
     *
     * @return string
     */
    public static function connect()
    {
        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8";
        try {
            $dbh = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
    }
}
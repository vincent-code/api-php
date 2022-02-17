<?php

namespace Database;

class DBConnector
{

    /**
     * DB connection string
     *
     * @var string
     */
    protected $dbh;

    /**
     * Create a new database connection string
     *
     * @return void
     */
    function __construct()
    {
        $this->dbh = DB::connect();
    }

}

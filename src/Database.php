<?php


namespace App;

class Database
{
    /**
     * @var string
     */
    private $database_name;

    /**
     * @var mixed
     */
    private $connection;

    public function __construct($database)
    {
        $connection = new \mysqli("localhost", "root", "root");
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }
        $query = "CREATE DATABASE IF NOT EXISTS " . $database;
        if ($connection->query($query) === TRUE) {
            //echo "Database created successfully with the name $database";
        } else {
            echo "Error with creating: " . $connection->error;
        }
        $query = "CREATE TABLE IF NOT EXISTS ". $database ." (
                id INT(6) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(300) NOT NULL,
                link VARCHAR(200) NOT NULL,
                validity INT NOT NULL,
                sale_end INT NOT NULL,
                image_src VARCHAR(200) NOT NULL
                ) DEFAULT CHARSET=utf8;";
        $connection->connect("localhost", "root", "root", $database);
        if ($connection->query($query) === TRUE) {

        } else {
            echo $connection->error;
        }

        $this->connection = $connection;
        $this->database_name = $database;
    }

    /**
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->database_name;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
<?php

/*
Класс взял из старого проекта:
https://github.com/Winicred/PizzaProject/blob/master/inc/database.php

Убрал лишние методы и добавил метод для получения конфигурации сайта
*/

class Database
{

    // переменные для подключения к базе данных
    private PDO $conn;
    private string $host;
    private string $user;
    private string $password;
    private string $database_name;

    // конструктор класса
    function __construct()
    {
        $this->host = '31.31.196.43';
        $this->user = 'u1636113_default';
        $this->password = '8t1vmKn49PY3uN6r';
        $this->database_name = 'u1636113_titify';
        $this->connect();
    }

    // метод для подключения к базе данных
    public function connect(): PDO
    {
        try {

            // создание нового объекта PDO и передача ему параметров подключения
            $this->conn = new PDO(
                'mysql:host=' . $this->host
                . ';dbname=' . $this->database_name
                ,
                $this->user,
                $this->password,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
        } catch (Exception $e) {

            // вывод ошибки подключения к базе данных
            die('Connection error: ' . $e->getMessage());
        }

        // возвращаем объект PDO
        return $this->conn;
    }

    // метод для отключения от базы данных
    public function __destruct()
    {
        unset($this->conn);
    }

    // метод для получения конфигурации сайта
    public function get_config_data()
    {
        $db_response = $this->conn->query("SELECT * FROM config LIMIT 1");
        $db_response->setFetchMode(PDO::FETCH_OBJ);
        return $db_response->fetch();
    }
}
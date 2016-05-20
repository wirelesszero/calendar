<?php
namespace App\Models;

use PDO;
use PDOException;

class Model {

    private $mysqli;

	private $connection;

 	public function __construct()
    {
		$host = HOST;
		$db_name = DB;
		$username = USERNAME;
		$password = PASSWORD;

    	try {
		    $this->connection = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $exception){
		    echo "Connection error: " . $exception->getMessage();
		}
    }

    /**
     * Получить данные по sql-запросу
     * @param string $sql
     * @return array
     */
    public function getByQuery($sql)
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        // fetch all rows into an array.
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            $rows[] = $row;
        }
        unset($stmt);

        return $rows;
    }

    /**
     * Получить данные модели по id
     * @param string $id
     * @return array
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE id = " . $id;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        // fetch row into an array.
        $row = $stmt->fetch();
        unset($stmt);

        return $row;
    }

    /**
     * Получить все данные модели
     * @param string $id
     * @return array
     */
    public function getAll()
    {
        $sql = "SELECT * FROM " . static::$table;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        // fetch row into an array.
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            $rows[] = $row;
        }
        unset($stmt);

        return $rows;
    }

    /**
     * Создать модель в базе
     * @return string lastInsertId
     */
    public function create()
    {
    	$fields = implode(', ', array_keys($this->properties));

        $values = '';
        foreach ($this->properties as $key => $value) {
            $values .= ' :' . $key . ',';
        }
        $values = substr($values, 0, -1);

    	if ($this->properties) {
    		$stmt = $this->connection->prepare("INSERT INTO " .  static::$table . "($fields) VALUES( $values )");
			$stmt->execute($this->properties);
    	}
        unset($stmt);

        return $this->connection->lastInsertId();
    }

    /**
     * Установить параметры модели
     * @param string $name
     * @param string $value
     * @return null
     */
    public function __set($name, $value)
    {
    	$this->properties[$name] = $value;
    }

    /**
     * Получить параметр модели
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
		if (!key_exists($name, $this->properties)) {
			throw new PDOException("Undefined property $name", 1);
		}
		return $this->properties[$name];
    }
}

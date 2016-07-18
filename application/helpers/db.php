<?php
namespace helpers;
use \pdo;

 class Db {
	private $db;
    private static $_instance;
	private static $id;
	public  $tableCols;	//ключ name для имени	status для статуса
	const ERR = 'Неверный параметр входных данных, ошибка ';
	const ERRSET = 'Действие для set не задано или указано неверно.';
	const DBNAME = 'tst_rc';	//название БД 
	const HOST = 'localhost';	//по умолчанию localhost
	const USER = 'root';	//укажите имя пользователя для подключения к бд
	const PASWD = '';	//укажите пароль пользователя
	const TABLE = 'users1';	//используемая приложением таблица в бд 

    public function __construct() {
		$dsn = "mysql:host=".self::HOST.";dbname=".self::DBNAME.";";
		$options = array(
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		);
		try {
			$this->db = new PDO($dsn, self::USER, self::PASWD, $options);
		} catch (\PDOException $e) {
			print "Ошибка подключения к базе данных: " . $e->getMessage() . "<br/>";
			die('Проверьте настройки');
		}
	}
//создание таблицы
	public function createTable($tableCols){
		$col1 = Help::translit($tableCols[name], 'ruEn');	//транслит ru->en имени в базу
		$col2 = Help::translit($tableCols[status], 'ruEn');	//транслит ru->en статуса в базу
		$query = $this->db->prepare("CREATE TABLE ".self::TABLE." (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, ".$col1." VARCHAR(50),".
									$col2." ENUM('0', '1') NOT NULL)");
		$query->execute();
		return $this->getColumnsName(); //считываем названия колонок из соданной таблицы
	}
//вставляем данные в таблицу из csv файла
	public function insertRow($rowCsv){
		$rowCsv['name'] = addslashes($rowCsv['name']);	//экранируем ' и "	
		//INSERT INTO `users1` (name, status) VALUES(?,?);
		$query = $this->db->prepare("INSERT INTO ".self::TABLE." (".$this->tableCols['name'].", ".$this->tableCols['status'].") VALUES(?, ?)");		
		if (!($query->execute(array($rowCsv['name'], $rowCsv['status'])))) {
			echo "Не удалось выполнить запрос: (" . $query->errno . ") " . $query->error;
		}		
	}
//выбираем случайную строку из таблицы	
	public function selectOneRandomRow(){
		$query = $this->db->prepare("SELECT * FROM ".self::TABLE." ORDER BY RAND() LIMIT 1");
		$query->execute();
		$res = $query->fetchAll();
		return ($res) ? $res[0] : false;	//возвращаю первый элемент массива, чтобы потом легче было работать с массивом		
	}
//выбрать строку по конкретному id	
	public function selectRow($id){
		$query = $this->db->prepare("SELECT * FROM ".self::TABLE." WHERE id=".$id."");
		$query->execute();
		$res = $query->fetchAll();	
		return ($res) ? $res[0] : false;	//возвращаю первый элемент массива, чтобы потом легче было работать с массивом		
	}
//обновить Status в конкретной строке
	public function updateRow($row){	
		//UPDATE `users1` SET Status=? WHERE id=?
		$query = $this->db->prepare("UPDATE ".self::TABLE.
										" SET ".$this->tableCols['status']."='".$row['status']."' WHERE id=".$row['id']."");
		$query->execute();
	}
//получить названия колонок из базы	
	public function getColumnsName(){
		$query = $this->db->prepare("SHOW COLUMNS FROM ".self::TABLE." FROM ".self::DBNAME);
		$res = $query->execute();
		$res = $query->fetchAll();
		return [
				'name' => $res[1]['Field'],
				'status' => $res[2]['Field']	//возвращаю массив нужных значений
				];
	}
//проверить есть ли нужная таблица в базе	
	public function showTables(){
		$query = $this->db->prepare("SHOW TABLES");
		$res = $query->execute();
		$res = $query->fetchAll();
		$arrTables = [];
		foreach($res as $table){
			$arrTables[] = $table["Tables_in_".self::DBNAME];
		};
		return (in_array(self::TABLE, $arrTables)) ? true : false;
		/*if (in_array(self::TABLE, $arrTables)){
			return true;
		}else{
			
			return false;
		}
		*/
	}
}
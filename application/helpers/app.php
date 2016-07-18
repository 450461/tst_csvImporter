<?php
namespace helpers;

class App{

	public static $db;
	public static $csv;
	public static $table;

	public static function run(){
		App::$db = new Db();
		App::$csv = new Csv();
		App::$table = new Table();
	}

}
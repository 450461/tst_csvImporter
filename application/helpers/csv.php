<?php
namespace helpers;

class Csv
{
	const FILE = "Тестовое задание.csv";
	const ERR = "Файл не найден, ";

//получаем данные из CSV файла
    public  function getData()
    {
		$filePath = self::FILE; 
		$this->checkFile($filePath);	//проверяю существует ли указанный файл

		$file = fopen ( $filePath, 'r' );
		$csvArray=array();
		while (($result=fgetcsv($file, 2000, ';'))!==false){
			$result = [
							'name' => $result[0],
							'status' => $result[1]	//задаю названия ключей в массиве для колонок в файле
						];
			array_push($csvArray, $result);
		}
		fclose ( $file );
		return $csvArray;
    }
//прорка существования файла
	private function checkFile($filePath){
		if (file_exists($filePath )){
			return true;
		}else{
			 throw new \Exception(self::ERR.' '.self::FILE);
		}
	}
}
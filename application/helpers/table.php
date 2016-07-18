<?php
namespace helpers;

class Table
{
//проверяем таблицу, основной метод из которого вызываются все остальные	
	public function checkTable(){
		if (App::$db->showTables()){		//если таблица есть		
			App::$db->tableCols = App::$db->getColumnsName();	//считываем названия колонок, сохраняем в свойстве объекта $db
			return $this->getRow();			//получаем строку из таблицы и возвращаем результат 
		}else{			//если таблицы в базе нету
			$arrCsv = App::$csv->getData();	//достаем данные из CSV файла		
			App::$db->tableCols = App::$db->createTable($arrCsv[0]);
			/*создаем таблицу, передавая туда первый ключ полученного массива из CSV файла
				тоесть массив с названием колонок, потом там же обращаемся в таблицу, получаем названия 
					только что созданных колонок и заносим их в свойство объекта $db	*/			
			$arrCsv = array_slice($arrCsv, 1);	//удаляем первый ключ из "CSV массива", тоесть данные с названием колонок
			$this->insertAll($arrCsv);		//вставляем данные в базу
			return $this->getRow();		//получаем строку из таблицы и возвращаем результат			
		}
	}	
//получить одну случайную строку из таблицы	
    private function getRow(){
		$row = App::$db->selectOneRandomRow();	//получаем строку из базы
		$this-> reversStatus($row);	//изменяем значение статуса		
		return App::$db->selectRow($row['id']);	//получаем строку с измененным статусом, возвращаем результат
	}
//вставляем строки из массива в таблицу	
	private function insertAll($arrCsv){
		foreach ($arrCsv as $rowCsv){
			App::$db->insertRow($rowCsv);	//перебераем массив вставляя строки в таблицу
		}
	}
//изменение статуса в строке	
	private function reversStatus($row){
		$rowStatusName = App::$db->tableCols['status'];	//узнаем как называется колонка с нужным значением
		switch ($row[$rowStatusName]){
			case 0:
					$row['status'] = '1';	//меняем на новое значение
					App::$db->updateRow($row);	//обновляем это значение в базе
					break;
			case 1:	
					$row['status'] = '0';
					App::$db->updateRow($row);
					break;
		}
	}
}
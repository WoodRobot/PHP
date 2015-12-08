<?php
class P{
	protected $sql;
	//-------------------------------------
    function __construct() {
    $this -> sql = $this -> p_con();
    }
	//-------------------------------------
	function is($value){
		if(is_int($value)){
			return 'INT';
		}
		if(is_string($value)){
			return 'STRING';
		}
		if(is_array($value)){
			return 'ARRAY';
		}
		if(is_bool($value)){
			return 'BOOL';
		}
		if(is_null($value)){
			return 'STRING';
		}
		return '';
	}
	//-------------------------------------
	function safe(& $sql, $location, $var, $type){
		switch($type){
			default:
			case 'STRING':
			    $var = addslashes($var);
				$var = "'".$var."'";
			    break;
			case 'INTEGER':
			case 'INT':
			    $var = (int)$var;
			    break;
		 }// switch
		for ($i=1; $i <= $location; $i++){
			$pos = strpos($sql, '?');
		}// for
		$sql = substr($sql, 0, $pos) .$var .substr($sql, $pos+1);
	}// function safe --------------------------------------
	function p_con(){
    $dsn = 'mysql:host = localhost;dbname=test';
    $db = new PDO($dsn, 'root', 'root');
	$db -> beginTransaction();
	$db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //设置可捕获异常
	$db -> exec("SET NAMES 'UTF8'");
	return $db;
	}
	//-------------------------------------
	function p_add($db, $data = array()){
	$val = "INSERT INTO $db VALUES (";
	for($i = 0; $i < count($data); $i++){
    $val = $val."?,";
	}
	$val = substr($val,0,strlen($val)-1);
	$val = $val.")";
	for($i = 0; $i < count($data); $i++){
	$this -> safe($val, 1, $data[$i], $this -> is($data[$i]));
	}
	try{
	$res = $this -> sql -> exec($val);
	$this -> sql -> commit();
	return true;
	}catch(PDOexception $err){
	$this -> sql -> rollBack();
	return false;
	}
	}// function p_add --------------------------------------
	function p_del($db, $data = array()){
	$val = "DELETE FROM $db where ";
	for($i = 0; $i < count($data); $i++){
		if(@$times == 0){
		@$val = $val.$data[$i].' = ?&&';
		$times = 2;
		}
		$times--;
	}
    $val = substr($val,0,strlen($val)-2);
	for($i = 0; $i < count($data)/2; $i++){
    $this -> safe($val, 1, $data[$i+$i+1], $this -> is($data[$i+$i+1]));
	}
	try{
	$res = $this -> sql -> exec($val);
	$this -> sql -> commit();
	return true;
	}catch(PDOexception $err){
	$this -> sql -> rollBack();
	return false;
	}
	}// function p_del --------------------------------------
	function p_search($db, $data = array()){
	$val = "SELECT * FROM $db where ";
	for($i = 0; $i < count($data); $i++){
		if(@$times == 0){
		@$val = $val.$data[$i].' = ?&&';
		$times = 2;
		}
		$times--;
	}
    $val = substr($val,0,strlen($val)-2);
	for($i = 0; $i < count($data)/2; $i++){
    $this -> safe($val, 1, $data[$i+$i+1], $this -> is($data[$i+$i+1]));
	}
	try{
    $res = $this -> sql -> query($val) -> fetchAll(PDO::FETCH_ASSOC);
	$this -> sql -> commit();
	return $res;
	}catch(PDOexception $err){
	$this -> sql -> rollBack();
	return false;
	}
	}// function p_search --------------------------------------
	function p_edit($db,$set = array(),$data = array()){
	for($i = 0,$times = 0,$s = "UPDATE $db "; $i < count($set); $i++){
		if($times == 0){
		  if($i == 0){$s = $s.' SET '.$set[$i].' = ?,';}else{$s = $s.$set[$i].' = ?,';}
		$times = 2;
		}
		$times--;
	}
	$s = substr($s,0,strlen($s)-1);
	for($i = 0; $i < count($set)/2; $i++){
    $this -> safe($s, 1, $set[$i+$i+1], $this -> is($set[$i+$i+1]));
	}
	
	$val = $s." where ";
	for($i = 0; $i < count($data); $i++){
		if(@$times == 0){
		@$val = $val.$data[$i].' = ?&&';
		$times = 2;
		}
		$times--;
	}
	$val = substr($val,0,strlen($val)-2);
	for($i = 0; $i < count($data)/2; $i++){
    $this -> safe($val, 1, $data[$i+$i+1], $this -> is($data[$i+$i+1]));
	}

	try{
    $res = $this -> sql -> query($val);
	$this -> sql -> commit();
	return true;
	}catch(PDOexception $err){
	$this -> sql -> rollBack();
	return false;
	}
	}// function p_edit --------------------------------------

	
}//class End

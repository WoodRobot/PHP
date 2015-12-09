<?php
/**
* SQL安全操作类
*
* -------------------------
* 安全性还没有进行完善的测试哈
* -------------------------
*
* @description 统一采用数组来处理SQL语句的参数
* @author WoodRobot osmands@live.com
* @version 1.02
* @modify 更明确地数据库配置变量,新增p_sql方法可自由执行sql语句.
* -------------------------
* p_sql(1,2,3) 参数1:数组型数据,参数2:是否返回结果,参数三:是否事务处理
* 如果参数2为true返回数组:$con [],[]为SQL语句对应的返回结果,之后为返回结果内的数组内容
* 例如 print_r($con[0][0]) 打印第一条结果的第一个项目 
*/

class P{
	protected $sql;
	//配置数据库信息
	protected $user = 'root';
	protected $pwd = 'root';
	protected $db = 'test';
	// define -------------------------------------
	
    function __construct() {
    @$this -> sql = $this -> p_con();
    }
	// function safe construct-------------------------------------
	
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
	// function is -------------------------------------
	
	function safe($sql, $location, $var, $type, $b = false){
		switch($type){
			default:
			    $val = 'OUT';
				die();
			case 'STRING':
			    $var = addslashes($var);
			    $var = addcslashes($var,'(.)./.-.=.;.*');
				$var = "'".$var."'";
			    break;
			case 'INTEGER':
			    $var = (int)$var;
			    break;
			case 'ARRAY':
			    $val = 'OUT';
				die();
			case 'BOOL':
			    $val = 'OUT';
				die();
			case 'INT':
			    $var = (int)$var;
			    break;
		 }// switch
		if($b == true){
			 return $var;
		}
		for ($i=1; $i <= $location; $i++){
			$pos = strpos($sql, '?');
		}// for
		$sql = substr($sql, 0, $pos) .$var .substr($sql, $pos+1);
		return $sql;
	}
	// function safe --------------------------------------
	
	function p_con(){
    $dsn = 'mysql:host = localhost;dbname='.$this -> db;
    $db = new PDO($dsn, $this -> user , $this -> pwd);
	$db -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //设置可捕获异常
	$db -> query("SET NAMES 'UTF8'");
	return $db;
	}
	// function con -------------------------------------
	
	function p_sql($value = array(),$return = false,$sw = true){
	if(empty($value)){
    return false;
	}
	$u = count($value);
	if($sw == true){
	@$this -> sql -> beginTransaction();
	}

    
	if($return == false){
		
		try{
			for ($m=0; $m<$u; $m++){
				$j = substr_count($value [$m],'{');
				for ($i=1; $i <= $j; $i++){
    		    	$o = strpos($value [$m], '}')+1;
					$p1 = strpos($value [$m], '{')+1;
					$p2 = $o - $p1 - 1;
					$head =  substr($value [$m], 0, $p1-1);
					$foot =  substr($value [$m],  $o, strlen($value [$m]));
					$val =  substr($value [$m], $p1, $p2);
					$var = $this -> safe('','',$val,$this -> is($val),true);
					$value [$m]= $head.$var.$foot;
				}// for
			$res = @$this -> sql -> query($value [$m]);
			}
	    if($sw == true){@$this -> sql -> commit();}
	    return true;
	    }catch(PDOexception $err){
			if($sw == true){@$this -> sql -> rollBack();}
	        return false;
	    }
		
	}else{
		try{
			for ($m=0; $m<$u; $m++){
				$j = substr_count($value [$m],'{');
				for ($i=1; $i <= $j; $i++){
    		    	$o = strpos($value [$m], '}')+1;
					$p1 = strpos($value [$m], '{')+1;
					$p2 = $o - $p1 - 1;
					$head =  substr($value [$m], 0, $p1-1);
					$foot =  substr($value [$m],  $o, strlen($value [$m]));
					$val =  substr($value [$m], $p1, $p2);
					$var = $this -> safe('','',$val,$this -> is($val),true);
					$value [$m]= $head.$var.$foot;
				}// for
			
			$res [$m]= @$this -> sql -> query($value [$m]) -> fetchAll(PDO::FETCH_ASSOC);
			}
	    if($sw == true){@$this -> sql -> commit();}
	    return $res;
	    }catch(PDOexception $err){
	        if($sw == true){@$this -> sql -> rollBack();}
	        return false;
	    }
		
	}
	
	}
	// function p_sql --------------------------------------
	
	function p_add($db, $data = array()){
	$val = "INSERT INTO $db VALUES (";
	for($i = 0; $i < count($data); $i++){
    $val = $val."?,";
	}
	$val = substr($val,0,strlen($val)-1);
	$val = $val.")";
	for($i = 0; $i < count($data); $i++){
		$sha1 = strpos($data[$i],'/safe');
		if($sha1){
			 $val = $this -> safe($val, 1,sha1($data[$i]), $this -> is($data[$i]));
		}else{
			 $val = $this -> safe($val, 1, $data[$i], $this -> is($data[$i]));
		}
	
	}
	try{
	$res = @$this -> sql -> query($val);
	return true;
	}catch(PDOexception $err){
	return false;
	}
	}
	// function p_add --------------------------------------
	
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
     $val = $this -> safe($val, 1, $data[$i+$i+1], $this -> is($data[$i+$i+1]));
	}
	try{
	$res = @$this -> sql -> query($val);
	return true;
	}catch(PDOexception $err){
	return false;
	}
	}
	// function p_del --------------------------------------
	
	function p_search($db, $data = array()){
	if(empty($data)){
	$val = "SELECT * FROM $db";
	$res = @$this -> sql -> query($val) -> fetchAll(PDO::FETCH_ASSOC);
	return $res;
	}
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
    $val =  $val = $this -> safe($val, 1, $data[$i+$i+1], $this -> is($data[$i+$i+1]));
	}
	try{
    $res = @$this -> sql -> query($val) -> fetchAll(PDO::FETCH_ASSOC);
	return $res;
	}catch(PDOexception $err){
	return false;
	}
	}
	// function p_search --------------------------------------
	
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
		$sha1 = strpos($set[$i+$i+1],'/safe');
		if($sha1){
			$this -> safe($s, 1,sha1($set[$i+$i+1]), $this -> is($set[$i+$i+1]));
		}else{
			$this -> safe($s, 1, $set[$i+$i+1], $this -> is($set[$i+$i+1]));
		}
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
			 $val = $this -> safe($val, 1, $data[$i+$i+1], $this -> is($data[$i+$i+1]));

	
	}
	try{
    $res = @$this -> sql -> query($val);
	return true;
	}catch(PDOexception $err){
	return false;
	}
	}
	// function p_edit --------------------------------------

	
}//class End

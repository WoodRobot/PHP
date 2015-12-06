<?php
class con{
	//-------------------------------------
    function __construct() {
    $sql = $this -> con_mysql();
    }
	//-------------------------------------
    private function safe($value,$t = true){
	$value=str_replace("_","\_",$value);
    $value=str_replace("%","\%",$value);
    $value=str_replace("&","\&",$value);
    $value=str_replace("&&","\&&",$value);
	if($t == true){
	$val = "'".str_replace('"', null, str_replace("'", null, $value))."'";
	}else{
	$val = str_replace('"', null, str_replace("'", null, $value));
	}
	
	return $val;
	}
	//-------------------------------------
	function con_mysql(){
	$con = mysqli_connect('localhost', 'root', 'a456321');
	mysqli_query($con, 'SET NAMES utf8');
	mysqli_select_db($con, 'sql_project');
	return $con;
	}
	//-------------------------------------
	function con_add($db, $data = array()){
	$sql = $this -> con_mysql();
	$dat = '';
		for($i = 0; $i < count($data); $i++){
		    $dat = $dat." '".$data[$i]."', ";
		}
	$dat = substr($dat,0,strlen($dat)-2); 
	$res = mysqli_query($sql, "INSERT INTO $db VALUES ($dat)");
	if($res != false){return true;}else{return false;};
	}
	//-------------------------------------
    function con_login($u, $p){
	$sql = $this -> con_mysql();	
	$user = $this -> safe($u);
	$pwd = $this -> safe($p);
	$res = mysqli_query($sql, "SELECT * FROM user where user = $user && pwd = $pwd");
	$row = @mysqli_fetch_array($res);
	if($u == $row ['user'] && $p == $row ['pwd']){return $row;}else{return false;};
    }
	//-------------------------------------
	function con_select($t, $w = array()){
	$table = $this -> safe($t,false);
	$dat = '';
	$times = 0;
	for($i = 0; $i < count($w); $i++){
	    
		if($times == 0){
		$dat = $dat.$this -> safe($w[$i],false).' = '.$this -> safe($w[$i+1]).'&&';
		$times = 2;
		}
		$times--;
			
	
	}
	$dat = substr($dat,0,strlen($dat)-2); 
	$sql = $this -> con_mysql();
	if($w == array()){
		$res = mysqli_query($sql, "SELECT * FROM $table");
	}else{
		$res = mysqli_query($sql, "SELECT * FROM $table where $dat");
	}
	
	return $res;
	}
	//-------------------------------------
	function con_check($name = false){
	$sql = $this -> con_mysql();	
	@$user = $_COOKIE['user'];
	@$pwd = $_COOKIE['pwd'];
	$res = mysqli_query($sql, "SELECT * FROM user where user = '$user' && pwd = '$pwd'");
	$row = mysqli_fetch_array($res);
	if($user == NULL || $pwd == NULL){return false;}
	if(@$_COOKIE['user'] == @$row ['user'] && @$_COOKIE['pwd'] == @$row ['pwd']){
		if($name == true){
		return $row ['user'];
		}else{return true;}
		
	}else{return false;}
    }
	//-------------------------------------
	function con_delete($t, $w = array()){
	$table = $this -> safe($t,false);
	$dat = '';
	$times = 0;
	for($i = 0; $i < count($w); $i++){
	    
		if($times == 0){
		$dat = $dat.$this -> safe($w[$i],false).' = '.$this -> safe($w[$i+1]).'&&';
		$times = 2;
		}
		$times--;
	}
	$dat = substr($dat,0,strlen($dat)-2); 
	$sql = $this -> con_mysql();
	$res = mysqli_query($sql, "DELETE FROM $table where $dat");
	if($res != false){return true;}else{return false;};
	
	}
	//-------------------------------------
	function con_edit($t, $s = array(), $w = array()){
	$table = $this -> safe($t,false);
	$sett = '';
	$times = 0;
	for($i = 0; $i < count($s); $i++){
	    
		if($times == 0){
		$sett = $sett.$this -> safe($s[$i],false).' = '.$this -> safe($s[$i+1]).',';
		$times = 2;
		}
		$times--;
	}
	$sett = substr($sett,0,strlen($sett)-1); //-
	
	$dat = '';
	$times = 0;
	for($i = 0; $i < count($w); $i++){
	    
		if($times == 0){
		$dat = $dat.$this -> safe($w[$i],false).' = '.$this -> safe($w[$i+1]).'&&';
		$times = 2;
		}
		$times--;
	}
	$dat = substr($dat,0,strlen($dat)-2); //-
	
	$sql = $this -> con_mysql();
	$res = mysqli_query($sql, "UPDATE $table set $sett where $dat");
	if($res != false){return true;}else{return false;};
	
	}
	
	

}//class End

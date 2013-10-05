<?PHP


class dbMySQL{

    var $result;
    
 
	function Connect($hostname, $username, $password){
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		return @mysql_connect($this->hostname, $this->username, $this->password) or die(mysql_errno());
	}


	function pConnect($hostname, $username, $password){
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		return @mysql_pconnect($this->hostname, $this->username, $this->password) or die(mysql_errno());
	}

	function dbCreate($database){
		$this->database = $database;
		return @mysql_create_db($this->database, $this->link_id);
	}


	function dbSelect($database){
		$this->database = $database;
		return @mysql_select_db($this->database);
	}

	function dbDrop($database, $conexao){
        $this->database = $database;
		$this->conexao  = $conexao;
        return @mysql_drop_db($this->database, $this->conexao);
	}


	function ChangeUser($username, $password, $conexao){
		$this->username = $username;
		$this->password = $password;
		$this->conexao  = $conexao;
		return @mysql_change_user($this->username, $this->password, $this->conexao);
	}

 
	function dbClose($conexao){
	    $this->conexao  = $conexao;
		return @mysql_close($this->conexao);
	}

	function Query($sql_query){
		$this->sql_query = $sql_query;
		$this->resultado = @mysql_query($this->sql_query);
//		$this->resultado = @mysql_query($this->sql_query);				
		return $this->resultado;
	}

	function dbQuery($database, $sql_query){
		$this->database  = $database;
		$this->sql_query = $sql_query;
		$this->resultado = @mysql_db_query($this->database, $this->sql_query) or die (mysql_errno());
		return $this->resultado;
	}

	function AffectedRows($conexao){
	    $this->conexao   = $conexao;
		return @mysql_affected_rows($this->conexao);
	}

     function DataSeek($resultado, $numero_da_linha){
		$this->resultado = $resultado;
	    $this->linha     = $numero_da_linha;
		return @mysql_data_seek($this->resultado, $this->linha);
	}

	function FetchArray($resultado){
	    $this->resultado = $resultado;
		return @mysql_fetch_array($this->resultado);
	}

	function FetchField($resultado, $deslocamento_do_campo){
        $this->resultado             = $resultado;
	    $this->deslocamento_do_campo = $deslocamento_do_campo;
		return @mysql_fetch_field($this->resultado, $this->deslocamento_do_campo);
	}

	function FetchLengths($resultado){
		$this->resultado = $resultado;
		return @mysql_fetch_lengths($this->resultado);
	}


	function FetchObject($resultado){
	    $this->resultado = $resultado;
		return @mysql_fetch_object($this->resultado);
	}


	function FetchRow($resultado){
	    $this->resultado = $resultado;
		return @mysql_fetch_row($this->resultado);
	}


	function FieldFlags($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_flags($this->resultado, $this->deslocamento);
	}

	function FieldLen($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_len($this->resultado, $this->deslocamento);
	}

 
	function FieldName($resultado, $indice_do_campo){
		$this->resultado = $resultado;
	    $this->indice    = $indice_do_campo;
		return @mysql_field_name($this->resultado, $this->indice);
	}

    function FieldSeek($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_name($this->resultado, $this->deslocamento_do_campo);
	}

	function FieldTable($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_seek($this->resultado, $this->deslocamento);
	}

  
	function FieldType($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_type($this->resultado, $this->deslocamento);
	}

	function ListDbs($resultado){
	    $this->resultado = $resultado;
		return @mysql_list_dbs($this->resultado);
	}

	function ListFields($database, $tabela, $resultado){
		$this->database  = $database;
		$this->tabela    = $tabela;
		$this->resultado = $resultado;
		return @mysql_list_fields($this->database, $this->tabela, $this->resultado);
	}

    
	function ListTables($database){
		$this->database = $database;
		return @mysql_list_tables($this->database);
	}

	function FreeResult($resultado){
	    $this->resultado = $resultado;
		return @mysql_free_result($this->resultado);
	}

  
	function InsertID($conexao){
	    $this->conexao = $conexao;
		return @mysql_insert_id($this->conexao);
	}

	function MyErrno($conexao){
		$this->conexao = $conexao;
	    return @mysql_errno($this->conexao);
	}


	function MyError3($conexao){
	    $this->conexao = $conexao;
		return @mysql_error($this->conexao);
	}

	function MyError(){
		return @mysql_error();
	}
	
	function MyResult($resultado, $id_do_campo, $id_da_coluna){
	    $this->resultado = $resultado;
		$this->id_campo  = $id_do_campo;
		$this->id_coluna = $id_da_coluna;
		return @mysql_result($this->resultado, $this->id_coluna, $this->id_campo);
	}

	function NumFields($resultado){
	    $this->resultado = $resultado;
		return @mysql_num_fields($this->resultado);
	}

	function NumRows($resultado){
	    $this->resultado = $resultado;
		return @mysql_num_rows($this->resultado);
	}
}
?>
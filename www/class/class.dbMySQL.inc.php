<?PHP
/*
=====================================================================
                            dbMySQL Class                           
=====================================================================
Nome:
	dbMySQL Class      - Classe de Conexão e Manipulação de dados

Autor:
	Rodrigo do N Maciel <rk_maciel@yahoo.com.br>

Descricao:
    Classe documentada de manipulacao de dados com MySQL
    Referencia de todas as funcoes PHP4/MySQL

Uso:
    Salve todo esse código (juntamente com este cabeçalho)
    em um arquivo chamado * class.dbMySQl.php * e o inclua 
    em seu diretório de include_path ou na raiz de seu site.
    *** No final do arquivo encontra-se um exemplo de como
        efetuar uma Conexão/Consulta com esta classe.

Report Bug:
	Por favor, reporte qualquer bugs encontrado para meu endereço de
	e-Mail : rk_maciel@yahoo.com.br.
	Se voce criar um Fix/Patch para este bug, envie para o e-Mail
	citado acima, para que eu possa incorporar neste script e com os
	devidos créditos.

Historico da Versao:
	1.0    03/08/2004  - Release Inicial
                      
=====================================================================
              Copyright (C) 2004  Rodrigo do N Maciel               
=====================================================================
*/


/**
 * @desc   Classe dbMySQL
 *         Metodos de acesso ao MySQL
 *
 * @author Rodrigo do N maciel <rk_maciel@yahoo.com.br>
 * @copyright   Copyright (C) 2004  Rodrigo do N Maciel
 *
 * @package     kernel
 * @subpackage  core
 */
class dbMySQL{

    var $result;
    
    /**
     * @desc Abre um link no host como o usuário especificado com a senha.
     *
     * @param  string $hostname             Hostname do MySQL
     * @param  string $username             Usuário do MySQL 
     * @param  string $password             Senha do Usuário do MySQL
     *
     * @return resource
     */
	function Connect($hostname, $username, $password){
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		return @mysql_connect($this->hostname, $this->username, $this->password) or die(mysql_errno());
	}


    /**
     * @desc Abre uma conexão persistente com o banco de dados. Todos os argumentos são opcionais.
     * Tenha cuidado - mysql_close e o término do script não fecharão a conexão.
     *
     * @param  string $hostname             Hostname do MySQL
     * @param  string $username             Usuário do MySQL 
     * @param  string $password             Senha do Usuário do MySQL
     *
     * @return resource
     */
	function pConnect($hostname, $username, $password){
		$this->hostname = $hostname;
		$this->username = $username;
		$this->password = $password;
		return @mysql_pconnect($this->hostname, $this->username, $this->password) or die(mysql_errno());
	}

	/**
     * @desc   Cria um novo Banco de Dados MySQL no host associado com o link aberto mais próximo.
     *
     * @param  string $database             Banco de Dados
     *
     * @return bool
     */
	function dbCreate($database){
		$this->database = $database;
		return @mysql_create_db($this->database, $this->link_id);
	}

	/**
     * @desc   Seleciona um novo Banco de Dados MySQL no host associado.
	 *
     * @param  string $database             Banco de Dados
     *
     * @return bool
     */
	function dbSelect($database){
		$this->database = $database;
		return @mysql_select_db($this->database);
	}

    /**
     * @desc   Exclui ("dropa") o banco de dados MySQL especificado.
     *
     * @param  string $database             Banco de Dados
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return bool
     */
	function dbDrop($database, $conexao){
        $this->database = $database;
		$this->conexao  = $conexao;
        return @mysql_drop_db($this->database, $this->conexao);
	}

	/**
     * @desc   Alterna o usuário do MySQL para um link aberto.
     *
     * @param  string $username             Usuário do MySQL
     * @param  string $password             Senha do Usuario do MySQL
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return bool
     */
	function ChangeUser($username, $password, $conexao){
		$this->username = $username;
		$this->password = $password;
		$this->conexao  = $conexao;
		return @mysql_change_user($this->username, $this->password, $this->conexao);
	}

    /**
     * @desc   Fecha o link identificado (normalmente desnecessário).
     * 
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return bool
     */
	function dbClose($conexao){
	    $this->conexao  = $conexao;
		return @mysql_close($this->conexao);
	}

    /**
     * @desc   Envia uma consulta ao banco de dados.
     *
     * @param  string $sql_query            String SQL Query
     *
     * @return resource
     */
	function Query($sql_query){
		$this->sql_query = $sql_query;
//		$this->resultado = @mysql_query($this->sql_query) or die (mysql_errno());
		$this->resultado = @mysql_query($this->sql_query);		
		return $this->resultado;
	}

    /**
     * @desc   Envia uma consulta ao banco de dados especifico.
     *
     * @param  string $database             Banco de Dados
     * @param  string $sql_query            String SQL Query
     *
     * @return bool
     */
	function dbQuery($database, $sql_query){
		$this->database  = $database;
		$this->sql_query = $sql_query;
		$this->resultado = @mysql_db_query($this->database, $this->sql_query) or die (mysql_errno());
		return $this->resultado;
	}

    /**
     * @desc   Utilize depois de uma consulta INSERT, UPDATE ou DELETE não para verificar o número de linhas alteradas.
     *
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return int
     */
	function AffectedRows($conexao){
	    $this->conexao   = $conexao;
		return @mysql_affected_rows($this->conexao);
	}

    /**
     * @desc   Move o ponteiro interno de linha para número especificado de linha.
     *         Utiliza uma função de busca para retornar os dados dessa linha.
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $numero_da_linha         Numero da Linha a ser encontrada
     *
     * @return bool
     */
     function DataSeek($resultado, $numero_da_linha){
		$this->resultado = $resultado;
	    $this->linha     = $numero_da_linha;
		return @mysql_data_seek($this->resultado, $this->linha);
	}

    /**
     * @desc   Busca conjunto de resultados como array associativo.
     * O tipo de resultado pode ser MYSQL_ASSOC, MYSQL_NUM ou MYSQL_BOTH (padrão).
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return array
     */
	function FetchArray($resultado){
	    $this->resultado = $resultado;
		return @mysql_fetch_array($this->resultado);
	}

    /**
     * @desc   Retorna informações sobre um campo como um objeto.
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $deslocamento_do_campo   Deslocamento do Campo
     *
     * @return object
     */
	function FetchField($resultado, $deslocamento_do_campo){
        $this->resultado             = $resultado;
	    $this->deslocamento_do_campo = $deslocamento_do_campo;
		return @mysql_fetch_field($this->resultado, $this->deslocamento_do_campo);
	}

    /**
     * @desc Retorna o comprimento de cada campo em um conjunto de resultados
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return array
     */
	function FetchLengths($resultado){
		$this->resultado = $resultado;
		return @mysql_fetch_lengths($this->resultado);
	}

    /**
     * @desc Busca o conjunto de resultados como um objeto.
     * O tipo de resultado pode ser MYSQL_ASSOC, MYSQL_NUM ou MYSQL_BOTH (padrão).
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return array
     */
	function FetchObject($resultado){
	    $this->resultado = $resultado;
		return @mysql_fetch_object($this->resultado);
	}


    /**
     * @desc   Busca o conjunto de rasultados como um array.
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return array
     */
	function FetchRow($resultado){
	    $this->resultado = $resultado;
		return @mysql_fetch_row($this->resultado);
	}


    /**
     * @desc Retorna flags associadas com campo enumerado. Ex.: NOT NULL, AUTO_INCREMENT, BINARY).
     * 
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $deslocamento_do_campo   Deslocamento do Campo
     *
     * @result string
     */	
	function FieldFlags($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_flags($this->resultado, $this->deslocamento);
	}

    /**
     * @desc   Retorna o comprimento do campo enumerado.
     * 
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $deslocamento_do_campo   Deslocamento do Campo
     * 
     * @return int
     */
	function FieldLen($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_len($this->resultado, $this->deslocamento);
	}

    /**
     * @desc   Retorna o nome do campo enumerao pelo indice
     * 
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $indice_do_campo         Indice do Campo
     *
     * @return string
     */
	function FieldName($resultado, $indice_do_campo){
		$this->resultado = $resultado;
	    $this->indice    = $indice_do_campo;
		return @mysql_field_name($this->resultado, $this->indice);
	}

    /**
     * @desc   Retorna o nome do campo enumerado. Utilizado com mysql_fetch_field.
     * 
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $deslocamento_do_campo   Deslocamento do Campo
     *
     * @return bool
     */
    function FieldSeek($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_name($this->resultado, $this->deslocamento_do_campo);
	}

    /**
     * @desc   Retorna o nome da tabela do campo especificado.
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $deslocamento_do_campo   Deslocamento do Campo
     *
     * @return string
     */
	function FieldTable($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_seek($this->resultado, $this->deslocamento);
	}

    /**
     * @desc   Retorna o tipo de deslocamento de campo. Ex.: TINYINT, BLOB, VARCHAR).
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $deslocamento_do_campo   Deslocamento do Campo
     *
     * @return string
     */
	function FieldType($resultado, $deslocamento_do_campo){
		$this->resultado    = $resultado;
	    $this->deslocamento = $deslocamento_do_campo;
		return @mysql_field_type($this->resultado, $this->deslocamento);
	}

    /**
     * @desc   Retorna ponteiro de resultados do banco de dados no mysql. Utilizado com mysql_table_name.
     * 
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return resource
     */
	function ListDbs($resultado){
	    $this->resultado = $resultado;
		return @mysql_list_dbs($this->resultado);
	}

    /**
     * @desc   Retorna o ID do resultado para utilização nas funções mysql_field, sem realizar uma consulta real.
     *
     * @param  string $database             Banco de Dados
     * @param  string $tabela               Tabela de Dados
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return resource
     */
	function ListFields($database, $tabela, $resultado){
		$this->database  = $database;
		$this->tabela    = $tabela;
		$this->resultado = $resultado;
		return @mysql_list_fields($this->database, $this->tabela, $this->resultado);
	}

    /**
     * @desc   Retorna ponteiro de resultado das tabelas no banco de dados. Utilizado com mysql_table_name.
     *
     * @param  string $database             Banco de Dados
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return 
     */
	function ListTables($database){
		$this->database = $database;
		return @mysql_list_tables($this->database);
	}

    /**
     * @desc   Libera memória utilizada pelo conjunto de resultados (normalmente desnecessário).
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     */
	function FreeResult($resultado){
	    $this->resultado = $resultado;
		return @mysql_free_result($this->resultado);
	}

    /**
     * @desc   Retorna o ID de AUTO_INCREMENTED de INSERT ou FALSE se a inserção falhou ou
     *         se a última consulta não era uma inserção.
     *
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return int
     */
	function InsertID($conexao){
	    $this->conexao = $conexao;
		return @mysql_insert_id($this->conexao);
	}

    /**
     * @desc   Retorna o ID de erro.
     *
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return int
     */
	function MyErrno($conexao){
		$this->conexao = $conexao;
	    return @mysql_errno($this->conexao);
	}

    /**
     * @desc   Retorna um mensagem de erro de texto.
     *
     * @param  resource $conexao            Resource #ID da Conexao
     *
     * @return string
     */

	function MyError($conexao){
	    $this->conexao = $conexao;
		return @mysql_error($this->conexao);
	}

    /**
     * @desc   Retorna resultado de campo único. O identificador de campo pode ser :
     *         deslocamento de campo (0);
     *         nome de campo (nome);
     *         nome de tabela de ponto (campo.tabela)
     *
     * @param  int $resultado               Resultado de uma SQL Query
     * @param  int $id_de_campo             ID do Campo
     * @param  int $id_da_coluna            ID da Coluna
     *
     * @return mixed
     */
	function MyResult($resultado, $id_do_campo, $id_da_coluna){
	    $this->resultado = $resultado;
		$this->id_campo  = $id_do_campo;
		$this->id_coluna = $id_da_coluna;
		return @mysql_result($this->resultado, $this->id_coluna, $this->id_campo);
	}

    /**
     * @desc   Retorna o número de de campos em um conjunto de resultados
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     *
     * @return int
     */
	function NumFields($resultado){
	    $this->resultado = $resultado;
		return @mysql_num_fields($this->resultado);
	}

	/**
	* @desc   Retorna o número de linhas em um conjunto de resultados.
	*
	* @param  resource $resultado           Resultado de uma SQL Query
	*
	* @return int
	*/
	function NumRows($resultado){
	    $this->resultado = $resultado;
		return @mysql_num_rows($this->resultado);
	}

    /**
     * @desc   Utilizado com qualquer função mysql_list para retornar o valor referenciado por um ponteiro de resultado.
     *
     * @param  resource $resultado          Resultado de uma SQL Query
     * @param  int $coluna                  ID da Coluna
     *
     * @return mixed
     */
	function TableName($resultado, $coluna){
        $this->resultado = $resultado;
	    $this->coluna    = $coluna;
		return @mysql_table_name($this->resultado, $this->coluna);
	}

}

/*
... Exemplo

require('class.dbMySQL.php');

$db = new dbMySQL();

$link_id = $db->Connect($DB_HOSTNAME, $DB_USERNAME, $DB_PASSWORD);

if ($link_id){
    $db->dbSelect($DB_DATABASE);
}

$rSet = $db->Query("SELECT * FROM login WHERE login.user='$user' AND login.pass='$senha'");

if ($db->NumRows($rSet) > 0){
    $row = $db->FetchObject($rSet);
    $_SESSION['user'] = $row->user;
    $_SESSION['nome'] = $row->nome;
    header("Location: pagina_dos_usuarios_autenticados.php");
}else{
    echo "Usuário/Senha inválidos";
}

... Fim de Exemplo
*/
?>
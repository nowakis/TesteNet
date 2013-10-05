<?

class BancodeDados {

	private $host;
	private $username;
	private $password;
	private $connection;
	private $squema;

	public function BancodeDados() {
		//$xml = simplexml_load_file($resource);

		global $_host;
		global $_user;
		global $_senha;
		global $_banco;

		$this->host     = $_host;
		$this->username = $_user;
		$this->password = $_senha;
		$this->squema   = $_banco;
	}

	public function conecta() {
		$this->connection = mysql_connect($this->host, $this->username,$this->password);
		mysql_select_db($this->squema,$this->connection);
		return $this->connection;
	}

	public function desconecta() {
		mysql_close($this->connection);
	}

	public function updateSQL($sql) {
		$r = mysql_query($sql,$this->connection);
		if($r == 0) {
			return false;
		} else {
			return true;
		}
	}

	public function executaSQL($sql) {
		$retorno = mysql_query($sql,$this->connection);
		return $retorno;
	}

	public function fetchArray($res) {
		$retorno = mysql_fetch_array($res);
		return $retorno;
	}

	public function numRows($res) {
		return @mysql_num_rows($res);
	}

	public function erroNumero() {
		return @mysql_errno($this->connection);
	}

	public function insert_id() {
		return @mysql_insert_id();
	}

	public function mysql_error() {
		return @mysql_error();
	}

	public function iniciarTransacao() {
		$this->executaSQL("begin");
	}

	public function efetivarTransacao() {
		$this->executaSQL("commit");
	}

	public function desfazerTransacao() {
		$this->executaSQL("rollback");
	}
}
?>
<?
class DAO { 

	public $banco; 
	public $_login_instituicao	= "";
	public $_login_professor		= "";
	public $_login_aluno		= "";

	public function setBancoDados(BancodeDados $banco) { 
		$this->banco = $banco;
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;
		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	public function getBancoDados() { 
		return $this->banco;
	}
}
?>
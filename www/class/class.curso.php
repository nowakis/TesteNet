<?

class Curso {

	private $curso					= "";
	private $instituicao			= "";
	private $nome					= "";

	public $Xcurso					= "";
	public $Xinstituicao			= "";
	public $Xnome					= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Curso(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->curso;
	}

	function setId($curso){
		$this->curso = $curso;
	}

	function getInstituicao(){
		return $this->instituicao;
	}

	function setInstituicao($instituicao){
		$this->instituicao = $instituicao;
	}

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
	}

}
?>
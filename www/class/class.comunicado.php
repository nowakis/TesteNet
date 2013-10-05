<?

class Comunicado {

	private $comunicado				= "";
	private $instituicao			= "";
	private $curso					= "";
	private $professor				= "";
	private $titulo					= "";
	private $data					= "";
	private $comentario				= "";
	private $obrigatorio			= "";

	public $Xcomunicado				= "";
	public $Xinstituicao			= "";
	public $Xcurso					= "";
	public $Xprofessor				= "";
	public $Xtitulo					= "";
	public $Xdata					= "";
	public $Xcomentario				= "";
	public $Xobrigatorio			= "";


	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Comunicado(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->comunicado;
	}

	function setId($comunicado){
		$this->comunicado = $comunicado;
	}

	function getInstituicao(){
		return $this->instituicao;
	}

	function setInstituicao($instituicao){
		$this->instituicao = $instituicao;
	}
	
	function getCurso(){
		return $this->curso;
	}

	function setCurso($curso){
		$this->curso = $curso;
	}
	
	function getProfessor(){
		return $this->professor;
	}

	function setProfessor($professor){
		$this->professor = $professor;
	}
	
	function getTitulo(){
		return $this->titulo;
	}

	function setTitulo($titulo){
		$this->titulo = $titulo;
	}
	
	function getData(){
		return $this->data;
	}

	function setData($data){
		$this->data = $data;
	}
	
	function getComentario(){
		return $this->comentario;
	}

	function setComentario($comentario){
		$this->comentario = $comentario;
	}
	
	function getObrigatorio(){
		return $this->obrigatorio;
	}

	function setObrigatorio($obrigatorio){
		$this->obrigatorio = $obrigatorio;
	}
}
?>
<?

class Disciplina {

	private $disciplina				= "";
	private $instituicao			= "";
	private $curso					= "";
	private $professor				= "";
	private $nome					= "";

	private $topicos				= array();
	private $topicos_nao_excluidos	= array();

	public $Xdisciplina				= "";
	public $Xinstituicao			= "";
	public $Xcurso					= "";
	public $Xprofessor				= "";
	public $Xnome					= "";

	public $Xtopicos				= array();
	public $Xtopicos_nao_excluidos	= array();

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Disciplina(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->disciplina;
	}

	function setId($disciplina){
		$this->disciplina = $disciplina;
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

	function getNome(){
		return $this->nome;
	}

	function setNome($nome){
		$this->nome = $nome;
	}


	/* DISCIPLINA X TOPICO */
	function addTopico($topico){
		array_push($this->topicos,$topico);
	}

	function getQtdeTopico(){
		return sizeof($this->topicos);
	}

	function getTopico($index){
		return $this->topicos[$index];
	}

	function addTopicoNaoExcluidos($topico){
		array_push($this->topicos_nao_excluidos,$topico);
	}

	function getTopicoNaoExcluidos(){
		if (count($this->topicos_nao_excluidos)>0){
			return implode(",",$this->topicos_nao_excluidos);
		}else{
			return "0";
		}
	}

}
?>
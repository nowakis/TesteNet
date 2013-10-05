<?
class Prova {

	private $prova					= "";
	private $titulo					= "";
	private $disciplina				= "";
	private $professor				= "";
	private $topicos				= array();
	private $numero_perguntas		= "";
	private $data					= "";
	private $data_inicio			= "";
	private $data_termino			= "";
	private $perguntas				= array();
	private $dificuldade			= "";
	private $liberada				= "";

	public $Xprova					= "";
	public $Xtitulo					= "";
	public $Xdisciplina				= "";
	public $Xprofessor				= "";
	public $Xtopicos				= array();
	public $Xnumero_perguntas		= "";
	public $Xdata					= "";
	public $Xdata_inicio			= "";
	public $Xdata_termino			= "";
	public $Xperguntas				= array();
	public $Xdificuldade			= "";
	public $Xliberada				= "";

	public $perguntasNaoExcluidas	= "0";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	private $prova_nao_liberada		= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function Prova(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->prova;
	}

	function setId($prova){
		$this->prova = $prova;
	}
	
	function getTitulo(){
		return $this->titulo;
	}

	function setTitulo($titulo){
		$this->titulo = $titulo;
	}

	function getDisciplina(){
		return $this->disciplina;
	}

	function setDisciplina($disciplina){
		$this->disciplina = $disciplina;
	}
	
	function getProfessor(){
		return $this->professor;
	}

	function setProfessor($professor){
		$this->professor = $professor;
	}
	
	function addTopico($topico){
		array_push($this->topicos,$topico);
	}

	function getQtdeTopico(){
		return count($this->topicos);
	}

	function getTopico($index){
		return $this->topicos[$index];
	}

	function getNumeroPerguntas(){
		return $this->numero_perguntas;
	}

	function setNumeroPerguntas($numero_perguntas){
		$this->numero_perguntas = $numero_perguntas;
	}
		
	function getData(){
		return $this->data;
	}

	function setData($data){
		$this->data = $data;
	}
			
	function getDataInicio(){
		return $this->data_inicio;
	}

	function setDataInicio($data_inicio){
		$this->data_inicio = $data_inicio;
	}
			
	function getDataTermino(){
		return $this->data_termino;
	}

	function setDataTermino($data_termino){
		$this->data_termino = $data_termino;
	}
	
	function addPergunta($pergunta){
		array_push($this->perguntas,$pergunta);
		if (strlen($pergunta->getId())>0){
			$this->perguntasNaoExcluidas .= ", ".$pergunta->getId();
		}
	}

	function getQtdePerguntas(){
		return count($this->perguntas);
	}

	function getPergunta($index){
		return $this->perguntas[$index];
	}

	function getQtdePerguntasFiltro($tipo_pergunta){
		$qtde_perguntas = array();
		if (strlen($tipo_pergunta)>0){
			$qtde_perguntas = 0;
			for ($i=0;$i<count($this->perguntas); $i++){
				if ($this->perguntas[$i]->getTipoPergunta()->getId()==$tipo_pergunta){
					$qtde_perguntas++;
				}
			}
		}
		return $qtde_perguntas;
	}
			
	function getDificuldade(){
		return $this->dificuldade;
	}

	function setDificuldade($dificuldade){
		$this->dificuldade = $dificuldade;
	}

	function getLiberada(){
		return $this->liberada;
	}

	function setLiberada($liberada){
		$this->liberada = $liberada;
	}

	function getProvaNaoLiberada(){
		return $this->prova_nao_liberada;
	}

	function setProvaNaoLiberada($motivo){
		$this->prova_nao_liberada = $motivo;
	}

}
?>
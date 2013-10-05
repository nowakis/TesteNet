<?

class ProvaPergunta {

	private $prova_pergunta			= "";
	private $topico					= "";
	private $titulo					= "";
	private $tipo_pergunta			= "";
	private $dificuldade			= "";
	private $pergunta_origem		= "";
	private $peso					= "";
	private $respostas				= array();

	public $Xprova_pergunta			= "";
	public $Xtopico					= "";
	public $Xtitulo					= "";
	public $Xtipo_pergunta			= "";
	public $Xdificuldade			= "";
	public $Xpergunta_origem		= "";
	public $Xpeso					= "";
	public $Xrespostas				= "";

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function ProvaPergunta(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->prova_pergunta;
	}

	function setId($prova_pergunta){
		$this->prova_pergunta = $prova_pergunta;
	}

	function getTopico(){
		return $this->topico;
	}

	function setTopico($topico){
		$this->topico = $topico;
	}
	
	function getTitulo(){
		return $this->titulo;
	}

	function setTitulo($titulo){
		$this->titulo = $titulo;
	}	

	function getTituloReduzido($tamanho){
		$extensao = (strlen(nl2br($this->titulo))>$tamanho)?"...":"";
		return substr($this->titulo,0,$tamanho).$extensao;
	}

	function getTipoProvaPergunta(){
		return $this->tipo_pergunta;
	}

	function setTipoProvaPergunta($tipo_pergunta){
		$this->tipo_pergunta = $tipo_pergunta;
	}
		
	function getDificuldade($formato = ''){
		if ($this->dificuldade <> 25 AND $this->dificuldade <> 50 AND $this->dificuldade <> 75){
			if ($this->dificuldade > 74){
				$this->dificuldade = 75;
			}
			if ($this->dificuldade > 40 AND $dificuldade < 74 ){
				$this->dificuldade = 75;
			}
			if ($this->dificuldade < 40 ){
				$this->dificuldade = 75;
			}
		}

		if ($formato=='texto'){
			$tmp_dificuldade = "Médio";
			switch ($this->dificuldade) { 
					case "25" : $tmp_dificuldade = "<font color='#00CC00'>Fácil</font>";
								break;
					case "50" : $tmp_dificuldade = "<font color='#EF7E01'>Médio</font>";
								break;
					case "75" : $tmp_dificuldade = "<font color='#FF0000'>Difícil</font>";
								break;
			}
			return $tmp_dificuldade;

		}elseif ($formato=='so_texto'){
			$tmp_dificuldade = "Médio";
			switch ($this->dificuldade) { 
					case "25" : $tmp_dificuldade = "Fácil";
								break;
					case "50" : $tmp_dificuldade = "Médio";
								break;
					case "75" : $tmp_dificuldade = "Difícil";
								break;
			}
			return $tmp_dificuldade;
		}else{
			return $this->dificuldade;
		}
	}

	function setDificuldade($dificuldade){
		$this->dificuldade = $dificuldade;
	}

	function getPeso(){
		return $this->peso;
	}

	function setPeso($peso){
		$this->peso = $peso;
	}

	function addResposta($resposta){
		array_push($this->respostas,$resposta);
	}

	function getQtdeResposta(){
		return sizeof($this->respostas);
	}

	function getResposta($index){
		return $this->respostas[$index];
	}

}
?>
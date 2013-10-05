<?

class Pergunta {

	private $pergunta			= "";
	private $topico				= "";
	private $titulo				= "";
	private $tipo_pergunta		= "";
	private $dificuldade		= "";
	private $fonte				= "";
	private $ativa				= "";
	private $respostas			= array();

	public $Xpergunta			= "";
	public $Xtopico				= "";
	public $Xtitulo				= "";
	public $Xtipo_pergunta		= "";
	public $Xdificuldade		= "";
	public $Xfonte				= "";
	public $Xativa				= "";
	public $Xrespostas			= "";

	private $msg_erro			= array();
	private $msg_ok				= array();
	private $msg				= "";

	public $_login_instituicao	= "";
	public $_login_professor	= "";
	public $_login_aluno		= "";

	function Pergunta(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getId(){
		return $this->pergunta;
	}

	function setId($pergunta){
		$this->pergunta = $pergunta;
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

	function getTipoPergunta(){
		return $this->tipo_pergunta;
	}

	function setTipoPergunta($tipo_pergunta){
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
					case "25" : $tmp_dificuldade = "<font color='#00CC00'><strong>Fácil</strong></font>";
								break;
					case "50" : $tmp_dificuldade = "<font color='#EF7E01'><strong>Médio</strong></font>";
								break;
					case "75" : $tmp_dificuldade = "<font color='#FF0000'><strong>Difícil</strong></font>";
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

	function getFonte(){
		return $this->fonte;
	}

	function setFonte($fonte){
		$this->fonte = $fonte;
	}
	
	function getAtiva(){
		return $this->ativa;
	}

	function setAtiva($ativa){
		$this->ativa = $ativa;
	}

	function addResposta($resposta){
		array_push($this->respostas,$resposta);
	}

	function getQtdeResposta(){
		return count($this->respostas);
	}

	function getQtdeRespostaCorreta(){
		$qtde_resposta_correta = 0;
		$i = 0;
		while ( $i < count($this->respostas)){
			if ( $this->respostas[$i]->getRespostaCorreta() ) {
				$qtde_resposta_correta++;
			}
			$i++;
		}
		return $qtde_resposta_correta;
	}

	function getResposta($index){
		return $this->respostas[$index];
	}
	
	function getRespostaOrdem($index){
		for ($i=0; $i< $this->getQtdeResposta(); $i++){
			if ( $this->getResposta($i)->getOrdem() == $index ){
				return $this->respostas[$i];
			}
		}
	}

}
?>
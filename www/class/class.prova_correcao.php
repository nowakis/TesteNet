<?
class ProvaCorrecao {

	private $prova					= NULL;
	private $provas_respondidas		= array();

	public $Xprova					= "";
	public $Xprovas_respondidas		= array();

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function ProvaCorrecao(){
		global $_login_instituicao;
		global $_login_professor;
		global $_login_aluno;

		$this->_login_instituicao	= $_login_instituicao;
		$this->_login_professor		= $_login_professor;
		$this->_login_aluno			= $_login_aluno;
	}

	function getProva(){
		return $this->prova;
	}

	function setProva($prova){
		$this->prova = $prova;
	}
	
	function addProvaRespondida($prova_respondida){
		array_push($this->provas_respondidas,$prova_respondida);
	}

	function getQtdeProvaRespondida(){
		return count($this->provas_respondidas);
	}

	function getProvaRespondida($index){
		return $this->provas_respondidas[$index];
	}

	function getPerguntaCorrecao($perg = null){
		for ($i=0;$i<count($this->provas_respondidas);$i++) {
			$perguntas = $this->provas_respondidas[$i];
			if ( strlen($perguntas->getDataTermino()>0) ) {
				for ($j=0;$j<$perguntas->getQtdePerguntasRespondida();$j++){
					$pergunta = $perguntas->getPerguntaRespondida( $j );
					$nota_pergunta = $pergunta[2];
					#print_r($pergunta);
					#echo"<br>";
					if (strlen($nota_pergunta)==0){
						if (is_object($perg)){
							#print "<br>".$pergunta[1].'<>'.$perg->getId()."<br>";
							if ($pergunta[1] != $perg->getId()){
								continue;
							}
						}
						return $nota_pergunta[0];
					}
					#echo"<hr>";
				}
			}
		}
		return false;
	}
}
?>
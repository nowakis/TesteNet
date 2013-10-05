<?
class ProvaRespondida {

	private $prova					= NULL;
	private $aluno					= NULL;
	private $data_inicio			= "";
	private $data_termino			= "";
	private $qtde_perguntas			= "";
	private $qtde_acertos			= "";
	private $nota					= "";
	private $professor				= NULL;
	private $nota_liberada			= "";
	
	private $perguntas				= array();
	private $respostas				= array();

	public $Xprova					= "";
	public $Xaluno					= "";
	public $Xdata_inicio			= "";
	public $Xdata_termino			= "";
	public $Xqtde_perguntas			= "";
	public $Xqtde_acertos			= "";
	public $Xnota					= "";
	public $Xprofessor				= NULL;
	public $Xnota_liberada			= "";

	public $Xperguntas				= array();
	public $Xrespostas				= array();

	private $msg_erro				= array();
	private $msg_ok					= array();
	private $msg					= "";

	private $prova_nao_liberada		= "";

	public $_login_instituicao		= "";
	public $_login_professor		= "";
	public $_login_aluno			= "";

	function ProvaRespondida(){
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
	
	function getAluno(){
		return $this->aluno;
	}

	function setAluno($aluno){
		$this->aluno = $aluno;
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
	
	function getQtdePerguntas(){
		return $this->qtde_perguntas;
	}

	function setQtdePerguntas($qtde_perguntas){
		$this->qtde_perguntas = $qtde_perguntas;
	}
	
	function getQtdeAcertos(){
		return $this->qtde_acertos;
	}

	function setQtdeAcertos($qtde_acertos){
		$this->qtde_acertos = $qtde_acertos;
	}
	
	function getNota(){
		if (strlen($this->getNotaLiberada()>0)) {
			return $this->nota;
		}else{
			return false;
		}
	}

	function setNota($nota){
		$this->nota = RoundNota(round($nota,2));
	}
	
	function getProfessor(){
		return $this->professor;
	}

	function setProfessor($professor){
		$this->professor = $professor;
	}
		
	function getNotaLiberada(){
		return $this->nota_liberada;
	}

	function setNotaLiberada($nota_liberada){
		$this->nota_liberada = $nota_liberada;
	}

	function zerarPerguntasRespostas(){
		$this->perguntas = NULL;
		$this->perguntas = array();
		$this->respostas = NULL;
		$this->respostas = array();
	}

	function addPerguntaRespondida($id_pergunta,$prova_pergunta, $valor_corrigido = null){
		array_push($this->perguntas,array($id_pergunta,$prova_pergunta,$valor_corrigido));
	}

	function getQtdePerguntasRespondida(){
		return count($this->perguntas);
	}

	function getPerguntaRespondida($index){
		return $this->perguntas[$index];
	}
	
	function getPerguntaId($prova_pergunta){
		$ret_pergunta = array();
	#print_r($this->perguntas);
	#echo "<hr>";
		$i=0;
		while ( $i < count($this->perguntas)){
			if ($this->perguntas[$i][1] == $prova_pergunta){
				return $i;
			}
			$i++;
		}
		return null;
	}
	
	function addResposta($prova_aluno_resposta, $prova_pergunta, $prova_resposta, $resposta_texto, $resposta_correta, $valor ){


		#echo " RESPOSTA $prova_aluno_resposta, $prova_pergunta, $prova_resposta, $resposta_texto, $resposta_correta, $valor <hr>";
		array_push($this->respostas,array(	$prova_aluno_resposta, 
											$prova_pergunta, 
											$prova_resposta, 
											$resposta_texto, 
											$resposta_correta, 
											$valor ));

	}

	function getQtdeRespostas(){
		return count($this->respostas);
	}

	function setValorCorrigidoPergunta($prova_pergunta, $valor_corrigido){
		$pergunta    = $this->getPerguntaId($prova_pergunta);

		$i=0;
		if (strlen($pergunta)>0 ){
			$this->perguntas[$pergunta][2] = $valor_corrigido;
			#echo "<br>";
			#print_r($this->perguntas[$pergunta]);
			#print "(NOTA: ".$valor_corrigido.")";
		}
	}

	function setValorCorrigidoResposta($prova_pergunta, $prova_resposta, $resposta_correta, $valor ){

		#echo "$prova_pergunta, $prova_resposta, $resposta_correta, $valor <br>";
		$respostas    = $this->getRespostasPergunta($prova_pergunta);
		$i=0;
		while ( $i < count($respostas)){
			if ($respostas[$i][2] == $prova_resposta){
				$id_resposta    = $this->getRespostasPerguntaId($prova_resposta);
				$this->respostas[$id_resposta][4] = $resposta_correta;
				$this->respostas[$id_resposta][5] = $valor;
			}
			$i++;
		}
	}

	function getRespostasPergunta($prova_pergunta){

		#echo "Procurnado por ($prova_pergunta) ";
		$ret_respostas = array();
		$i=0;
		#print_r($this->respostas);
		while ( $i < count($this->respostas)){
			if ($this->respostas[$i][1] == $prova_pergunta){
				#echo "..encontrou (".$this->respostas[$i][1].") ";
				array_push($ret_respostas, $this->respostas[$i]);
			}
			$i++;
		}
		return $ret_respostas;
	}

	function getRespostasPerguntaId($prova_resposta){
		$i=0;
		while ( $i < count($this->respostas)){
			if ($this->respostas[$i][2] == $prova_resposta){
				return $i;
			}
			$i++;
		}
		return null;
	}

	

	function getRespostasPerguntaItem($prova_pergunta, $id_resposta){
		$respostas    = $this->getRespostasPergunta($prova_pergunta);
#echo "<br><br>";
#		print_r($respostas);
		$ret_respostas = NULL;
		$i=0;
		while ( $i < count($respostas)){
			#print $respostas[$i][2]." == ".$id_resposta."<br>";
			if ($respostas[$i][2] == $id_resposta){
				$ret_respostas = $respostas[$i];
			}
			$i++;
		}
		return $ret_respostas;
	}

	function getResposta($index){
		return $this->respostas[$index];
	}

	function getProvaRespondida(){
		if ( strlen($this->getDataTermino()) > 0 ) {
			return true;
		}else{
			return false;
		}
	}
}
?>
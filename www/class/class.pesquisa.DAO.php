<?

require_once("class.DAO.php");

class PesquisaDAO extends DAO {

	public function gravarPesquisaDAO(Pesquisa $pesquisa){

		$banco = $this->getBancoDados(); 

		$query = "INSERT INTO tbl_pesquisa (
						instituicao,
						professor,
						aluno,
						data
				) VALUES (
						$pesquisa->Xinstituicao,
						$pesquisa->Xprofessor,
						$pesquisa->Xaluno,
						$pesquisa->Xdata
					)";

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Pesquisa. ($query) "); 
		}

		$pesquisa->setId($banco->insert_id());

		for ($i=0;$i<$pesquisa->getQtdePergunta();$i++){

			$array_pergunta = $pesquisa->getPergunta($i);
			$pergunta       = $array_pergunta[0];
			$resposta       = $array_pergunta[1];

			if (strlen($pergunta)>0){
				$query = "INSERT INTO tbl_pesquisa_pergunta (
								pesquisa,
								pergunta,
								resposta
							) VALUES (
								 ".$pesquisa->getId().",
								'".$pergunta."',
								'".$resposta."'
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao inserir DISCIPLINA ALUNO. ($query) "); 
				}
			}
		}

		if (getRealIpAddr() != '127.0.0.1'){
			/* Envio de E-Mail para Avisar Pesquisa */
			$mail             = new PHPMailer();
			$body             = "Nova pesquisa realizada ".date('d/m/Y H:i');

			$mail->From       = "testenetweb@gmail.com";
			$mail->FromName   = "TesteNet";
			$mail->Subject    = "TesteNet - Pesquisa Realizada!";
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
			$mail->MsgHTML($body);
			$mail->AddAddress('testenetweb@gmail.com', 'Suporte TesteNet');
			$mail->Send();
		}
	}

	public function recuperarPesquisa($id_pesquisa){

		$query ="SELECT tbl_pesquisa.pesquisa         AS pesquisa,
						tbl_pesquisa.instituicao      AS instituicao,
						tbl_pesquisa.professor        AS professor,
						tbl_pesquisa.aluno            AS aluno,
						DATE_FORMAT(tbl_pesquisa.data , '%d/%m/%Y %H:%i') AS data
				FROM tbl_pesquisa
				WHERE pesquisa = $id_pesquisa ";
		$banco = $this->getBancoDados(); 
		$comunicado = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma pesquisa encontrada.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade		= new SessionFacade($banco); 
				#$obj_instituicao	= $sessionFacade->recuperarInstituicao($linha["instituicao"]);
				#$obj_aluno			= $sessionFacade->recuperarAluno($linha["aluno"]);
				$obj_professor		= $sessionFacade->recuperarProfessor($linha["professor"]);

				$pesquisa = new Pesquisa(); 
				$pesquisa->setId($linha['pesquisa']);
				#$pesquisa->setInstituicao($obj_instituicao);
				$pesquisa->setProfessor($obj_professor);
				#$pesquisa->setAluno($obj_alunio);
				$pesquisa->setData($linha["data"]);
			}
			return $pesquisa; 
		} else {
			throw new Exception("Erro ao recuperar Pesquisa ($query)"); 
		}
	}

	public function recuperarFazerPesquisaDAO($professor) {

		$banco = $this->getBancoDados(); 

		if (is_object($professor)){
			$sql = "SELECT count(*) AS qtde_acessos
					FROM tbl_log_acesso
					WHERE professor = ".$professor->getId();
			$retorno = $banco->executaSQL($sql);
			if($retorno != NULL) {
				$linha = mysql_fetch_array($retorno);
				$qtde_acessos = $linha["qtde_acessos"];
				if ($qtde_acessos > 5){
					return 1;
				}else{
					return 0;
				}
			}
		}
		return NULL;
	}

	
	public function recuperarPesquisaTodosDAO($professor) {

		$banco = $this->getBancoDados(); 

		$filtro_sql = "";

		$sql = "SELECT pesquisa
				FROM tbl_pesquisa 
				ORDER BY tbl_pesquisa.data ASC"; 

		if (is_object($professor)){
			$sql = "SELECT pesquisa
					FROM tbl_pesquisa 
					WHERE tbl_pesquisa.professor = ".$professor->getId()."
					ORDER BY tbl_pesquisa.data DESC
					LIMIT 1"; 
		}

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$pesquisas = array();
			$i = 0;
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma pesquisa encontrada.",0);
			}
			while($linha = mysql_fetch_array($retorno)) {
				array_push($pesquisas,$this->recuperarPesquisa($linha["pesquisa"]));
			}
			return $pesquisas;
		} else {
			return NULL;
		}
	}

}
?>
<?

require_once("class.DAO.php");

class DivulgacaoDAO extends DAO {

	public function gravarDivulgacaoDAO(Divulgacao $divulgacao){

		$banco = $this->getBancoDados(); 

		$query = "INSERT INTO tbl_divulgacao (
						professor,
						aluno,
						data
				) VALUES (
						$divulgacao->Xprofessor,
						$divulgacao->Xaluno,
						$divulgacao->Xdata
					)";

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Divulgacao. ($query) "); 
		}

		$divulgacao->setId($banco->insert_id());

		for ($i=0;$i<$divulgacao->getQtdePergunta();$i++){

			$array_pergunta = $divulgacao->getPergunta($i);
			$pergunta       = $array_pergunta[0];
			$resposta       = $array_pergunta[1];

			if (strlen($pergunta)>0){
				$query = "INSERT INTO tbl_divulgacao_pergunta (
								divulgacao,
								pergunta,
								resposta
							) VALUES (
								 ".$divulgacao->getId().",
								'".$pergunta."',
								'".$resposta."'
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao inserir DISCIPLINA ALUNO. ($query) "); 
				}
			}
		}

		/* Envio de E-Mail para Avisar Divulgacao */
		$mail             = new PHPMailer();
		$body             = "Nova divulgacao realizada ".date('d/m/Y H:i');

		$mail->From       = "testenetweb@gmail.com";
		$mail->FromName   = "TesteNet";
		$mail->Subject    = "TesteNet - Divulgacao Realizada!";
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML($body);
		$mail->AddAddress('testenetweb@gmail.com', 'Suporte TesteNet');
		$mail->Send();
	}

	public function recuperarDivulgacao($id_divulgacao){

		$query ="SELECT tbl_divulgacao.divulgacao         AS divulgacao,
						tbl_divulgacao.instituicao      AS instituicao,
						tbl_divulgacao.professor        AS professor,
						tbl_divulgacao.aluno            AS aluno,
						DATE_FORMAT(tbl_divulgacao.data , '%d/%m/%Y %H:%i') AS data
				FROM tbl_divulgacao
				WHERE divulgacao = $id_divulgacao ";
		$banco = $this->getBancoDados(); 
		$comunicado = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma divulgacao encontrada.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade		= new SessionFacade($banco); 
				#$obj_instituicao	= $sessionFacade->recuperarInstituicao($linha["instituicao"]);
				#$obj_aluno			= $sessionFacade->recuperarAluno($linha["aluno"]);
				$obj_professor		= $sessionFacade->recuperarProfessor($linha["professor"]);

				$divulgacao = new Divulgacao(); 
				$divulgacao->setId($linha['divulgacao']);
				#$divulgacao->setInstituicao($obj_instituicao);
				$divulgacao->setProfessor($obj_professor);
				#$divulgacao->setAluno($obj_alunio);
				$divulgacao->setData($linha["data"]);
			}
			return $divulgacao; 
		} else {
			throw new Exception("Erro ao recuperar Divulgacao ($query)"); 
		}
	}

	public function recuperarDivulgacaoTodosDAO($professor) {

		$banco = $this->getBancoDados(); 

		$filtro_sql = "";

		$sql = "SELECT divulgacao
				FROM tbl_divulgacao 
				ORDER BY tbl_divulgacao.data ASC"; 

		if (is_object($professor)){
			$sql = "SELECT divulgacao
					FROM tbl_divulgacao 
					WHERE tbl_divulgacao.professor = ".$professor->getId()."
					ORDER BY tbl_divulgacao.data DESC
					LIMIT 1"; 
		}

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$divulgacaos = array();
			$i = 0;
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma divulgacao encontrada.",0);
			}
			while($linha = mysql_fetch_array($retorno)) {
				array_push($divulgacaos,$this->recuperarDivulgacao($linha["divulgacao"]));
			}
			return $divulgacaos;
		} else {
			return NULL;
		}
	}

}
?>
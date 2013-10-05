<?

require_once("class.DAO.php");

class RespostaDAO extends DAO {

	public function gravaDadosResposta(Resposta $resposta){

		$banco = $this->getBancoDados(); 

		if (strlen($resposta->getId())>0 AND 1==2){
			$query = " UPDATE tbl_resposta SET
							resposta_texto   = $resposta->Xresposta_texto,
							resposta_correta = $resposta->Xresposta_correta,
							resposta_filho   = $resposta->Xresposta_filho
						WHERE resposta = ".$resposta->getId();

			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir Resposta. ($query) "); 
			}
		}else{
			$query = "INSERT INTO tbl_resposta (
							pergunta,
							resposta_texto,
							resposta_correta,
							resposta_filho
					) VALUES (
							$resposta->Xpergunta,
							$resposta->Xresposta_texto,
							$resposta->Xresposta_correta,
							$resposta->Xresposta_filho
						)";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir Resposta. ($query) "); 
			}
			$resposta->setId($banco->insert_id());
		}
	}

	public function recuperarResposta($id_resposta){

		$query ="SELECT tbl_resposta.resposta             AS resposta,
						tbl_resposta.pergunta             AS pergunta,
						tbl_resposta.resposta_texto       AS resposta_texto,
						tbl_resposta.resposta_correta     AS resposta_correta,
						tbl_resposta.resposta_filho       AS resposta_filho
				FROM tbl_resposta
				WHERE tbl_resposta.resposta = $id_resposta ";

		$banco = $this->getBancoDados(); 
		$resposta = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma resposta encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				if (strlen(trim($linha["resposta_filho"]))>0){
					$sessionFacade   = new SessionFacade($banco); 
					$obj_resposta_filho = $sessionFacade->recuperarResposta($linha["resposta_filho"]);
				}else{
					$obj_resposta_filho = NULL;
				}

				$resposta = new Resposta(); 
				$resposta->setId($linha['resposta']);
				$resposta->setPergunta($linha["pergunta"]);
				$resposta->setRespostaCorreta($linha["resposta_correta"]);
				$resposta->setRespostaTexto($linha["resposta_texto"]);
				$resposta->setRespostaFilho($obj_resposta_filho);
			}
			return $resposta; 
		} else {
			throw new Exception("Erro ao recuperar Resposta ($query)"); 
		}
	}
}
?>
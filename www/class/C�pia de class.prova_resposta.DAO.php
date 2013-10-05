<?

require_once("class.DAO.php");

class ProvaRespostaDAO extends DAO {

	public function gravaDadosProvaResposta(ProvaResposta $prova_resposta){

		$banco = $this->getBancoDados(); 

		if (strlen($prova_resposta->getId())>0 AND 1==2){
			$query = " UPDATE tbl_prova_resposta SET
							resposta_texto   = $prova_resposta->Xprova_resposta_texto,
							resposta_correta = $prova_resposta->Xprova_resposta_correta,
							resposta_filho   = $prova_resposta->Xprova_resposta_filho
						WHERE resposta = ".$prova_resposta->getId();

			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir ProvaResposta. ($query) "); 
			}
		}else{
			$query = "INSERT INTO tbl_prova_resposta (
							prova_pergunta,
							resposta_texto,
							resposta_correta,
							resposta_filho
					) VALUES (
							$prova_resposta->Xpergunta,
							$prova_resposta->Xprova_resposta_texto,
							$prova_resposta->Xprova_resposta_correta,
							$prova_resposta->Xprova_resposta_filho
						)";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir Prova Resposta. ($query) "); 
			}
			$prova_resposta->setId($banco->insert_id());
		}
	}

	public function recuperarProvaResposta($id_prova_resposta){

		$query ="SELECT tbl_prova_resposta.prova_resposta       AS prova_resposta,
						tbl_prova_resposta.prova_pergunta       AS prova_pergunta,
						tbl_prova_resposta.resposta_texto       AS resposta_texto,
						tbl_prova_resposta.resposta_correta     AS resposta_correta,
						tbl_prova_resposta.resposta_filho       AS resposta_filho
				FROM tbl_prova_resposta
				WHERE tbl_prova_resposta.prova_resposta = $id_prova_resposta ";

		$banco = $this->getBancoDados(); 
		$prova_resposta = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma resposta da prova encontrada.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				if (strlen(trim($linha["resposta_filho"]))>0){
					$sessionFacade   = new SessionFacade($banco); 
					$obj_prova_resposta_filho = $sessionFacade->recuperarProvaResposta($linha["resposta_filho"]);
				}else{
					$obj_prova_resposta_filho = NULL;
				}

				$prova_resposta = new ProvaResposta(); 
				$prova_resposta->setId($linha['prova_resposta']);
				$prova_resposta->setPerguntaResposta($linha["prova_pergunta"]);
				$prova_resposta->setRespostaCorreta($linha["resposta_correta"]);
				$prova_resposta->setRespostaTexto($linha["resposta_texto"]);
				$prova_resposta->setRespostaFilho($obj_prova_resposta_filho);
			}
			return $prova_resposta; 
		} else {
			throw new Exception("Erro ao recuperar Resposta Prova (SQL: $query )"); 
		}
	}
}
?>
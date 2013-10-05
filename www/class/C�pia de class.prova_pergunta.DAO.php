<?

require_once("class.DAO.php");

class ProvaPerguntaDAO extends DAO {

	public function gravaDadosProvaPergunta(ProvaPergunta $pergunta){

		$banco = $this->getBancoDados(); 

		if (strlen($pergunta->getId())>0){
			$query = " UPDATE tbl_prova_pergunta SET
							topico        = $pergunta->Xtopico,
							tipo_pergunta = $pergunta->Xtipo_pergunta,
							titulo        = $pergunta->Xtitulo,
							dificuldade   = $pergunta->Xdificuldade,
							peso          = $pergunta->Xpeso
						WHERE prova_pergunta = ".$pergunta->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PERGUNTA PROVA. (SQL: $query ) "); 
			}
		}else{
			$query = "INSERT INTO tbl_prova_pergunta (
							topico,
							tipo_pergunta,
							titulo,
							dificuldade,
							peso
					) VALUES (
							$pergunta->Xtopico,
							$pergunta->Xtipo_pergunta,
							$pergunta->Xtitulo,
							$pergunta->Xdificuldade,
							$pergunta->Xpeso
						)";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PERGUNTA PROVA. (SQL: $query ) "); 
			}
			$pergunta->setId($banco->insert_id());
		}
	}

	public function apagarRespostas(ProvaPergunta $pergunta){

		$banco = $this->getBancoDados(); 

		if (strlen($pergunta->getId())>0){
			$query = " DELETE FROM tbl_prova_resposta WHERE prova_pergunta = ".$pergunta->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PERGUNTA PROVA. (SQL: $query ) "); 
			}
		}
	}

	public function recuperarProvaPergunta($id_pergunta){

		$query ="SELECT tbl_prova_pergunta.prova_pergunta     AS prova_pergunta,
						tbl_prova_pergunta.topico             AS topico,
						tbl_prova_pergunta.tipo_pergunta      AS tipo_pergunta,
						tbl_prova_pergunta.titulo             AS titulo,
						tbl_prova_pergunta.dificuldade        AS dificuldade,
						tbl_prova_pergunta.peso               AS peso
				FROM tbl_prova_pergunta
				WHERE tbl_prova_pergunta.prova_pergunta = $id_pergunta ";

		$banco = $this->getBancoDados(); 
		$pergunta = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma pergunta encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) {

				$sessionFacade		= new SessionFacade($banco); 
				$obj_topico			= $sessionFacade->recuperarTopico($linha["topico"]);
				$obj_tipo_pergunta	= $sessionFacade->recuperarTipoProvaPergunta($linha["tipo_pergunta"]);

				$pergunta = new ProvaPergunta(); 
				$pergunta->setId($linha['prova_pergunta']);
				$pergunta->setTopico($obj_topico);
				$pergunta->setTipoProvaPergunta($obj_tipo_pergunta);
				$pergunta->setTitulo($linha["titulo"]);
				$pergunta->setDificuldade($linha["dificuldade"]);
				$pergunta->setPeso($linha["peso"]);

				$query ="SELECT tbl_prova_resposta.prova_resposta      AS prova_resposta,
								tbl_prova_resposta.prova_pergunta      AS prova_pergunta,
								tbl_prova_resposta.resposta_texto      AS resposta_texto,
								tbl_prova_resposta.resposta_correta    AS resposta_correta,
								tbl_prova_resposta.resposta_filho      AS resposta_filho
						FROM tbl_prova_resposta
						JOIN tbl_prova_pergunta      USING(prova_pergunta)
						WHERE tbl_prova_pergunta.prova_pergunta = $id_pergunta";

				if ($obj_tipo_pergunta->getId()=="5"){
					$query .= " AND tbl_resposta.resposta_filho IS NOT NULL ";
				}

				$resposta_item = NULL; 

				$retorno_item = $banco->executaSQL($query); 
				if($retorno_item != NULL) {
					while($linha_item = $banco->fetchArray($retorno_item)) { 

/*	COMENTADO - ACHO que esta parte toda nao precisa, e sim somente da parte de baizo...testar e se der algum problema voltar a tras.
						if (strlen(trim($linha_item["resposta_filho"]))>0){
							$obj_resposta_filho = $sessionFacade->recuperarResposta($linha_item["resposta_filho"]);
						}else{
							$obj_resposta_filho = NULL;
						}

						$resposta_item = new Resposta(); 
						$resposta_item->setId($linha_item['prova_resposta']);
						$resposta_item->setProvaPergunta($linha_item['prova_pergunta']);
						$resposta_item->setRespostaTexto($linha_item['resposta_texto']);
						$resposta_item->setRespostaCorreta($linha_item['resposta_correta']);
						$resposta_item->setRespostaFilho($obj_resposta_filho);
						$pergunta->addResposta($resposta_item);
*/

						$resposta_item = $sessionFacade->recuperarResposta($linha_item["prova_resposta"]); 
						$pergunta->addResposta($resposta_item);
					}
				}
			}
			return $pergunta; 
		} else {
			throw new Exception("Erro ao recuperar ProvaPergunta ($query)"); 
		}
	}

	public function recuperarTodos($obrigatorio) {

		$sql = "SELECT DISTINCT prova_pergunta
				FROM tbl_prova_pergunta
				JOIN tbl_topico     USING(topico)
				JOIN tbl_disciplina USING(disciplina)
				JOIN tbl_curso      USING(curso)
				WHERE tbl_curso.instituicao = $this->_login_instituicao
				ORDER BY tbl_curso.nome ASC, tbl_disciplina.nome ASC, tbl_topico.descricao ASC, tbl_prova_pergunta.tipo_pergunta ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$pergunta = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0 AND $obrigatorio!='opcional'){
				throw new Exception("Nenhuma prova / pergunta encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$perguntas[$i++] = $this->recuperarProvaPergunta($linha["pergunta"]);
			}
			return $perguntas;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas prova pergunta "); 
		}
	}
}
?>
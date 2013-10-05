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
							fonte         = $pergunta->Xfonte,
							peso          = $pergunta->Xpeso
						WHERE prova_pergunta = ".$pergunta->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PERGUNTA PROVA. (SQL: $query ) "); 
			}
		}else{
			$query = "INSERT INTO tbl_prova_pergunta (
							prova,
							topico,
							tipo_pergunta,
							titulo,
							dificuldade,
							fonte,
							peso,
							pergunta_origem
					) VALUES (
							$pergunta->Xprova_id,
							$pergunta->Xtopico,
							$pergunta->Xtipo_pergunta,
							$pergunta->Xtitulo,
							$pergunta->Xdificuldade,
							$pergunta->Xfonte,
							$pergunta->Xpeso,
							$pergunta->Xpergunta_origem
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

	public function atualizarOrdemRespostaDAO(ProvaPergunta $pergunta){

		$banco = $this->getBancoDados(); 

		$query ="SELECT tbl_prova_resposta.prova_resposta     AS prova_resposta,
						tbl_prova_resposta.ordem              AS ordem
				FROM tbl_prova_resposta
				WHERE tbl_prova_resposta.prova_pergunta = ".$pergunta->getId()."
				AND   tbl_prova_resposta.resposta_filho IS NOT NULL
				";
		$retorno = $banco->executaSQL($query); 
		if($retorno == NULL) {
			throw new Exception("Erro ao atualizar a ordem das respostas. $query ",0);
		}

		$qtde_respostas = $pergunta->getQtdeResposta();

		$rand = new UniqueRand();
		#echo "<hr>($qtde_respostas)";
		while($linha = $banco->fetchArray($retorno)) {
			$aux_resposta = $linha["prova_resposta"];
			$aux_ordem    = $linha["ordem"];

			$ordem = $rand->uRand(0,$qtde_respostas-1);

			$query = "	UPDATE tbl_prova_resposta SET ordem = ".$ordem."
						WHERE prova_resposta = ".$aux_resposta;
			#echo nl2br($query);
			#echo "<br>";
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar a ordem das respostas interno.",0);
			}
		}
	}

	public function recuperarProvaPergunta($id_pergunta){

		$query ="SELECT tbl_prova_pergunta.prova_pergunta     AS prova_pergunta,
						tbl_prova_pergunta.topico             AS topico,
						tbl_prova_pergunta.tipo_pergunta      AS tipo_pergunta,
						tbl_prova_pergunta.titulo             AS titulo,
						tbl_prova_pergunta.dificuldade        AS dificuldade,
						tbl_prova_pergunta.fonte              AS fonte,
						tbl_prova_pergunta.peso               AS peso,
						tbl_prova_pergunta.pergunta_origem    AS pergunta_origem
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
				$obj_tipo_pergunta	= $sessionFacade->recuperarTipoPergunta($linha["tipo_pergunta"]);

				$pergunta = new ProvaPergunta(); 
				$pergunta->setId($linha['prova_pergunta']);
				$pergunta->setTopico($obj_topico);
				$pergunta->setTipoPergunta($obj_tipo_pergunta);
				$pergunta->setTitulo($linha["titulo"]);
				$pergunta->setDificuldade($linha["dificuldade"]);
				$pergunta->setFonte($linha["fonte"]);
				$pergunta->setPeso($linha["peso"]);
				$pergunta->setPerguntaOrigem($linha["pergunta_origem"]);

				$query ="SELECT tbl_prova_resposta.prova_resposta      AS prova_resposta,
								tbl_prova_resposta.prova_pergunta      AS prova_pergunta,
								tbl_prova_resposta.resposta_texto      AS resposta_texto,
								tbl_prova_resposta.resposta_correta    AS resposta_correta,
								tbl_prova_resposta.resposta_filho      AS resposta_filho
						FROM tbl_prova_resposta
						JOIN tbl_prova_pergunta      USING(prova_pergunta)
						WHERE tbl_prova_pergunta.prova_pergunta = $id_pergunta";
#echo nl2br($query);
#echo "<hr>";
				if ($obj_tipo_pergunta->getId()=="4" OR $obj_tipo_pergunta->getId()=="5"){
					$query .= " AND tbl_prova_resposta.resposta_filho IS NOT NULL ";
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

						$resposta_item = $sessionFacade->recuperarProvaResposta($linha_item["prova_resposta"]); 
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
			throw new Exception("Erro em query da recupeção de todas prova pergunta "); 
		}
	}


	/* Perguntas Respondidas */

	public function recuperarProvaPerguntaRespondida(Prova $prova, Aluno $aluno){

		$query ="SELECT tbl_prova_pergunta.prova_pergunta     AS prova_pergunta,
						tbl_prova_pergunta.topico             AS topico,
						tbl_prova_pergunta.tipo_pergunta      AS tipo_pergunta,
						tbl_prova_pergunta.titulo             AS titulo,
						tbl_prova_pergunta.dificuldade        AS dificuldade,
						tbl_prova_pergunta.fonte              AS fonte,
						tbl_prova_pergunta.peso               AS peso,
						tbl_prova_pergunta.pergunta_origem    AS pergunta_origem
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
				$obj_tipo_pergunta	= $sessionFacade->recuperarTipoPergunta($linha["tipo_pergunta"]);

				$pergunta = new ProvaPergunta(); 
				$pergunta->setId($linha['prova_pergunta']);
				$pergunta->setTopico($obj_topico);
				$pergunta->setTipoPergunta($obj_tipo_pergunta);
				$pergunta->setTitulo($linha["titulo"]);
				$pergunta->setDificuldade($linha["dificuldade"]);
				$pergunta->setFonte($linha["fonte"]);
				$pergunta->setPeso($linha["peso"]);
				$pergunta->setPerguntaOrigem($linha["pergunta_origem"]);

				$query ="SELECT tbl_prova_resposta.prova_resposta      AS prova_resposta,
								tbl_prova_resposta.prova_pergunta      AS prova_pergunta,
								tbl_prova_resposta.resposta_texto      AS resposta_texto,
								tbl_prova_resposta.resposta_correta    AS resposta_correta,
								tbl_prova_resposta.resposta_filho      AS resposta_filho
						FROM tbl_prova_resposta
						JOIN tbl_prova_pergunta      USING(prova_pergunta)
						WHERE tbl_prova_pergunta.prova_pergunta = $id_pergunta";

				if ($obj_tipo_pergunta->getId()=="4" OR $obj_tipo_pergunta->getId()=="5"){
					$query .= " AND tbl_resposta.resposta_filho IS NOT NULL ";
				}

				$resposta_item = NULL; 

				$retorno_item = $banco->executaSQL($query); 
				if($retorno_item != NULL) {
					while($linha_item = $banco->fetchArray($retorno_item)) { 
						$resposta_item = $sessionFacade->recuperarProvaResposta($linha_item["prova_resposta"]); 
						$pergunta->addResposta($resposta_item);
					}
				}
			}
			return $pergunta; 
		} else {
			throw new Exception("Erro ao recuperar ProvaPergunta ($query)"); 
		}
	}

}
?>
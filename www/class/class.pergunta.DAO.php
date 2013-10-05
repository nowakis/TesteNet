<?

require_once("class.DAO.php");

class PerguntaDAO extends DAO {

	public function gravaDadosPergunta(Pergunta $pergunta){

		$banco = $this->getBancoDados(); 

		if (strlen($pergunta->getId())>0){
				$query = " UPDATE tbl_pergunta SET
								topico        = $pergunta->Xtopico,
								tipo_pergunta = $pergunta->Xtipo_pergunta,
								titulo        = $pergunta->Xtitulo,
								dificuldade   = $pergunta->Xdificuldade,
								fonte         = $pergunta->Xfonte,
								ativa         = $pergunta->Xativa
							WHERE pergunta = ".$pergunta->getId();
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir PERGUNTA. ($query) "); 
				}
		}else{
				$query = "INSERT INTO tbl_pergunta (
								topico,
								tipo_pergunta,
								titulo,
								dificuldade,
								fonte,
								ativa
						) VALUES (
								$pergunta->Xtopico,
								$pergunta->Xtipo_pergunta,
								$pergunta->Xtitulo,
								$pergunta->Xdificuldade,
								$pergunta->Xfonte,
								$pergunta->Xativa
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir PERGUNTA. ($query) "); 
				}
				$pergunta->setId($banco->insert_id());
		}
	}

	public function apagarRespostas(Pergunta $pergunta){

		$banco = $this->getBancoDados(); 

		if (strlen($pergunta->getId())>0){
			$query = " DELETE FROM tbl_resposta WHERE pergunta = ".$pergunta->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao atualizar / inserir PERGUNTA. ($query) "); 
			}
		}
	}

	public function recuperarPergunta($id_pergunta){

		$query ="SELECT tbl_pergunta.pergunta           AS pergunta,
						tbl_pergunta.topico             AS topico,
						tbl_pergunta.tipo_pergunta      AS tipo_pergunta,
						tbl_pergunta.titulo             AS titulo,
						tbl_pergunta.dificuldade        AS dificuldade,
						tbl_pergunta.fonte              AS fonte,
						tbl_pergunta.ativa              AS ativa
				FROM tbl_pergunta
				WHERE tbl_pergunta.pergunta = $id_pergunta ";

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

				$pergunta = new Pergunta(); 
				$pergunta->setId($linha['pergunta']);
				$pergunta->setTopico($obj_topico);
				$pergunta->setTipoPergunta($obj_tipo_pergunta);
				$pergunta->setTitulo($linha["titulo"]);
				$pergunta->setDificuldade($linha["dificuldade"]);
				$pergunta->setFonte($linha["fonte"]);
				$pergunta->setAtiva($linha["ativa"]);

				$query ="SELECT tbl_resposta.resposta            AS resposta,
								tbl_resposta.pergunta            AS pergunta,
								tbl_resposta.resposta_texto      AS resposta_texto,
								tbl_resposta.resposta_correta    AS resposta_correta,
								tbl_resposta.resposta_filho      AS resposta_filho
						FROM tbl_resposta
						JOIN tbl_pergunta      USING(pergunta)
						WHERE tbl_pergunta.pergunta = $id_pergunta";

				if ($obj_tipo_pergunta->getId()=="4" or $obj_tipo_pergunta->getId()=="5"){
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
						$resposta_item->setId($linha_item['resposta']);
						$resposta_item->setPergunta($linha_item['pergunta']);
						$resposta_item->setRespostaTexto($linha_item['resposta_texto']);
						$resposta_item->setRespostaCorreta($linha_item['resposta_correta']);
						$resposta_item->setRespostaFilho($obj_resposta_filho);
						$pergunta->addResposta($resposta_item);
*/

						$resposta_item = $sessionFacade->recuperarResposta($linha_item["resposta"]); 
						$pergunta->addResposta($resposta_item);
					}
				}
			}
			return $pergunta; 
		} else {
			throw new Exception("Erro ao recuperar Pergunta ($query)"); 
		}
	}

	public function recuperarTodos($obrigatorio) {

		$sql = "SELECT DISTINCT pergunta
				FROM tbl_pergunta 
				JOIN tbl_topico     USING(topico)
				JOIN tbl_disciplina USING(disciplina)
				JOIN tbl_curso      USING(curso)
				WHERE tbl_curso.instituicao = $this->_login_instituicao
				ORDER BY tbl_curso.nome ASC, tbl_disciplina.nome ASC, tbl_topico.descricao ASC, tbl_pergunta.tipo_pergunta ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$pergunta = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0 AND $obrigatorio!='opcional'){
				throw new Exception("Nenhuma pergunta encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$perguntas[$i++] = $this->recuperarPergunta($linha["pergunta"]);
			}
			return $perguntas;
		} else {
			throw new Exception("Erro em query da recupeção de todas"); 
		}
	}
	
	public function recuperarQtde() {

		$sql = "SELECT count(*) AS qtde
				FROM tbl_pergunta 
				JOIN tbl_topico     USING(topico)
				JOIN tbl_disciplina USING(disciplina)
				WHERE tbl_disciplina.instituicao = ".$this->_login_instituicao; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			return $linha['qtde'];
		} else {
			throw new Exception("Erro em query da recupeção da qtde de aluno"); 
		}
	}

	public function recuperarTodosDisciplina($disciplina) {
		
		$banco = $this->getBancoDados(); 

		$sql = "SELECT pergunta
				FROM tbl_pergunta 
				JOIN tbl_topico     USING(topico)
				JOIN tbl_disciplina USING(disciplina)
				JOIN tbl_curso      USING(curso)
				WHERE tbl_curso.instituicao = $this->_login_instituicao
				AND   tbl_topico.disciplina = ".$disciplina->getId()."
				ORDER BY tbl_pergunta.topico ASC"; 
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$pergunta = NULL;
			$i = 0;
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma pergunta encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$perguntas[$i++] = $this->recuperarPergunta($linha["pergunta"]);
			}
			return $perguntas;
		} else {
			throw new Exception("Erro em query da recupeção de todas perguntas / prova / disciplina"); 
		}
	}

	public function recuperarTodosPorTopico($topico, $dificuldade, $qtde_perguntas = 1, $perguntas_inseridas = array() ) {
		
		$banco = $this->getBancoDados(); 

		if ($qtde_perguntas<0){
			$qtde_perguntas = 1;
		}

		if (count($perguntas_inseridas)>0){
			$perguntas_inseridas = implode(",",$perguntas_inseridas);
		}else{
			$perguntas_inseridas = "0";
		}

		$query = "SELECT tbl_pergunta.pergunta, 
						CASE WHEN tbl_pergunta.dificuldade = ".$dificuldade." THEN -1 ELSE tbl_pergunta.dificuldade END AS dificuldade
				FROM tbl_pergunta 
				JOIN tbl_topico     USING(topico)
				JOIN tbl_disciplina USING(disciplina)
				JOIN tbl_curso      USING(curso)
				WHERE tbl_curso.instituicao  = ".$this->_login_instituicao."
				AND tbl_pergunta.topico      = ".$topico."
				AND tbl_pergunta.dificuldade IS NOT NULL
				AND tbl_pergunta.pergunta NOT IN (".$perguntas_inseridas.")
				AND tbl_pergunta.ativa IS TRUE
				ORDER BY dificuldade ASC 
				LIMIT ".$qtde_perguntas; 
		$retorno = $banco->executaSQL($query);
		if($retorno != NULL) {

			$perguntas = array();
			$i = 0;

			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma pergunta encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				array_push($perguntas,$this->recuperarPergunta($linha["pergunta"]));
			}
			return $perguntas;
		} else {
			throw new Exception("Erro em query da recupeção de todas perguntas / prova / topico (SQL: $query )"); 
		}
	}
}
?>
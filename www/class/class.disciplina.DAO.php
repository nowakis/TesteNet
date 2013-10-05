<?

require_once("class.DAO.php");

class DisciplinaDAO extends DAO {

	public function gravaDadosDisciplina(Disciplina $disciplina){

		$banco = $this->getBancoDados(); 

		if (strlen($disciplina->getId())>0){
			$query = " UPDATE tbl_disciplina SET
							professor = $disciplina->Xprofessor,
							curso     = $disciplina->Xcurso,
							nome      = $disciplina->Xnome
						WHERE disciplina       = ".$disciplina->getId()."
						AND   instituicao      = $disciplina->_login_instituicao ";
		}else{
			$query = "INSERT INTO tbl_disciplina (
							instituicao,
							professor,
							curso,
							nome
					) VALUES (
							$disciplina->_login_instituicao,
							$disciplina->Xprofessor,
							$disciplina->Xcurso,
							$disciplina->Xnome
						)";
		}

		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Disciplina. ($query) "); 
		}

		if (strlen($disciplina->getId())==0){
			$disciplina->setId($banco->insert_id());
		}
	}

	public function recuperarDisciplina($id_disciplina){

		$query ="SELECT tbl_disciplina.disciplina           AS disciplina,
						tbl_disciplina.instituicao          AS instituicao,
						tbl_disciplina.professor            AS professor,
						tbl_disciplina.curso                AS curso,
						tbl_disciplina.nome                 AS nome
				FROM tbl_disciplina
				WHERE tbl_disciplina.instituicao   = $this->_login_instituicao
				AND   tbl_disciplina.disciplina    = $id_disciplina ";

		$banco = $this->getBancoDados(); 
		$disciplina = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma disciplina encontrada.",0);
			}

			$sessionFacade   = new SessionFacade($banco); 

			while($linha = $banco->fetchArray($retorno)) { 

				$obj_instituicao = $sessionFacade->recuperarInstituicao($linha["instituicao"]);
				$obj_professor	 = $sessionFacade->recuperarProfessor($linha["professor"]);
				$obj_curso		 = $sessionFacade->recuperarCurso($linha["curso"]);

				$disciplina = new Disciplina(); 
				$disciplina->setId($linha['disciplina']);
				$disciplina->setInstituicao($obj_instituicao);
				$disciplina->setProfessor($obj_professor);
				$disciplina->setCurso($obj_curso);
				$disciplina->setNome($linha["nome"]);
			}

			$topicos = array();
			#$topicos = $sessionFacade->recuperarTopicoTodosDAO($id_disciplina); // ENTROU EM RECURSAO, POR ISSO FOI COMENTADO
			if (count($topicos)>0){
				for ($i=0; $i<count($topicos);$i++){
					$disciplina->addTopico($topicos[$i]);
				}
			}

			return $disciplina; 
		} else {
			throw new Exception("Erro ao recuperar Disciplina ($query)"); 
		}
	}

	public function recuperarTodos($curso,$obrigatorio) {

		$banco = $this->getBancoDados(); 

		$sql = "SELECT curso
				FROM tbl_curso
				WHERE instituicao = ".$this->_login_instituicao."
				LIMIT 1"; 
		$retorno = $banco->executaSQL($sql);
		if ($banco->numRows($retorno) == 0 and $obrigatorio != 'opcional'){
			throw new Exception("Nenhum Curso cadastrado. Para realizar esta operação, acesse o Menu Instituição / Curso e cadastre um curso.",0);
		}

		$sql_curso = "";

		if (strlen($curso)>0){
			$sql_curso = " AND tbl_disciplina.curso = ".$curso;
		}

		$sql = "SELECT disciplina
				FROM tbl_disciplina 
				JOIN tbl_curso USING(curso)
				WHERE tbl_disciplina.instituicao = $this->_login_instituicao
				".$sql_curso."
				ORDER BY tbl_curso.nome ASC, tbl_disciplina.nome ASC"; 


		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$disciplina = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0 and $obrigatorio != 'opcional'){
				#$teste = $_SERVER['PHP_SELF'];
				throw new Exception("Nenhuma disciplina encontrada. É necessário cadastrar para realizar esta operação. Acesse o menu Instituição / Disciplinas.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$disciplinas[$i++] = $this->recuperarDisciplina($linha["disciplina"]);
			}
			return $disciplinas;
		} else {
			throw new Exception("Erro em query da recupeção de todas as disciplinas. ($sql)"); 
		}
	}

	public function recuperarQtde() {

		$banco = $this->getBancoDados(); 

		$sql = "SELECT count(*) AS qtde
				FROM tbl_disciplina 
				WHERE instituicao = ".$this->_login_instituicao;
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$linha = mysql_fetch_array($retorno);
			return $linha['qtde'];
		} else {
			throw new Exception("Erro em query da recupeção da qtde de disciplinas"); 
		}
	}

	public function apagaDadosDisciplinaTopico(Disciplina $disciplina){

		$banco = $this->getBancoDados(); 

		if (strlen($disciplina->getId())>0){
			$query = " DELETE FROM tbl_topico WHERE disciplina = ".$disciplina->getId()." AND topico NOT IN (".$disciplina->getTopicoNaoExcluidos().")";
			#throw new Exception($query); 
			if(!$banco->updateSQL($query)) {
				if ($banco->erroNumero()=='1451'){
					throw new Exception("Tópico não pode ser excluído pois já faz referencia a uma pergunta."); 
				}else{
					throw new Exception("Não foi possível excluir o tópico desta disciplina, prova, etc. Erro desconhecido. Tente novamente."); 
				}
			}
		}
	}

	public function excluirDisciplinaDAO(Disciplina $disciplina){

		$banco = $this->getBancoDados(); 

		$query = " DELETE FROM tbl_disciplina
					WHERE disciplina = ".$disciplina->getId();
		if(!$banco->updateSQL($query)) {
			$erro = $banco->mysql_error();
			if (strpos($erro,"foreign key constraint fails")) {
				throw new Exception("Erro ao excluir. Verifique se esta disciplina está relacionado a uma pergunta, prova, etc. Exclua os tópicos antes de excluir a Disciplina. Exclusão não concluída! "); 
			}else{
				throw new Exception("Erro ao excluir disciplina. (".$banco->mysql_error().")"); 
			}
		}
	}

}
?>
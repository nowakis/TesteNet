<?

require_once("class.DAO.php");

class TopicoDAO extends DAO {

	public function gravaDadosTopico(Topico $topico){

		if (strlen($topico->getId())>0){
				$query = " UPDATE tbl_topico SET
									disciplina       = $topico->Xdisciplina,
									descricao        = $topico->Xdescricao
							WHERE topico = ".$topico->getId();
		}else{
				$query = "INSERT INTO tbl_topico (
									disciplina,
									descricao
						) VALUES (
									$topico->Xdisciplina,
									$topico->Xdescricao
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir Topico. ($query) "); 
		}

		if (strlen($topico->getId())==0){
			$topico->setId($banco->insert_id());
		}
	}

	public function recuperarTopico($id_topico){

		$query ="SELECT tbl_topico.topico                AS topico,
						tbl_topico.disciplina            AS disciplina,
						tbl_topico.descricao             AS descricao
				FROM tbl_topico
				WHERE tbl_topico.topico = $id_topico ";

		$banco = $this->getBancoDados(); 
		$topico = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma topico encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade		= new SessionFacade($banco); 
				$obj_disciplina		= $sessionFacade->recuperarDisciplina($linha["disciplina"]);
				$topico = new Topico(); 
				$topico->setId($linha['topico']);
				$topico->setDisciplina($obj_disciplina);
				$topico->setDescricao($linha["descricao"]);
			}
			return $topico; 
		} else {
			throw new Exception("Erro ao recuperar Topico ($query)"); 
		}
	}

	public function recuperarTodos($disciplina_id = '') {

		if (strlen($disciplina_id)>0){
			$sql_condicao_disciplina = " AND tbl_disciplina.disciplina = ".$disciplina_id;
		}else{
			$sql_condicao_disciplina = "";
		}

		$sql = "SELECT DISTINCT topico
				FROM tbl_topico 
				JOIN tbl_disciplina USING(disciplina)
				WHERE tbl_disciplina.instituicao = $this->_login_instituicao
				".$sql_condicao_disciplina."
				ORDER BY tbl_disciplina.nome ASC, tbl_topico.descricao ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$topicos = array();
			$i = 0;
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma topico encontrado.",0);
			}
			while($linha = mysql_fetch_array($retorno)) {
				array_push($topicos,$this->recuperarTopico($linha["topico"]));
			}
			#print_r ($topicos);
			#exit;
			return $topicos;
		} else {
			return NULL;
		}
	}
}
?>
<?

require_once("class.DAO.php");

class ProvaImagemDAO extends DAO {

	public function gravaDadosImagem(Imagem $imagem){

		$banco = $this->getBancoDados(); 

		if (strlen($imagem->getId())>0){
				$query = " UPDATE tbl_prova_pergunta_imagem SET
								pergunta        = $imagem->Xpergunta,
								descricao       = $imagem->Xdescricao,
								path            = $imagem->Xpath,
								thumb           = $imagem->Xthumb
							WHERE imagem        = ".$imagem->getId();
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir IMAGEM. ($query) "); 
				}
		}else{
				$query = "INSERT INTO tbl_prova_pergunta_imagem (
								pergunta,
								descricao,
								path,
								thumb
						) VALUES (
								$imagem->Xpergunta,
								$imagem->Xdescricao,
								$imagem->Xpath,
								$imagem->Xthumb
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir IMAGEM. ($query) "); 
				}
				$imagem->setId($banco->insert_id());
		}
	}


	public function apagarImagem(Imagem $imagem){

		$banco = $this->getBancoDados(); 

		if (strlen($imagem->getId())>0){
			$query = " DELETE FROM tbl_pergunta_imagem WHERE imagem = ".$imagem->getId();
			if(!$banco->updateSQL($query)) {
				throw new Exception("Erro ao apagar IMAGEM. ($query) "); 
			}
		}
	}

	public function recuperarImagem($id_imagem){

		$query ="SELECT tbl_prova_pergunta_imagem.imagem         AS imagem,
						tbl_prova_pergunta_imagem.prova_pergunta AS pergunta,
						tbl_prova_pergunta_imagem.descricao      AS descricao,
						tbl_prova_pergunta_imagem.path           AS path,
						tbl_prova_pergunta_imagem.thumb          AS thumb
				FROM tbl_prova_pergunta_imagem
				WHERE tbl_prova_pergunta_imagem.imagem = ".$id_imagem;

		$banco = $this->getBancoDados(); 
		$imagem = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma imagem encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) {
				$imagem = new Imagem(); 
				$imagem->setId($linha['imagem']);
				$imagem->setPergunta($linha["pergunta"]);
				$imagem->setDescricao($linha["descricao"]);
				$imagem->setPath($linha["path"]);
				$imagem->setThumb($linha["thumb"]);
			}
			return $imagem; 
		} else {
			throw new Exception("Erro ao recuperar Imagem ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT DISTINCT imagem
				FROM tbl_prova_pergunta_imagem 
				JOIN tbl_prova_pergunta USING(prova_pergunta)
				JOIN tbl_disciplina     USING(disciplina)
				JOIN tbl_curso          USING(curso)
				WHERE tbl_curso.instituicao = $this->_login_instituicao
				ORDER BY tbl_curso.nome ASC, tbl_disciplina.nome ASC, tbl_prova_pergunta.descricao ASC, tbl_prova_pergunta_imagem.path ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$imagem = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma imagem encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$imagems[$i++] = $this->recuperarImagem($linha["imagem"]);
			}
			return $imagems;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas"); 
		}
	}

	public function recuperarTodosPergunta($pergunta) {
		
		$banco = $this->getBancoDados(); 

		$sql = "SELECT imagem
				FROM tbl_prova_pergunta_imagem 
				WHERE tbl_prova_pergunta_imagem.pergunta  = ".$pergunta->getId()."
				ORDER BY tbl_prova_pergunta_imagem.prova_pergunta ASC"; 
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$imagem = NULL;
			$i = 0;
			
			if ($banco->numRows($retorno) == 0){
				#throw new Exception("Nenhuma imagem encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$imagems[$i++] = $this->recuperarImagem($linha["imagem"]);
			}
			return $imagems;
		} else {
			throw new Exception("Erro em query da recupeчуo de todas imagems / prova / disciplina"); 
		}
	}
}
?>
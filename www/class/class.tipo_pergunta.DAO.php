<?

require_once("class.DAO.php");

class TipoPerguntaDAO extends DAO {

	public function gravaDadosTipoPergunta(TipoPergunta $tipo_pergunta){

		if (strlen($tipo_pergunta->getId())>0){
				$query = " UPDATE tbl_tipo_pergunta SET
								descricao        = $tipo_pergunta->Xdescricao,
								qtde_respostas   = $tipo_pergunta->Xqtde_respostas,
								imagem           = $tipo_pergunta->Ximagem
							WHERE tipo_pergunta    = ".$tipo_pergunta->getId();
		}else{
				$query = "INSERT INTO tbl_tipo_pergunta (
								descricao,
								qtde_respostas,
								imagem
						) VALUES (
								$tipo_pergunta->Xdescricao,
								$tipo_pergunta->Xqtde_respostas,
								$tipo_pergunta->Ximagem
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir TIPO_PERGUNTA. ($query) "); 
		}
	}

	public function recuperarTipoPergunta($id_tipo_pergunta){

		$query ="SELECT tipo_pergunta        AS tipo_pergunta,
						descricao            AS descricao,
						qtde_respostas       AS qtde_respostas,
						imagem               AS imagem
				FROM tbl_tipo_pergunta
				WHERE tipo_pergunta = $id_tipo_pergunta ";

		$banco = $this->getBancoDados(); 
		$tipo_pergunta = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma tipo_pergunta encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$tipo_pergunta = new TipoPergunta(); 
				$tipo_pergunta->setId($linha['tipo_pergunta']);
				$tipo_pergunta->setDescricao($linha["descricao"]);
				$tipo_pergunta->setQtdeRespostas($linha["qtde_respostas"]);
				$tipo_pergunta->setImagem($linha["imagem"]);
			}
			return $tipo_pergunta; 
		} else {
			throw new Exception("Erro ao recuperar TipoPergunta ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tipo_pergunta 
				FROM tbl_tipo_pergunta 
				WHERE visivel IS TRUE
				ORDER BY descricao ASC";
		$banco = $this->getBancoDados();
		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$tipo_perguntas = NULL;
	
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma tipo de pergunta encontrado.",0);
			}
			$i = 0;
			while($linha = mysql_fetch_array($retorno)) {
				$tipo_perguntas[$i++] = $this->recuperarTipoPergunta($linha["tipo_pergunta"]);
			}
			return $tipo_perguntas;
		} else {
			throw new Exception("Erro em query da recupeчуo de todos os tipos de pergunta."); 
		}
	}
}
?>
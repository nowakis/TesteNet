<?

require_once("class.DAO.php");

class FornecedorDAO extends DAO {

	public function gravaDadosFornecedor(Fornecedor $fornecedor){

		if (strlen($fornecedor->getId())>0){
				$query = "UPDATE tbl_proprietario SET
							nome             = $fornecedor->Xnome,
							cpf              = $fornecedor->Xcpf,
							rg               = $fornecedor->Xrg,
							endereco         = $fornecedor->Xendereco,
							numero           = $fornecedor->Xnumero,
							complemento      = $fornecedor->Xcomplemento,
							bairro           = $fornecedor->Xbairro,
							cidade           = $fornecedor->Xcidade,
							estado           = $fornecedor->Xestado,
							cep              = $fornecedor->Xcep,
							pais             = $fornecedor->Xpais,
							email            = $fornecedor->Xemail,
							login            = $fornecedor->Xlogin,
							senha            = $fornecedor->Xsenha,
							observacao       = $fornecedor->Xobservacao,
							ativo            = $fornecedor->Xativo
						WHERE proprietario   = ".$fornecedor->getId();
		}else{
				$query = "INSERT INTO tbl_proprietario (
								nome             ,
								cpf              ,
								rg               ,
								endereco         ,
								numero           ,
								complemento      ,
								bairro           ,
								cidade           ,
								estado           ,
								cep              ,
								pais             ,
								email            ,
								login            ,
								senha            ,
								observacao       ,
								ativo            
						) VALUES (
								$fornecedor->Xnome       ,
								$fornecedor->Xcpf        ,
								$fornecedor->Xrg         ,
								$fornecedor->Xendereco   ,
								$fornecedor->Xnumero     ,
								$fornecedor->Xcomplemento,
								$fornecedor->Xbairro     ,
								$fornecedor->Xcidade     ,
								$fornecedor->Xestado     ,
								$fornecedor->Xcep        ,
								$fornecedor->Xpais       ,
								$fornecedor->Xemail      ,
								$fornecedor->Xlogin      ,
								$fornecedor->Xsenha      ,
								$fornecedor->Xobservacao ,
								$fornecedor->Xativo       
							)";
				$query2 = " INSERT INTO tbl_proprietario_fornecedor (proprietario,fazenda) VALUES (,$fornecedor->_login_fazenda)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir FORNECEDOR. ($query) "); 
		}
		if (strlen($query2)>0){
			if(!$banco->updateSQL($query2)) {
				throw new Exception("Erro ao atualizar / inserir FORNECEDOR. ($query) "); 
			}
		}
	}

	public function recuperarFornecedor($id_fornecedor){

		$query ="SELECT tbl_proprietario.proprietario        AS proprietario,
						tbl_proprietario.nome                AS nome,
						tbl_proprietario.cpf                 AS cpf,
						tbl_proprietario.rg                  AS rg,
						tbl_proprietario.endereco            AS endereco,
						tbl_proprietario.numero              AS numero,
						tbl_proprietario.complemento         AS complemento,
						tbl_proprietario.bairro              AS bairro,
						tbl_proprietario.cidade              AS cidade,
						tbl_proprietario.estado              AS estado,
						tbl_proprietario.cep                 AS cep,
						tbl_proprietario.pais                AS pais,
						tbl_proprietario.email               AS email,
						tbl_proprietario.login               AS login,
						tbl_proprietario.senha               AS senha,
						tbl_proprietario.observacao          AS observacao,
						tbl_proprietario.ativo               AS ativo
				FROM tbl_proprietario
				JOIN tbl_proprietario_fornecedor ON tbl_proprietario_fornecedor.proprietario = tbl_proprietario.proprietario 
					AND tbl_proprietario_fornecedor.fazenda = $this->_login_fazenda
				WHERE tbl_proprietario.proprietario   = $id_fornecedor ";

		$banco = $this->getBancoDados(); 
		$fornecedor = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma fornecedor encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$fornecedor = new Fornecedor(); 
				$fornecedor->setId($linha['proprietario']);
				$fornecedor->setNome($linha["nome"]);
				$fornecedor->setCpf($linha["cpf"]);
				$fornecedor->setRg($linha["rg"]);
				$fornecedor->setEndereco($linha["endereco"]);
				$fornecedor->setNumero($linha["numero"]);
				$fornecedor->setComplemento($linha["complemento"]);
				$fornecedor->setBairro($linha["bairro"]);
				$fornecedor->setCidade($linha["cidade"]);
				$fornecedor->setEstado($linha["estado"]);
				$fornecedor->setCep($linha["cep"]);
				$fornecedor->setPais($linha["pais"]);
				$fornecedor->setEmail($linha["email"]);
				$fornecedor->setLogin($linha["login"]);
				$fornecedor->setSenha($linha["senha"]);
				$fornecedor->setAtivo($linha["ativo"]);
				$fornecedor->setObservacao($linha["observacao"]);
			}
			return $fornecedor; 
		} else {
			throw new Exception("Erro ao recuperar Fornecedor ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tbl_proprietario.proprietario
				FROM tbl_proprietario 
				JOIN tbl_proprietario_fornecedor ON tbl_proprietario_fornecedor.proprietario = tbl_proprietario.proprietario
				WHERE tbl_proprietario_fornecedor.fazenda = $this->_login_fazenda
				ORDER BY tbl_proprietario.proprietario ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$fornecedor = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma fornecedor encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$fornecedors[$i++] = $this->recuperarFornecedor($linha["proprietario"]);
			}
			return $fornecedors;
		} else {
			throw new Exception("Erro em query da recupeчуo de todos fornecedores"); 
		}
	}
}
?>
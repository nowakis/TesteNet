<?

require_once("class.DAO.php");

class ProprietarioDAO extends DAO {

	public function gravaDadosProprietario(Proprietario $proprietario){

		if (strlen($proprietario->getId())>0){
				$query = "UPDATE tbl_proprietario SET
							nome             = $proprietario->Xnome,
							cpf              = $proprietario->Xcpf,
							rg               = $proprietario->Xrg,
							endereco         = $proprietario->Xendereco,
							numero           = $proprietario->Xnumero,
							complemento      = $proprietario->Xcomplemento,
							bairro           = $proprietario->Xbairro,
							cidade           = $proprietario->Xcidade,
							estado           = $proprietario->Xestado,
							cep              = $proprietario->Xcep,
							pais             = $proprietario->Xpais,
							email            = $proprietario->Xemail,
							login            = $proprietario->Xlogin,
							senha            = $proprietario->Xsenha,
							observacao       = $proprietario->Xobservacao,
							ativo            = $proprietario->Xativo
						WHERE proprietario   = ".$proprietario->getId();
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
								$proprietario->Xnome       ,
								$proprietario->Xcpf        ,
								$proprietario->Xrg         ,
								$proprietario->Xendereco   ,
								$proprietario->Xnumero     ,
								$proprietario->Xcomplemento,
								$proprietario->Xbairro     ,
								$proprietario->Xcidade     ,
								$proprietario->Xestado     ,
								$proprietario->Xcep        ,
								$proprietario->Xpais       ,
								$proprietario->Xemail      ,
								$proprietario->Xlogin      ,
								$proprietario->Xsenha      ,
								$proprietario->Xobservacao ,
								$proprietario->Xativo       
							)";
		}

		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir MARCA. ($query) "); 
		}
	}

	public function recuperarProprietario($id_proprietario){

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
				WHERE tbl_proprietario.proprietario   = $id_proprietario ";

		$banco = $this->getBancoDados(); 
		$proprietario = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma proprietario encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$proprietario = new Proprietario(); 
				$proprietario->setId($linha['proprietario']);
				$proprietario->setNome($linha["nome"]);
				$proprietario->setCpf($linha["cpf"]);
				$proprietario->setRg($linha["rg"]);
				$proprietario->setEndereco($linha["endereco"]);
				$proprietario->setNumero($linha["numero"]);
				$proprietario->setComplemento($linha["complemento"]);
				$proprietario->setBairro($linha["bairro"]);
				$proprietario->setCidade($linha["cidade"]);
				$proprietario->setEstado($linha["estado"]);
				$proprietario->setCep($linha["cep"]);
				$proprietario->setPais($linha["pais"]);
				$proprietario->setEmail($linha["email"]);
				$proprietario->setLogin($linha["login"]);
				$proprietario->setSenha($linha["senha"]);
				$proprietario->setAtivo($linha["ativo"]);
				$proprietario->setObservacao($linha["observacao"]);
			}
			return $proprietario; 
		} else {
			throw new Exception("Erro ao recuperar Proprietario ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tbl_proprietario.proprietario
				FROM tbl_proprietario 
				JOIN tbl_proprietario_fazenda ON tbl_proprietario_fazenda.proprietario = tbl_proprietario.proprietario
				WHERE tbl_proprietario_fazenda.fazenda = $this->_login_fazenda
				ORDER BY tbl_proprietario.proprietario ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$proprietario = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma proprietario encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$proprietarios[$i++] = $this->recuperarProprietario($linha["proprietario"]);
			}
			return $proprietarios;
		} else {
			throw new Exception("Erro em query da recupeчуo de todos proprietarios"); 
		}
	}
}
?>
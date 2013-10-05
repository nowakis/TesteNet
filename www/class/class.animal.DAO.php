<?

require_once("class.DAO.php");

class AnimalDAO extends DAO {

	public function gravaDadosAnimal(Animal $animal){

		if (strlen($animal->getId())>0){
				$query = "UPDATE tbl_animal SET
							numero               = $animal->Xnumero,
							apelido              = $animal->Xapelido,
							especie              = $animal->Xespecie,
							raca                 = $animal->Xraca,
							marca                = $animal->Xmarca,
							nascimento           = $animal->Xnascimento,
							obito                = $animal->Xobito,
							entrada              = $animal->Xentrada,
							pai                  = $animal->Xpai,
							animal_pai           = $animal->Xanimal_pai,
							mae                  = $animal->Xmae,
							animal_mae           = $animal->Xanimal_mae,
							sexo                 = $animal->Xsexo,
							tipo_criacao         = $animal->Xtipo_criacao,
							pureza               = $animal->Xpureza,
							pelagem              = $animal->Xpelagem,
							proprietario         = $animal->Xproprietario,
							crias                = $animal->Xcrias,
							valor_compra         = $animal->Xvalor_compra,
							previsao_valor_venda = $animal->Xprevisao_valor_venda,
							previsao_data_venda  = $animal->Xprevisao_data_venda,
							desmamado            = $animal->Xdesmamado,
							observacao           = $animal->Xobservacao,
							star                 = $animal->Xstar,
							status               = $animal->Xstatus,
							peso                 = $animal->Xpeso,
							faturamento_item     = $animal->Xfaturamento_item
						WHERE animal  = ".$animal->getId()."
						AND   fazenda = $animal->_login_fazenda ";
		}else{
				$query = "INSERT INTO tbl_animal (
								fazenda,
								numero,
								apelido,
								especie,
								raca,
								marca,
								nascimento,
								obito,
								entrada,
								pai,
								animal_pai,
								mae,
								animal_mae,
								sexo,
								tipo_criacao,
								pureza,
								pelagem,
								proprietario,
								crias,
								valor_compra,
								previsao_valor_venda,
								previsao_data_venda,
								desmamado,
								observacao,
								star,
								status,
								peso,
								faturamento_item
						) VALUES (
								$animal->_login_fazenda,
								$animal->Xnumero,
								$animal->Xapelido,
								$animal->Xespecie,
								$animal->Xraca,
								$animal->Xmarca,
								$animal->Xnascimento, 
								$animal->Xobito, 
								$animal->Xentrada,
								$animal->Xpai,
								$animal->Xanimal_pai,
								$animal->Xmae,
								$animal->Xanimal_mae,
								$animal->Xsexo,
								$animal->Xtipo_criacao,
								$animal->Xpureza,
								$animal->Xpelagem,
								$animal->Xproprietario,
								$animal->Xcrias, 
								$animal->Xvalor_compra,
								$animal->Xprevisao_valor_venda,
								$animal->Xprevisao_data_venda, 
								$animal->Xdesmamado,
								$animal->Xobservacao,
								$animal->Xstar,
								$animal->Xstatus,
								$animal->Xpeso,
								$animal->Xfaturamento_item
							)";
		}
		$banco = $this->getBancoDados(); 
		if(!$banco->updateSQL($query)) {
			throw new Exception("Erro ao atualizar / inserir animal. ($query) "); 
		}
	}

	public function recuperarAnimal($id_animal){

		$query ="SELECT tbl_animal.animal                               AS animal,
						tbl_animal.numero                               AS numero ,
						tbl_animal.apelido                              AS apelido,
						tbl_animal.especie                              AS especie,
						tbl_animal.raca                                 AS raca,
						tbl_animal.marca                                AS marca,
						tbl_animal.faixa                                AS faixa,
						tbl_animal.fazenda                              AS fazenda,
						DATE_FORMAT(tbl_animal.entrada , '%d/%m/%Y')    AS entrada,
						DATE_FORMAT(tbl_animal.saida , '%d/%m/%Y')      AS saida,
						tbl_animal.pai                                  AS pai,
						tbl_animal.mae                                  AS mae,
						tbl_animal.sexo                                 AS sexo,
						DATE_FORMAT(tbl_animal.nascimento , '%d/%m/%Y') AS nascimento,
						tbl_animal.tipo_criacao                         AS tipo_criacao,
						tbl_animal.pureza                               AS pureza,
						tbl_animal.pelagem                              AS pelagem,
						tbl_animal.proprietario                         AS proprietario,
						tbl_animal.valor_compra                         AS valor_compra,
						tbl_animal.previsao_valor_venda                 AS previsao_valor_venda,
						tbl_animal.valor_venda                          AS valor_venda,
						DATE_FORMAT(tbl_animal.previsao_data_venda , '%d/%m/%Y') AS previsao_data_venda,
						tbl_animal.crias                                AS crias,
						tbl_animal.grupo                                AS grupo,
						tbl_animal.local                                AS local,
						tbl_animal.excluido                             AS excluido,
						tbl_animal.observacao                           AS observacao,
						DATE_FORMAT(tbl_animal.data_digitacao , '%d/%m/%Y') AS data_digitacao,
						tbl_animal.star                                 AS star,
						tbl_animal.status                               AS status,
						tbl_animal.peso                                 AS peso,
						tbl_animal.faturamento_item                     AS faturamento_item
				FROM tbl_animal
				WHERE tbl_animal.fazenda       = $this->_login_fazenda
				/* AND   tbl_animal.proprietario  = $this->_login_proprietario */
				AND   tbl_animal.animal        = $id_animal  ";

		$banco = $this->getBancoDados(); 
		$animal = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {


			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhum animal encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 
				$animal = new Animal();
				//$raca   = new Raca();
		
				$sessionFacade = new SessionFacade($banco); 
				$obj_proprietario = $sessionFacade->recuperarProprietario($linha["proprietario"]);
				$obj_marca        = $sessionFacade->recuperarMarca($linha["marca"]);
				$obj_especie      = $sessionFacade->recuperarEspecie($linha["especie"]);
				$obj_raca         = $sessionFacade->recuperarRaca($linha["raca"]);
				$obj_tipo_criacao = $sessionFacade->recuperarTipoCriacao($linha["tipo_criacao"]);
				$obj_pelagem      = $sessionFacade->recuperarPelagem($linha["pelagem"]);

				$animal->setId($linha['animal']);
				$animal->setNumero($linha["numero"]);
				$animal->setApelido($linha["apelido"]);
				#$animal->setRaca($linha["raca"]);
				$animal->setRaca($obj_raca);
				$animal->setEspecie($obj_especie);
				#$animal->setMarca($linha["marca"]);
				$animal->setMarca($obj_marca);
				$animal->setNascimento($linha["nascimento"]);
				$animal->setObito($linha["obito"]);
				$animal->setEntrada($linha["entrada"]);
				$animal->setPai($linha["pai"]);
				$animal->setAnimalPai($linha["animal_pai"]);
				$animal->setMae($linha["mae"]);
				$animal->setAnimalMae($linha["animal_mae"]);
				$animal->setSexo($linha["sexo"]);
				#$animal->setTipoCriacao($linha["tipo_criacao"]);
				$animal->setTipoCriacao($obj_tipo_criacao);
				$animal->setPureza($linha["pureza"]);
				#$animal->setPelagem($linha["pelagem"]);
				$animal->setPelagem($obj_pelagem);
				#$animal->setProprietario($linha["proprietario"]);
				$animal->setProprietario($obj_proprietario);
				$animal->setCrias($linha["crias"]);
				$animal->setValorCompra($linha["valor_compra"]);
				$animal->setPrevisaoValorVenda($linha["previsao_valor_venda"]);
				$animal->setPrevisaoDataVenda($linha["previsao_data_venda"]);
				$animal->setValorVenda($linha["valor_venda"]);
				$animal->setDesmamado($linha["desmamado"]);
				$animal->setObservacao($linha["observacao"]);
				$animal->setStar($linha["star"]);
				$animal->setStatus($linha["status"]);
				$animal->setPeso($linha["peso"]);
				$animal->setFaturamentoItem($linha["faturamento_item"]);
			}
			return $animal; 
		} else {
			throw new Exception("Erro ao recuperar Animal ($query)"); 
		}
	}

	public function recuperarTodos() {
		$sql = "select id from cliente order by id desc"; 

		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {
			$clientes = NULL;
			$i = "0";
			while($linha = mysql_fetch_array($retorno)) {
				$clientes[$i] = $this->recuperarPorId($linha["id"]);
				$i++; 
			}
			return $clientes;
		} else {
			throw new Exception("ERRO NA QUERY CLIENTEDAO MÉTODO RECUPERAR CLIENTE TODOS"); 
		}
	}
}
?>
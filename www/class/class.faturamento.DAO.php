<?

require_once("class.DAO.php");

class FaturamentoDAO extends DAO {

	public function gravaDadosFaturamento(Faturamento $faturamento){

		$banco = $this->getBancoDados(); 

		if (strlen($faturamento->getId())>0){
				$query = "UPDATE tbl_faturamento SET
							proprietario     = $faturamento->Xproprietario,
							fornecedor       = $faturamento->Xfornecedor,
							nota_fiscal      = $faturamento->Xnota_fiscal,
							serie            = $faturamento->Xserie,
							emissao          = $faturamento->Xemissao,
							saida            = $faturamento->Xsaida,
							conferida        = $faturamento->Xconferida,
							cancelada        = $faturamento->Xcancelada,
							exportado        = $faturamento->Xexportado,
							transportadora   = $faturamento->Xtransportadora,
							frete            = $faturamento->Xfrete,
							cfop             = $faturamento->Xcfop,
							natureza         = $faturamento->Xnatureza,
							total_nota       = $faturamento->Xtotal_nota,
							base_icms        = $faturamento->Xbase_icms,
							base_ipi         = $faturamento->Xbase_ipi,
							valor_icms       = $faturamento->Xvalor_icms,
							valor_ipi        = $faturamento->Xvalor_ipi,
							observacao       = $faturamento->Xobservacao
						WHERE faturamento    = ".$faturamento->getId();
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir FATURAMENTO. ($query) "); 
				}

		}else{
				$query = "INSERT INTO tbl_faturamento (
								fazenda          ,
								proprietario     ,
								fornecedor        ,
								nota_fiscal      ,
								serie            ,
								emissao          ,
								saida            ,
								conferida        ,
								cancelada        ,
								exportado        ,
								transportadora   ,
								frete            ,
								cfop             ,
								natureza         ,
								total_nota       ,
								base_icms        ,
								base_ipi         ,
								valor_icms       ,
								valor_ipi        ,
								observacao
						) VALUES (
								$faturamento->_login_fazenda,  
								$faturamento->Xproprietario,   
								$faturamento->Xfornecedor,     
								$faturamento->Xnota_fiscal,    
								$faturamento->Xserie,          
								$faturamento->Xemissao,        
								$faturamento->Xsaida,          
								$faturamento->Xconferida,      
								$faturamento->Xcancelada,      
								$faturamento->Xexportado,      
								$faturamento->Xtransportadora, 
								$faturamento->Xfrete,          
								$faturamento->Xcfop,           
								$faturamento->Xnatureza,       
								$faturamento->Xtotal_nota,     
								$faturamento->Xbase_icms,      
								$faturamento->Xbase_ipi,       
								$faturamento->Xvalor_icms,     
								$faturamento->Xvalor_ipi,      
								$faturamento->Xobservacao      
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir FATURAMENTO. ($query) "); 
				}
				$faturamento->setId($banco->insert_id());
		}
	}

	public function recuperarFaturamento($id_faturamento){

		$query ="SELECT faturamento                         AS faturamento,
						fazenda                             AS fazenda,
						proprietario                        AS proprietario,
						fornecedor                          AS fornecedor,
						nota_fiscal                         AS nota_fiscal,
						serie                               AS serie,
						DATE_FORMAT(emissao , '%d/%m/%Y')   AS emissao,
						DATE_FORMAT(saida , '%d/%m/%Y')     AS saida,
						DATE_FORMAT(conferida , '%d/%m/%Y %H:%i:%s') AS conferida,
						DATE_FORMAT(cancelada , '%d/%m/%Y %H:%i:%s') AS cancelada,
						exportado                           AS exportado,
						transportadora                      AS transportadora,
						frete                               AS frete,
						cfop                                AS cfop,
						natureza                            AS natureza,
						total_nota                          AS total_nota,
						base_icms                           AS base_icms,
						base_ipi                            AS base_ipi,
						valor_icms                          AS valor_icms,
						valor_ipi                           AS valor_ipi,
						observacao                          AS observacao
				FROM tbl_faturamento
				WHERE faturamento   = $id_faturamento ";

		$banco = $this->getBancoDados(); 
		$faturamento = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma faturamento encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade = new SessionFacade($banco); 
				$obj_proprietario = $sessionFacade->recuperarProprietario($linha["proprietario"]);
				$obj_fornecedor   = $sessionFacade->recuperarFornecedor($linha["fornecedor"]);

				$faturamento = new Faturamento();
				$faturamento->setId($linha['faturamento']);
				$faturamento->setProprietario($obj_proprietario);
				$faturamento->setFornecedor($obj_fornecedor);
				$faturamento->setNotaFiscal($linha['nota_fiscal']);
				$faturamento->setSerie($linha['serie']);
				$faturamento->setEmissao($linha['emissao']);
				$faturamento->setSaida($linha['saida']);
				$faturamento->setConferida($linha['conferida']);
				$faturamento->setCancelada($linha['cancelada']);
				$faturamento->setExportado($linha['exportado']);
				$faturamento->setTransportadora($linha['transportadora']);
				$faturamento->setFrete($linha['frete']);
				$faturamento->setCfop($linha['cfop']);
				$faturamento->setNatureza($linha['natureza']);
				$faturamento->setTotalNota($linha['total_nota']);
				$faturamento->setBaseIcms($linha['base_icms']);
				$faturamento->setBaseIpi($linha['base_ipi']);
				$faturamento->setValorIcms($linha['valor_icms']);
				$faturamento->setValorIpi($linha['valor_ipi']);
				$faturamento->setObservacao($linha['observacao']);

				$query ="SELECT faturamento_item AS faturamento_item,
								faturamento      AS faturamento,
								animal           AS animal,
								especie          AS especie,
								raca             AS raca,
								qtde             AS qtde,
								preco            AS preco
						FROM tbl_faturamento_item
						WHERE faturamento   = $id_faturamento ";

				$faturamento_item = NULL; 

				$retorno_item = $banco->executaSQL($query); 
				if($retorno_item != NULL) {
					while($linha_item = $banco->fetchArray($retorno_item)) { 

						$sessionFacade = new SessionFacade($banco); 
						$obj_especie = $sessionFacade->recuperarEspecie($linha_item["especie"]);
						$obj_raca    = $sessionFacade->recuperarRaca($linha_item["raca"]);

						$faturamento_item = new FaturamentoItem(); 
						$faturamento_item->setId($linha_item['faturamento_item']);
						$faturamento_item->setFaturamento($linha_item['faturamento_item']);
						$faturamento_item->setEspecie($obj_especie);
						$faturamento_item->setRaca($obj_raca);
						$faturamento_item->setQtde($linha_item['qtde']);
						$faturamento_item->setPreco($linha_item['preco']);

						$faturamento->addItem($faturamento_item);
					}
				} else {
					throw new Exception("Erro ao recuperar Faturamento Item ($query)"); 
				}

			}
			return $faturamento; 
		} else {
			throw new Exception("Erro ao recuperar Faturamento ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tbl_faturamento.faturamento
				FROM tbl_faturamento 
				WHERE fazenda = $this->_login_fazenda
				ORDER BY faturamento ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$faturamento = NULL;
			$faturamentos = array();
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma faturamento encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$faturamentos[$i++] = $this->recuperarFaturamento($linha["faturamento"]);
			}
			return $faturamentos;
		} else {
			throw new Exception("Erro em query da recupeчуo de todos faturamentos"); 
		}
	}
}
?>
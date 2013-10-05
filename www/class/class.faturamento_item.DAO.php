<?

require_once("class.DAO.php");

class FaturamentoItemDAO extends DAO {

	public function gravaDadosFaturamentoItem(FaturamentoItem $faturamento_item){

		$banco = $this->getBancoDados(); 

		if (strlen($faturamento_item->getId())>0){
				$query = "UPDATE tbl_faturamento_item SET
							especie    = $faturamento_item->Xespecie,
							raca       = $faturamento_item->Xraca,
							qtde       = $faturamento_item->Xqtde,
							preco      = $faturamento_item->Xpreco
						WHERE faturamento_item = ".$faturamento_item->getId();
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir FATURAMENTO ITEM. ($query) "); 
				}

		}else{
				$query = "INSERT INTO tbl_faturamento_item (
								faturamento    ,
								especie        ,
								raca           ,
								qtde           ,
								preco          
						) VALUES (
								$faturamento_item->Xfaturamento,
								$faturamento_item->Xespecie,    
								$faturamento_item->Xraca,       
								$faturamento_item->Xqtde,       
								$faturamento_item->Xpreco       
							)";
				if(!$banco->updateSQL($query)) {
					throw new Exception("Erro ao atualizar / inserir FATURAMENTO ITEM. ($query) "); 
				}
		}
	}

	public function recuperarFaturamentoItem($id_faturamento_item){

		$query ="SELECT faturamento_item AS faturamento_item,
						faturamento      AS faturamento,
						especie          AS especie,
						raca             AS raca,
						qtde             AS qtde,
						preco            AS preco
				FROM tbl_faturamento_item
				WHERE faturamento_item = $id_faturamento_item ";

		$banco = $this->getBancoDados(); 
		$faturamento_item = NULL; 
		$retorno = $banco->executaSQL($query); 
		if($retorno != NULL) {

			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma faturamento item encontrado.",0);
			}

			while($linha = $banco->fetchArray($retorno)) { 

				$sessionFacade = new SessionFacade($banco); 
				$obj_especie = $sessionFacade->recuperarEspecie($linha["especie"]);
				$obj_raca    = $sessionFacade->recuperarRaca($linha["raca"]);

				$faturamento_item = new FaturamentoItem(); 
				$faturamento_item->setId($linha['faturamento_item']);
				$faturamento_item->setFaturamento($linha['faturamento']);
				$faturamento_item->setEspecie($obj_especie);
				$faturamento_item->setRaca($obj_raca);
				$faturamento_item->setQtde($linha['qtde']);
				$faturamento_item->setPreco($linha['preco']);
			}
			return $faturamento_item; 
		} else {
			throw new Exception("Erro ao recuperar Faturamento item ($query)"); 
		}
	}

	public function recuperarTodos() {

		$sql = "SELECT tbl_faturamento_item.faturamento_item
				FROM tbl_faturamento_item 
				WHERE faturamento = $this->_login_fazenda
				ORDER BY faturamento ASC"; 
		$banco = $this->getBancoDados(); 

		$retorno = $banco->executaSQL($sql);
		if($retorno != NULL) {

			$faturamento = NULL;
			$i = "0";
			
			if ($banco->numRows($retorno) == 0){
				throw new Exception("Nenhuma faturamento encontrado.",0);
			}

			while($linha = mysql_fetch_array($retorno)) {
				$faturamentos_item[$i++] = $this->recuperarFaturamentoItem($linha["faturamento_item"]);
			}
			return $faturamentos_item;
		} else {
			throw new Exception("Erro em query da recupeчуo de todos faturamentos itens"); 
		}
	}
}
?>
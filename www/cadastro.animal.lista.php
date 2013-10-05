<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
********************************************************************************************/

##############################################################################
## INCLUDES E CONEXÔES BANCO
##############################################################################

session_start();
include_once "class.Template.inc.php";
require_once('banco.inc.php');
require_once("autentica_usuario.php");
include_once "funcoes.php";

include_once "class.banco.php";
include_once "class.SessionFacade.php";
include_once "class.animal.php";

header("Content-Type: text/html;  charset=ISO-8859-1",true);

$lista_ordem="";
$desc=" ASC";
$query_adicional = ""; 

$msg_erro="";
$msg="";


##############################################################################
##############                       STAR                   	##############
##############################################################################	

	if (isset($_GET['star']) && strlen($_GET['star'])>0 && isset($_GET['codigo']) && strlen($_GET['codigo'])>0){
		$codi=$_GET['codigo'];
		$estrela=$_GET['star'];
		
		if ($estrela==1) {$estrela=0;} 
		else             {$estrela=1;}
		
		$query = "UPDATE tbl_animal SET star=$estrela WHERE animal = $codi LIMIT 1";
		$rSet = $db->Query($query);
		exit();
	}


##############################################################################
##############                       DETALHES                  	##############
##############################################################################	

	if (isset($_GET['detalhes']) && strlen($_GET['detalhes'])>0){
		$codi=$_GET['detalhes'];
	
		$query = "SELECT tbl_animal.animal                             AS animal,
							tbl_animal.numero                                 AS numero ,
							tbl_animal.apelido                                AS apelido,
							tbl_animal.raca                                   AS raca,
							tbl_animal.marca                                  AS marca,
							tbl_animal.faixa                                  AS faixa,
							tbl_animal.fazenda                                AS fazenda,
							DATE_FORMAT(tbl_animal.entrada , '%d/%m/%Y')      AS entrada,
							DATE_FORMAT(tbl_animal.saida , '%d/%m/%Y')        AS saida,
							tbl_animal.pai                                    AS pai,
							tbl_animal.mae                                    AS mae,
							if(tbl_animal.sexo ='M', 'MACHO', 'FEMEA')        AS sexo,
							DATE_FORMAT(tbl_animal.nascimento , '%d/%m/%Y')   AS nascimento,
							tbl_animal.tipo_criacao                                   AS tipo_criacao,
							tbl_animal.proprietario                           AS proprietario,
							tbl_animal.valor_compra                           AS valor_compra,
							tbl_animal.previsao_valor_venda                         AS previsao_valor_venda,
							tbl_animal.valor_venda                            AS valor_venda,
							DATE_FORMAT(tbl_animal.previsao_data_venda,'%d/%m/%Y') AS previsao_data_venda,
							tbl_animal.crias                                  AS crias,
							tbl_animal.grupo                                  AS grupo,
							tbl_animal.local                                  AS local,
							tbl_animal.excluido                               AS excluido,
							tbl_animal.observacao                             AS observacao,
							DATE_FORMAT(tbl_animal.data_digitacao , '%d/%m/%Y') AS data_digitacao,
							tbl_animal.star AS star,
							tbl_animal.status AS status,

							tbl_raca.raca            AS raca,
							tbl_raca.codigo          AS raca_codigo ,
							tbl_raca.nome            AS raca_nome,
							tbl_raca.descricao       AS raca_descricao,
							tbl_raca.data            AS raca_data,
							tbl_raca.status          AS raca_status,
							tbl_raca.observacao      AS raca_observacao,
							tbl_raca.ativo           AS raca_ativo,

							tbl_fazenda.nome         AS fazenda_nome,
							tbl_fazenda.razao        AS fazenda_razao ,
							tbl_fazenda.descricao    AS fazenda_descricao,
							tbl_fazenda.endereco     AS fazenda_endereco,
							tbl_fazenda.cidade       AS fazenda_cidade,
							tbl_fazenda.estado       AS fazenda_estado,
							tbl_fazenda.proprietario AS fazenda_proprietario,
							tbl_fazenda.status       AS fazenda_status,

							tbl_marca.marca AS marca_codigo,
							tbl_marca.proprietario AS marca_proprietario
			FROM tbl_animal
			LEFT JOIN tbl_fazenda USING(fazenda)
			LEFT JOIN tbl_raca ON tbl_animal.raca=tbl_raca.raca
			LEFT JOIN tbl_marca ON tbl_animal.marca=tbl_marca.marca
			WHERE tbl_animal.animal = $codi
			AND   tbl_animal.excluido IS NULL
					";	
		$rSet = $db->Query($query);
		$linha = $db->FetchArray($rSet);
		echo "Nascimento:    ".$linha['nascimento']."<br>
					Pai:    ".$linha['pai']."<br>
					Mãe:    ".$linha['mae']."<br>
					Crias:    ".$linha['crias']."<br>
					Sexo:    ".$linha['sexo']."<br>
					OBS:    ".$linha['observacao']."";
		exit();
}
					
	
		
##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Animais Cadastrados";
$sub_titulo="Lista dos Animais Cadastrados";

include "cabecalho.php";

	
##############################################################################
##############                       EXCLUIR                   	##############
##############################################################################	

	if (isset($_GET['Excluir']) && strlen($_GET['Excluir'])>0){
		$codi=$_GET['Excluir'];
		
		$query = "UPDATE tbl_animal SET excluido=NOW() WHERE animal = $codi LIMIT 1";
		$rSet = $db->Query($query);
		$msg="Animal excluído com sucesso!";
	}

	
##############################################################################
##############                       AÇÕES                   	##############
##############################################################################	

if (isset($_POST['txtAcao']) && strlen($_POST['txtAcao'])>0){
	$acao=$_POST['txtAcao'];

	if (isset($_POST['animais'])){
	
/*		$animais_selecionados = "";
		while (list($campo,$valor_campo) = each($_POST['animais']))	{
			$animais_selecionados .= $valor_campo.",";
		}
		$animais_selecionados = substr($animais_selecionados,0,strlen($animais_selecionados)-1); // TODOS ANIMAIS SELECIONADOS
		*/
		switch($acao){
			case "vender": 
							break;
			case "vacinar": 
							$txtVacina		=	trim($_POST['txtVacinas']);
							$msg_erro		.=	(strlen(trim($_POST['txtdata']))!=10)?'<br>Data inválida!':'';
							$txtData		=	@converte_data(trim($_POST['txtdata']));
							
							if (strlen($txtVacina)==0)
								$msg_erro .="Nenhuma vacina selecionada!";
								
							$rSet = $db->Query("BEGIN");								
							
							if (strlen($msg_erro)==0){
								$campo="";
								$valor_campo="";$cont=0;
								while (list($campo,$valor_campo) = each($_POST['animais']))	{							
									$query = "INSERT tbl_aplicacao_vacina (vacina,animal,data) VALUES ($txtVacina,$valor_campo,'$txtData')";
//									$rSet = @mysql_query($query);
									$rSet = $db->Query($query);
									$msg_erro .= $db->MyError();
//									$msg_erro .= mysql_error();
									if (strlen($msg_erro)>0){
										break;
										
									}

								}
							}
							if (strlen($msg_erro)==0){
								$rSet = $db->Query("COMMIT");								
								$msg.="Vacinas aplicadas com sucesso!";
							}
							else {
								$rSet = $db->Query("ROLLBACK");								
							}

							break;
			case "baixa": 
							break;
			case "lote": 
							$txtLote		=	trim($_POST['txtLote']);
							$msg_erro		.=	(strlen(trim($_POST['txtdata']))!=10)?'<br>Data inválida!':'';
							$txtData		=	@converte_data(trim($_POST['txtdata']));
							
							if (strlen($txtLote)==0)
								$msg_erro .="Nenhum lote selecionado!";
								
							$rSet = $db->Query("BEGIN");								
							
							if (strlen($msg_erro)==0){
								$campo="";
								$valor_campo="";$cont=0;
								while (list($campo,$valor_campo) = each($_POST['animais']))	{							
									$query = "INSERT tbl_animal_lote (lote,animal,data) VALUES ($txtLote,$valor_campo,'$txtData')";
//									$rSet = @mysql_query($query);
									$rSet = $db->Query($query);
									$msg_erro .= $db->MyError();
//									$msg_erro .= mysql_error();
									if (strlen($msg_erro)>0){
										break;
										
									}

								}
							}
							if (strlen($msg_erro)==0){
								$rSet = $db->Query("COMMIT");								
								$msg.="Animais colocados no Lote com sucesso!";
							}
							else {
								$rSet = $db->Query("ROLLBACK");								
							}
					break;																		
							
		
		}
	}
}
	
	
##############################################################################
##############                       Filtrar                   	##############
##############################################################################		
	
if (isset($_POST['Filtrar']) && strlen($_POST['Filtrar'])>0 && $_POST['Filtrar']=='Filtrar'){

	$query_adicional = ""; 
	
	if (isset($_POST['txtvalor']) && strlen($_POST['txtvalor'])>0){
		$query_adicional .= " AND tbl_animal.numero like '%".$_POST['txtvalor']."%' ";
	}
	if (isset($_POST['txtmarca']) && strlen($_POST['txtmarca'])>0){
		$query_adicional .= " AND tbl_animal.marca = ".$_POST['txtmarca']." ";
	}
	if (isset($_POST['txtcategoria']) && strlen($_POST['txtcategoria'])>0){
		$query_adicional .= " AND tbl_animal.categoria = ".$_POST['txtcategoria']." "; 
	}

}

	
	
	
	
##############################################################################
##############                       ORDEM                   	##############
##############################################################################	

	if (isset($_SESSION["lista_ordem"]) && strlen($_SESSION["lista_ordem"])>0){
		$lista_ordem=$_SESSION['lista_ordem'];
		$desc=$_SESSION['ASC_DESC'];
	}
	if (isset($_GET['ordem']) && strlen($_GET['ordem'])>0){
		$lista_ordem = $_GET['ordem'];
		if (isset($_SESSION["lista_ordem"]) && strlen($_SESSION["lista_ordem"])>0){
			if ($_GET['ordem']==$_SESSION["lista_ordem"]){
				if (trim($desc)=="ASC")
					$desc=" DESC";
				else $desc=" ASC";
			}
		}
		$_SESSION['ASC_DESC'] = $desc;
		$_SESSION["lista_ordem"]= $lista_ordem;
	}
	
	if (strlen($lista_ordem)>0){
		switch ($lista_ordem){
			case "marca":
				$lista_ordem = " ORDER BY tbl_animal.marca $desc";
			 break;
			case "numero":
				$lista_ordem = " ORDER BY LPAD(tbl_animal.numero,10,'0') $desc";
			 break;
			case "nascimento":
				$lista_ordem = " ORDER BY tbl_animal.nascimento $desc";
			 break;
			case "crias":
				$lista_ordem = " ORDER BY tbl_animal.crias $desc";
			 break;
			case "peso":
				$lista_ordem = " ORDER BY tbl_animal.peso $desc";
			 break;			 
			case "raca":
				$lista_ordem = " ORDER BY tbl_animal.raca $desc";
			 break;			 			 
			case "idade":
				$lista_ordem = " ORDER BY idade $desc";
			 break;			 			 			 
		}
	}
	
					
	

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array('cadastro.animal.lista' => 'cadastro.animal.lista.htm'));



##############################################################################
##############               MSG DE ERRO OU SUCESSO           	##############
##############################################################################	

############### MSG

	if (strlen($msg_erro)>0)
		$model->assign_vars(array('MSG' => "<br><div id='msg_ok'><img src='imagens/forbidden.gif' align='absmiddle' style='padding-right:5px'/> $msg_erro</div>"));	

	if (strlen($msg)>0)
		$model->assign_vars(array('MSG' => $msg));			

############### MSG	


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

####### PAGINAÇÂO - INICIO

	$query = "SELECT count(*)
				FROM tbl_animal
				JOIN tbl_fazenda               USING(fazenda)
				LEFT JOIN tbl_raca             ON tbl_animal.raca  = tbl_raca.raca
				LEFT JOIN tbl_marca            ON tbl_animal.marca = tbl_marca.marca
				WHERE tbl_animal.fazenda      = $_login_fazenda
				/* AND   tbl_animal.proprietario = $_login_proprietario */
				AND   tbl_animal.excluido IS NULL
				$query_adicional
				";	
	$rSet = $db->Query($query);
	$linha = $db->FetchArray($rSet);
	$numero_registro = $linha[0];

	
	if (!isset($_GET['pg']) || empty($_GET['pg']))	$_GET['pg'] = 1;
	if ($_GET['pg']==0) $_GET['pg'] = 1;		
	$npp = 30;
	$paginaAtual = $_GET['pg'];
	$numero_paginas = ceil($numero_registro/$npp);
	
	$PAGINACAO = "";
	$tmp = $paginaAtual-1;
	if ($paginaAtual==1)
		$PAGINACAO .= "<span class='next'>&#171; Anterior</span>";
	if ($paginaAtual>1)
		$PAGINACAO .= "<a href='?pg=$tmp' class='next'  title='Voltar para a Página Anterior'><b>Anterior</b></a>";

	for ($i=1;$i<=$numero_paginas;$i++){
		if ($paginaAtual==$i){
			if ($numero_paginas>1) $PAGINACAO .= "<span class=\"current\">$i</span>";
		}else{
			$PAGINACAO .= "<a href='?pg=$i' title='Ir para página $i'>$i</a>";
		}
	}

	$tmp = $paginaAtual+1;
	if ($paginaAtual < $numero_paginas) $PAGINACAO .= " <a href='?pg=$tmp' class='next' title='Ir para a próxima página'><b>Próximo &#187;</b></a>";
	else								$PAGINACAO .= "<span class='next'>Próximo &#187;</span>";;
	
	$paginaAtual = ($paginaAtual-1)*$npp;
	$paginaLimite = $npp;
	
	$tmp1=$paginaAtual+1;
	$tmp2=$paginaAtual+$npp;	
	$model->assign_vars(array('PAGINACAO' => $PAGINACAO."<br><br>Animal <b>$tmp1</b> até <b>$tmp2</b> de um total de <b>$numero_registro</b>"));
####### PAGINAÇÂO - FIM

####### ESTATISTICAS - INICIO

############### ANIMAIS

	
	if (isset($msg) && strlen($msg)>0){
		$model->assign_vars(array('MSG' => "<br><div id='msg_ok'><img src='imagens/warning.gif' align='absmiddle' style='padding-right:5px'/> $msg</div>"));	
	}

	$query = "	SELECT tbl_animal.animal AS animal
				FROM tbl_animal
				JOIN tbl_fazenda USING(fazenda)
				LEFT JOIN tbl_raca  ON tbl_animal.raca  = tbl_raca.raca
				LEFT JOIN tbl_marca ON tbl_animal.marca = tbl_marca.marca
				WHERE tbl_animal.fazenda      = $_login_fazenda
				
				AND   tbl_animal.excluido IS NULL
				$query_adicional
				$lista_ordem
				LIMIT $paginaAtual,$paginaLimite
			";
	#echo nl2br($query);
	#exit;
	$banco = new BancodeDados(); 
	$sessionFacade = new SessionFacade($banco); 
	try {
		$banco->conecta(); 
		$retorno = $banco->executaSQL($query); 
		
		while($linha = $banco->fetchArray($retorno)) {
			$cabeca = $sessionFacade->recuperarAnimal($linha['animal']);
			$model->assign_block_vars('animal',array('DATAENTRADA'	=>	$cabeca->getEntrada(),
													'ANIMAL'		=>	$cabeca->getId(),
													'NUMERO'		=>	$cabeca->getNumero(),
													'NASCIMENTO'	=>	$cabeca->getNascimento(),
													'APELIDO'		=>	$cabeca->getApelido(),
													'PAI'			=>	$cabeca->getPai(),
													'MARCA'			=>	is_object($cabeca->getMarca())?$cabeca->getMarca()->getCodigo():"",
													'MAE'			=>	$cabeca->getMae(),
													'SEXO'			=>	$cabeca->getSexo(),
													'ESPECIE'		=>	is_object($cabeca->getEspecie())?$cabeca->getEspecie()->getNome():"",
													'RACA'			=>	is_object($cabeca->getRaca())?$cabeca->getRaca()->getNome():"",
													'TIPO_CRIACAO'	=>	$cabeca->getTipoCriacao(),
													'PROPRIETARIO'	=>	is_object($cabeca->getProprietario())?$cabeca->getProprietario()->getNome():"",
													'COMPRA'		=>	$cabeca->getValorCompra(),
													'PREVISAOVENDA'	=>	$cabeca->getPrevisaoValorVenda(),
													'OBS'			=>	$cabeca->getObservacao(),
													'NASCIMENTO'	=>	$cabeca->getNascimento(),
													'CRIAS'			=>	$cabeca->getCrias(),
													'PESO'			=>	$cabeca->getPeso(),
													'STAR'			=>	$cabeca->getStar()?"1":"0",
													'CLASSE'		=>  ($count++%2==0)?"class='odd'":"",
													'IDADE'			=>	$cabeca->getNascimento()
													));			  
		}
		$banco->desconecta(); 

	}catch(Exception $e) { 
		$banco->desconecta(); 
		//array_push($msg_erro,$e->getMessage());
		$model->assign_block_vars('naoecnontrado', array('MSG'	=>	'Nenhum animal neste lote!'.$e->getMessage()));
	}
	


$model->pparse('cadastro.animal.lista');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>

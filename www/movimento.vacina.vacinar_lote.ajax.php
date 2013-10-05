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
include_once "funcoes.php";

$theme = ".";
$model = new Template($theme);

	if (isset($_SESSION['login_fazenda'])){
		$login_fazenda=$_SESSION['login_fazenda'];
	}
	
$imprimir_cabecalho="";

header("Content-Type: text/html;  charset=ISO-8859-1",true);


##############################################################################
##############                      CABECALHO                  	##############
##############################################################################	

if (isset($_GET['cabecalho']) && strlen($_GET['cabecalho'])>0){
	$imprimir_cabecalho=$_GET['cabecalho'];
}

if ($imprimir_cabecalho=="true"){
	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>TeleMediaBR - {TITULO}</title>
	<link href="CalendarControl.css" rel=stylesheet type=text/css>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<SCRIPT language=javascript src="CalendarControl.js"></SCRIPT>
	<SCRIPT language=javascript src="funcoes.js"></SCRIPT>
	</head>
	<body>';
}

##############################################################################
##############                       DETALHES                  	##############
##############################################################################	

	if (isset($_GET['detalhes']) && strlen($_GET['detalhes'])>0){
		$codi=$_GET['detalhes'];
	
		$query = "SELECT tbl_animal.animal AS animal,
					 tbl_animal.numero  AS numero ,
					 tbl_animal.apelido   AS apelido,
					 tbl_animal.raca  AS raca,
					 tbl_animal.marca  AS marca,
					 tbl_animal.faixa  AS faixa,					 
					 tbl_animal.fazenda  AS fazenda,
					 DATE_FORMAT(tbl_animal.entrada , '%d/%m/%Y') AS entrada,
					 DATE_FORMAT(tbl_animal.saida , '%d/%m/%Y') AS saida,
					 tbl_animal.pai  AS pai,
					 tbl_animal.mae AS mae,
					 if(tbl_animal.sexo ='M', 'Masculino', 'Feminino') AS sexo,
					 DATE_FORMAT(tbl_animal.nascimento , '%d/%m/%Y') AS nascimento,					 
					 tbl_animal.tipo AS tipo,
					 tbl_animal.proprietario AS proprietario,
					 tbl_animal.valor_compra AS valor_compra,
					 tbl_animal.previsao_venda AS previsao_venda,
					 tbl_animal.valor_venda  AS valor_venda,
					 DATE_FORMAT(tbl_animal.previsao_data_venda , '%d/%m/%Y') AS previsao_data_venda,
					 tbl_animal.crias  AS crias,
					 tbl_animal.grupo  AS grupo,
					 tbl_animal.local   AS local,
					 tbl_animal.excluido  AS excluido,
					 tbl_animal.observacao  AS observacao,
					 DATE_FORMAT(tbl_animal.data_digitacao , '%d/%m/%Y') AS data_digitacao,
					 tbl_animal.star AS star,
					 tbl_animal.status AS status,
					 
					 tbl_raca.raca AS raca,
					 tbl_raca.codigo AS raca_codigo ,
					 tbl_raca.nome  AS raca_nome,
					 tbl_raca.descricao   AS raca_descricao,
					 tbl_raca.data   AS raca_data,
					 tbl_raca.status   AS raca_status,
					 tbl_raca.observacao    AS raca_observacao,
					 tbl_raca.ativo    AS raca_ativo,
					 
					 tbl_fazenda.nome AS fazenda_nome,
 					 tbl_fazenda.razao  AS fazenda_razao ,
					 tbl_fazenda.descricao AS fazenda_descricao,
					 tbl_fazenda.endereco AS fazenda_endereco,
					 tbl_fazenda.cidade AS fazenda_cidade,
					 tbl_fazenda.estado AS fazenda_estado,
					 tbl_fazenda.proprietario AS fazenda_proprietario,
					 tbl_fazenda.status AS fazenda_status 	,
					 
					 tbl_marca.marca AS marca_codigo,
					 tbl_marca.proprietario AS marca_proprietario			 
				
			 FROM tbl_animal
			 	LEFT JOIN tbl_fazenda USING(fazenda)
			 	LEFT JOIN tbl_raca ON tbl_animal.raca=tbl_raca.raca
				LEFT JOIN tbl_marca ON tbl_animal.marca=tbl_marca.marca
				WHERE tbl_animal.animal=$codi
				AND excluido IS NULL
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
##############                INDEXA O TEMPLATE             	##############
##############################################################################	


$model->set_filenames(array('movimento.vacina.vacinar_lote.ajax' => 'movimento.vacina.vacinar_lote.ajax.htm'));


##############################################################################
##############                  CARREGA OS DADOS             	##############
##############################################################################	


	$lote = " AND tbl_animal_lote.lote=".$_GET['lote'];
	$lote_tmp = trim($_GET['lote']);
	$query = "SELECT nome,
					DATE_FORMAT(data_inicio , '%d/%m/%Y') AS data_inicio,
					DATE_FORMAT(data_fim , '%d/%m/%Y') AS data_fim,					
					categoria,
					obs
				 FROM tbl_lote
					WHERE fazenda=$login_fazenda
					AND lote= $lote_tmp";	

	$rSet = $db->Query($query);
	if ($db->NumRows($rSet)>0){
		$linha 		= $db->FetchArray($rSet);
		$nome 		= $linha['nome'];
		$inicio 	= $linha['data_inicio'];
		$fim 		= $linha['data_fim'];
		$categoria 	= $linha['categoria'];
		$obs 		= $linha['obs'];	
		$model->assign_vars(array('LOTE' => "<div style='align:center;padding:15px 5px;font-size:16px;font-weight:bold'>Lote $nome<br> <b style='font-size:12px'>Categoria: $categoria <br> Início: $inicio - Fim: $fim</b><br><b style='font-size:10'>Obs: $obs</b></div>"));	
	}

	
	$query = "SELECT tbl_animal.animal AS animal,
					 tbl_animal.numero  AS numero ,
					 tbl_animal.apelido   AS apelido,
					 tbl_animal.raca  AS raca,
					 tbl_animal.marca  AS marca,
					 tbl_animal.faixa  AS faixa,					 
					 tbl_animal.fazenda  AS fazenda,
					 DATE_FORMAT(tbl_animal.entrada , '%d/%m/%Y') AS entrada,
					 DATE_FORMAT(tbl_animal.saida , '%d/%m/%Y') AS saida,
					 tbl_animal.pai  AS pai,
					 tbl_animal.mae AS mae,
					 if(tbl_animal.sexo ='M', 'Macho', 'Fêmea') AS sexo,
					 DATE_FORMAT(tbl_animal.nascimento , '%d/%m/%Y') AS nascimento,					 
					 tbl_animal.tipo AS tipo,
					 tbl_animal.proprietario AS proprietario,
					 0.00+(tbl_animal.valor_compra) AS valor_compra,
					 tbl_animal.previsao_venda AS previsao_venda,
					 0.00+(tbl_animal.valor_venda)  AS valor_venda,
					 DATE_FORMAT(tbl_animal.previsao_data_venda , '%d/%m/%Y') AS previsao_data_venda,
					 tbl_animal.crias  AS crias,
					 tbl_animal.grupo  AS grupo,
					 tbl_animal.local   AS local,
					 tbl_animal.excluido  AS excluido,
					 tbl_animal.observacao  AS observacao,
					 DATE_FORMAT(tbl_animal.data_digitacao , '%d/%m/%Y') AS data_digitacao,
					 tbl_animal.star AS star,
					 tbl_animal.status AS status,
					 tbl_animal.peso AS peso,				
					 round(DATEDIFF(NOW(), tbl_animal.nascimento)*12/365) AS idade,
					 
					 tbl_raca.raca AS raca,
					 tbl_raca.codigo AS raca_codigo ,
					 tbl_raca.nome  AS raca_nome,
					 tbl_raca.descricao   AS raca_descricao,
					 tbl_raca.data   AS raca_data,
					 tbl_raca.status   AS raca_status,
					 tbl_raca.observacao    AS raca_observacao,
					 tbl_raca.ativo    AS raca_ativo,
					 
					 tbl_fazenda.nome AS fazenda_nome,
 					 tbl_fazenda.razao  AS fazenda_razao ,
					 tbl_fazenda.descricao AS fazenda_descricao,
					 tbl_fazenda.endereco AS fazenda_endereco,
					 tbl_fazenda.cidade AS fazenda_cidade,
					 tbl_fazenda.estado AS fazenda_estado,
					 tbl_fazenda.proprietario AS fazenda_proprietario,
					 tbl_fazenda.status AS fazenda_status,
					 
					 tbl_marca.codigo AS marca_codigo,
					 tbl_marca.proprietario AS marca_proprietario,
					 tbl_marca.cor AS marca_cor
				
			 FROM tbl_animal
			 	LEFT JOIN tbl_fazenda USING(fazenda)
			 	LEFT JOIN tbl_raca ON tbl_animal.raca=tbl_raca.raca
				LEFT JOIN tbl_marca ON tbl_animal.marca=tbl_marca.marca
				JOIN tbl_animal_lote ON tbl_animal_lote.animal=tbl_animal.animal
				WHERE tbl_animal.fazenda=$login_fazenda
				AND tbl_animal.excluido IS NULL
				$lote
				";	
			 
	$rSet = $db->Query($query);
	$count=0;	
	while ($linha = $db->FetchArray($rSet)){
		  $model->assign_block_vars('animal', array('DATAENTRADA'	=>	$linha['entrada'],
																'ANIMAL'		=>	$linha['animal'],		  
																'NUMERO'		=>	$linha['numero'],
																'NASCIMENTO'	=>	$linha['nascimento'],
																'APELIDO'		=>	$linha['apelido'],
																'PAI'			=>	$linha['pai'],
																'MARCA'			=>	"<b style='color:".$linha['marca_cor']."'>".$linha['marca_codigo']."</b>",																
																'MAE'			=>	$linha['mae'],
																'SEXO'			=>	$linha['sexo'],
																'RACA'			=>	$linha['raca_nome'],									
																'TIPO'			=>	$linha['tipo'],
																'PROPRIETARIO'	=>	$linha['proprietario'],
																'COMPRA'		=>	$linha['valor_compra'],
																'PREVISAOVENDA'	=>	$linha['previsao_venda'],
																'OBS'			=>	$linha['observacao'],
																'NASCIMENTO'	=>	$linha['nascimento'],
																'APELIDO'		=>	$linha['apelido'],
																'PAI'			=>	$linha['pai'],
																'MAE'			=>	$linha['mae'],
																'SEXO'			=>	$linha['sexo'],
																'CRIAS'			=>	$linha['crias'],
																'TIPO'			=>	$linha['tipo'],
																'ENTRADA'		=>	$linha['entrada'],
																'COMPRA'		=>	$linha['valor_compra'],
																'PREVISAOVENDA'	=>	$linha['previsao_venda'],
																'OBS'			=>	$linha['observacao'],
																'PESO'			=>	$linha['peso'],																
																'STAR'			=>	$linha['star']?"1":"0",
																'CLASSE'		=>  ($count++%2==0)?"classe_1":"classe_2",
																'IDADE'			=>	$linha['idade']
																));			  
	}
	if ($count==0){
		$model->assign_block_vars('naoecnontrado', array('MSG'	=>	'Nenhum animal neste lote!'));
	}
	

		 
$model->pparse('movimento.vacina.vacinar_lote.ajax');


?>

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

header("Content-Type: text/html;  charset=ISO-8859-1",true);

$lista_ordem="";
$desc=" ASC";

$msg_erro="";
$msg="";

##############################################################################
##############                       ORDEM                   	##############
##############################################################################	

	if (isset($_SESSION["lista_ordem_vacinas"]) && strlen($_SESSION["lista_ordem_vacinas"])>0){
		$lista_ordem=$_SESSION['lista_ordem_vacinas'];
		$desc=$_SESSION['ASC_DESC'];
	}
	if (isset($_GET['ordem']) && strlen($_GET['ordem'])>0){
		$lista_ordem = $_GET['ordem'];
		if (isset($_SESSION["lista_ordem_vacinas"]) && strlen($_SESSION["lista_ordem_vacinas"])>0){
			if ($_GET['ordem']==$_SESSION["lista_ordem_vacinas"]){
				if (trim($desc)=="ASC")
					$desc=" DESC";
				else $desc=" ASC";
			}
		}
		$_SESSION['ASC_DESC'] = $desc;
		$_SESSION["lista_ordem_vacinas"]= $lista_ordem;
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
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="movimento";
$titulo="Animais Vacinados";
$sub_titulo="Lista dos Animais Vacinados";

include "cabecalho.php";

##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$model->set_filenames(array('movimento.vacina.vacinados' => 'movimento.vacina.vacinados.htm'));


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

####### PAGINAÇÂO - INICIO

	$query = "SELECT count(*)				
			 FROM tbl_animal
			 	LEFT JOIN tbl_fazenda USING(fazenda)
			 	LEFT JOIN tbl_raca ON tbl_animal.raca=tbl_raca.raca
				LEFT JOIN tbl_marca ON tbl_animal.marca=tbl_marca.marca
				JOIN tbl_aplicacao_vacina ON tbl_aplicacao_vacina.animal=tbl_animal.animal
				JOIN tbl_vacina ON tbl_vacina.vacina = tbl_aplicacao_vacina.vacina			
				WHERE tbl_animal.fazenda=$login_fazenda
				AND excluido IS NULL
				";	
	$rSet = $db->Query($query);
	$linha = $db->FetchArray($rSet);
	$numero_registro = $linha[0];

	
	if (!isset($_GET['pg']) || empty($_GET['pg']))	$_GET['pg'] = 1;
	if ($_GET['pg']==0) $_GET['pg'] = 1;		
	$npp = 20;
	$paginaAtual = $_GET['pg'];
	$numero_paginas = ceil($numero_registro/$npp);
	
	$PAGINACAO = "";
	$tmp=$paginaAtual-1;
	if ($paginaAtual==1)
		$PAGINACAO .= "<span class='next'>&#171; Anterior</span>";
	if ($paginaAtual>1)
		$PAGINACAO .= "<a href='?pg=$tmp' class='next'  title='Voltar para a Página Anterior'><b>Anterior</b></a>";

	for ($i=1;$i<=$numero_paginas;$i++){
		if ($paginaAtual==$i){
			if ($numero_paginas>1) $PAGINACAO .= "<span class=\"current\">$i</span>";
			}
		else
			$PAGINACAO .= "<a href='?pg=$i' title='Ir para página $i'>$i</a>";
	}

	$tmp=$paginaAtual+1;
	if ($paginaAtual < $numero_paginas) $PAGINACAO .= " <a href='?pg=$tmp' class='next' title='Ir para a próxima página'><b>Próximo &#187;</b></a>";
	else								$PAGINACAO .= "<span class='next'>Próximo &#187;</span>";;
	
	$paginaAtual = ($paginaAtual-1)*$npp;
	$paginaLimite = $npp;
	
	$tmp1=$paginaAtual+1;
	$tmp2=$paginaAtual+$npp;	
	$model->assign_vars(array('PAGINACAO' => $PAGINACAO."<br><br>Animal <b>$tmp1</b> até <b>$tmp2</b> de um total de <b>$numero_registro</b>"));
####### PAGINAÇÂO - FIM

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
					 tbl_marca.cor AS marca_cor,
					 
					 DATE_FORMAT(tbl_aplicacao_vacina.data , '%d/%m/%Y') AS vacina_data,					 
					 tbl_aplicacao_vacina.observacao AS vacina_obs,
					 
					 tbl_vacina.nome AS vacina_nome
				
			 FROM tbl_animal
			 	LEFT JOIN tbl_fazenda USING(fazenda)
			 	LEFT JOIN tbl_raca ON tbl_animal.raca=tbl_raca.raca
				LEFT JOIN tbl_marca ON tbl_animal.marca=tbl_marca.marca
				JOIN tbl_aplicacao_vacina ON tbl_aplicacao_vacina.animal=tbl_animal.animal
				JOIN tbl_vacina ON tbl_vacina.vacina = tbl_aplicacao_vacina.vacina
				WHERE tbl_animal.fazenda=$login_fazenda
				AND excluido IS NULL
				ORDER BY vacina_data DESC
				LIMIT $paginaAtual,$paginaLimite
				";	
			 
	$rSet = $db->Query($query);
	$msg_erro .= $db->MyError();
	
	if (isset($msg) && strlen($msg)>0){
		$model->assign_vars(array('MSG' => "<br><div id='msg_ok'><img src='imagens/warning.gif' align='absmiddle' style='padding-right:5px'/> $msg</div>"));	
	}
	if (isset($msg_erro) && strlen($msg_erro)>0){
		$model->assign_vars(array('MSG' => "<br><div id='msg_ok'><img src='imagens/warning.gif' align='absmiddle' style='padding-right:5px'/> $msg</div>"));	
	}	
	
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
																'CRIAS'			=>	$linha['crias'],
																'ENTRADA'		=>	$linha['entrada'],
																'COMPRA'		=>	$linha['valor_compra'],
																'PESO'			=>	$linha['peso'],																
																'DATAVACINA'	=>	$linha['vacina_data'],																
																'NOMEVACINA'	=>	$linha['vacina_nome'],																																																
																'STAR'			=>	$linha['star']?"1":"0",
																'CLASSE'		=>  ($count++%2==0)?"classe_1":"classe_2",
																'IDADE'			=>	$linha['idade']
																));			  
	}
	if ($count==0){
		$model->assign_block_vars('naoecnontrado', array('MSG'	=>	'Nenhum animal neste lote!'));
	}				

		 
																				  	
	

				
$model->pparse('movimento.vacina.vacinados');

##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>

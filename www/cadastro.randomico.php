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

require_once('banco.inc.php');

		
##############################################################################
##############                INCLUDES : CABECALHO             	##############
##############################################################################	

$layout="cadastro";
$titulo="Animais Cadastrados";
$sub_titulo="Lista dos Animais Cadastrados";

include "cabecalho.php";


##############################################################################
##############                      PAGINA                  	##############
##############################################################################	

	for ($i=1;$i<=250;$i++){
		$numero=rand(10,5000);
		$apelido=rand();
		$raca=rand(1,3);
		$marca=rand(1,3);
		$faixa=rand(10,99);		
		$fazenda=rand(1,2);
		$entrada=rand(2000,2006)."-".rand(10,12)."-".rand(10,30);
		//$saida=rand(2005,2006)."-".rand(10,12)."-".rand(10,30);
		$pai=rand();
		$mae=rand();
		$sexo=(rand(1,2)==1)?"f":"m";
		$nascimento=rand(2000,2006)."-".rand(10,12)."-".rand(10,30);
		$tipo=rand(1,3);
		$proprietario=rand(1,5);
		$valor_compra=rand(10,5000);
		$previsao_venda=rand(10,5000);
		$valor_venda=rand(10,5000);
		$previsao_data_venda=rand(2007,2008)."-".rand(10,12)."-".rand(10,30);
		$crias=rand(0,99);
		$grupo=rand(1,99);
		$local=rand(1,99);
		//$excluido=rand(2000,2007)."-".rand(10,12)."-".rand(10,30);
		$observacao="Observações gerais...";
		$data_digitacao=rand(2000,2006)."-".rand(10,12)."-".rand(10,30);
		$star=rand(0,1);				
		$status=rand(0,1);				
		$peso=rand(1,300);								
		
		$query = "INSERT INTO tbl_animal
					(
					 numero,
					 apelido,
					 raca,
					 marca,
					 faixa,
					 fazenda,
					 entrada,
					 pai,
					 mae,
					 sexo,
					 nascimento,					 
					 tipo ,
					 proprietario,
					 valor_compra,
					 previsao_venda,
					 valor_venda,
					 previsao_data_venda,
					 crias,
					 grupo,
					 local,
					 observacao,
					 data_digitacao,
					 star,
					 status,
					 peso)					 
					 VALUES (
		'$numero',
		'$apelido',
		$raca,
		$marca,
		$faixa,
		$fazenda,
		'$entrada',
		'$pai',
		'$mae',
		'$sexo',
		'$nascimento',
		$tipo,
		$proprietario,
		$valor_compra,
		$previsao_venda,
		$valor_venda,
		'$previsao_data_venda',
		$crias,
		$grupo,
		$local,
		'$observacao',
		'$data_digitacao',
		$star,
		$status,
		$peso		 
					 )
				";	
//				echo $query;
			 
		$rSet = $db->Query($query);

	}
	

	
##############################################################################
##############                INCLUDES : RODAPE             	##############
##############################################################################	

include "rodape.php";


?>

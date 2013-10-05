<?php
/******************************************************************
Script .........: Controle de Gado e Fazendas
Por ............: Fabio Nowaki
Data ...........: 30/08/2006
********************************************************************************************/
//ini_set('session.cache_limiter', 'private');

//	$destroi	=	session_destroy();****************************************/

##############################################################################
## INCLUDES E CONEXÔES BANCO
##############################################################################

session_start();

include "class.Template.inc.php";
require('class.dbMySQL.inc.php');

$db = new dbMySQL();
$link_id = $db->Connect("localhost", "root", "");
$db->dbSelect("fazenda");

//$link_id = $db->Connect("mysql.kinghost.net", "telemediajp", "112233");
//$db->dbSelect("telemediajp");	


##############################################################################
##############                INDEXA O TEMPLATE             	##############
##############################################################################	

$theme = ".";
	
$model = new Template($theme);

$model->set_filenames(array('pagina' => 'principal.htm'));


##############################################################################
##############      AUTENTICACAO E REDIRECIONA PARA MODULOS     ##############
##############################################################################	

if (!isset($_SESSION['sess_nome']))
{	
	$model->set_filenames(array('pagina' => 'login.htm'));
	if (isset($_REQUEST['modulo']) && $_REQUEST['modulo']=="login")	
		$model->assign_vars(array('MSG' => "Nome de usuário ou senha inválidos. Tente novamente."));		
	else
		$model->assign_vars(array());
	$model->pparse('pagina');
	exit();
}

$model->assign_vars(array('LOGIN' => $_SESSION['sess_login']));		

##############################################################################
##############      AUTENTICACAO E REDIRECIONA PARA MODULOS     ##############
##############################################################################	

if (!isset($_REQUEST['modulo'])) $modulo="inicio";
else $modulo = $_REQUEST['modulo'];


if (isset($_POST['MudarFazenda']) || isset($_POST['MudarFazenda_x'])){
	$codfaz = $_POST['fazendas'];
	$rSet = $db->Query("SELECT * FROM fazendas WHERE codigo='$codfaz' LIMIT 1");
	$row = $db->FetchArray($rSet);
	$_SESSION['sess_fazenda_codigo']= $row['codigo'];
	$_SESSION['sess_fazenda_nome']= $row['nome'];
	$modulo="adm";
}


$rSet = $db->Query("SELECT * FROM fazendas");
$lista_fazendas = "";
while ($row = $db->FetchArray($rSet)){
		if (isset($_SESSION['sess_fazenda_codigo']) && $_SESSION['sess_fazenda_codigo']==$row['codigo'])
			$lista_fazendas .= '<option value="'.$row['codigo'].'" SELECTED>'.$row['nome'].'</option>';
		else
			$lista_fazendas .= '<option value="'.$row['codigo'].'">'.$row['nome'].'</option>';		
}
			
if (!isset($_SESSION['sess_fazenda_codigo']))
	$modulo="inicio";
else{
	$fazenda_codigo = $_SESSION['sess_fazenda_codigo'];
	$model->assign_vars(array('NOMEFAZENDA' => $_SESSION['sess_fazenda_nome'])); 
}


##############################################################################
##############                   FUNÇÕES GERAIS                 ##############
##############################################################################	

function converte_data($date)
{
    $date = explode("-", ereg_replace('/', '-', $date));
    $date = ''.$date[2].'/'.$date[1].'/'.$date[0];
    return $date;
}

function mover_animal($valor_campo,$data_move,$destino,$db,$fazenda_codigo,$totalCusto){
	$rSet = $db->Query("SELECT * FROM animais WHERE  status='1' AND codigo='$valor_campo' LIMIT 1");
	$row = $db->FetchArray($rSet);
	$dataEntrada = date("Y-m-d",strtotime($row['data_entrada']));
	$dataSaida = date("Y-m-d",strtotime($data_move));
	
    $dataSaidaSeparado = explode("-", ereg_replace('/', '-', $dataSaida));
		
	$rSet = $db->Query("SELECT * FROM financas WHERE mes='".$dataSaidaSeparado[1]."' AND ano='".$dataSaidaSeparado[0]."' AND fazenda='$fazenda_codigo' LIMIT 1");
	$row = $db->FetchArray($rSet);
	
	$erro="";
	$retorno="";
	
	if ($row){
		if ($row['status']=="0"){
			$erro .="<br>Erro: o mês selecionado já está faturado. Por favor, verifique a data.";
		}
		else{
			if ($dataSaida>=$dataEntrada){
				$retorno = atualiza_custo_animal($valor_campo,$data_move,$totalCusto,$db,$fazenda_codigo);
				if ($retorno) $erro .= "<br>Erro na atualização dos gastos do animal.";
			}
			else {
				$erro .= "<br>Data da informada é anterior a data da entrada do animal na fazenda. Por favor, verifique.";
			}
		}
	}
	else{
		$erro .="Nenhum mês encontrado. Por favor verifique o mês selecionado.";
	}
	return $erro;
}

function vender_animal($valor_campo,$data_venda,$preco_venda,$db,$fazenda_codigo){
	$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
	$row = $db->FetchArray($rSet);
	$dataEntrada = date("Y-m-d",strtotime($row['data_entrada']));
	$dataSaida = date("Y-m-d",strtotime($data_venda));
	
    $dataSaidaSeparado = explode("-", ereg_replace('/', '-', $dataSaida));
	
	// pega custo por dia
	$rSetGastos = $db->Query("SELECT * FROM custos WHERE fazenda='$fazenda_codigo'");
	$rowGastos = $db->FetchArray($rSetGastos);
	$totalCusto = $rowGastos['funcionarios']+$rowGastos['vacinas']+$rowGastos['sal']+$rowGastos['administracao']+$rowGastos['arrendamentos']+$rowGastos['hormonios']+$rowGastos['outros'];
						
	
	$rSet = $db->Query("SELECT * FROM financas WHERE mes='".$dataSaidaSeparado[1]."' AND ano='".$dataSaidaSeparado[0]."' AND fazenda='$fazenda_codigo' LIMIT 1");
	$row = $db->FetchArray($rSet);
	
	$erro="";
	$retorno="";
	
	if ($row){
		if ($row['status']=="0"){
			$erro .="<br>Erro: o mês selecionado já está faturado. Por favor, verifique a data.";
		}
		else{
			if ($dataSaida>=$dataEntrada){
				$retorno = atualiza_custo_animal($valor_campo,$data_venda,$totalCusto,$db,$fazenda_codigo);
				if (!$retorno){
					$rSet = $db->Query("UPDATE financas SET vendas=vendas+'$preco_venda',vendidos=vendidos+1 WHERE codigo='".$row['codigo']."' AND fazenda='$fazenda_codigo' LIMIT 1");
					
				}
				else $erro .= $retorno;
			}
			else {
				$erro .= "<br>Data da informada é anterior a data da entrada do animal na fazenda. Por favor, verifique.";
			}
		}
	}
	else{
		$erro .="Nenhum mês encontrado. Por favor verifique o mês selecionado.";
	}
	return $erro;
}

function atualiza_custo_animal($codigoAnimal,$data,$custos,$db,$fazenda_codigo){
	
	$erros = "";
	$valor = 0;

	$rSetAnimal = $db->Query("SELECT * FROM animais WHERE codigo='$codigoAnimal' LIMIT 1");
	$rowAnimal = $db->FetchArray($rSetAnimal);
	$dataEntrada = date("Y-m-d",strtotime($rowAnimal['data_entrada']));
	$dataSaida = date("Y-m-d",strtotime($data));

    $dataEntradaSeparado = explode("-", ereg_replace('/', '-', $dataEntrada));	
    $dataSaidaSeparado = explode("-", ereg_replace('/', '-', $dataSaida));
	
	$rSet = $db->Query("SELECT * FROM financas WHERE mes='".$dataSaidaSeparado[1]."' AND ano='".$dataSaidaSeparado[0]."' AND fazenda='$fazenda_codigo' LIMIT 1");
	$row = $db->FetchArray($rSet);	
	if ($row){
		if ($row['status']=="0"){
			$erros .="<br>Erro: o mês selecionado já está faturado. Por favor, verifique a data.";
		}
		else {
			if ($dataSaida>=$dataEntrada){
				$valor=$custos;
				if ($dataSaidaSeparado[1]==$dataEntradaSeparado[1])
					$valor = ($custos*($dataEntradaSeparado[1]-$dataSaidaSeparado[2]))/30;
				else if ($dataSaidaSeparado[2]<30)
						$valor = ($dataSaidaSeparado[2]*$custos)/30;
				
			} else $erros .= "<br>Data da informada é anterior a data da entrada do animal na fazenda. Por favor, verifique.";
		}
	}
	else{
		$erros .="Nenhum mês do faturamento encontrado. Por favor verifique o mês selecionado.";
	}
	if ($erros=="")	{
	
				
				$rSetSeparado = $db->Query("SELECT * FROM financas WHERE codigo='".$row['codigo']."' LIMIT 1");
				$rowSeparado = $db->FetchArray($rSetSeparado);
				$marcas = explode("|", $rowSeparado['marcas']);
				$marcas2="";
				$i=0;
				$achou=0;
				for($i=0;$i<sizeof($marcas);$i++){
					$dados = explode("=", $marcas[$i]);
					if ($dados[0]==$rowAnimal['proprietario']){
						$dados[1]=$dados[1]+$valor;
						$marcas[$i] = $dados[0]."=".$dados[1];
						$achou=1;
					}
				}
				if ($achou==0){
					array_push($marcas,$rowAnimal['proprietario']."=".$valor);
					if ($i==0)
						$marcas2 = $marcas;
					else $marcas2 = implode("|", $marcas);
				}
				else {
					$marcas2 = implode("|", $marcas);
				}
					
	
		$rSet = $db->Query("UPDATE financas SET valor=valor+'$valor', marcas='$marcas2' WHERE codigo='".$row['codigo']."' LIMIT 1");
		return false;
		}
	else {
		return $erro;
	}

}

##############################################################################
##############                      INICIO                  	##############
##############################################################################

$model->assign_vars(array('FAZENDAS' => $lista_fazendas));
		
switch ($modulo) {
  case "inicio":
  			
			//$model->assign_block_vars('pg_inicial', array());
			$model->assign_block_vars('menu_inicio', array('NOME' => $_SESSION['sess_nome'],
														   'EMAIL' =>$_SESSION['sess_email'],
														   'CODIGO' =>$_SESSION['sess_codigo'],
														   'LOGIN' =>$_SESSION['sess_login'] ));  

			$model->assign_block_vars('inicio', array('FAZENDAS' => $lista_fazendas)); 														
												   	
	  break;
	  
  case "adm":
		$msg ="";		
		$erros ="";
		$model->assign_block_vars('menu_adm', array());  		
		####### ANIMAIS ########
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="animais"){
			$model->assign_block_vars('animais', array());  
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="cadastrar"){		
				if (isset($_POST['Cadastrar'])){
					$txtdataentrada="".converte_data($_POST['txtdataentrada']);
					$txtnumero="".addslashes($_POST['txtnumeroA']);
					$txtproprietario="".addslashes($_POST['txtproprietario']);
					$txtidade="".addslashes($_POST['txtidade']);	
					$txtcompra=str_replace(".","","".addslashes($_POST['txtcompra']));
					$txtobs="".addslashes($_POST['txtobs']);
/*				for ($i=0;$i<1000;$i++){
					$txtnumero=rand(300, 1500);
					$txtidade=rand(1,60);
					$txtcompra=rand(600,9000);
					$txtproprietario=rand(1,2);
					$fazenda_codigo=rand(2,3);*/
					$rSet = $db->Query("INSERT into animais (data,data_entrada,numero,proprietario,fazenda,idade,preco_compra,obs) values ('".date('Y-m-d')."','$txtdataentrada','$txtnumero','$txtproprietario','$fazenda_codigo','$txtidade','$txtcompra','$txtobs')");
					
					$msg .= "Animal adicionado com sucesso. <a href='?modulo=adm&menu=animais&sub=visao'>Clique aqui para visualizar os animais.</a>";			
				}
				$lista_proprietarios="";
				$rSet = $db->Query("SELECT * FROM proprietarios");
				while ($row = $db->FetchArray($rSet))
					$lista_proprietarios .= '<option value="'.$row['codigo'].'">'.$row['marca'].'</option>';		
				
				$model->assign_vars(array('BOOT' => 'txtnumeroA'));
				$model->assign_block_vars('animais.cadastrar', array('DATAENTRADA' => date('d/m/Y'),'PROPRIETARIOS' => $lista_proprietarios,'FAZENDAS' => $lista_fazendas, 'MSG' => $msg));
			}
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="detalhes"){	
				if (isset($_POST['gravar'])){
					$txtcodigo="".addslashes($_POST['txtcodigo']);
					$txtnumero="".addslashes($_POST['txtnumeroA']);
					$txtproprietario="".addslashes($_POST['txtproprietario']);
					$txtidade="".addslashes($_POST['txtidade']);	
					$txtcompra=str_replace(".","","".addslashes($_POST['txtcompra']));																
					$txtvenda=str_replace(".","","".addslashes($_POST['txtvenda']));																					
					$txtvacinas=str_replace("\n","|","".addslashes($_POST['txtvacinas']));
					$txttransfer=str_replace("\n","|","".addslashes($_POST['txttransfer']));
					$txthormonios=str_replace("\n","|","".addslashes($_POST['txthormonios']));
					$txtcios=str_replace("\n","|","".addslashes($_POST['txtcios']));
					$txtultrasom=str_replace("\n","|","".addslashes($_POST['txtultrasom']));																				
					$txtobs="".addslashes($_POST['txtobs']);
					
					$rSet = $db->Query("UPDATE animais SET	numero='$txtnumero', proprietario='$txtproprietario', idade='$txtidade',	preco_compra='$txtcompra', preco_venda='$txtvenda', vacinas='$txtvacinas', transferencias='$txttransfer', hormonios='$txthormonios', ultrason='$txtultrasom', cios='$txtcios', obs='$txtobs' WHERE codigo='$txtcodigo' LIMIT 1");
					$msg .= "Dados do animal alterado com sucesso. <a href='?modulo=adm&menu=animais&sub=visao'>Clique aqui para visualizar os animais.</a>";			
					$model->assign_block_vars('animais.aviso', array('MSG' => $msg));
				}
				else{
					if (isset($_POST['txtnumero'])){
						$procuraNumero = "".addslashes($_POST['txtnumero']);
						$procuraMarca = "".addslashes($_POST['RadioGroup']);
						$rSet = $db->Query("SELECT * FROM animais WHERE numero='$procuraNumero' AND proprietario='$procuraMarca' AND fazenda='$fazenda_codigo' LIMIT 1");
						$row = $db->FetchArray($rSet);
						}
					else {
						if (isset($_REQUEST['codigo'])){
							$codigo_alterar = $_REQUEST['codigo'];
							$rSet = $db->Query("SELECT * FROM animais WHERE codigo ='$codigo_alterar' LIMIT 1");
							$row = $db->FetchArray($rSet);
							}
						}
					}
					
					if ($row){
					
						$rSet = $db->Query("SELECT * FROM proprietarios");
						$lista_proprietarios="";
						while ($rowMarca = $db->FetchArray($rSet)){
							if ($rowMarca['codigo'] == $row['proprietario'])
								$lista_proprietarios .= '<option value="'.$rowMarca['codigo'].'" selected>'.$rowMarca['marca'].'</option>';
							else $lista_proprietarios .= '<option value="'.$rowMarca['codigo'].'">'.$rowMarca['marca'].'</option>';
							}
							
							
						///////////// Seleciona proximo e anterior
						$rSetNext = $db->Query("SELECT codigo FROM animais WHERE status='1' AND fazenda='$fazenda_codigo' AND codigo>'".$row['codigo']."' ORDER BY codigo ASC");
						$rowNext = $db->FetchArray($rSetNext);
						$proximo = $rowNext['codigo'];
						
						$rSetNext = $db->Query("SELECT codigo FROM animais WHERE status='1' AND fazenda='$fazenda_codigo' AND codigo<'".$row['codigo']."' ORDER BY codigo DESC");
						$rowNext = $db->FetchArray($rSetNext);
						$anterior = $rowNext['codigo'];
						///////////// Seleciona proximo e anterior						
							
						$model->assign_block_vars('animais.detalhes', array('CODIGO' => $row['codigo'],
																		  'NUMERO' => $row['numero'],
																		  'MARCA' => $row['proprietario'],
																		  'IDADE' => $row['idade'],
																		  'DATA_ENTRADA' => date("d/m/Y",strtotime($row['data_entrada'])),
																		  'DATA_SAIDA' => $row['data_saida']?date("d/m/Y",strtotime($row['data_saida'])):"Não vendido",
																		  'PRECO_COMPRA' => number_format($row['preco_compra'], 2, ',', '.'),
																		  'PRECO_VENDA' => number_format($row['preco_venda'], 2, ',', '.'),
																		  'VACINAS' => str_replace("|","\n",$row['vacinas']),
																		  'HORMONIOS' =>  str_replace("|","\n",$row['hormonios']),
																		  'CIOS' =>  str_replace("|","\n",$row['cios']),
																		  'TRANSFERENCIAS' =>  str_replace("|","\n",$row['transferencias']),
																		  'ULTRASON' =>  str_replace("|","\n",$row['ultrason']),
																		  'OBS' => $row['obs'],
																		  'PROPRIETARIOS' => $lista_proprietarios,
																		  'FAZENDAS' => $lista_fazendas,
																		  'PROXIMO' => $proximo?$proximo:$row['codigo'],
																		  'ANTERIOR' => $anterior?$anterior:$row['codigo']));
					}
					else {
						$model->assign_block_vars('animais.aviso', array('MSG' => "Nenhum animal encontrado."));
						}
				
			}			
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="acao"){		
				if (isset($_POST['animais']))
					{
					if (isset($_POST['mover_x']))
						{
						$codigoAnimal = "";
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados' align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
							
						//$lista_fazendas
						// contem a lista das fazendas // criado no topo deste doc
						
						$model->assign_block_vars('animais.acao_mover', array('FAZENDAS' => $lista_fazendas,'MSG' => $msg));

						$model->assign_block_vars('animais.acao_mover.selecionados', array());
						while (list($campo,$valor_campo) = each($_POST['animais']))	{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_mover.selecionados.dados', array('CODIGO' => $valor_campo,
																						  'NUMERO' => $row['numero'],
																						  'MARCA' => $row['proprietario'],
																						  'IDADE' => $row['idade'],
																						  'COMPRA' =>  number_format($row['preco_compra'], 2, ',', '.'),
																						  'DATA' => $row['data_entrada'],
																						  'OBS' => $row['obs'])); 
								}
						}
					if (isset($_POST['mover2_x']))
						{
						$rSet = $db->Query("SELECT * FROM custos WHERE fazenda='$fazenda_codigo' LIMIT 1");
						$row = $db->FetchArray($rSet);
						$totalGastos = $row['funcionarios']+$row['vacinas']+$row['sal']+$row['administracao']+$row['arrendamentos']+$row['hormonios']+$row['outros'];
						
						$msg="";
						$praonde = $_POST['select_fazenda'];
						$data = converte_data($_POST['txtdataT']);
						$i=0;
						while (list($campo,$valor_campo) = each($_POST['animais']))
							{	
								$msgRetorno = mover_animal($valor_campo,$data,$praonde,$db,$fazenda_codigo,$totalGastos);
								if ($msgRetorno==""){
									$rSet = $db->Query("UPDATE animais SET fazenda='$praonde', data_entrada='$data' WHERE codigo='$valor_campo' LIMIT 1");
									$i++;
								}
								else{
									$msg .= "<br>Erro: ".$msgRetorno;
								}
								
							}
						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Mover",'MSG' => "$i animais foram transferidos de fazenda com sucesso.<br>$msg"));	
						}
						
					if (isset($_POST['MoverUnico']))
						{
						$txtcodigo  = "".addslashes($_POST['animais']);
						$txtdestino = "".addslashes($_POST['fazenda_destino']);
						$data = converte_data($_POST['txtdataT']);
						$txtmarca = "".addslashes($_POST['txtmarca']);						
						$txtnumero = "".addslashes($_POST['txtnumero']);												
						
						//// calcular gastos
						$rSet = $db->Query("SELECT * FROM custos WHERE fazenda='$fazenda_codigo' LIMIT 1");
						$row = $db->FetchArray($rSet);
						$totalGastos = $row['funcionarios']+$row['vacinas']+$row['sal']+$row['administracao']+$row['arrendamentos']+$row['hormonios']+$row['outros'];
						/// fim calcular gastos
						
						$msgRetorno = mover_animal($txtcodigo,$data,$txtdestino,$db,$fazenda_codigo,$totalGastos);
								if ($msgRetorno==""){
									$rSet = $db->Query("UPDATE animais SET fazenda='$txtdestino', data_entrada='$data' WHERE codigo='$txtcodigo' LIMIT 1");
									$msg .= "O animal $txtnumero da marca $txtmarca foi movido para a fazenda selecionada com sucesso.";
								}
								else{
									$msg .= "Erro: ".$msgRetorno;
								}
						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Mover",'MSG' => $msg));	
						}						
					if (isset($_POST['vender_x']))
						{
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados'  align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
						$model->assign_block_vars('animais.acao_vender', array('MSG' => $msg));						
						$model->assign_block_vars('animais.acao_vender.selecionados', array());
						$msg .="A venda esta sendo executada...";
						while (list($campo,$valor_campo) = each($_POST['animais']))
							{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_vender.selecionados.dados', array('CODIGO' => $valor_campo,
																					  'NUMERO' => $row['numero'],
																					  'MARCA' => $row['proprietario'],
																					  'IDADE' => $row['idade'],
																					  'DATA' => $row['data_entrada'],
																					  'COMPRA' => number_format($row['preco_compra'], 2, ',', '.'),	
																					  'OBS' => $row['obs'])); 
							}
						}
					if (isset($_POST['txtpreco_venda']))
						{
						$preco_venda = str_replace(".","",$_POST['txtpreco_venda']);
						$data_venda = converte_data($_POST['txtdata_venda']);
						$campo="";
						while (list($campo,$valor_campo) = each($_POST['animais']))
							{	
								$retorno=vender_animal($valor_campo,$data_venda,$preco_venda,$db,$fazenda_codigo);
								if ($retorno==""){
									$rSet = $db->Query("UPDATE animais SET preco_venda='$preco_venda', data_saida='$data_venda', status='0' WHERE codigo='$valor_campo' LIMIT 1");	
								} else $erros .= "<br>Erro: Animal ".$valor_campo." -> $retorno";
							}
						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Venda",'MSG' => "Os $campo animais selecionados foram vendidos com sucesso. (R$ $preco_venda)<br>$erros "));	
						}
					if (isset($_POST['txtvalorVendaUnico']))
						{
						$txtcodigo  = "".addslashes($_POST['animais']);
						$data_venda = converte_data($_POST['txtdata_venda']);						
						$txtvalor = str_replace(".","","".addslashes($_POST['txtvalorVendaUnico']));
						$txtmarca = "".addslashes($_POST['txtmarca']);						
						$txtnumero = "".addslashes($_POST['txtnumero']);												
								
						$retorno=vender_animal($txtcodigo,$data_venda,$txtvalor,$db,$fazenda_codigo);
						if ($retorno==""){
							$rSet = $db->Query("UPDATE animais SET preco_venda='$txtvalor', data_saida='$data_venda' WHERE codigo='$txtcodigo' LIMIT 1");	
						} else $erros .= "<br>Erro: Animal $txtnumero ($txtmarca) -> $retorno";
																
						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Venda",'MSG' => "O animal $txtnumero da marca $txtmarca foi vendido por R$ $txtvalor.<br><br>$erros"));	
						}														
					if (isset($_POST['hormonio_x']))
						{
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados' align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
							
						$model->assign_block_vars('animais.acao_hormonio', array('FAZENDAS' => $lista_fazendas,'MSG' => $msg));

						$model->assign_block_vars('animais.acao_hormonio.selecionados', array());
						while (list($campo,$valor_campo) = each($_POST['animais']))	{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_hormonio.selecionados.dados', array('CODIGO' => $valor_campo,
																						  'NUMERO' => $row['numero'],
																						  'MARCA' => $row['proprietario'],
																						  'IDADE' => $row['idade'],
																						  'COMPRA' =>  number_format($row['preco_compra'], 2, ',', '.'),
																						  'DATA' => $row['data_entrada'],
																						  'OBS' => $row['obs'])); 
								}
						}	
					if (isset($_POST['txtdataHormonio']))
						{
						$campo="";
						$data = $_POST['txtdataHormonio'].'|';
						while (list($campo,$valor_campo) = each($_POST['animais']))
							{	
								$rSet = $db->Query("UPDATE animais SET hormonios=CONCAT(hormonios,'$data') WHERE codigo='$valor_campo' LIMIT 1");
							}
						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Hormônios",'MSG' => "Os $campo animais selecionados sofreram aplicação de hormônio em $data com sucesso."));	
						}									
			
					if (isset($_POST['vacina_x']))
						{
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados' align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
							
						$model->assign_block_vars('animais.acao_vacinas', array('FAZENDAS' => $lista_fazendas,'MSG' => $msg));

						$model->assign_block_vars('animais.acao_vacinas.selecionados', array());
						while (list($campo,$valor_campo) = each($_POST['animais']))	{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_vacinas.selecionados.dados', array('CODIGO' => $valor_campo,
																						  'NUMERO' => $row['numero'],
																						  'MARCA' => $row['proprietario'],
																						  'IDADE' => $row['idade'],
																						  'COMPRA' =>  number_format($row['preco_compra'], 2, ',', '.'),
																						  'DATA' => $row['data_entrada'],
																						  'OBS' => $row['obs'])); 
								}
						}	
					if (isset($_POST['txtdataVacinas']))
						{
						$campo="";
						$data = $_POST['txtdataVacinas'].'('.$_POST['txtTipoVacinas'].')|';
						while (list($campo,$valor_campo) = each($_POST['animais']))
								$rSet = $db->Query("UPDATE animais SET vacinas=CONCAT(vacinas,'$data') WHERE codigo='$valor_campo' LIMIT 1");

						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Vacinas",'MSG' => "Os $campo animais selecionados sofreram aplicação de hormônio em $data com sucesso."));	
						}	
					if (isset($_POST['transferencia_x']))
						{
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados' align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
							
						$model->assign_block_vars('animais.acao_transferencia', array('FAZENDAS' => $lista_fazendas,'MSG' => $msg));

						$model->assign_block_vars('animais.acao_transferencia.selecionados', array());
						while (list($campo,$valor_campo) = each($_POST['animais']))	{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_transferencia.selecionados.dados', array('CODIGO' => $valor_campo,
																						  'NUMERO' => $row['numero'],
																						  'MARCA' => $row['proprietario'],
																						  'IDADE' => $row['idade'],
																						  'COMPRA' =>  number_format($row['preco_compra'], 2, ',', '.'),
																						  'DATA' => $row['data_entrada'],
																						  'OBS' => $row['obs'])); 
								}
						}	
					if (isset($_POST['txtdataTransferencia']))
						{
						$campo="";
						$data = $_POST['txtdataTransferencia'].'|';
						while (list($campo,$valor_campo) = each($_POST['animais']))
								$rSet = $db->Query("UPDATE animais SET transferencias=CONCAT(transferencias,'$data') WHERE codigo='$valor_campo' LIMIT 1");

						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Transferência",'MSG' => "Os $campo animais selecionados sofreram transferência em $data com sucesso."));	
						}							
					if (isset($_POST['cio_x']))
						{
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados' align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
							
						$model->assign_block_vars('animais.acao_cio', array('FAZENDAS' => $lista_fazendas,'MSG' => $msg));

						$model->assign_block_vars('animais.acao_cio.selecionados', array());
						while (list($campo,$valor_campo) = each($_POST['animais']))	{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_cio.selecionados.dados', array('CODIGO' => $valor_campo,
																						  'NUMERO' => $row['numero'],
																						  'MARCA' => $row['proprietario'],
																						  'IDADE' => $row['idade'],
																						  'COMPRA' =>  number_format($row['preco_compra'], 2, ',', '.'),
																						  'DATA' => $row['data_entrada'],
																						  'OBS' => $row['obs'])); 
								}
						}	
					if (isset($_POST['txtdataCio']))
						{
						$campo="";
						$data =$_POST['txtdataCio']. '|';
						while (list($campo,$valor_campo) = each($_POST['animais']))
								$rSet = $db->Query("UPDATE animais SET cios=CONCAT(cios,'$data') WHERE codigo='$valor_campo' LIMIT 1");

						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Cios",'MSG' => "Os $campo animais selecionados foram alterados com sucesso."));	
						}
					if (isset($_POST['ultrasom_x']))
						{
						$msg .="<img src='imagens/mostrar_detalhes.gif' alt='Mostar animais selecionados' align='absmiddle' /> Foram selecionados ". count($_POST['animais'])." animais. Clique aqui para ve-los e confirma-los.";												
							
						$model->assign_block_vars('animais.acao_ultrasom', array('FAZENDAS' => $lista_fazendas,'MSG' => $msg));

						$model->assign_block_vars('animais.acao_ultrasom.selecionados', array());
						while (list($campo,$valor_campo) = each($_POST['animais']))	{	
								$rSet = $db->Query("SELECT * FROM animais WHERE codigo='$valor_campo' LIMIT 1");
								$row = $db->FetchArray($rSet);
								$model->assign_block_vars('animais.acao_ultrasom.selecionados.dados', array('CODIGO' => $valor_campo,
																						  'NUMERO' => $row['numero'],
																						  'MARCA' => $row['proprietario'],
																						  'IDADE' => $row['idade'],
																						  'COMPRA' =>  number_format($row['preco_compra'], 2, ',', '.'),
																						  'DATA' => $row['data_entrada'],
																						  'OBS' => $row['obs'])); 
								}
						}						
					if (isset($_POST['txtdataUltrasom']))
						{
						$campo="";
						if ($_POST['txtSexoUltrasom']!="") 
						     $data =$_POST['txtdataUltrasom'].'('.$_POST['txtSexoUltrasom'].')|';
						else 
						     $data =$_POST['txtdataUltrasom']. '|';
							 
						while (list($campo,$valor_campo) = each($_POST['animais']))
								$rSet = $db->Query("UPDATE animais SET ultrason=CONCAT(ultrason,'$data') WHERE codigo='$valor_campo' LIMIT 1");

						$model->assign_block_vars('animais.acao_aviso', array('TITULO' => " - Ultrasom",'MSG' => "Os $campo animais selecionados foram alterados com sucesso."));	
						}						
				}															
			}						
		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="star"){
				$estrela = $_REQUEST['star'];
				$codigo = $_REQUEST['codigo'];
				if ($estrela=='0')
					$estrela = '1';
				else
					if ($estrela=='1')
						$estrela = '0';					
				$rSet = $db->Query("UPDATE animais SET star='$estrela' WHERE codigo='$codigo' LIMIT 1");
				exit();
			}		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
				
				$model->assign_vars(array('FAZENDAS' => $lista_fazendas));		
								
									
				$modo_visao ="";
				
				if (isset($_REQUEST['view']))
					$modo_visao = $_REQUEST['view'];
				else $modo_visao = "1";
				

				$menu_Marca="";
				$lista_proprietarios="";
				$rSetMarca = $db->Query("SELECT * FROM proprietarios");
				$i=1;
				while ($rowMarca = $db->FetchArray($rSetMarca)){
					  $menu_Marca .= '<li ><a href="?modulo=adm&menu=animais&sub=visao&view='.$modo_visao.'&filtro=marcas&codigo='.$rowMarca['codigo'].'" title="Somente Marca '.$rowMarca['marca'].'">'.$rowMarca['marca'].'</a></li>';
					  $lista_proprietarios .= $rowMarca['marca'].'<input type="radio" name="RadioGroup" value="'.$rowMarca['codigo'].'"  checked="checked" /> &nbsp;&nbsp;';
					  if ($i++ % 2 ==0) $lista_proprietarios .= "<br>&nbsp;&nbsp;";
				}
				$rSet1 = $db->Query("SELECT * FROM animais WHERE status='1' and fazenda='$fazenda_codigo'");
				$rSet2 = $db->Query("SELECT * FROM proprietarios");
				$dados = $db->NumRows($rSet1)." animais nesta fazenda ";
				while($marca = $db->FetchArray($rSet2)){
					$rSet3=$db->Query("SELECT * FROM animais WHERE status='1' AND proprietario='".$marca['codigo']."' AND fazenda='$fazenda_codigo'");
					$dados .= " - (".$marca['marca'].": ".$db->NumRows($rSet3).") ";
				}
						
				$model->assign_block_vars('animais.visao', array('VIEW' => "&view=".$modo_visao,'MENU_MARCA' => $menu_Marca,'MARCAS' => $lista_proprietarios,'TOTAL' => $db->NumRows($rSet1)));				
				
				$modo_visao = "animais.visao.modo".$modo_visao;				
				
				$model->assign_block_vars($modo_visao, array('RESUMO' => $dados));						
				
				$modo_visao .= ".dados";
				
				$filtro="";
				if (isset($_REQUEST['filtro'])){
					$filtro = $_REQUEST['filtro'];
					if ($filtro=="numero"){
						$filtro = " ORDER BY numero ";
					}	
					if ($filtro=="marca"){
						$filtro = " ORDER BY proprietario ";
					}										
					if ($filtro=="idade"){
						$filtro = " ORDER BY idade ";
					}
					if ($filtro=="data"){
						$filtro = " ORDER BY data_entrada ";
					}
					if ($filtro=="star"){
						$filtro = " AND star='1' ";
					}					
					if ($filtro=="cio"){
						$filtro = " ORDER BY cios ";
					}
					if ($filtro=="transferencia"){
						$filtro = " ORDER BY transferencias ";
					}
					if ($filtro=="hormonios"){
						$filtro = " ORDER BY hormonios ";
					}					
					if ($filtro=="ultrason"){
						$filtro = " ORDER BY ultrason ";
					}																				
					if ($filtro=="marcas"){
						$cod = $_REQUEST['codigo'];
						$filtro = " AND proprietario='$cod'";
					}																									
				}
			    $rSet = $db->Query("SELECT * FROM animais WHERE status='1' AND fazenda='$fazenda_codigo' $filtro");


				$count=0;	
				while ($row = $db->FetchArray($rSet)){
					  $rSetMarca = $db->Query("SELECT * FROM proprietarios WHERE codigo='".$row['proprietario']."' LIMIT 1");
					  $rowMarca = $db->FetchArray($rSetMarca);
	 				  $model->assign_block_vars($modo_visao, array('NUMERO' => $row['numero'],
					  															      'MARCA' => $rowMarca['marca'],
																					  'DATA_ENTRADA' => date("d/m/Y",strtotime($row['data_entrada'])),
																					  'DATA' => date("d/m/Y",strtotime($row['data'])),
																					  'COMPRA' => 'R$ '. number_format($row['preco_compra'], 2, ',', '.'),
																					  'CODIGO' => $row['codigo'],
																					  'IDADE' => $row['idade'],
																					  'HORMONIOS' => str_replace("|"," - ",$row['hormonios']),
																					  'VACINAS' => str_replace("|"," - ",$row['vacinas']),
																					  'CIOS' => str_replace("|"," - ",$row['cios']),
																					  'TRANSFERENCIAS' => str_replace("|"," - ",$row['transferencias']),
																					  'ULTRASOM' => str_replace("|"," - ",$row['ultrason']),
																					  'OBS' => $row['obs'],
																					  'CORMARCA' => $rowMarca['cor'],
																					  'STAR' => $row['star'],
																					  'COR' =>  $count++%2>0?'bgcolor="#FCF9DA"':''));
				}
			}
		}
		####### CUSTOS ########
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="custos"){			
			$model->assign_block_vars('custos', array());  		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="editar"){		
				if (isset($_POST['Gravar'])){
					$txtfuncionarios="".addslashes($_POST['txtfuncionarios']);
					$txtvacinas="".addslashes($_POST['txtvacinas']);
					$txtsal="".addslashes($_POST['txtsal']);
					$txtadm="".addslashes($_POST['txtadm']);
					$txtarrendamentos="".addslashes($_POST['ttxtarrendamentos']);
					$txthormonios="".addslashes($_POST['txthormonios']);
					$txtoutros="".addslashes($_POST['txtoutros']);
					$txtobs="".addslashes($_POST['txtobs']);	
					$rSet = $db->Query("UPDATE custos SET funcionarios='$txtfuncionarios', vacinas='$txtvacinas', sal='$txtsal', administracao='$txtadm', arrendamentos='$txtarrendamentos', hormonios='$txthormonios', outros='$txtoutros', obs='$txtobs' WHERE fazenda='$fazenda_codigo' LIMIT 1");
					$msg .= "Dados alterados com sucesso. <a href='?modulo=adm&menu=custos&sub=visao'>Clique aqui para visualiza-los</a>";
				}
				$rSet = $db->Query("SELECT * FROM custos WHERE fazenda='$fazenda_codigo'");
				$row = $db->FetchArray($rSet);
				$model->assign_block_vars('custos.editar', array('FUNCIONARIOS' => number_format($row['funcionarios'], 2, ',', '.'),
					  											'VACINAS' =>number_format($row['vacinas'], 2, ',', '.'),
																'SAL' =>  number_format($row['sal'], 2, ',', '.'),
																'ADM' => number_format($row['administracao'], 2, ',', '.'),
																'ARRENDAMENTOS' =>number_format($row['arrendamentos'], 2, ',', '.'),
																'HORMONIOS' => number_format($row['hormonios'], 2, ',', '.'),
																'OUTROS' => number_format($row['outros'], 2, ',', '.'),																	
																'OBS' => $row['obs'],
																'MSG' => $msg,
																'NOMEFAZENDA' => $_SESSION['sess_fazenda_nome']));				

			}
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
			    $rSet = $db->Query("SELECT * FROM custos WHERE fazenda='$fazenda_codigo'");
				$row = $db->FetchArray($rSet);
				$total = $row['funcionarios']+$row['vacinas']+$row['sal']+$row['administracao']+$row['arrendamentos']+$row['hormonios']+$row['outros'];
 				$model->assign_block_vars('custos.visao', array('FUNCIONARIOS' => number_format($row['funcionarios'], 2, ',', '.'),
					  											'VACINAS' => number_format($row['vacinas'], 2, ',', '.'),
																'SAL' => number_format($row['sal'], 2, ',', '.'),
																'ADM' => number_format($row['administracao'], 2, ',', '.'),
																'ARRENDAMENTOS' =>number_format($row['arrendamentos'], 2, ',', '.'),
																'HORMONIOS' => number_format($row['hormonios'], 2, ',', '.'),
																'OUTROS' => number_format($row['outros'], 2, ',', '.'),																
																'OBS' => $row['obs'],
																'TOTAL' => number_format($total, 2, ',', '.')));
			}
			
		}	//////////////////////////// END CUSTOS	
		
		####### FINANCEIRO ########
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="financeiro"){			
			$model->assign_block_vars('financeiro', array());  		
			
			/// PROCESSARRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="processar"){		
				if (isset($_REQUEST['codigo'])){
					$txtcodigo="".addslashes($_REQUEST['codigo']);
					$rSet = $db->Query("SELECT * FROM financas WHERE codigo='$txtcodigo' LIMIT 1");
					$row = $db->FetchArray($rSet);
					$dataTbl = date("Y-m",strtotime($row['ano']."-".$row['mes']."-01"));
					if (date("Y-m")>=$dataTbl && $row['status']=="1"){
						
						// pega custo por dia
						$rSet2 = $db->Query("SELECT * FROM custos WHERE fazenda='$fazenda_codigo'");
						$row2 = $db->FetchArray($rSet2);
						$totalCusto = $row2['funcionarios']+$row2['vacinas']+$row2['sal']+$row2['administracao']+$row2['arrendamentos']+$row2['hormonios']+$row2['outros'];
						
						// conta numero de animais

						$acumulativo = 0;						

						$rSetAnimal = $db->Query("SELECT * FROM animais WHERE status='1' AND fazenda='$fazenda_codigo'");
						$NumeroDeAnimais = $db->NumRows($rSetAnimal);						
						while ($rowAnimal = $db->FetchArray($rSetAnimal)){
							$data = explode("-", ereg_replace('/', '-', $rowAnimal['data_entrada']));
							if ($row['mes']==$data[1]){
								$retornoDias = 31 - $data[2];
								$acumulativo += ($totalCusto*$retornoDias)/30;
								}
							else{
								$retornoDias = 30;
								$acumulativo += $totalCusto;
								}
							//echo '<br>Data: '.$rowAnimal['data_entrada'].' - Retorno: '.$retornoDias.' -> R$ '.$acumulativo;	
						}
						
						if (date("Y-m")>$dataTbl){
							// atualiza financas
							$rSet = $db->Query("UPDATE financas SET valor=valor+'$acumulativo',animais='$NumeroDeAnimais',data='".date("Y-m-d")."', custos='$totalCusto',status='0' WHERE codigo='$txtcodigo' LIMIT 1");
							// insere novo mes
							$anoNovo = $row['ano'];
							$mesNovo = $row['mes']+1;
							if ($mesNovo>12) {
								$anoNovo++;
								$mesNovo=1;
								}
							$rSet = $db->Query("INSERT INTO financas (fazenda,ano,mes) values ('$fazenda_codigo','$anoNovo','$mesNovo')");
							$msg .= "Mês ".$row['mes']."/".$row['ano']." faturado com sucesso.";
						 }
						else{
							$msg .= "Este mês selecionado é o  mês corrente. Não foi alterado os dados, somente mostrado as previsões de gastos.";
							$msg .= "<br><br><b>Previsão de gastos com animais neste mês:</b><br><br>".str_pad("Gastos:",30,".",STR_PAD_RIGHT)." R$ ".number_format($acumulativo, 2, ',', '.')." <br>".str_pad("Custos por Cabeça:",30,".",STR_PAD_RIGHT)." R$ ".number_format($totalCusto, 2, ',', '.')." <br>".str_pad("Animais na Fazenda:",30,".",STR_PAD_RIGHT)." ".$NumeroDeAnimais." cabeças";
							}
					}
					else{
						$msg .= "Erro: ocorreu um erro desconhecido durante a operação.";
					}
				}
				$model->assign_block_vars('financeiro.processar', array('MSG' => $msg));
			} /// FIM PROCESSARRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRRR
			
			// CONFIGURACOESSSSSSSSSSSSSSSSS
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="configuracoes"){		
				if (isset($_REQUEST['a']) && $_REQUEST['a']=="criarano"){
					$rSet = $db->Query("SELECT ano FROM financas WHERE fazenda='$fazenda_codigo' ORDER BY ano DESC");
					$row = $db->FetchArray($rSet);
					$proximano=0;
					if (!$row) $proximano="2006";  //nao tem nada criado, cria novo
					else $proximano=$row['ano']+1; //jah tem criado, cria proximo

					for ($i=1;$i<=12;$i++) 
						$rSet = $db->Query("INSERT INTO financas (fazenda,ano,mes) values ('$fazenda_codigo','$proximano','$i')");
					}
				$rSet = $db->Query("SELECT DISTINCT ano FROM financas WHERE fazenda='$fazenda_codigo' ORDER by ano");
				$anos="";
				while ($row = $db->FetchArray($rSet)){
					$anos .= $row['ano'].' - ';
					}					
				$model->assign_block_vars('financeiro.configuracoes', array('MSG' => $msg,'ANOS' =>$anos));
			}	
			
			// visaoooooooooooooooooooooooooooooooooooo
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
			    
				$rSet = $db->Query("SELECT DISTINCT ano FROM financas WHERE fazenda='$fazenda_codigo' ORDER by ano");
				$anos="";
				$anoAtual=date("Y");
				
				if (isset($_POST['txtano']))
					$anoAtual=$_POST['txtano'];
					
				while ($row = $db->FetchArray($rSet)){
					if ($anoAtual==$row['ano'])
						$anos .= '<option value="'.$row['ano'].'" selected>'.$row['ano'].'</option>';
					else
						$anos .= '<option value="'.$row['ano'].'">'.$row['ano'].'</option>';
					}	
				$model->assign_block_vars('financeiro.visao', array('ANOS' =>$anos));		
				
				$array = array('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
//				'MES' => sprintf("%02d", $row['mes']),
					
				$rSet = $db->Query("SELECT * FROM financas WHERE fazenda='$fazenda_codigo' AND ano='$anoAtual' ORDER by mes ASC ");
				$count=0;
				while ($row = $db->FetchArray($rSet)){
					  	
					  //$marcas = explode("|",$row['marcas']);
					  //$rSetMarca = $db->Query("SELECT * FROM proprietarios WHERE codigo='".$marcas[0]."' LIMIT 1");
					  	
	 				  $model->assign_block_vars('financeiro.visao.dados', array('CODIGO' => $row['codigo'],
					  														  'FAZENDA' => $row['fazenda'],				  					  														  'MES' => $array[$row['mes']-1],
																			  'ANO' => $row['ano'],
																			  'DATA' => empty($row['data'])?"":date("d/m/Y",strtotime($row['data'])),
																			  'VALOR' => 'R$ '.number_format($row['valor'], 2, ',', '.'),
																			  'VENDAS' => 'R$ '.number_format($row['vendas'], 2, ',', '.'),
																			  'VENDIDOS' => $row['vendidos'],
																			  'COMPRADOS' => $row['comprados'],
																			  'COMPRAS' => 'R$ '.number_format($row['compras'], 2, ',', '.'),
																			  'CUSTOS' => 'R$ '.number_format($row['custos'], 2, ',', '.'),
																			  'ANIMAIS' => $row['animais'],
																			  'MARCAS' => $row['marcas'],
																			  'STATUS' => $row['status']=='0'?'<img src="imagens/check.gif" alt="Processar" align="absmiddle"/> ':'<img src="imagens/processar.gif" alt="Processar"  align="absmiddle" onclick="javascript:perguntar(\'Deseja processar este mês?\n\nCertifique-se que tenha feito todas as operações, como transferência dos animais de fazenda, antes de continuar, pois pode gerar valores incorretos e incosistência no banco de dados!!!\n\n\Deseja continuar e fechar o balanço do mês? Caso o mês seja o mês corrente, será mostrado a previsão de gastos.\',\'?modulo=adm&menu=financeiro&sub=processar&codigo='.$row['codigo'].'\')" />',
																			  'OBS' => $row['obs'],																			  	'COR' =>  date("Y-m",strtotime($row['ano']."-".$row['mes']."-01"))==date("Y-m")?'bgcolor="#FFDC8A"':($row['status']=="1"?'bgcolor="#FEC5C9"':($count++%2>0?'bgcolor="#FCF9DA"':''))));
				}
			}
			
		}		///////////////  FIM FINANCEIRO	
		
			
		if (!isset($_REQUEST['menu'])){
			$rSet = $db->Query("SELECT * FROM animais WHERE status='1' AND fazenda='$fazenda_codigo'");
			$dados="";
			$dados .= "<br>Numero de Animais na Fazenda: ".$db->NumRows($rSet)."<br>";
			
			$rSet = $db->Query("SELECT * FROM proprietarios");
			while($marca = $db->FetchArray($rSet)){
				$rSet2=$db->Query("SELECT * FROM animais WHERE  status='1' AND proprietario='".$marca['codigo']."' AND fazenda='$fazenda_codigo'");
				$dados .= "<br>Marca ".$marca['marca'].": ".$db->NumRows($rSet2)." animais";
			}
						
		
			$model->assign_block_vars('inicioADM', array('DADOS' => $dados));
		}		
	  break;
	  
###################################################################################################################
######################### CASE VER     ############################################################################
###################################################################################################################	  
  	  case "ver":
	  
		$model->assign_block_vars('menu_ver', array());  	  
		
		if (!isset($_REQUEST['menu'])){	
			$rSet = $db->Query("SELECT * FROM animais WHERE status='1' AND fazenda='$fazenda_codigo'");
			$dados="";
			$dados .= "<br>Numero de Animais na Fazenda: ".$db->NumRows($rSet)."<br>";
			
			$rSet = $db->Query("SELECT * FROM proprietarios");
			while($marca = $db->FetchArray($rSet)){
				$rSet2=$db->Query("SELECT * FROM animais WHERE  status='1' AND proprietario='".$marca['codigo']."' AND fazenda='$fazenda_codigo'");
				$dados .= "<br>Marca ".$marca['marca'].": ".$db->NumRows($rSet2)." animais";
			}
						
		
			$model->assign_block_vars('inicioVER', array('DADOS' => $dados));		
		}		
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="animais"){
  			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
				$model->assign_block_vars('ver', array());
				$model->assign_vars(array('FAZENDAS' => $lista_fazendas));		
				$modo_visao ="";
				
				if (isset($_REQUEST['view']))
					$modo_visao = $_REQUEST['view'];
				else $modo_visao = "1";

				$menu_Marca="";
				$lista_proprietarios="";
				$rSetMarca = $db->Query("SELECT * FROM proprietarios");
				$i=1;
				while ($rowMarca = $db->FetchArray($rSetMarca)){
					  $menu_Marca .= '<li ><a href="?modulo=adm&menu=animais&sub=visao&view='.$modo_visao.'&filtro=marcas&codigo='.$rowMarca['codigo'].'" title="Somente Marca '.$rowMarca['marca'].'">'.$rowMarca['marca'].'</a></li>';
					  $lista_proprietarios .= $rowMarca['marca'].'<input type="radio" name="RadioGroup" value="'.$rowMarca['codigo'].'"  checked="checked" /> &nbsp;&nbsp;';
					  if ($i++ % 2 ==0) $lista_proprietarios .= "<br>&nbsp;&nbsp;";
				}
				$rSet1 = $db->Query("SELECT * FROM animais WHERE status='1' and fazenda='$fazenda_codigo'");
				$rSet2 = $db->Query("SELECT * FROM proprietarios");
				$dados = $db->NumRows($rSet1)." animais nesta fazenda ";
				while($marca = $db->FetchArray($rSet2)){
					$rSet3=$db->Query("SELECT * FROM animais WHERE status='1' AND proprietario='".$marca['codigo']."' AND fazenda='$fazenda_codigo'");
					$dados .= " - (".$marca['marca'].": ".$db->NumRows($rSet3).") ";
				}
						
				$model->assign_block_vars('ver.visao', array('VIEW' => "&view=".$modo_visao,'MENU_MARCA' => $menu_Marca,'MARCAS' => $lista_proprietarios,'TOTAL' => $db->NumRows($rSet1)));				
				
				$modo_visao = "ver.visao.modo".$modo_visao;				
				
				$model->assign_block_vars($modo_visao, array('RESUMO' => $dados));						
				
				$modo_visao .= ".dados";
				
				$filtro="";
				if (isset($_REQUEST['filtro'])){
					$filtro = $_REQUEST['filtro'];
					if ($filtro=="numero"){
						$filtro = " ORDER BY numero ";
					}	
					if ($filtro=="marca"){
						$filtro = " ORDER BY proprietario ";
					}										
					if ($filtro=="idade"){
						$filtro = " ORDER BY idade ";
					}
					if ($filtro=="data"){
						$filtro = " ORDER BY data_entrada ";
					}
					if ($filtro=="star"){
						$filtro = " AND star='1' ";
					}					
					if ($filtro=="cio"){
						$filtro = " ORDER BY cios ";
					}
					if ($filtro=="transferencia"){
						$filtro = " ORDER BY transferencias ";
					}
					if ($filtro=="hormonios"){
						$filtro = " ORDER BY hormonios ";
					}					
					if ($filtro=="ultrason"){
						$filtro = " ORDER BY ultrason ";
					}																				
					if ($filtro=="marcas"){
						$cod = $_REQUEST['codigo'];
						$filtro = " AND proprietario='$cod'";
					}																									
				}
			    $rSet = $db->Query("SELECT * FROM animais WHERE status='1' AND fazenda='$fazenda_codigo' $filtro");


				$count=0;	
				while ($row = $db->FetchArray($rSet)){
					  $rSetMarca = $db->Query("SELECT * FROM proprietarios WHERE codigo='".$row['proprietario']."' LIMIT 1");
					  $rowMarca = $db->FetchArray($rSetMarca);
	 				  $model->assign_block_vars($modo_visao, array('NUMERO' => $row['numero'],
					  															      'MARCA' => $rowMarca['marca'],
																					  'DATA_ENTRADA' => date("d/m/Y",strtotime($row['data_entrada'])),
																					  'DATA' => date("d/m/Y",strtotime($row['data'])),
																					  'COMPRA' => 'R$ '. number_format($row['preco_compra'], 2, ',', '.'),
																					  'CODIGO' => $row['codigo'],
																					  'IDADE' => $row['idade'],
																					  'HORMONIOS' => str_replace("|"," - ",$row['hormonios']),
																					  'VACINAS' => str_replace("|"," - ",$row['vacinas']),
																					  'CIOS' => str_replace("|"," - ",$row['cios']),
																					  'TRANSFERENCIAS' => str_replace("|"," - ",$row['transferencias']),
																					  'ULTRASOM' => str_replace("|"," - ",$row['ultrason']),
																					  'OBS' => $row['obs'],
																					  'CORMARCA' => $rowMarca['cor'],
																					  'STAR' => $row['star'],
																					  'COR' =>  $count++%2>0?'bgcolor="#FCF9DA"':''));
				}
				}
				}// fim se menu animais
	  
	  
	  
	  		break;	  
	  
###################################################################################################################
######################### CASE SISTEMA ############################################################################
###################################################################################################################	  
  	  case "sys":
			$msg ="";		
			$erros ="";
			$model->assign_block_vars('menu_sys', array());
			
			######## INICIO DO MENU SISTEMA  		
			if (!isset($_REQUEST['menu'])){
			$rSet = $db->Query("SELECT * FROM fazendas");
			$dados1 = "<br />Fazendas Cadastradas (".$db->NumRows($rSet)."): ";
			while($row = $db->FetchArray($rSet))
				$dados1 .= "<br> - ".$row['nome'];				
				
			$rSet = $db->Query("SELECT * FROM proprietarios");				
			$dados2 = "<br><br>Marcas Cadastradas (".$db->NumRows($rSet)."): ";
			while($row = $db->FetchArray($rSet))
				$dados2 .= "<br> - ".$row['nome']." (".$row['marca'].")";				
			
			$rSet = $db->Query("SELECT * FROM usuarios");				
			$dados3 = "<br><br>Usuários Cadastrados (".$db->NumRows($rSet)."): ";
			while($row = $db->FetchArray($rSet)){
				$dados3 .= "<br> - ".$row['nome']." (".$row['login'].") - ";				
				$dados3 .= $row['nivel']=="0"?'Visitante':'';
				$dados3 .= $row['nivel']=="100"?'Administrador':'';
			}				
				
	
						
		
			$model->assign_block_vars('inicioSYS', array('DADOS' => $dados1.$dados2.$dados3));
		}		
		####### USUÁRIOS ########
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="usuarios"){			
			$model->assign_block_vars('usuarios', array());  		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="cadastrar"){		
				if (isset($_POST['Cadastrar'])){
					$txtnome="".addslashes($_POST['txtnome']);
					$txtdata=date("Y-m-d");
					$txtlogin="".addslashes($_POST['txtlogin']);
					$txtsenha=md5(addslashes($_POST['txtsenha']));
					$txtnivel="".addslashes($_POST['txtnivel']);
					$txtemail="".addslashes($_POST['txtemail']);					
					$txtobs="".addslashes($_POST['txtobs']);	
					$rSet = $db->Query("INSERT into usuarios (nome,data,login,senha,nivel,email,obs) values ('$txtnome','$txtdata','$txtlogin','$txtsenha','$txtnivel','$txtemail','$txtobs')");
					$msg .= "<strong>'$txtnome'</strong> cadastrado com sucesso. <a href='?modulo=adm&menu=usuarios&sub=visao'>Clique aqui para visualizar os usuários cadastrados.</a>";
				}
				$model->assign_block_vars('usuarios.cadastrar', array('MSG' => $msg));
			}
		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
			    $rSet = $db->Query("SELECT * FROM usuarios ORDER BY nome");
				$model->assign_block_vars('usuarios.visao', array());											
				$count=0;
				while ($row = $db->FetchArray($rSet)){
	 				  $model->assign_block_vars('usuarios.visao.dados', array('CODIGO' => $row['codigo'],
					  														  'NOME' => $row['nome'],
																			  'DATA' => date("d/m/Y",strtotime($row['data'])),
																			  'LOGIN' => $row['login'],
																			  'NIVEL' => $row['nivel'],
																			  'EMAIL' => $row['email'],
																			  'OBS' => $row['obs'],
																			  'COR' =>  $count++%2>0?'bgcolor="#FCF9DA"':''));
				}
			}
			
		}	//////////////////////////// END USUARIOS
		####### FAZENDAS ########
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="fazendas"){			
			$model->assign_block_vars('fazendas', array());  		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="cadastrar"){		
				if (isset($_POST['Cadastrar'])){
					$txtnome="".addslashes($_POST['txtnome']);
					$txtendereco="".addslashes($_POST['txtendereco']);
					$txtinscricao="".addslashes($_POST['txtinscricao']);										
					$txtdados="".addslashes($_POST['txtdados']);
					$txtobs="".addslashes($_POST['txtobs']);	
					$rSet = $db->Query("INSERT into fazendas (nome,endereco,inscricao,dados,obs) values ('$txtnome','$txtendereco','$txtinscricao','$txtdados','$txtobs')");
					$resultado = mysql_insert_id();
					if ($resultado){
						$rSet = $db->Query("INSERT into custos (fazenda) values ('$resultado')");
						$msg .= "Fazenda inserido com sucesso. <a href='?modulo=sys&menu=fazendas&sub=visao'>Clique aqui para visualizar as fazendas cadastradas.</a>";			
					}
					else{
						$msg .= "Ocorreu um erro durante a operação e os dados não foram cadastrados. ($resultado)";			
					}
					
					
				}
				$model->assign_block_vars('fazendas.cadastrar', array('MSG' => $msg));
			}
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="editar"){		
				if (isset($_POST['Gravar'])){
					$txtcodigo="".addslashes($_REQUEST['codigo']);				
					$txtnome="".addslashes($_POST['txtnome']);
					$txtendereco="".addslashes($_POST['txtendereco']);
					$txtinscricao="".addslashes($_POST['txtinscricao']);										
					$txtdados="".addslashes($_POST['txtdados']);
					$txtobs="".addslashes($_POST['txtobs']);	
					$rSet = $db->Query("UPDATE fazendas SET nome='$txtnome',endereco='$txtendereco',inscricao='$txtinscricao',dados='$txtdados',obs='$txtobs' WHERE codigo='$txtcodigo'");
					$msg .= "Dados da fazenda alterado com sucesso. <a href='?modulo=sys&menu=fazendas&sub=visao'>Clique aqui para visualizar as fazendas cadastradas.</a>";			
					}
				$cod = $_REQUEST['codigo'];
			    $rSet = $db->Query("SELECT * FROM fazendas WHERE codigo='$cod'");
				$row = $db->FetchArray($rSet);
 				$model->assign_block_vars('fazendas.editar', array('CODIGO' => $row['codigo'],
																  'NOME' => $row['nome'],
																  'ENDERECO' => $row['endereco'],
																  'INSCRICAO' => $row['inscricao'],
																  'DADOS' => $row['dados'],
																  'OBS' => $row['obs'],
																  'MSG' => $msg));				
				
			}			
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
			    $rSet = $db->Query("SELECT * FROM fazendas ORDER BY nome");
				$model->assign_block_vars('fazendas.visao', array());											
				$count=0;
				while ($row = $db->FetchArray($rSet)){
	 				  $model->assign_block_vars('fazendas.visao.dados', array('CODIGO' => $row['codigo'],
					  														  'NOME' => $row['nome'],
																			  'ENDERECO' => $row['endereco'],
																			  'INSCRICAO' => $row['inscricao'],
																			  'DADOS' => $row['dados'],
																			  'OBS' => $row['obs'],
																			  'COR' =>  $count++%2>0?'bgcolor="#FCF9DA"':''));
				}
			}
			
		}		
		####### MARCAS ########
		if (isset($_REQUEST['menu']) && $_REQUEST['menu']=="marcas"){			
			$model->assign_block_vars('marcas', array());  		
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="cadastrar"){		
				if (isset($_POST['Cadastrar'])){
					$txtnome="".addslashes($_POST['txtnome']);
					$txtmarca="".addslashes($_POST['txtmarca']);
					$txtobs="".addslashes($_POST['txtobs']);	
					$txtcor="".addslashes($_POST['txtcor']);						
										
					$rSet = $db->Query("INSERT into proprietarios (nome,marca,obs,cor) values ('$txtnome','$txtmarca','$txtobs','$txtcor')");
					$msg .= "Marca inserido com sucesso. <a href='?modulo=sys&menu=marcas&sub=visao'>Clique aqui para visualizar os proprietários.</a>";			
				}
				$model->assign_block_vars('marcas.cadastrar', array('MSG' => $msg));
			}
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="editar"){		
				if (isset($_POST['Gravar'])){
					$txtnome="".addslashes($_POST['txtnome']);
					$txtmarca="".addslashes($_POST['txtmarca']);
					$txtobs="".addslashes($_POST['txtobs']);	
					$txtcor="".addslashes($_POST['txtcor']);						
					$rSet = $db->Query("UPDATE proprietarios SET nome='$txtnome',marca='$txtmarca',obs='$txtobs',cor='$txtcor' WHERE codigo='".$_REQUEST['codigo']."'");
					$msg .= "Marca inserido com sucesso. <a href='?modulo=sys&menu=marcas&sub=visao'>Clique aqui para visualizar os proprietários.</a>";			
				}
			    $rSet = $db->Query("SELECT * FROM proprietarios WHERE codigo='".$_REQUEST['codigo']."'");
				$row = $db->FetchArray($rSet);
	 			$model->assign_block_vars('marcas.editar', array('CODIGO' => $row['codigo'],
					  											 'MARCA' => $row['marca'],
																 'NOME' => $row['nome'],
																 'OBS' => $row['obs'],
																 'COR' => $row['cor'],
																 'MSG' => $msg));				
			}			
			if (isset($_REQUEST['sub']) && $_REQUEST['sub']=="visao"){
			    $rSet = $db->Query("SELECT * FROM proprietarios ORDER BY nome");
				$model->assign_block_vars('marcas.visao', array());											
				$count=0;
				while ($row = $db->FetchArray($rSet)){
	 				  $model->assign_block_vars('marcas.visao.dados', array('CODIGO' => $row['codigo'],
					  															      'MARCA' => $row['marca'],
																					  'NOME' => $row['nome'],
																					  'OBS' => $row['obs'],
																					  'CORMARCA' => $row['cor'],
																					  'COR' =>  $count++%2>0?'bgcolor="#FCF9DA"':''));
				}
			}
		}	
		/////////////////////////////////// END MARCAS
						
		
		
		break;

	  
}
##############################################################################
##############                  MOSTRA PAGINA               	##############
##############################################################################

// imprima na tela o conteúdo de OUTPUT
$model->pparse('pagina');


?>

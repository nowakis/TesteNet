<?
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once "www/funcoes.php";
require_once "www/class/class.SessionFacade.php";
require_once "www/banco.con.php";

$msg_erro = array();

if (isset($_GET['professor']) AND strlen(trim($_GET['professor']))>0){

	$professor = trim($_GET['professor']);
	try {
		$prof = $sessionFacade->recuperarProfessor($professor); 

		if ( is_object($prof)){
			$professor		= $prof->getId();
			$nome			= $prof->getNome();
			$email			= $prof->getEmail();
			$login			= $prof->getLogin();
			$senha			= $prof->getSenha();
			$ativo			= $prof->getAtivo();
		}else{
			array_push($msg_erro,"Professor não encontrado!");
		}
	}catch(Exception $e) {
		array_push($msg_erro,$e->getMessage());
	}
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TesteNet! Provas On-Line</title>

<link rel="stylesheet" type="text/css" href="estilo.css" media="screen" />
</head>

<body>

<div id="box">

<div id="head"></div>

<div id="content">

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td  width="10" valign="top" background="imagens/fundo_esquerda_aux.jpg">
			<img src="imagens/fundo_lado_inteira_esquerdo.jpg" />
		</td>
		<td valign="top" width="767">
			<img src="imagens/logo_fotos-07.jpg" USEMAP="#map1" border=0/>
				<MAP NAME="map1">
					<AREA HREF="index.php"    ALT="Início"      TITLE="Início"      SHAPE=RECT COORDS="22, 215,108,236">
					<AREA HREF="cadastro.php" ALT="Cadastre-se" TITLE="Cadastre-se" SHAPE=RECT COORDS="111,215,218,236">
					<AREA HREF="ajuda.php"    ALT="Ajuda"       TITLE="Ajuda"       SHAPE=RECT COORDS="198,215,275,236">
					<AREA HREF="index.php"    ALT="Início"      TITLE="Início"      SHAPE=RECT COORDS="24,22,68,36">
					<AREA HREF="contato.php"  ALT="Contato"     TITLE="Contato"     SHAPE=RECT COORDS="90,24,150,36">
				</MAP>

			<div id='conteudo'>
				<div id='conteudo_direita'>
					<div id='conteudo_login'>
						<h1>Login</h1>
						<p>Digite abaixo o seu login e senha de acesso</p>

						<form method='POST' action='www/login.php' name='frm_login'>
							<p>Login:<br>
							<input type="text" name="login" value='<?=$_GET['login'];?>'>
							</p>
							<p>Senha:<br>
							<input type="password" name="senha">
							</p>
							<p><input type="submit" name='Acessar' value='Acessar'></p>
						</form>
						<p>Sem cadastro? <a href='cadastro.php'>Cadastre-se</a></p>
					</div>
				</div>
				<div id='conteudo_meio'>
					<?
						if (count($msg_erro)>0){
							echo fn_formata_msg_erro($msg_erro);
						}
					?>


					<h1>Cadastro efetuado com sucesso!</h1>
					<p>Parab&eacute;ns <?=$nome?>, o cadastro foi efetuado com sucesso!</p>

					<p>Anote as informa&ccedil;&otilde;es abaixo. Elas s&atilde;o necess&aacute;rias para acessar o sistema.</p>

					<p>Login: <b> <?=$login?></b></p>
					<p>Senha: <b> (n&atilde;o vis&iacute;vel por motivos de seguran&ccedil;a)</b></p>
					<p>Email: <b> <?=$email?></b></p>
					<p></p>
					<p>Voc&ecirc; receber&aacute; um email com todas as informa&ccedil;&otilde;es.</p>


			</div>
		</td>
		<td  width="10" valign="top" background="imagens/fundo_direita_aux.jpg">
			<img src="imagens/fundo_lado_inteira_direita.jpg" />
		</td>
	</tr>
	<tr>
		<td colspan="3"><img src="imagens/fundo_tabela.jpg" /></td>
	</tr>
	</table>
</div>

<br clear="all" />
<br clear="all" />
</div>
<script type="text/javascript">
	var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
	document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
	var pageTracker = _gat._getTracker("UA-5848095-2");
	pageTracker._trackPageview();
</script>
</body>
</html>

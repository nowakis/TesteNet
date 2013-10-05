<?
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
require_once "www/funcoes.php";
require_once "www/class/class.SessionFacade.php";
require_once "www/banco.con.php";

##############################################################################
##############                  CADASTRAR                       ##############
##############################################################################

$msg_erro = array();

if (isset($_POST['Cadastrar']) AND strlen(trim($_POST['Cadastrar']))>0) {
	
	$professor_nome		= addslashes(trim($_POST['professor_nome']));
	$email				= addslashes(trim($_POST['email']));
	$login				= addslashes(trim($_POST['login']));
	$senha				= addslashes(trim($_POST['senha']));
	$nivel_ensino		= addslashes(trim($_POST['nivel_ensino']));
	$area_atuacao		= addslashes(trim($_POST['area_atuacao']));
	$instituicao_nome	= addslashes(trim($_POST['instituicao_nome']));

	$login_unificado = '1';

	try {

		$banco->iniciarTransacao();

		$instit = new Instituicao();
		$instit->setNome($instituicao_nome);
		$instit->setUnificado($login_unificado);
		$sessionFacade->gravarInstituicao($instit);

		global $_login_instituicao;
		$_login_instituicao = $instit->getId();

		$prof = new Professor();
		$prof->setNome($professor_nome);
		$prof->setNivelEnsino($nivel_ensino);
		$prof->setAreaAtuacao($area_atuacao);
		$prof->setEmail($email);
		$prof->setLogin($login);
		$prof->setSenha($senha);
		$prof->setAtivo('1');
		$sessionFacade->gravarProfessor($prof);

		$mail             = new PHPMailer();

		$body             = $mail->getFile('www/emails/cadastro_professor.html');

		$variaveis = array("{PROFESSOR}", "{LOGIN}", "{SENHA}");
		$valores   = array($professor_nome,$login, $senha);
		$body      = str_replace($variaveis, $valores, $body);

		$mail->From       = "testenetweb@gmail.com";
		$mail->FromName   = "TesteNet";
		$mail->Subject    = "TesteNet - Seja Bem Vindo!";
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
		$mail->MsgHTML($body);
		$mail->AddAddress($email, $professor_nome);
		$mail->AddBCC('testenetweb@gmail.com', 'Suporte TesteNet');
		$mail->Send();

		$banco->efetivarTransacao();
		$banco->desconecta(); 
		header("Location: cadastro.completo.php?professor=".$prof->getId());
		exit;
	} catch(Exception $e) { 
		$banco->desfazerTransacao();
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
					<h1>Cadastro</h1>
					<p>Todos os campos s&atilde;o obrigat&oacute;rios</p>

					<?
						if (count($msg_erro)>0){
							echo fn_formata_msg_erro($msg_erro);
						}
					?>

					<form method='POST' action='cadastro.php' name='frm_cadastro'>
						<p><strong>Digite seu nome completo:</strong><br>
						<input type="text" name="professor_nome" size='40' value='<?=$professor_nome?>'>
						</p>
						<p><strong>Qual &eacute; o seu n&iacute;vel de ensino?</strong><br>
							<input type="radio" name="nivel_ensino" value='Fundamental'> Fundamental &nbsp;&nbsp;&nbsp;
							<input type="radio" name="nivel_ensino" value='Medio'> M&eacute;dio &nbsp;&nbsp;&nbsp;
							<input type="radio" name="nivel_ensino" value='Superior' checked> Superior &nbsp;&nbsp;&nbsp;
						</p>
						<p><strong>Qual &eacute; sua &aacute;rea de atua&ccedil;&atilde;o?</strong><br>
							<select name='area_atuacao'>
								<option value='Administracao'>Administra&ccedil;&atilde;o</option>
								<option value='Arquitetura'>Arquitetura</option>
								<option value='Biologia'>Biologia</option>
								<option value='Ciencias Sociais'>Ci&ecirc;ncias Sociais</option>
								<option value='Computacao'>Computa&ccedil;&atilde;o</option>
								<option value='Direito'>Direito</option>
								<option value='Engenharia'>Engenharia</option>
								<option value='Fisica'>F&iacute;sica</option>
								<option value='Matematica'>Matem&aacute;tica</option>
								<option value='Medicina'>Medicina</option>
								<option value='Quimica'>Qu&iacute;mica</option>
								<option value='Pedagogia'>Pedagogia</option>
								<option value='Portugues'>Portugu&ecirc;s</option>
								<option value='Sociologia'>Sociologia</option>
								<option value='Outras'>Outras</option>
							</select>
						</p>
						<p><strong>E-Mail:</strong><br>
						<input type="text" name="email" size='30' value='<?=$email?>'>
						</p>
						<p><strong>Escolha seu login:</strong><br>
						<input type="text" name="login" size='30' value='<?=$login?>'>
						</p>
						<p><strong>Senha:</strong><br>
						<input type="password" name="senha" size='10' >
						</p>
						<center><hr style='width:440px;'></center>
						<p><strong>Nome da Institui&ccedil;&atilde;o:</strong><br>
						<input type="text" name="instituicao_nome" maxlength='100' size='40' value='<?=$instituicao_nome?>'>
						</p>
						<p></p>
						<p></p>
						<br>
						<br>
						<p><input type="submit" name='Cadastrar' value='Cadastrar'></p>

					</form>

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

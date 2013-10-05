<?


function validip($ip) {
	if (!empty($ip) && ip2long($ip)!=-1) {
		$reserved_ips = array (
		array('0.0.0.0','2.255.255.255'),
		array('10.0.0.0','10.255.255.255'),
		array('127.0.0.0','127.255.255.255'),
		array('169.254.0.0','169.254.255.255'),
		array('172.16.0.0','172.31.255.255'),
		array('192.0.2.0','192.0.2.255'),
		array('192.168.0.0','192.168.255.255'),
		array('255.255.255.0','255.255.255.255')
		);
 
		foreach ($reserved_ips as $r) {
			$min = ip2long($r[0]);
			$max = ip2long($r[1]);
			if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
		}
		return true;
	} else {
		return false;
	}
 }
 
 function getip() {
	if (validip($_SERVER["HTTP_CLIENT_IP"])) {
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip) {
		if (validip(trim($ip))) {
			return $ip;
		}
	}
	if (validip($_SERVER["HTTP_X_FORWARDED"])) {
		return $_SERVER["HTTP_X_FORWARDED"];
	} elseif (validip($_SERVER["HTTP_FORWARDED_FOR"])) {
		return $_SERVER["HTTP_FORWARDED_FOR"];
	} elseif (validip($_SERVER["HTTP_FORWARDED"])) {
		return $_SERVER["HTTP_FORWARDED"];
	} elseif (validip($_SERVER["HTTP_X_FORWARDED"])) {
		return $_SERVER["HTTP_X_FORWARDED"];
	} else {
		return $_SERVER["REMOTE_ADDR"];
	}
 }


function getRealIpAddr()

{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip=$_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  {
	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
  }else{
	$ip=$_SERVER['REMOTE_ADDR'];
  }
  return $ip;
}


function RoundNota($nota) {
	$diff = $nota - floor($nota);
	if ($diff<=0.25 AND $diff > 0) {
		$nota = floor($nota) + 0.25;
	}
	if ($diff>0.25 && $diff<=0.5) {
		$nota = floor($nota) + 0.5;
	}
	if ($diff>0.5 && $diff<=0.75) {
		$nota = floor($nota) + 0.75;
	}
	if ($diff>0.75) {
		$nota = floor($nota) + 1;
	}
	return $nota;
}


// returns the percentage of the string "similarity"
function str_compare($str1, $str2) {
		$count = 0;

		$str1 = ereg_replace("[^a-z]", ' ', strtolower($str1));
		while(strstr($str1, '  ')) {
			$str1 = str_replace('  ', ' ', $str1);
		}
		$str1 = explode(' ', $str1);
	   
		$str2 = ereg_replace("[^a-z]", ' ', strtolower($str2));
		while(strstr($str2, '  ')) {
			$str2 = str_replace('  ', ' ', $str2);
		}
		$str2 = explode(' ', $str2);
	   
		if(count($str1)<count($str2)) {
			$tmp = $str1;
			$str1 = $str2;
			$str2 = $tmp;
			unset($tmp);
		}
	   
		for($i=0; $i<count($str1); $i++) {
			if(in_array($str1[$i], $str2)) {
				$count++;
			}
		}
	   
		return $count/count($str2)*100;
}

function str_limpo($s){
	$s = retira_acentuacao($s);
	$s = str_replace(" ","",$s);
	return $s;
}


function LCS_Length($s1, $s2){
	$m = strlen($s1);
	$n = strlen($s2);

	//this table will be used to compute the LCS-Length, only 128 chars per string are considered
	$LCS_Length_Table = array(array(128),array(128)); 

	//reset the 2 cols in the table
	for($i=1; $i < $m; $i++) $LCS_Length_Table[$i][0]=0;
	for($j=0; $j < $n; $j++) $LCS_Length_Table[0][$j]=0;

	for ($i=1; $i <= $m; $i++) {
		for ($j=1; $j <= $n; $j++) {
			if ($s1[$i-1]==$s2[$j-1])
				$LCS_Length_Table[$i][$j] = $LCS_Length_Table[$i-1][$j-1] + 1;
			else if ($LCS_Length_Table[$i-1][$j] >= $LCS_Length_Table[$i][$j-1])
				$LCS_Length_Table[$i][$j] = $LCS_Length_Table[$i-1][$j];
			else
				$LCS_Length_Table[$i][$j] = $LCS_Length_Table[$i][$j-1];
		}
	}
	return $LCS_Length_Table[$m][$n];
}

function get_lcs($s1, $s2){
	//ok, now replace all spaces with nothing
	$s1 = strtolower(str_limpo($s1));
	$s2 = strtolower(str_limpo($s2));

	$lcs = LCS_Length($s1,$s2); //longest common sub sequence

	$ms = (strlen($s1) + strlen($s2)) / 2;

	return (($lcs*100)/$ms);
}

function _similar($str1, $str2) {
	$strlen1=strlen($str1);
	$strlen2=strlen($str2);
	$max=max($strlen1, $strlen2);

	$splitSize=250;
	if($max>$splitSize)
	{
		$lev=0;
		for($cont=0;$cont<$max;$cont+=$splitSize)
		{
			if($strlen1<=$cont || $strlen2<=$cont)
			{
				$lev=$lev/($max/min($strlen1,$strlen2));
				break;
			}
			$lev+=levenshtein(substr($str1,$cont,$splitSize), substr($str2,$cont,$splitSize));
		}
	}
	else
	$lev=levenshtein($str1, $str2);

	$porcentage= -100*$lev/$max+100;
	if($porcentage>75)//Ajustar con similar_text
	similar_text($str1,$str2,$porcentage);

	return $porcentage;
}


function retira_acentuacao($s){
	$s = strtr($s, "µÀÁÂÃÄÅÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖØÙÚÛÜİßàáâãäåæçèéêëìíîïğñòóôõöøùúûüıÿ", "uAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
/*	$s = ereg_replace("[áàâãäÁÀÃÂÄ]","a", $s);
	$s = ereg_replace("[èéêëÈÉÊË]","e", $s);
	$s = ereg_replace("[ìíîïÌÍÎÏ]","i", $s);
	$s = ereg_replace("[ÒÓÔÕÖòóôõö]","o", $s);
	$s = ereg_replace("[ÙÚÛÜùúûü]","u", $s);
	$s = ereg_replace("[Çç]","c", $s);
*/
	return $s;
}

/** 
 * Função para converte padrões de data
 * 
 * @param string $date Data a ser convertida
 * @since 10/01/2008
 * @author Fabio Nowaki<fabio.nowaki@gmail.com> 
 * @return string 
 */


 /* COMPARAR DATAS */

 function comparar_datas (){
	$exp_date = "2006-01-16";
	$todays_date = date("Y-m-d");

	$today = strtotime($todays_date);
	$expiration_date = strtotime($exp_date);

	if ($expiration_date > $today) {
	 $valid = "yes";
	} else {
	 $valid = "no";
	}

	/* OUTRA MANEIRA DE COMPARAR */
	// mktime is as follows (hour, minute, second, month, day, year)
	$lastpost = mktime(5,15,0,10,1,2002);

	echo 'Last Post was: ', date("M-d-Y H:m:s a", $lastpost), '<br>';

	if (canPost($lastpost, 24)) {
		echo 'The time now is: ', date("M-d-Y H:m:s a", time()), '<br>';
		print 'ok to post';
	} else {
		echo 'next post time is ', date("M-d-Y H:m:s a", $lastpost+(3600*24));
	}


	function canPost($lastpost, $diff){
		// Lets turn hours into seconds
		$diff = $diff*3600;

		// When can the user post next?
		$nextpost = $lastpost+$diff;

		// What time is it now?
		$timenow = time();


		// Is the time now greater than the
		// next available post time?
		if ($timenow > $nextpost) {
		return true;
		} else {
		return false;
		}
	}
}


function converte_data($date){
	$date = explode("-", ereg_replace('/', '-', $date));
	$date = ''.$date[2].'/'.$date[1].'/'.$date[0];
	return $date;
}

function ConverteData($Data,$adicional = ''){

	$retorno = "";
	$tmp_data = explode(" ",$Data);

	if (count($tmp_data)==2){
		$Data = trim($tmp_data[0]);
		$Hora = trim($tmp_data[1]);
	}

	if (strstr($Data, "/")){
		$d = explode ("/", $Data);
		if (count($d)!=3) {
			return false;
		}
		$retorno = "$d[2]-$d[1]-$d[0]";

	}elseif(strstr($Data, "-")){
		$d = explode ("-", $Data);
		if (count($d)!=3) {
			return false;
		}
		$retorno = "$d[2]/$d[1]/$d[0]"; 
	}else{
		return false;
	}

	if (strlen($Hora)>0){
		$retorno .= " ".$Hora;
	}
	if (strlen($adicional)>0){
		$retorno = $adicional.$retorno.$adicional;
	}
	return $retorno;
}

function FormataData($Data,$separador = ''){

	$retorno = "";
	$tmp_data = explode(" ",$Data);

	if (count($tmp_data)==2){
		$Data = trim($tmp_data[0]);
		$Hora = trim($tmp_data[1]);
	}

	if (strstr($Data, "/")){
		$d = explode ("/", $Data);
		if (count($d)!=3) {
			return false;
		}
		if (strlen($d[2])==2){
			$retorno = "$d[2]/$d[1]/$d[0]";
		}else{
			$retorno = "$d[0]/$d[1]/$d[2]";
		}
	}elseif(strstr($Data, "-")){
		$d = explode ("-", $Data);
		if (count($d)!=3) {
			return false;
		}
		$retorno = "$d[2]/$d[1]/$d[0]"; 
	}

	if (strlen($Hora)>0){
		$h = explode (":", $Hora);
		if (count($h)!=2 AND count($h)!=3) {
			return false;
		}
		if (strlen($separador)>0){
			$retorno .= " ".$separador." $h[0]:$h[1]";;
		}else{
			$retorno .= " "."$h[0]:$h[1]";;
		}
	}
	return $retorno;
}

function fn_formata_valor($valor,$destino=''){
	if (strlen(trim($valor))>0){
		if ($destino == 'BANCO'){
			$valor = str_replace(array(' ','/',',','-'),"",$valor);
		}elseif ($destino == 'TELA'){
			$valor = str_replace(array(' ','/',',','-'),"",$valor);
			$valor = number_format($valor,2,".",' ');
		}else{
			$valor = str_replace(array(' ','/',',','-'),"",$valor);
		}
	}
	return $valor;
}

function fn_formata_msg_ok($msg){
/*
	$retorno = "";
	$retorno.= "<div class='msg_ok'>";
	$retorno.= "<ul>";
	if (count($msg)==1){
		$retorno.= "<span>".$msg[0]."</span>";
	}else{
		for ($i=0; $i< count($msg); $i++){
			$retorno .= "<li>".$msg[$i]."</li>";
		}
	}
	$retorno.= "</ul>";
	$retorno.= "</div>";
*/
	$retorno = "";
	$retorno.= "<div class='info'>";
	#$retorno.= "<h2>Operação realizada com sucesso!</h2>";
	if (count($msg)==1){
		$retorno.= "<h2>".$msg[0]."</h2>";
	}else{
		for ($i=0; $i< count($msg); $i++){
			if ($i==0){
				$retorno .= "<h2>".$msg[$i]."</h2>";
			}else{
				$retorno .= "<p>".$msg[$i]."</p>";
			}
		}
	}
	
	$retorno.= "</div>";
	return $retorno;
}

function fn_formata_msg_erro($msg){
/*
	$retorno = "";
	$retorno.= "<div class='msg_erro'>";
	$retorno.= "<span>No momento da operação, ocorreu os erros abaixo. Verifique e tente novamente.</span>";
	$retorno.= "<ul>";

	for ($i=0; $i< count($msg); $i++){
		$retorno .= "<li>".$msg[$i]."</li>";
	}
	$retorno.= "</ul>";
	$retorno.= "</div>";
*/
	$retorno = "";
	$retorno.= "<div class='attention'>";
	$retorno.= "<h2>No momento da operação, ocorreu os erros abaixo. Verifique e tente novamente</h2>";
	if (count($msg)==1){
		$retorno.= "<p>".$msg[0]."</p>";
	}else{
		for ($i=0; $i< count($msg); $i++){
			$retorno .= "<p>".$msg[$i]."</p>";
		}
	}
	$retorno.= "</div>";

	return $retorno;
}


function fn_mostra_mensagens($model,$msg_ok,$msg_erro){
	if (isset($msg_ok) && count($msg_ok)>0){
		$model->assign_vars(array('MSG' => fn_formata_msg_ok($msg_ok)));
	}
	if (isset($msg_erro) && count($msg_erro)>0){
		$model->assign_vars(array('MSG_ERRO' => fn_formata_msg_erro($msg_erro)));
	}
}


function optionDisciplina($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	$retorno .= $id=="-1"?"<option value='' SELECTED>Selecione a disciplina</option>":"";

	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}
function optionProfessor($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"<option value=''></option>";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}

function optionTopico($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	$retorno .= $id=="-1"?"<option value='' SELECTED>Selecione...</option>":"";

	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getDescricao()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getDescricao()."</option>";
		}
	}
	return $retorno;
}

function optionTipoPergunta($arrai, $id = ""){
	$retorno = "";
	#$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getDescricao()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getDescricao()."</option>";
		}
	}
	return $retorno;
}



function optionCurso($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED></option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}


function optionCursoComunicado($arrai, $id = ""){
	$retorno = "";
	$retorno .= $id==""?"<option value='' SELECTED>PARA TODOS OS CURSOS</option>":"";
	for ($i=0; $i<sizeof($arrai);$i++){
		if ($arrai[$i]->getId() == $id){
			$retorno .= "<option value='".$arrai[$i]->getId()."' SELECTED>".$arrai[$i]->getNome()."</option>";
		}else{
			$retorno .= "<option value='".$arrai[$i]->getId()."'>".$arrai[$i]->getNome()."</option>";
		}
	}
	return $retorno;
}


?>
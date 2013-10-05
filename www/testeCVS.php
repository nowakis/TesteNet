<?php
/******************************************************************
Script .........: Exporta para CVS
Por ............: Fabio Nowaki
Data ...........: 18/02/2008
********************************************************************************************/

require_once('banco.inc.php');
$path = "c:/animais.csv"; 

$query = "SELECT * INTO OUTFILE '$path' FIELDS TERMINATED BY ';' ENCLOSED BY '' LINES TERMINATED BY '\r\n' FROM tbl_animal";
$rSet = $db->Query($query);

?>



<?
class UniqueRand{
	var $alreadyExists = array();

	function uRand($min = NULL, $max = NULL){
		$break='false';
		while($break=='false'){
			$rand=mt_rand($min,$max);

			if(array_search($rand,$this->alreadyExists)===false){
				$this->alreadyExists[]=$rand;
				$break='stop';
			}else{
				#echo " $rand already!  ";
				#print_r($this->alreadyExists);
			}
		}
		return $rand;
	}
}

?>
<?php
	require_once('../../config.php');
	$con = mysqli_connect(HOST,USERNAME,PASSWORD,DB);
	$con -> set_charset("utf8");
	$text = $_GET['text'];
	$maybetexts = array();

	function Select2($con, $text, &$maybetexts, &$nameandplace) {
		$goodsdetail = array("gName", "gPlace", "gDePlace", "gDetail");
		foreach ($goodsdetail as $searchtext) {
			$sql = "select gName,".$searchtext." from goods where ".$searchtext." LIKE '%".$text."%' and isLose = 1";
			$query = mysqli_query($con,$sql);
			if($query){
				while($row = mysqli_fetch_array($query)){

					$maytext = $row{$searchtext};
					if($row[0] == "饭卡" && $searchtext == "gDetail"){
						if(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $text)>0){
							if(substr($row[1],0,2) == 'CN' && !in_array(substr($row[1],2), $maybetexts))
								$maytext = substr($row[1],2);
							else if(substr($row[1],17,2) == 'CN' && !in_array(substr($row[1],19), $maybetexts))
								$maytext = substr($row[1],19);
						}
						else if(substr($row[1],0,2) == 'CC' && !in_array(substr($row[1],2,15), $maybetexts)){
							$maytext = substr($row[1],2,15);
						}
					}else {
						if(mb_strlen($maytext,"utf8") > 6 ){
							$index = strpos($maytext,$text);
							$x = substr($maytext, $index, 18);
							$maytext = $x."...";
						}
					}
					if(!in_array($maytext, $maybetexts)){
						$maybetexts[] = $maytext;
					}
				}
			}
		}
	}

	Select2($con, $text, $maybetexts, $nameandplace);

	$len = mb_strlen($text,"utf8");
	if($len > 1 && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $text)>0){
		$i = 0;
		while($i < $len){
			$texts[] = mb_substr($text,$i++,1,"utf8");
		}
		foreach ($texts as $t) {
			Select2($con, $t, $maybetexts, $nameandplace);
		}
	}

	// foreach ($maybetexts as $value) {
	// 	echo $value."</br>";
	// }
	header('Content-type', 'application/json');
	echo json_encode($maybetexts);

?>
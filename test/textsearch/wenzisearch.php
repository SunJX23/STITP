<?php
	//关键字查询
	function Select1($con, $detext, &$result, &$except){
		$goodsdetail = array("gName", "gPlace", "gDePlace", "gDetail");
		foreach ($goodsdetail as $searchtext) {
			$sql = "select gID,gName,gDate,gPlace,gDePlace,gDetail,gContact,gImage from goods where ".$searchtext." LIKE '%".$detext."%' and isLose = 1".$except;
			$query = mysqli_query($con,$sql);
			if($query){
				while($row = mysqli_fetch_array($query)){
					$result[]=$row;
					$except = $except." and gID != ".$row[0];
				}
			}
		}
	}


	require_once('../../config.php');
	$con = mysqli_connect(HOST,USERNAME,PASSWORD,DB);
	$con -> set_charset("utf8");

	$text = $_POST['text'] ? $_POST['text'] : null;
	$result = array();
	$except = "";

	//精准关键字查询
	Select1($con, $text, $result, $except);

	//部分关键字查询
	$len = mb_strlen($text,"utf8");
	if($len > 1 && preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $text)>0){
		$i = 0;
		while($i < $len){
			$texts[] = mb_substr($text,$i++,1,"utf8");
		}
		foreach ($texts as $t) {
			Select1($con, $t, $result, $except);
		}
	}

	//拼音关键字查询


	$size=sizeof($result);
	if ($size){
		$null="无";
		foreach($result as $xinxi)
		{
			$sname=$xinxi['gName'];
			$sdate=$xinxi['gDate'];
			$splace=$xinxi['gPlace'];
			$scontact=$xinxi['gContact'];

			if(!$splace||$splace=="未知")
				$splace = $xinxi['gDePlace'] ? $xinxi['gDePlace'] : $null;

			else if($xinxi['gDePlace']) 
				$splace=$splace."  ".$xinxi['gDePlace'];

			if(!$scontact) $scontact=$null;

			if($sname=="饭卡"){
				$card=$xinxi['gDetail'];
				switch (substr($card,0,2)){
					case 'CC':
						$cno=substr($card,2,15);
						if(substr($card,17,2)=="CN"){
							$cname=substr($card,19);
						}
						else $cname=$null;
						break;
					case 'CN':
						$cno=$null;
						$cname=substr($card,2);
						break;
					default:
						$cno=$null;
						$cname=$null;
						break;
				}
				if(!$xinxi['gDetail']) 
					$detail=$null;
				else 
					$detail=$xinxi['gDetail'];

				echo "物品：".$sname."</br>";
				echo "姓名：".$cname."</br>";
				echo "卡号：".$cno."</br>";
			}else{
				if($xinxi['gName']=="其他" && $xinxi['gDetail'])
					$sname=$xinxi['gDetail'];
				else
					$sname=$sname." ".$xinxi['gDetail'];
				echo "物品：".$sname."</br>";

			}
				echo "时间：".$sdate."</br>";
				echo "地点：".$splace."</br>";
				echo "联系：".$scontact."</br></br>";
		}
	}
	else{
		echo "对不起，无此类物品</br></br>";
	}
?>
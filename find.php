<?php
	//error_reporting(0);
	require_once('./class/imgedit.class.php');
	require_once('config.php');
	$con=mysqli_connect(HOST,USERNAME,PASSWORD,DB);
	$con->set_charset("utf8");
	$num=mysqli_query($con,"select count(*) from goods");
	list($gID)=mysqli_fetch_row($num);
	$gID+=1;
	$gName=$_POST['name'];//物品名
	$cardn=$_POST['cardn'];//饭卡名
	$cardc=$_POST['cardc'];//饭卡号
	$dy=$_POST['datey'];//年
	$dm=$_POST['datem'];//月
	$dd=$_POST['dated'];//日
	$gDate=$dy."-".$dm."-".$dd;//时间
	$isBack=0;
	$uID="uid";
	$gPlace=$_POST['place'];//丢失地点
	$gDePlace=$_POST['deplace'];//具体地点
	$gDetail=$_POST['detail'];//物品详情
	$gContact=$_POST['contact'];//联系方式
	$isLose=0;//丢失

	if($gName=='饭卡'){
		if($cardn!=null||$cardc！=null){
			if($cardc!=null) $detial='CC'.$cardc;
			if($cardn!=null) $detial=$detial.'CN'.$cardn;
			$gDetail=$detial;
		}
	}

	if($gPlace==null){$gPlace="未知";}

	// 允许上传的图片后缀
	//$allowedExts = array("gif", "jpeg", "jpg", "png","pjpeg","x-png");
	// echo $_FILES["file"]["name"];
	$temp = explode(".", $_FILES["file"]["name"]);
	// echo $_FILES["file"]["size"];
	$extension = end($temp);     // 获取文件后缀名
	if (($_FILES["file"]["type"] == "image/gif")
	|| ($_FILES["file"]["type"] == "image/jpeg")
	|| ($_FILES["file"]["type"] == "image/jpg")
	|| ($_FILES["file"]["type"] == "image/pjpeg")
	|| ($_FILES["file"]["type"] == "image/x-png")
	|| ($_FILES["file"]["type"] == "image/png"))
	//&& in_array($extension, $allowedExts)))
	{
		if ($_FILES["file"]["size"]<10240000){
			if ($_FILES["file"]["error"] > 0)
			{
				echo "错误：: " . $_FILES["file"]["error"] . "<br>";
			}
			else
			{	
				$x="00";
				while (file_exists("photo/".date('YmdHis').$x.".".$extension)){
					$x++;
				}
				$gImage="photo/".date('YmdHis').$x.".".$extension;
				
				if(is_uploaded_file($_FILES['file']['tmp_name'])){
					// $up_path='photo/'.$_FILES["file"]["name"];
					// if(!$up_path) echo "up_path地址不存在".$up_path."<br>";

					//压缩图片
					$image = new edit_imagick($_FILES['file']['tmp_name']);
					$image->reSize($_FILES['file']['tmp_name']);

					$up_image=move_uploaded_file($_FILES["file"]["tmp_name"], $gImage);
					//clearstatcache();
				}
				// if($up_image) echo "物品图片信息上传成功<br>";
				// else echo "物品图片信息上传失败<br>";
				//rename("photo/".$_FILES["file"]["name"],$gImage);
			}
		}
		else{
			echo "图片大小不得超过10M";
		}
	}
	// else{
	// 	echo $_FILES["file"]["type"]."<br>";
	// 	echo "非法的文件格式";
	// }
	if($gName){
		$up_data=mysqli_query($con,"insert into goods(gID,gName,gDate,isBack,uID,gPlace,gDePlace,gDetail,gContact,gImage,isLose) values ('$gID','$gName','$gDate','$isBack','$uID','$gPlace','$gDePlace','$gDetail','$gContact','$gImage','$isLose')");
	}

	if (!$up_data) echo "物品信息上传失败<br>";

	//if($up) echo "上传成功"."<br>";
	//else echo "上传失败"."<br>";
	// echo $gID."<br>";
	// echo $gName."<br>";
	// echo $gDate."<br>";
	// echo $isBack."<br>";
	// echo $uID."<br>";
	// echo $gPlace."<br>";
	// echo $gDetail."<br>";
	// echo $gImage."<br>";
	mysqli_close($con);
?>
<?php
	//var_dump ($_POST);
	require("../../../config.php");
	$database = "if20_tammeoja_1";
	//kui on idee sisestatud ja nuppu vajutatud, salvestame selle andmebaasi.
	if(isset($_POST["ideasubmit"]) and !empty($_POST["ideasubmit"])) {
		$conn = new mysqli($serverhost, $serverusername, $serverpassword, $database);
		//valmistan ette sql käsu
		$stmt = $conn->prepare("INSERT INTO myideas (idea) VALUES(?)");
		echo $conn->error;
		//seome käsuga päris andmed
		//i - integer, d- decimal, s - string
		$stmt->bind_param("s", $_POST["ideainput"]);
		$stmt->execute();
		$stmt->close();
		$conn->close();
	}

	$fulltimenow = date("H:i:s");
	$hournow = date("H");
	$partofday = "lihtsalt aeg";
	if($hournow < 6) {
		$partofday = "uneaeg";
	} // enne 6
	if($hournow >= 8 and $hournow <= 18) {
		$partofday = "õppimise aeg";
	}
  
	$weekdayNameset = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	$monthNameset = ["jaanuar", "veebruar", "märts", "aprill", "mai", "juuni", "juuli", "august", "september", "oktoober", "november", "detsember"];
	$weekdaynow = date("N");
	$dayofmonth = date("d. ");
	$monthnow = date("m");

	#vaatame semestri kulgemist
	$semesterstart = new DateTime("2020-8-31");
	$semesterend = new DateTime("2020-12-13");
	$semesterduration = $semesterstart->diff($semesterend);
	$semesterdurationdays = $semesterduration->format("%r%a");
	$today = new DateTime("now");
	$semesterleft = $semesterstart->diff($today);
	$semesterleftdays = $semesterleft->format("%r%a");
	//percentages below
	$percentageelapsed = round($semesterleftdays/$semesterdurationdays*100,2);
	$percentageleft = 100-$percentageelapsed;

	//õppetöö protsent
	if($today = $semesterduration) { //õppetöö aktiivne
		$semesterstatus = "1. semestri õppetöö on aktiivne ning on möödunud " .$semesterleftdays 
		." päeva. Läbi on " .$percentageelapsed ." protsenti. Jäänud on " .$percentageleft ." protsenti";
	}
	if($today > $semesterduration) { // läbi
		$semesterstatus = "1. semestri õppetöö on läbi. Läbi on 100%.";
	}
	if($today < $semesterduration) { //pole alanud
		$semesterstatus = "1. semestri õppetöö ei ole veel alanud. Läbi on 0%.";
	}
	
	//annan ette lubatud pildivormingute loendi
	$picfiletypes = ["image/jpeg", "image/png"];

	//loeme piltide kataloogi sisu ja näitame pilte
	//$allfiles = scandir("../vp_pics/");
	$allfiles = array_slice(scandir("../vp_pics/"), 2);
	// var_dump($allfiles);
	//$picfiles = array_slice($allfiles, 2);
	$picfiles = [];
	foreach($allfiles as $thing) {
		$fileinfo = getImagesize("../vp_pics/" .$thing);
		if(in_array($fileinfo["mime"], $picfiletypes) == true) {
			array_push($picfiles, $thing);
		}
	}

	//paneme kõik pildid ekraanile
	$piccount = count($picfiles);
	//$i + 1;
	//$i ++
	$imghtml = '<img src="../vp_pics/' .$picfiles[rand(0,3)] .'" alt="Tallinna Ülikool">';
	// for($i = 0; $i < $piccount; $i ++) {
	// 	$imghtml .= '<img src="../vp_pics/' .$picfiles[$i] .'" alt="Tallinna Ülikool">';
	// }
	require("header.php");
?>
<!DOCTYPE html>
<html lang="en">
	<body>
		<img src="../img/vp_banner.png" alt="Veebiprogrammeerimise pilt">
		<ul>
			<li><a href="home.php">Avaleht</a></li>
			<li><a href="idearesults.php">Mõtted</a></li>
			<li><a href="listfilms.php">Filmid</a></li>
			<li><a href="addfilms.php">Lisa film</a></li>
			<li><a href="adduser.php">Lisa kasutaja</a></li>
		</ul>

		<h1>Võite vaadata vabalt ringi</h1><br>
		<p>Ring on <a href="https://areait.com.au/wp-content/uploads/2017/12/circle-png-circle-icon-1600.png">siin</a>!</p>
		<p>Lehe avamise hetk: <?php echo $dayofmonth .$monthNameset[$monthnow-1] .", " .$weekdayNameset[$weekdaynow-1] .", " .$fulltimenow; ?>.</p>
		<p><?php echo "Kellaajaliselt, praegu oleks " .$partofday ."."; ?></p>
		<p><?php echo "Veebilehe looja on " .$username ."." ?><p>
		<h3><?php echo $semesterstatus; ?>.<h3>
		<hr>
		<?php echo $imghtml; ?>
		<hr>
		<form method="POST">
			<label>Sisesta oma pähe tulnud mõte!</label>
			<input type="text" name="ideainput" placeholder="Kirjuta siia oma mõte!">
			<input type="submit" name="ideasubmit" value="Saada mõte ära!">
		</form>
		<p>Et näha kõik mõtted, vajutage <a href="idearesults.php">siia</a>!<p>
	</body>
</html>
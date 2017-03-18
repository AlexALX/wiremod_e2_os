<?php
/*
	Convert ALX E2 OS Downloaded file to binary
	Created by AlexALX
*/

// you need create folder "e2files" near script with CHMOD 777.
$dir = "e2files/";

function byte2strraw($num,$limit=4) {
	$ret = "";
	for($k=0;$k<$limit;$k++) {
		$tmp = ($num >> (8*$k)) & 0xFF;
		$ret .= chr($tmp);
	}
	return $ret;
}

function nicesize($bytes) {
	if ($bytes >= 1073741824) $bytes = number_format($bytes / 1073741824, 2) . ' GB';
	elseif ($bytes >= 1048576) $bytes = number_format($bytes / 1048576, 2) . ' MB';
	elseif ($bytes >= 1024) $bytes = number_format($bytes / 1024, 2) . ' KB';
	elseif ($bytes > 1) $bytes = $bytes . ' bytes';
	elseif ($bytes == 1) $bytes = $bytes . ' byte';
	else $bytes = '0 bytes';
	return $bytes;
}

function alxos_convert($File,$FName) {
	global $dir;
	$F = file_get_contents($File);
    if ($F!="") {
		$JSON = json_decode($F,true);
		if (!$JSON || count($JSON)<2) {
			echo "Error with parsing file!";
		}
		$cont = "";
		foreach($JSON[1] as $v) {
			$cont = $cont.byte2strraw((int)$v);
		}

		$cont = substr($cont,0,$JSON[0]["size"]);

		$FName = str_replace("../","",$FName); # just in case
		file_put_contents($dir.$FName."_conv.txt",$cont);
		echo "Conversion done!";
		echo "<br>File size: ".nicesize($JSON[0]["size"]);
		echo "<br>File name: ".$JSON[0]["name"];
		echo "<br>File ext: ".$JSON[0]["ext"];
		echo "<br><a href='?dw=".urlencode($FName)."&f=".urlencode($JSON[0]["name"].($JSON[0]["ext"]!=""?".".$JSON[0]["ext"]:""))."'>Download</a>";
	} else {
		echo "File not exists or empty!";
	}
}

if (isset($_GET["dw"]))  {
    $quoted = sprintf('"%s"', addcslashes(basename($_GET['f']), '"\\'));
    $file = preg_replace("/^[^A-Za-z0-9_. -]+$/","",$_GET['dw'])."_conv.txt";
    if (!file_exists($dir.$file) ) {
    	die("File not exists!");
    }
	$size   = filesize($dir.$file);
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=' . $quoted);
	header('Content-Transfer-Encoding: binary');
	header('Connection: Keep-Alive');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . $size);
	readfile($dir.$file);
	exit();
}

?><html>
<head>
<title>ALX OS - Convert file</title>
</head>
<body>
<form method='post' enctype='multipart/form-data'>Upload file: <input type='file' name='file'> <input type='submit' name='upload' value='OK'></form>
<?php if (isset($_POST["upload"])) {
	if (isset($_FILES["file"])) {
		alxos_convert($_FILES["file"]["tmp_name"],$_FILES["file"]["name"]);
	}
} ?>
</body>
</html>
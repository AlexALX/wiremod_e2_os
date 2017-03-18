<?php
/*
	Created by AlexALX
*/

header("Content-Type: text/html; Charset=cp1251;");
ob_start();

include("fs.php");

// You need have set CHMOD 777 to table.txt
$cfile = "table.txt";
define("FILE_SELF","index.php");

$lang = array(
	"en" => array(
    	"FORMAT_DISK"=>"FORMAT DISK",
    	"MK_FS_TBL"=>"MK FS TBL",
    	"MKFS"=>"MKFS",
    	"FORMAT_FS"=>"FORMAT FS",
    	"SEL_P"=>"Select partition",
    	"NO_P"=>"No partitions table found.",
    	"HDD_I"=>"HDD Info",
    	"HDD_C"=>"Capacity",
    	"HDD_S"=>"Sector size",
     	"vol_ex"=>"Volume with this label already exists!",
    	"hdd_ns"=>"Not enough disk space! Max size:",
    	"fs_msc"=>"The minimum cluster size is 32 bytes.",
    	"fs_c8"=>"The cluster must be a multiple of 8.",
    	"fs_maxc"=>"The maximum cluster size:",
    	"fs_mwc"=>"The minimum size of the partition with %s cluster:",
    	"err"=>"ERROR",
    	"err_sz"=>"Not enough disk space for the addition of the fs or reached the limit of partitions.",
    	"vol_name"=>"Volume name",
    	"size"=>"Size",
    	"csize"=>"Cluster size",
    	"type"=>"Type",
    	"mk_fs"=>"Create FS",
    	"path_no"=>"Path %s not exists.",
    	"path_cur"=>"Current path",
    	"fs_act"=>"FS Active",
    	"fs_type"=>"FS Type",
    	"fs_size"=>"FS Size",
    	"fs_free"=>"Free Space",
    	"fs_st"=>"FS Start",
    	"fs_end"=>"FS End",
    	"fs_tot"=>"FS Total",
    	"fs_emp"=>"Empty",
    	"fs_unk"=>"Unknown",
    	"fs_uns"=>"Unused space",
    	"fs_no"=>"File system not found.",
    	"go2p"=>"Go to partitions",
		"mkdir"=>"Create folder",
		"mkfile"=>"Create file",
		"upfile"=>"Upload file",
		"mkdir_em"=>"The folder name can not be empty!",
		"mkfile_ex"=>"The folder or file with the same name already exists.",
		"mkdir_er"=>"Unable to create the folder - not enough space on the disk.",
		"mkdir_name"=>"Folder name",
		"mkfile_em"=>"File name can not be empty!",
		"mkfile_er"=>"Failed to create a file - is not enough space on the disk.",
		"mkfile_name"=>"File name",
		"mkfile_er2"=>"Unable to save file - not enough space on the disk.",
		"file_preg"=>"A-Za-z0-9",
		"rmdir_root"=>"You can not delete the root directory!",
		"rmdir_nem"=>"You can not delete the directory - it is not empty!",
		"dir_cont"=>"Folder content",
		"f_c"=>"Creation",
		"f_m"=>"Modify",
		"f_a"=>"Access",
		"f_s"=>"File Size",
		"dir_e"=>"Folder empty.",
		"dir_c"=>"Folder is corrupt.",
		"f_ne"=>"File not exists.",
		"f_edit"=>"Edit file",
		"f_dw"=>"Download file",
		"f_em"=>"File empty.",
		"f_cont"=>"File content",
		"bytes"=>"byte",
		"byte"=>"byte",
		"kb"=>"KB",
		"mb"=>"MB",
		"gb"=>"GB",
	),
	"ru" => array(
    	"FORMAT_DISK"=>"ФОРМАТИРОВАТЬ ДИСК",
    	"MK_FS_TBL"=>"Создать таблицу разделов",
    	"MKFS"=>"Создать ФС",
    	"FORMAT_FS"=>"Форматировать ФС",
    	"SEL_P"=>"Выберите раздел",
    	"NO_P"=>"Таблица разделов не найдена.",
    	"HDD_I"=>"Информация о диске",
    	"HDD_C"=>"Емкость",
    	"HDD_S"=>"Размер сектора",
    	"vol_ex"=>"Раздел с такой меткой уже существует!",
    	"hdd_ns"=>"Недостаточно места на диске! Макс размер:",
    	"fs_msc"=>"Минимальный размер кластера: 32 байта.",
    	"fs_c8"=>"Кластер должен быть кратный 8ми.",
    	"fs_maxc"=>"Максимальный размер кластера:",
    	"fs_mwc"=>"Минимальный размер раздела при %s кластере:",
    	"err"=>"ОШИБКА",
    	"err_sz"=>"Недостаточно места на диске для добавления данной фс либо достигнут лимит разделов.",
    	"vol_name"=>"Название раздела",
    	"size"=>"Размер",
    	"csize"=>"Размер кластера",
    	"type"=>"Тип",
    	"mk_fs"=>"Создать ФС",
    	"path_no"=>"Путь %s не найден.",
    	"path_cur"=>"Текущий путь",
    	"fs_act"=>"ФС Активна",
    	"fs_type"=>"Тип ФС",
    	"fs_size"=>"Размер ФС",
    	"fs_free"=>"Свободно",
    	"fs_st"=>"Начало ФС",
    	"fs_end"=>"Конец ФС",
    	"fs_tot"=>"Всего",
    	"fs_emp"=>"Пусто",
    	"fs_unk"=>"Неизвестно",
    	"fs_uns"=>"Неиспользуемое пространство",
    	"fs_no"=>"Файловая система не найдена.",
    	"go2p"=>"Вернуться к разделам",
		"mkdir"=>"Создать папку",
		"mkfile"=>"Создать файл",
		"upfile"=>"Загрузить файл",
		"mkdir_em"=>"Имя папки не может быть пустым!",
		"mkfile_ex"=>"Папка или файл с таким именем уже существует.",
		"mkdir_er"=>"Не удалось создать папку - недостаточно места на диске.",
		"mkdir_name"=>"Название папки",
		"mkfile_em"=>"Имя файла не может быть пустым!",
		"mkfile_er"=>"Не удалось создать файл - недостаточно места на диске.",
		"mkfile_name"=>"Название файла",
		"mkfile_er2"=>"Не удалось сохранить файл - недостаточно места на диске.",
		"file_preg"=>"A-Za-zА-Яа-я0-9",
		"rmdir_root"=>"Невозможно удалить корневой каталог!",
		"rmdir_nem"=>"Невозможно удалить каталог - он не пустой!",
		"dir_cont"=>"Содерщание папки",
		"f_c"=>"Создан",
		"f_m"=>"Изменён",
		"f_a"=>"Открыт",
		"f_s"=>"Размер файла",
		"dir_e"=>"Папка пуста.",
		"dir_c"=>"Папка повреждена.",
		"f_ne"=>"Файл не существует.",
		"f_edit"=>"Редактировать",
		"f_dw"=>"Скачать файл",
		"f_em"=>"Файл пуст.",
		"f_cont"=>"Содержание файла",
		"bytes"=>"байт",
		"byte"=>"байт",
		"kb"=>"КБ",
		"mb"=>"МБ",
		"gb"=>"ГБ",
	),
);

$curlang = "en";

function get_msg($key) {
	global $curlang,$lang;
	return $lang[$curlang][$key];
}

$table = readTable(); // read virtual HDD
$needw = false;

$fstbl = readfstbl();

if (isset($_GET['format'])) {
	$table = array();
	$needw = true;
	redirect(FILE_SELF);
} elseif (isset($_GET['formatfs'])) {
	$fsinfo = WM1_readfs($fstbl,$_GET['formatfs']);
	$needw = true;
	WM1_format($fsinfo);
	redirect(FILE_SELF);
} elseif (isset($_GET['mkfstbl'])) {
	mkfstbl();
	$needw = true;
	redirect(FILE_SELF);
}

// Helper functions

function hddinfo() {
	echo get_msg("HDD_I").":";
	echo "<br>".get_msg("HDD_C").": ".nicesize(HDD_SIZE*HDD_SECTOR_SIZE);
	echo "<br>".get_msg("HDD_S").": ".nicesize(HDD_SECTOR_SIZE);
	echo "<hr>";
}

function nicesize($bytes) {
	if ($bytes >= 1073741824) $bytes = number_format($bytes / 1073741824, 2) . ' '.get_msg("gb");
	elseif ($bytes >= 1048576) $bytes = number_format($bytes / 1048576, 2) . ' '.get_msg("mb");
	elseif ($bytes >= 1024) $bytes = number_format($bytes / 1024, 2) . ' '.get_msg("kb");
	elseif ($bytes > 1) $bytes = $bytes . ' '.get_msg("bytes");
	elseif ($bytes == 1) $bytes = $bytes . ' '.get_msg("byte");
	else $bytes = '0 '.get_msg("byte");
	return $bytes;
}

function volumeexists($fstbl,$name) {
	if ($name=="") return 0;
	foreach($fstbl as $k=>$v) {
		if (!isset($v['type'])||$v['type']!=1) continue;
		$fs = WM1_readfs($fstbl,$k);
		if (!count($fs)) continue;
		if ($fs['volume_name']==$name) return 1;
	}
	return 0;
}

function findvolumebyname($fstbl,$name,&$fs) {
	if ($name=="") return -1;
	foreach($fstbl as $k=>$v) {
		if (!isset($v['type'])||$v['type']!=1||!$v['active']) continue;
		$fs = WM1_readfs($fstbl,$k);
		if (!count($fs)) continue;
		if ($fs['volume_name']==$name) return $k;
	}
	return -1;
}

function finddirbyname($fs,$dir_sc,$name) {
	$ls = WM1_listdir($fs,$dir_sc[0],$dir_sc[1]);
	if (!count($ls)) return array();
	foreach($ls as $k=>$v) {
		$ext = "";
		if ($v['ext']!="") $ext = ".".$v['ext'];
		if (($v['name'].$ext)==$name) return array($v['cluster'],$v['data_sector'],($v['attr'] >> 4) & 1);
	}
	return array();
}

function rmpathdots($path) {
	$npath = $path;
	foreach($path as $k=>$v) {
		if ($v==".") unset($npath[$k]);
		elseif ($v==".."&&$k>1) {
			unset($npath[$k-1]);
			unset($npath[$k]);
		}
	}
	return $npath;
}

function parsepath($path,$fstbl,&$fs) {
	if (!count($fstbl) || !count($path)) return array();
	$arr = array();
	//$fs = array();
	foreach($path as $k=>$v) {
		if ($v=="") break;
		if ($k==0) {
			$tmp = findvolumebyname($fstbl,$v,$fs);
			if ($tmp<0) return array();
			$arr[$k] = $tmp;
		} else {
			if ($k==1) {
				$tmp = array($fs['root_dir_cluster'],0);
			} else {
				$tmp = $arr[$k-1];
			}
			$tmp = finddirbyname($fs,$tmp,$v);
			if (!count($tmp)) return array();
			$arr[$k] = $tmp;
		}
	}
	return $arr;
}

function add2path($path,$add) {
	if ($add==".") return $path;
	if ($add=="..") {
		$path = explode("/",$path);
		unset($path[count($path)-1]);
		return implode("/",$path);
	}
	return $path."/".$add;
}

$exticons = array("txt"=>"txt_file.png","wmi"=>"image_link.png","png"=>"image.png","jpg"=>"image.png","gif"=>"image.png","bmp"=>"image.png");

function fileicon($ext) {
	global $exticons;
	$ext = strtolower($ext);
	if (isset($exticons[$ext])) return $exticons[$ext];
	return "application.png";
}

$head = false;
function head($curfs=-1) {
	global $head;
	if ($head) return;
	$head = true;
	echo "[ <a href='?format'>".get_msg("FORMAT_DISK")."</a> ] [ <a href='?mkfstbl'>".get_msg("MK_FS_TBL")."</a> ]<br>";
	echo "[ <a href='?mkfs'>".get_msg("MKFS")."</a> ]".($curfs>=0?" [ <a href='?formatfs=".$curfs."'>".get_msg("FORMAT_FS")."</a> ]":"")."<br>";
	echo "<hr>";
}

// end

if (!count($fstbl)) {
	head();
	hddinfo();
	echo get_msg("SEL_P").":<br><br>";
	echo get_msg("NO_P");
	die();
}

$fstypes = array(1=>"WM1");

if (isset($_GET['rmfs'])) {
	removefstbl($_GET['rmfs']);
	$needw = true;
	redirect(FILE_SELF);
} elseif (isset($_GET['mvfsdw'])) {
	movefstbl($fstbl,$_GET['mvfsdw'],1);
	$needw = true;
	redirect(FILE_SELF);
} elseif (isset($_GET['mvfsup'])) {
	movefstbl($fstbl,$_GET['mvfsup'],0);
	$needw = true;
	redirect(FILE_SELF);
}

if (isset($_GET['mkfs'])) {
	head();
	//echo WM1_mkfs($fstbl,256*126+128*7.75+24,8,"Test v1")."<BR>";  // 1024*127+128*7.75+16
	//echo WM1_mkfs($fstbl,HDD_SIZE-FS_TBL_SIZE-23,8,"Test v1");
	if (isset($_POST['mk'])) {
		$name = $_POST['name'];
		$size = $_POST['size'];
		$stype = $_POST['size_type'];
		if ($stype>1&&$stype<=3) {
			$size *= pow(1024,$stype-1);
		}
		$csize = $_POST['cluster'];
		$ctype = $_POST['cluster_type'];
		if ($ctype>1&&$ctype<=2) {
			$csize *= pow(1024,$ctype-1);
		}
		$type = $_POST['type'];
		$err = "";
		if (volumeexists($fstbl,$name)) $err .= get_msg("vol_ex")."\n<br>";
		if ($size>FS_MAX_SIZE*HDD_SECTOR_SIZE) $err .= get_msg("hdd_ns")." ".nicesize(FS_MAX_SIZE*HDD_SECTOR_SIZE)." (".(FS_MAX_SIZE*HDD_SECTOR_SIZE)." ".get_msg("bytes").")\n<br>";

		if ($csize<32) $err .= get_msg("fs_msc")."\n<br>";
		elseif($csize % 8) $err .= get_msg("fs_c8")."\n<br>";
		elseif($csize/HDD_SECTOR_SIZE>0xFFFF+HDD_SECTOR_SIZE) $err .= get_msg("fs_maxc")." ".nicesize(0xFFFF*HDD_SECTOR_SIZE+HDD_SECTOR_SIZE)." (".(0xFFFF*HDD_SECTOR_SIZE+HDD_SECTOR_SIZE)." ".get_msg("bytes").").\n<br>";
		//elseif($size % $csize) $err .= "Размер должен быть кратный кластеру.\n<br>";
		elseif(($size/HDD_SECTOR_SIZE)/($csize/HDD_SECTOR_SIZE)<9)
			$err .= sprintf(get_msg("fs_mwc"),nicesize($csize))." ".nicesize(9*$csize)." (".(9*$csize)." ".get_msg("bytes").").\n<br>";

		if ($err!="") {
			echo get_msg("err").":<br>".$err."<br>";
		} else {
			$ret = WM1_mkfs($fstbl,$size/HDD_SECTOR_SIZE,$csize/HDD_SECTOR_SIZE,$name);
			writeTable($table);
			if ($ret=="ERR_ADD2TBL") echo get_msg("err_sz")."<br>";
			elseif ($ret!="") echo $ret;
			else redirect(FILE_SELF);
		}
	}
	$fstype = "";
	foreach($fstypes as $k=>$v) {
		$fstype .= "<option value='$k'>$v</option>\n";
	}
	echo "<form method='post'><table>
	<tr><td>".get_msg("vol_name").":</td><td><input type='text' name='name'></td></tr>
	<tr><td>".get_msg("size").":</td><td><input type='text' name='size'><select name='size_type'>
		<option value='1'>".get_msg("bytes")."</option>
		<option value='2'>".get_msg("kb")."</option>
		<option value='3'>".get_msg("mb")."</option>
	</select></td></tr>
	<tr><td>".get_msg("csize").":</td><td><input type='text' name='cluster'><select name='cluster_type'>
		<option value='1'>".get_msg("bytes")."</option>
		<option value='2'>".get_msg("kb")."</option>
	</select></td></tr>
	<tr><td>".get_msg("type").":</td><td><select name='type'>".$fstype."</select></td></tr>
	<tr><td colspan=2><input type='submit' value='".get_msg("mk_fs")."' name='mk'></td></tr></table></form>";
	echo "<hr>";
}

$curfs = -1;
$curdir = 0;
$cursec = 0;
$isdir = 1;

$path = "";
if (isset($_GET['p'])) $path = $_GET['p'];
$bpath = htmlspecialchars($path);
$purl = "p=".urlencode($path);
$parr = explode("/",$path);
$fs = array();
if (count($parr)>0) {
	//$parr = rmpathdots($parr);
	$pdat = parsepath($parr,$fstbl,$fs);
	$c = count($pdat);
	$last = count($parr)-1;
	if ($parr[$last]=="") {
		unset($parr[$last]);
		$last--;
	}
	$bpath = "";
	$lastp = "";
	$parr = rmpathdots($parr);
	foreach($parr as $k=>$v) {
		//if ($v==".."||$v==".") break;
		$lastp .= ($k!=0?"/":"").htmlspecialchars($v);
		if ($k==$last) {
			$bpath .= "/".htmlspecialchars($v);
		} else {
			$bpath .= "/<a href='?p=".urlencode($lastp)."'>".htmlspecialchars($v)."</a>";
		}
	}
	$purl = "p=".$lastp;
	if (isset($parr[0])) {
		if (!$c) {
		    echo sprintf(get_msg("path_no"),"'/<a href='?p='>ROOT</a>".$bpath."'");
			die();
		}
		$curfs = $pdat[0];
		if ($c>1) {
			$tmp = end($pdat);
			$curdir = $tmp[0];
			$cursec = $tmp[1];
			$isdir = $tmp[2];
		}
	}
}

head($curfs);

$arr = array();

if ($path=="") {
	echo get_msg("path_cur").": /ROOT<hr>";

	hddinfo();

	echo get_msg("SEL_P").":<br><br>";
	echo "<table border='1' cellspacing='0' cellpadding='3'>
	<tr><td>".get_msg("fs_act")."</td>
	<td>".get_msg("fs_type")."</td>
	<td>".get_msg("vol_name")."</td>
	<td>".get_msg("fs_size")."</td>
	<td>".get_msg("fs_free")."</td>
	<td>".get_msg("csize")."</td>
	<td>".get_msg("fs_st")."</td>
	<td>".get_msg("fs_end")."</td>
	<td>".get_msg("fs_tot")."</td>
	<td></td>
	</tr>";

	$us = FS_MAX_SIZE;
	foreach($fstbl as $k=>$v) {
		$vname = "";
		$size = $v['total']*HDD_SECTOR_SIZE;
		$us -= $v['total'];
		$free = 0;
		$cluster = 0;
		if ($v['type']==1) {
			$fsinfo = WM1_readfs($fstbl,$k);
			//$vname = "<a href='?fs=".($k+1)."'>".$fsinfo['volume_name']."</a>";
			$vname = "<a href='?p=".urlencode($fsinfo['volume_name'])."'>".$fsinfo['volume_name']."</a>";
			$free = $fsinfo['free_clusters']*$fsinfo['sectors_in_cluster']*$fsinfo['bytes_in_sector'];
			$cluster = $fsinfo['sectors_in_cluster']*$fsinfo['bytes_in_sector'];
		}

		$up = ($k<3&&$v['type']==1&&$fstbl[$k+1]['type']!=1?"<a href='?mvfsdw=".$k."'><img src='icons/arrow_down.png'></a>":"");
		$down = ($k>0&&$v['type']==1&&$fstbl[$k-1]['type']!=1?"<a href='?mvfsup=".$k."'><img src='icons/arrow_up.png'></a>":"");

		echo "<tr><td>".$v['active']."</td>"
		."<td>".($v['type']==1?$fstypes[$v['type']]:($v['type']==0?get_msg("fs_emp"):get_msg("fs_unk")))."</td>"
		."<td>".$vname."</td><td>".nicesize($size)."</td><td>".nicesize($free)."</td><td>".nicesize($cluster)."</td>"
		."<td>".$v['start']."</td><td>".$v['end']."</td><td>".$v['total']."</td>"
		."<td>".$down.$up."<a href='?rmfs=".$k."'><img src='icons/cross.png'></a></td></tr>";
	}
	echo "</table>";
	echo "<br>".get_msg("fs_uns").": ".nicesize($us*HDD_SECTOR_SIZE)." (".$us*HDD_SECTOR_SIZE." ".get_msg("bytes").")";
} else {
	//$fs = WM1_readfs($fstbl,$curfs);
	if (!count($fs)) {
		echo get_msg("fs_no").".<br><br>[<a href='".FILE_SELF."'>".get_msg("go2p")."</a>]";
		die();
	}
	echo get_msg("path_cur").": /<a href='?p='>ROOT</a>".$bpath."<hr>";

	if ($isdir) {
		$curdir = ($curdir==0?$fs['root_dir_cluster']:$curdir);
		$ls = WM1_listdir($fs,$curdir,$cursec);
		if (count($ls) || $curdir==$fs['root_dir_cluster']) {
			//echo "[ <a href='?fs=".$curfs."&ent=".$curdir."&d=1&sd=".$cursec."&mkdir'>Create Folder</a> ] [ <a href='?fs=".$curfs."&ent=".$curdir."&d=1&sd=".$cursec."&mkfile'>Create File</a> ]<br><hr>";
			echo "[ <a href='?".$purl."&mkdir'>".get_msg("mkdir")."</a> ] [ <a href='?".$purl."&mkfile'>".get_msg("mkfile")."</a> ] [ <a href='?".$purl."&mkupload'>".get_msg("upfile")."</a> ]<br><hr>";
			if (isset($_GET['mkdir'])) {
				if (isset($_POST['name'])) {
					$name = preg_replace("/[^".get_msg("file_preg")."_+,.!@#$%^&(){\[\]}+-\s]/i","",$_POST['name']);
					$err = "";
					if ($name=="") $err = get_msg("mkdir_em");
					else {
						foreach($ls as $k=>$v) {
							if (($v['name'].$v['ext'])==$name) {
								$err = get_msg("mkfile_ex");
								break;
							}
						}
					}
					if ($err=="") {
						$err = WM1_mkfile($fs,$cursec,$name,"",1,$curdir);
						if ($err==-1) echo get_msg("mkdir_er")."<br>";
						else { $needw = true; redirect("?".$purl); }
					} else {
						echo $err;
					}
				}
				echo "<form method='post'>".get_msg("mkdir_name").": <input type='text' name='name'> <input type='submit' value='OK'></form>";
				echo "<hr>";
			} elseif (isset($_GET['mkfile'])) {
				if (isset($_POST['name'])) {
					$name = preg_replace("/[^".get_msg("file_preg")."_+,.!@#$%^&(){\[\]}+-\s]/i","",$_POST['name']);
					$ext = "";
					$arr = explode(".",$name);
					if (count($arr)>1) {
						$ext = end($arr);
						unset($arr[count($arr)-1]);
						$name = implode(".",$arr);
					}
					$err = "";
					if ($name=="") $err = get_msg("mkfile_em");
					else {
						foreach($ls as $k=>$v) {
							if (($v['name'].$v['ext'])==($name.$ext)) {
								$err = get_msg("mkfile_ex");
								break;
							}
						}
					}
					if ($err=="") {
						$err = WM1_mkfile($fs,$cursec,$name,$ext,0,$curdir);
						if ($err==-1) echo get_msg("mkfile_er")."<br>";
						//redirect("?fs=".$curfs."&ent=".$curdir."&d=1");
						else { $needw = true; redirect("?".$purl); }
					} else {
						echo $err;
					}
				}
				echo "<form method='post'>".get_msg("mkfile_name").": <input type='text' name='name'> <input type='submit' value='OK'> (name.ext)</form>";
				echo "<hr>";
			} elseif (isset($_GET['mkupload'])) {
				if (isset($_FILES['file'])) {
					$name = preg_replace("/[^".get_msg("file_preg")."_+,.!@#$%^&(){\[\]}+-\s]/i","",$_FILES['file']['name']);
					$ext = "";
					$arr = explode(".",$name);
					if (count($arr)>1) {
						$ext = end($arr);
						unset($arr[count($arr)-1]);
						$name = implode(".",$arr);
					}
					$err = "";
					if ($name=="") $err = get_msg("mkfile_em");
					else {
						foreach($ls as $k=>$v) {
							if (($v['name'].$v['ext'])==($name.$ext)) {
								$err = get_msg("mkfile_ex");
								break;
							}
						}
					}
					if ($err=="") {
						$err = WM1_mkfile($fs,$cursec,$name,$ext,0,$curdir);
						if ($err>0) {
							$needw = true;
							$fi = WM1_getfileinfo($err);
							$text = file_get_contents($_FILES['file']['tmp_name']);
							$cont = array();
							$sz = strlen($text);
							for($i=0;$i<strlen($text);$i+=4) {
								$cont[] = str2byte(substr($text,$i,4));
							}
							$err = WM1_rawwritefile($fs,$fi['cluster'],$err,$cont,$sz);
							if ($err==3) {
								$err = -1;
								WM1_removefile($fs,$fi['cluster'],$fi['data_sector']);
							}
						}
						if ($err==-1) {
							echo get_msg("mkfile_er2")."<br>";
						} else {
							redirect("?".$purl);
						}
					} else {
						echo $err;
					}
				}
				echo "<form method='post' enctype='multipart/form-data'>".get_msg("upfile").": <input type='file' name='file'> <input type='submit' value='OK'></form>";
				echo "<hr>";
			}
		}
		if (isset($_GET['rmfile'])) {
			if ($curdir==$fs['root_dir_cluster']) {
				echo get_msg("rmdir_root")."<hr>";
			} elseif(count($ls)>2) {
				echo get_msg("rmdir_nem")."<hr>";
			} else {
				WM1_removefile($fs,$curdir,$cursec);
				$needw = true;
				redirect("?".$_GET['rmfile']);
			}
		}
		echo get_msg("dir_cont").":<br><br>";
		if (count($ls)) {
			foreach($ls as $k=>$v) {
				$is_dir = ($v['attr'] >> 4) & 1;
				//if ($is_dir & $v["name"]==".") continue;
				$info = get_msg("f_c").": ".date("d/m/Y H:i:s",$v['time_create'])
				."\n".get_msg("f_m").": ".date("d/m/Y H:i:s",$v['time_modify'])
				."\n".get_msg("f_a").": ".date("d/m/Y H:i:s",$v['time_access'])
				."\n".get_msg("f_s").": ".nicesize($v['size']);
				$vname = $v['name'].($v['ext']!=""?".".$v['ext']:"");
				//echo "[".($is_dir?"DIR":"FILE")."] <a title='".$info."' href='?fs=".$curfs."&ent=".$v['cluster']."&d=".$is_dir."&sd=".$v['data_sector']."'>".$v['name'].($v['ext']!=""?".".$v['ext']:"")."</a>".(!$is_dir?" <a href='?fs=".$curfs."&ent=".$v['cluster']."&d=".$is_dir."&sd=".$v['data_sector']."&rmfile=".$curdir."&b2s=".$cursec."'><img src='icons/cross.png'></a>":"")."<br>";
				echo "<img src='icons/".($is_dir?"folder.png":fileicon($v['ext']))."' alt='".($is_dir?"DIR":"FILE")."'> <a title='".$info."' href='?".add2path($purl,urlencode($vname))."'>".$vname."</a>".($vname!="."&&$vname!=".."?" <a href='?".$purl."/".urlencode($vname)."&rmfile=".urlencode($purl)."'><img src='icons/cross.png'></a>":"")."<br>";
			}
			if (count($ls)<2||$curdir!=$fs['root_dir_cluster']&&count($ls)<=2) {
				echo "<br>".get_msg("dir_e");
			}
		} else {
			echo get_msg("dir_c");
		}
	} else {
		$exist = WM1_fileexists($fs,$cursec,$curdir);
		if (isset($_GET['rmfile'])&&$exist) {
			WM1_removefile($fs,$curdir,$cursec);
			$needw = true;
			redirect("?".$_GET['rmfile']);
		}
		if (!$exist) echo get_msg("f_ne");
		else {
			echo "[ <a href='?".$purl."&edit'>".get_msg("f_edit")."</a> ] [ <a href='?".$purl."&upload'>".get_msg("upfile")."</a> ] [ <a href='?".$purl."&download'>".get_msg("f_dw")."</a> ]<br><hr>";
			if (isset($_GET['upload'])) {
				if (isset($_FILES['file'])) {
					$text = file_get_contents($_FILES['file']['tmp_name']);
					$cont = array();
					$sz = strlen($text);
					for($i=0;$i<strlen($text);$i+=4) {
						$cont[] = str2byte(substr($text,$i,4));
					}
					$err = WM1_rawwritefile($fs,$curdir,$cursec,$cont,$sz);
					if ($err==3) {
						echo get_msg("mkfile_er2")."<br>";
					} else {
						$needw = true;
						redirect("?".$purl);
					}
				}
				// <label><input name='bin' type='checkbox' value='ON'> Binary</label>
				echo "<form method='post' enctype='multipart/form-data'>".get_msg("upfile").":<br><input name='file' type='file' value=''><input type='submit' value='OK'></form>";
				echo "<hr>";
			} elseif (isset($_GET['download'])) {
				$f = WM1_rawreadfile($fs,$curdir,$cursec);
				$fi = WM1_getfileinfo($cursec);
				$cont = "";
				foreach($f as $k=>$v) {
					$cont .= byte2strraw($v,0);
				}
				$cont = substr($cont,0,$fi['size']);
				ob_end_clean();
				header('Content-type: text/plain');
				header('Content-disposition: attachment; filename="'.end($parr).'"');
                echo $cont;
				die();
			} elseif (isset($_GET['edit'])) {
				if (isset($_POST['content'])) {
					$text = $_POST['content'];
					$cont = array();
					$sz = strlen($text);
					for($i=0;$i<strlen($text);$i+=4) {
						$cont[] = str2byte(substr($text,$i,4));
					}
					$err = WM1_rawwritefile($fs,$curdir,$cursec,$cont,$sz);
					if ($err==3) {
						echo get_msg("mkfile_er2")."<br>";
					} else {
						$needw = true;
						redirect("?".$purl);
					}
				}
				$f = WM1_rawreadfile($fs,$curdir,$cursec);
				$cont = "";
				foreach($f as $k=>$v) {
					$cont .= byte2str($v,0);
				}
				echo "<form method='post'>".get_msg("f_cont").":<br><textarea type='text' name='content' rows=20 cols=100>".htmlspecialchars($cont)."</textarea><br><input type='submit' value='OK'></form>";
				echo "<hr>";
			} else {
				$time = microtime(true);
				$f = WM1_rawreadfile($fs,$curdir,$cursec);
				//echo microtime(true)-$time."<br>";
				$fi = WM1_getfileinfo($cursec);
				echo get_msg("f_cont").":<br><br>";
				$empty = true;
				$tmp = explode(".",end($parr));
				$ext = strtolower(end($tmp));
				if ($ext=="jpg" || $ext=="gif" || $ext=="png"/* || $ext=="bmp"*/) {
					$cont = "";
					foreach($f as $k=>$v) {
						$cont .= byte2strraw($v,0);
						$empty = false;
					}
					$cont = substr($cont,0,$fi['size']);
					//$img = imagecreatefromjpeg($cont);
					echo "<img src='data:image/".$ext.";base64,".base64_encode($cont)."'>";
				} elseif ($ext=="bmp") {
					$start = microtime(true);
					$cont = "";
					foreach($f as $k=>$v) {
						$cont .= byte2strraw($v,0);
					}
					$cont = substr($cont,0,$fi['size']);
					$format = substr($cont,0,2);
                    $empty = false;
					if ($format=="BM") {
                    	$size = str2byte(substr($cont,34,4));
						$size = $fi["size"];
                    	$offset = str2byte(substr($cont,10,4));

                    	$hsize = str2byte(substr($cont,14,4));

						if ($hsize==12) {
							$width = str2byte(substr($cont,18,2));
							$height = str2byte(substr($cont,20,2));
							$bitcount = str2byte(substr($cont,24,2));
							$compression = 0;
							$colors = 1 << $bitcount;
						} else {
							$width = str2byte(substr($cont,18,4));
							$height = str2byte(substr($cont,22,4));
							$bitcount = str2byte(substr($cont,28,2));
							$compression = str2byte(substr($cont,30,4));
							$colors = str2byte(substr($cont,46,4));
							if ($colors==0) {
								$colors = 1 << $bitcount;
							}
						}

						function trailingZeros($n) {
							if(!$n) return 32;

							for($s = 0; !($n & 1); $s++) $n = $n >> 1;
							return $s;
						}

						$carr = array();

						if ($bitcount<=8) {
                        	$st = 14+$hsize;
							$colorsb = ($hsize==12?3:4);
                        	$image = substr($cont,$st);
                            for($i=0;$i<$colors;$i++) {
                            	$rgb = str2byte(substr($image,$i*$colorsb,$colorsb));
								$r = floor(($rgb >> 16) & 0xFF);
								$g = floor(($rgb >> 8) & 0xFF);
								$b = floor($rgb & 0xFF);

                            	$carr[$i] = array($r,$g,$b);
                            }
						} elseif ($bitcount==24 || $bitcount==32) {
                        	$rs = 16;
                        	$gs = 8;
                        	$bm = 0;
                        	$rl = 0xFF;
                        	$gl = 0xFF;
                        	$bl = 0xFF;
                    	} else {
                    		if ($compression==0) {
	                        	$rs = 10;
	                        	$gs = 5;
	                        	$bm = 0;
								$rl = 0x1F;
								$gl = 0x1F;
								$bl = 0x1F;
                    		} else {
		                    	$rm = str2byte(substr($cont,54,4));
		                    	$gm = str2byte(substr($cont,58,4));
		                    	$bm = str2byte(substr($cont,62,4));

								$rs = trailingZeros($rm);
								$gs = trailingZeros($gm);
								$bs = trailingZeros($bm);

								$rl = $rm >> $rs;
								$gl = $gm >> $gs;
								$bl = $bm >> $bs;

								echo " || ".$rl." ".$gl." ".$bl." ||";
							}
						}

						$image = substr($cont,$offset);

      					$rh = ($height>0?$height:-$height);
           				$fy = ($height<0?-1:1);

						$rs = $width*$rh;

						echo "<canvas width='".$width."' height='".$rh."' id='img' style='border: 10px solid #ccc;'></canvas>";

						echo "<script type='text/javascript'>"
						."	var canvas = document.getElementById('img');\n"
						."	var context = canvas.getContext('2d');\n";

						$x = $width; $y = 0;
						$ii = $bitcount/8;

						$x = 0; $y = $height-($fy>0?$fy:0); $of = 0;
						$ofn = floor(ceil($width*$ii/4)*4-$width*$ii);

						if ($bitcount<=4) {
							$ii = 8/$bitcount;
                            for($i=0;$i<=$size;$i++) {
								if ($x>=$width) {
									$of += $ofn;
									$x = 0;
									$y -= 1*$fy;
								}
								$rgb = str2byte(substr($image,$i+$of,1));
	       						for ($pi=$ii-1;$pi>=0;$pi--) {
	       							if ($bitcount==4) $ind = ($rgb >> $pi*4) & 0xF;
	       							else $ind = ($rgb >> $pi) & 1;
	       							$ct = $carr[$ind];
	       							$r = $ct[0];
	       							$g = $ct[1];
	       							$b = $ct[2];
									echo "	context.fillStyle = 'rgba($r,$g,$b,1)';\n";
									echo "	context.fillRect( $x, ".($fy==-1?$y+(-$height):$y).", 1, 1 );\n";
									$x++;
	       						}
                            }
						} else {
							$lastind = 0; $repeats = 0; $lasti = 0;
							for($i=0;$i<=$rs;$i+=$ii) {
								if ($x>=$width) {
									$of += $ofn;
									$x = 0;
									$y -= 1*$fy;
								}
	       						$ind = str2byte(substr($image,$i+$of,$ii));
	       						if ($bitcount==8) {
									/*if ($compression!=0) {
										if ($repeats==0) {
											$ind = str2byte(substr($image,$lasti,1));
											$lasti++;
											if ($ind==0) {
												$repeats = str2byte(substr($image,$lasti,1));
												$lastind = 0;
											} else {
												$lastind = str2byte(substr($image,$lasti,1));
												$repeats = $ind;
											}
											$lasti++;
											echo "alert('$repeats $lastind');";
										}

										if ($lastind==0) {
											$ind = str2byte(substr($image,$lasti,1));
											$lasti++;
										} else {
											$ind = $lastind;
										}
										$repeats--;
									}
									if (!isset($carr[$ind])) $carr[$ind] = array(0,128,255);*/
									$rgb = $carr[$ind];
									$r = $rgb[0];
									$g = $rgb[1];
									$b = $rgb[2];
	       						} else {
									$r = floor(($rgb >> $rs) & $rl); //floor(($rgb >> 10) & 0x1F);
									$g = floor(($rgb >> $gs) & $gl); //floor(($rgb >> 5) & 0x1F);
									$b = floor($rgb & $bl); //floor($rgb & 0x1F);
									$r *= floor(0xFF/$rl);
									$g *= floor(0xFF/$gl);
									$b *= floor(0xFF/$bl);
								}
								echo "	context.fillStyle = 'rgba($r,$g,$b,1)';\n";
								echo "	context.fillRect( $x, ".($fy==-1?$y+(-$height):$y).", 1, 1 );\n";
								$x++;
							}
						}

						echo "</script>"; // echo $ret;
					}

					//echo "<br><br>".(microtime(true)-$start);
				} else {
					$cont = "";
					foreach($f as $k=>$v) {
						$data = byte2str($v,0);
						if ($data!="") {
							$cont .= $data;
							$empty = false;
						}
					}
					$cont = substr($cont,0,$fi['size']);
					echo nl2br(htmlspecialchars($cont));
				}
				if ($empty) {
					echo "File empty.";
				}
			}
		}
	}
}

if ($needw) writeTable($table);

ob_end_flush();

?>
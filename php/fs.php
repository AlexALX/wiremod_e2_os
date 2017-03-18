<?php
/*
	Created by AlexALX
	Some code looks weird as for PHP
	It's for make it more similar to E2
*/

set_time_limit(60*5);
ini_set("memory_limit","128m");

/*===========================/
	HDD Functions & Info
/===========================*/

define("HDD_SIZE",256*1024*4);
define("HDD_SECTOR_SIZE",4);

$table = array();

function readTable() {
	global $cfile;
	$tbl = array();
	if (file_exists($cfile)) {
		$tmp = json_decode(file_get_contents($cfile),true);
		if ($tmp) $tbl = $tmp;
	}
	return $tbl;
}

function writeTable($tbl) {
	global $cfile;
	file_put_contents($cfile,json_encode($tbl));
}

function writeCell($addr,$val,$debug=false) {
	global $table;
	if ($addr<0||$addr>=HDD_SIZE) { echo "ERROR WRITE $addr "; return; } // out of hdd size
	if ($val!=0) $table[$addr] = $val;
	else unset($table[$addr]);
	if ($debug) echo $addr."=".$val." ".(isset($table[$addr])?$table[$addr]:0)."||";
}

function readCell($addr) {
	global $table;
	if ($addr<0||$addr>=HDD_SIZE) { /*print_r(debug_backtrace());*/ echo "ERROR READ $addr "; return 0; } // out of hdd size
	return (isset($table[$addr]) ? $table[$addr] : 0);
}

/*===========================/
	Helper functions & Info
/===========================*/

function redirect($url) {
	global $table,$needw;
	header("Location: ".$url);
	if ($needw) writeTable($table);
	die();
}

function str2byte($str) {
	if ($str=="") return 0;
	$str = substr($str,0,4);
	/*if (strlen($str)<4) {
		$str .= str_repeat(" ",4-strlen($str));
	}*/
	$str = str_split($str);
	$ret = 0;
	foreach($str as $k=>$v) {
    	$ret += (ord($v) << (8*$k));
	}
	return $ret;
}

function byte2str($num,$limit) {
	$ret = "";
	if (!$limit) $limit = 4;
	for($k=0;$k<$limit;$k++) {
		$tmp = ($num >> (8*$k)) & 0xFF;
		if ($tmp>0) $ret .= chr($tmp);
	}
	return $ret;
}

function byte2strraw($num,$limit) {
	$ret = "";
	if (!$limit) $limit = 4;
	for($k=0;$k<$limit;$k++) {
		$tmp = ($num >> (8*$k)) & 0xFF;
		$ret .= chr($tmp);
	}
	return $ret;
}

/*===========================/
	File System Table
/===========================*/

define("FS_TBL_SIZE",9);
define("FS_MAX_SIZE",HDD_SIZE-FS_TBL_SIZE-1);

function mkfstbl() {
	for ($n=0;$n<4;$n++) {
		$o = $n*2;
		writeCell($o,0);
		writeCell($o+1,0);
	}
	writeCell(FS_TBL_SIZE-1,0x55AA);
}

function findfreetbl($fstbl) {
	foreach($fstbl as $k=>$v) {
		if ($v['type']==0) return $k;
	}
	return -1;
}

function gettblsize($fstbl,$n) {
	$ts = 0;
	foreach($fstbl as $k=>$v) {
		if ($v['type']!=0) {
			$ts += $v['total'];
		}
	}
	return $ts;
}

function getlasttblsector($fstbl) {
	$max = 0;
	foreach($fstbl as $k=>$v) {
		if ($v['type']!=0 && $max<$v['end']) {
			 $max = $v['end'];
		}
	}
	return $max+1;
}

function findtblsectors($fstbl,$n) {
	$min = FS_TBL_SIZE;
	$max = HDD_SIZE-1;
	foreach($fstbl as $k=>$v) {
		if ($v['type']==0) continue;
		if ($n<$k && $v['start']<$max) {
			$max = $v['start'];
			break;
		}
		if ($n>$k && $v['end']>$min) {
			$min = $v['end'];
		}
	}
	return array($min,$max);
}

function addfs2tbl(&$fstbl,$n,$a,$t,$sz) {
	if ($n<0 || $n>3 || !count($fstbl) || $sz>HDD_SIZE-FS_TBL_SIZE || $sz>0xFFFFFF) return 0;
	if ((readCell($n*2) && 0xFFFF) != 0) return 0; // already used

	$ar = findtblsectors($fstbl,$n);
	$st = $ar[0];
	$ed = $ar[1];
	$ts = $ed-$st;
	if ($sz>$ts) return 0; // out of space

	$o = $n*2;
	writeCell($o,$a + ($st << 8)); 									// [1 byte - active] [3 bytes - start sector]
	writeCell($o+1, $t + ($sz << 8));								// [1 byte - FS type] [3 bytes - total sectors]
	// update fs tbl
	$fstbl[$n] = array("active"=>$a,"start"=>$st,"total"=>$sz,"type"=>$t,"end"=>$st+$sz);
	return $st;
}

function readfstbl() {
	if (readCell(FS_TBL_SIZE-1)!=0x55AA) return array();

	$fstbl = array();
	for($f=0;$f<4;$f++) {
		$o = $f*2;
		//if ((readCell($o+1) & 0xFF)==0) continue; // no fs
		$fstbl[$f] = array(
			"active" => readCell($o) & 0xFF,
            "start" => (readCell($o) >> 8) & 0xFFFFFF,
            "total" => (readCell($o+1) >> 8) & 0xFFFFFF,
            "type" => readCell($o+1) & 0xFF,
		);
		$fstbl[$f]['end'] = $fstbl[$f]['start']+$fstbl[$f]['total'];
	}
	return $fstbl;
}

function movefstbl($fstbl,$n,$up) {
	if ($n<0 || $n>3 || !count($fstbl)) return;
	if ($up) $up = 1; else $up = -1;
	$new_n = $n+$up;
	if ($new_n<0 || $new_n>3) return;
	$o = $n*2;
	if ($fstbl[$n]['type'] == 0 || $fstbl[$new_n]['type'] != 0) return;

	$st = $fstbl[$n]['start'];
	$sz = $fstbl[$n]['total'];
	$ed = $fstbl[$n]['end'];
	$ntmp = findtblsectors($fstbl,$n);
	// move fs data
	if ($up>0&&$ntmp[1]!=$ed||$up<0&&$ntmp[0]!=$st) {
		if ($up>0) {
			$nst = $ntmp[1]-$sz;
			$ned = $ntmp[1];
			for($i=$sz;$i>=0;$i--) {
				writeCell($nst+$i,readCell($st+$i));
				writeCell($st+$i,0);
			}
		} else {
			$nst = $ntmp[0];
			$ned = $ntmp[0]+$sz;
			for($i=0;$i<$sz;$i++) {
				writeCell($nst+$i,readCell($st+$i));
				writeCell($st+$i,0);
			}
		}
		$st = $nst;
	}

	writeCell($o,0);
	writeCell($o+1,0);
	$o = $new_n*2;
	writeCell($o,$fstbl[$n]['active'] + ($st << 8));
	writeCell($o+1,$fstbl[$n]['type'] + ($sz << 8));
}

function removefstbl($n) {
	if ($n<0 || $n>3 || readCell(FS_TBL_SIZE-1)!=0x55AA) return;
	$o = $n*2;
	writeCell($o,0);
	writeCell($o+1,0);
}

/*===========================/
	WM1 FS helper func
/===========================*/

function WM1_findsectorbycluster($fi,$c) {
	return $fi['reserved_sectors']+$fi['fat_tables']*$fi['sectors_for_fat']+(($c-1)*$fi['sectors_in_cluster']);
}

/*===========================/
	WM1 FS FAT func
/===========================*/

function WM1_readfatcluster($fi,$c) {
	$o = $fi['reserved_sectors']+($fi['current_fat']-1)*$fi['sectors_for_fat']+floor($c/2);
	return ((readCell($o) >> ($c % 2)*16) & 0xFFFF);
}

function WM1_findlastcluster($fi,$c) {
	$tmp = WM1_readfatcluster($fi,$c);
	if (!$tmp) return 0;
	if ($tmp<0xFFFD) {
		return WM1_findlastcluster($fi,$tmp);
	}
	return $c;
}

function WM1_findfreecluster($fi) {
	$o = $fi['reserved_sectors'];
	for($c=$fi['last_free_cluster'];$c<=$fi['total_clusters'];$c++) {
		if (((readCell($o+floor($c/2)) >> ($c % 2)*16) & 0xFFFF) == 0) {
			return $c;
		}
	}
	return 0;
}

function WM1_updatefat(&$fi,$c,$val) {
	// Update free clusters
	$o = $fi['reserved_sectors']+($fi['current_fat']-1)*$fi['sectors_for_fat']+floor($c/2);
	$cur = (readCell($o) >> ($c % 2)*16) & 0xFFFF;
	if ($cur==0||$val==0) {
		$tmp = ($val==0?1:-1);
		$fi['free_clusters'] = $fi['free_clusters']+$tmp;
		//writeCell($fi['reserved_sectors']-2,$fi['free_clusters'] + ($fi['last_free_cluster'] << 16));
		if ($val==0&&$cur!=0) {
			if ($fi['last_free_cluster']>$c) {
				$fi['last_free_cluster'] = $c;
				//writeCell($fi['reserved_sectors']-2,$fi['free_clusters'] + ($fi['last_free_cluster'] << 16));
			}
		} elseif($val!=0&&$cur==0) {
			if ($fi['last_free_cluster']<=$c) {
				$fi['last_free_cluster']++;
				$fi['last_free_cluster'] = WM1_findfreecluster($fi);
				//writeCell($fi['reserved_sectors']-2,$fi['free_clusters'] + ($fi['last_free_cluster'] << 16));
			}
		}
		writeCell($fi['reserved_sectors']-2,$fi['free_clusters'] + ($fi['last_free_cluster'] << 16));
	}

	// Update FATs
	for ($f=1;$f<=$fi['fat_tables'];$f++) {
		$o = $fi['reserved_sectors']+($f-1)*$fi['sectors_for_fat']+floor($c/2);
		$tmp = readCell($o) & 0xFFFF;
		$tmp2 = (readCell($o) >> 16) & 0xFFFF;
		if ($c % 2) $tmp2 = $val;
		else $tmp = $val;
		writeCell($o,$tmp + ($tmp2 << 16));
	}

	// erase cluster
	if ($cur==0&&$val!=0) {
		$o = WM1_findsectorbycluster($fi,$c);
		for ($i=0;$i<$fi['sectors_in_cluster'];$i++) {
			writeCell($o+$i,0);
		}
	}
}

function WM1_checkFAT($fi) {
	for($i=1;$i<=$fi['fat_tables'];$i++) {
		$o = $fi['reserved_sectors']+($i-1)*$fi['sectors_for_fat'];
		if (byte2str(readCell($o) & 0xFFFF,2)==$fi['media_descriptor']&&(readCell($o) >> 16 & 0xFFFF)==0xFFFF) {
			return $i;
		}
	}
	return 0;
}

/*===========================/
	WM1 FS General func
/===========================*/

function WM1_mkfs(&$fstbl,$size,$csize,$name) {
	if ($size>HDD_SIZE-FS_TBL_SIZE) return "ERR_MAXSIZE";
	if ($csize-1>0xFFFF) return "ERR_MAXCLUSTERSIZE";
	if ($csize % 8) return "ERR_CLUSTERSIZE";
	//if ($size % $csize) return "ERR_ERRSIZE";
	if ($size/$csize<9) return "ERR_ERRSIZE";

	// general info
	//$size = (256*1024)-13; // fs size
	//$csize = 32; // cluster size

	// calc fs tbl start/end/size
	$fsz = $size; // real sectors for this size - 262112

	// fs info
	$fats = 2;
	$rsv = 11;
	$tmp = ($fsz  - floor($fsz/$csize/2) * $fats) - $rsv; // 262112 - floor(4095)*1 - 10 = 258007
	if ($tmp<=0) return "ERR_MINSIZE";
	$fatsz = floor(floor($tmp/$csize)/2); // 4031
	if ($fatsz<=0) return "ERR_MINSIZE2";
	$md = "W1";
	$cst = $fatsz*2-1; // total available clusters
	$rdc = 2; // root dir cluster
	if ($cst<4) return "ERR_MINSIZE3";

	// add to fs table
	$st = addfs2tbl($fstbl,findfreetbl($fstbl),1,1,$fsz);
	if ($st==0) return "ERR_ADD2TBL";

	writeCell($st,str2byte("WM1")); 									// [4 bytes - fs str id]
	writeCell($st+1,$rsv + ($csize-1 << 16)); 							// [2 bytes - reserved sectors] [2 bytes - sectors in cluster]
	writeCell($st+2,$fats + ($fatsz << 8) + (1 << 24)); 				// [1 byte - FAT tables] [2 bytes - sectors for FAT] [1 byte - physical disk ID]
	writeCell($st+3,str2byte($md) + ($cst << 16));						// [2 bytes - media descriptor] [2 bytes - total clusters]
	writeCell($st+4,rand());											// [4 bytes - volume serial number]
	writeCell($st+5,str2byte(substr($name,0,4)));						// [4 bytes - volume name part1]
    writeCell($st+6,str2byte(substr($name,4,4)));						// [4 bytes - volume name part2]
    writeCell($st+7,str2byte(substr($name,8,4)));						// [4 bytes - volume name part3]
    writeCell($st+8,str2byte(substr($name,12,4)));						// [4 bytes - volume name part4]
    writeCell($st+9,$cst-3 + (4 << 16));								// [2 bytes - free clusters] [2 bytes - last free cluster]
    writeCell($st+10,$rdc + (0x55AA << 16));							// [2 bytes - cluster of root dir] [2 bytes - end signature]

	// fats
	$rsv += $st;
    for($k=1;$k<=$fats;$k++) {
    	$o = $rsv+($k-1)*$fatsz;
		writeCell($o,str2byte($md) + (0xFFFF << 16));					// [4 bytes - media descriptor + 0xFFFF]
		// clear FAT
		for($c=1;$c<$fatsz;$c++) {
			writeCell($o+$c,0);
		}
		// root dir
		writeCell($o+floor($rdc/2),0xFFFF << (($rdc % 2)*16));
    }

	// clear root dir
	$fi = array(
		"reserved_sectors"=>$rsv,
		"fat_tables"=>$fats,
		"sectors_for_fat"=>$fatsz,
		"sectors_in_cluster"=>$csize,
	);
	$o = WM1_findsectorbycluster($fi,$rdc);
	for ($c=0;$c<$csize;$c++) {
    	writeCell($o+$c,0);
    }
 	// create "." dir
	writeCell($o,str2byte("."));
	writeCell($o+2,$rdc << 16);
	writeCell($o+3,(1 << 28));

	return "";
}

function WM1_readfs($fstbl,$fs) {
	if (!isset($fstbl[$fs])) return array();
	$fsinfo = array();
	if ($fstbl[$fs]['type']!=1) return array(); // fs not supported
	$o = $fstbl[$fs]["start"];
	if (((readCell($o+10) >> 16) & 0xFFFF) != 0x55AA) return array(); // invalid FS
	$fsinfo = array(
		"fs_str_id" => byte2str(readCell($o),0),
		"bytes_in_sector" => HDD_SECTOR_SIZE,
		"reserved_sectors" => (readCell($o+1) & 0xFFFF)+$o,
		"sectors_in_cluster" => ((readCell($o+1) >> 16) & 0xFFFF)+1,
		"fat_tables" => readCell($o+2) & 0xFF,
		"sectors_for_fat" => (readCell($o+2) >> 8) & 0xFFFF,
		"disk_id" => (readCell($o+2) >> 24) & 0xFF,
		"media_descriptor" => byte2str(readCell($o+3) & 0xFFFF,2),
		"total_clusters" => (readCell($o+3) >> 16) & 0xFFFF,
		"volume_serial" => readCell($o+4),
		"volume_name" => byte2str(readCell($o+5),0).byte2str(readCell($o+6),0).byte2str(readCell($o+7),0).byte2str(readCell($o+8),0),
		"free_clusters" => readCell($o+9) & 0xFFFF,
		"last_free_cluster" => (readCell($o+9) >> 16) & 0xFFFF,
		"root_dir_cluster" => readCell($o+10) & 0xFFFF,
	);
	$fsinfo['current_fat'] = WM1_checkFAT($fsinfo);
	return $fsinfo;
}

function WM1_format(&$fi) {
	// clear FATs
	$max = $fi['sectors_for_fat']*$fi['fat_tables'];
	for ($i=1;$i<$max;$i++) {
		$o = $i+$fi['reserved_sectors'];
		writeCell($o,0);
	}

	// create root dir
	$rdc = $fi['root_dir_cluster'];
	for ($i=0;$i<$fi['fat_tables'];$i++) {
		$o = $fi['reserved_sectors']+$i*$fi['sectors_for_fat'];
		writeCell($o+floor($rdc/2),0xFFFF << (($rdc % 2)*16));
	}

	// update free clusters
	writeCell($fi['reserved_sectors']-2,$fi['total_clusters']-3 + (4 << 16));
	$fi['free_clusters'] = $fi['total_clusters']-3;

	// clear DATA
	$st = WM1_findsectorbycluster($fi,$rdc);
	$max = $fi['total_clusters']*$fi['sectors_in_cluster'];
	for ($i=1;$i<$max;$i++) {
		$o = $i+$st;
		writeCell($o,0);
	}

	// create "." dir
	$o = WM1_findsectorbycluster($fi,$rdc);
	writeCell($o,str2byte("."));
	writeCell($o+2,$rdc << 16);
	writeCell($o+3,(1 << 28));
}

/*===========================/
	WM1 FS Dir func
/===========================*/

function WM1_getsize($fi,$c) {
	$tmp = WM1_readfatcluster($fi,$c);
	if ($tmp==0) return 0;
	$size = $fi['sectors_in_cluster']*$fi['bytes_in_sector'];
	if ($tmp>0 && $tmp<0xFFFD) {
		$size += WM1_getsize($fi,$tmp);
	}
	return $size;
}

function WM1_findfreediroffset($fi,$dir) {
	$max = $fi['sectors_in_cluster']/8;
	$o = WM1_findsectorbycluster($fi,$dir);
	for ($i=0;$i<$max;$i++) {
		$c = (readCell($o+$i*8+2) >> 16) & 0xFFFF;
		if ($c==0) return $o+$i*8;
	}
	$tmp = WM1_readfatcluster($fi,$dir);
	if ($tmp>0 && $tmp<0xFFFD) {
		return WM1_findfreediroffset($fi,$tmp);
	}
	return 0;
}

function WM1_direxists($fi,$dir) {
	$tmp = WM1_readfatcluster($fi,$dir);
	if ($tmp==0) return false; // not exists, yes we can remove root dir
	if ($dir==$fi['root_dir_cluster']) return true; // root dir always exists if found
	// read "." folder
	$o = WM1_findsectorbycluster($fi,$dir);
	$c = (readCell($o+2) >> 16) & 0xFFFF;
	if (!$c || $c!=$dir) return false;
	return true;
}

function WM1_findfileindiroffset($fi,$dir,$file) {
	$max = $fi['sectors_in_cluster']/8;
	$o = WM1_findsectorbycluster($fi,$dir);
	for ($i=0;$i<$max;$i++) {
		$c = (readCell($o+$i*8+2) >> 16) & 0xFFFF;
		if ($c==$file) return $o+$i*8;
	}
	$tmp = WM1_readfatcluster($fi,$dir);
	if ($tmp>0 && $tmp<0xFFFD) {
		return WM1_findfileindiroffset($fi,$tmp,$file);
	}
	return 0;
}

function WM1_listdir($fi,$dir,$sd) {
	$ents = array();
	//$tmp = WM1_readfatcluster($fi,$dir);
	//if (!$tmp) return $ents; // not exists
	//$max = $fi['sectors_in_cluster']/8;
	//$tmp = WM1_direxists($fi,$dir);
	//if (!$tmp) return $ents; // not exists
	if ($sd>0&&$dir!=$fi['root_dir_cluster']) writeCell($sd+5,time());
	$of = WM1_findsectorbycluster($fi,$dir);
	/*if ($tmp>0 && $tmp<0xFFFD) {
		// array_merge too slow
		//$ents = array_merge($ents,WM1_listdir($fi,$tmp,0));
	} */
	$max = ceil(WM1_getsize($fi,$dir)/$fi['bytes_in_sector']/8);
	$ii = 0;
	for ($i=0;$i<$max;$i++) {
		if ($ii==$fi['sectors_in_cluster']) {
			$ii = 0;
			$of = WM1_readfatcluster($fi,$dir);
			if ($of==0 || $of>=0xFFFD) break;
			$dir = $of;
			$of = WM1_findsectorbycluster($fi,$dir);
		}
		$o = $ii+$of;
		$c = (readCell($o+2) >> 16) & 0xFFFF;
		$ii+=8;
		if ($c==0) continue;
		$ents[] = WM1_getfileinfo($o);
	}
	return $ents;
}

function WM1_mksysdirs(&$fi,$dir,$c) {
	$o = WM1_findsectorbycluster($fi,$c);
	// create "." dir
	writeCell($o,str2byte("."));
	writeCell($o+2,$c << 16);
	writeCell($o+3,(1 << 28));
	// create ".." dir
	if (($fi['sectors_in_cluster']/8)==1) {
		// special fix when cluster size is 32 bytes
		$o = WM1_findfreediroffset($fi,$c);
		if (!$o) {
			$l = WM1_findlastcluster($fi,$c);
			$c2 = WM1_findfreecluster($fi);
			if ($c2==0) return;
			WM1_updatefat($fi,$l,$c2);
			WM1_updatefat($fi,$c2,0xFFFF);
			$o = WM1_findsectorbycluster($fi,$c2);
		}
	} else $o += 8;
	writeCell($o,str2byte(".."));
	writeCell($o+2,$dir << 16);
	writeCell($o+3,(1 << 28));
}

/*===========================/
	WM1 FS File func
/===========================*/

function WM1_fileexists($fi,$sc,$file) {
	$tmp = WM1_readfatcluster($fi,$file);
	if ($tmp==0) return false; // not exists
	//$c = WM1_findfileindiroffset($fi,$dir,$file);
	//return ($c>0?true:false);
	if (((readCell($sc+2) >> 16) & 0xFFFF)==$file) return true;
	return false;
}

function WM1_removefile(&$fi,$c,$sc) {
	$tmp = WM1_readfatcluster($fi,$c);
	if ($tmp==0) return 0;
	/*$max = $fi['sectors_in_cluster'];
	$o = WM1_findsectorbycluster($fi,$c);
	for ($i=0;$i<$max;$i++) {
		writeCell($o+$i,0);
	}*/
	if ($sc>0) {
		for ($i=0;$i<8;$i++) {
			writeCell($sc+$i,0);
		}
	}
	if ($tmp>0 && $tmp<0xFFFD) {
		WM1_removefile($fi,$tmp,0);
	}
	WM1_updatefat($fi,$c,0);
}

function WM1_rawwritefile(&$fi,$c,$sd,$data,$fsz) {
	if ($c<=2) return 1;
	$size = WM1_getsize($fi,$c);
	if (!$size) return 2; // not exists, size=0
	$max = count($data);
	$sz = $size/($fi['sectors_in_cluster']*$fi['bytes_in_sector']);
	if (ceil($max/$fi['sectors_in_cluster'])-$sz>$fi['free_clusters']) return 3; // no space left
	WM1_removefile($fi,$c,0);
	WM1_updatefat($fi,$c,0xFFFF);
	if ($sd>0) {
		writeCell($sd+5,time());
		writeCell($sd+6,time());
		if ($fsz>0) {
			writeCell($sd+7,$fsz);
		} else {
			writeCell($sd+7,$max*$fi['bytes_in_sector']);  //ceil($max/$fi['sectors_in_cluster'])*$fi['sectors_in_cluster']
		}
	}
	$of = WM1_findsectorbycluster($fi,$c);
	$ii = 0;
	for ($i=0;$i<$max;$i++) {
		if ($ii==$fi['sectors_in_cluster']) {
			$ii = 0;
			$of = WM1_findfreecluster($fi);
			WM1_updatefat($fi,$c,$of);
			WM1_updatefat($fi,$of,0xFFFF);
			$c = $of;
			$of = WM1_findsectorbycluster($fi,$c);
		}
		$o = $ii+$of;
		writeCell($o,$data[$i]);
		$ii++;
	}
	return 0;
}

function WM1_rawreadfile($fi,$c,$sd) {
	if ($c<2) return array();
	$tmp = WM1_readfatcluster($fi,$c);
	if ($tmp==0) return array(); // not exists
	$sz = -1;
	if ($sd>0) {
		writeCell($sd+5,time());
		$sz = readCell($sd+7);
		if ($sz==0) return array();
	} else $sz = WM1_getsize($fi,$c);
	$ret = array();
	$max = $fi['sectors_in_cluster'];
	$of = WM1_findsectorbycluster($fi,$c);
	/*if ($sz<0) {
		for ($i=0;$i<$max;$i++) {
			$ret[] = readCell($of+$i);
		}
		if ($tmp>0 && $tmp<0xFFFD) {
			// array_merge too slow
			$ret = array_merge($ret,WM1_rawreadfile($fi,$tmp,0));
		}
	}*/
	if ($sz>0) {
		// much faster when size is known
		$max = ceil($sz/$fi['bytes_in_sector']);
		$ii = 0;
		for ($i=0;$i<$max;$i++) {
			if ($ii==$fi['sectors_in_cluster']) {
				$ii = 0;
				$of = WM1_readfatcluster($fi,$c);
				if ($tmp==0 || $tmp>=0xFFFD) break;
				$c = $of;
				$of = WM1_findsectorbycluster($fi,$c);
			}
			$o = $ii+$of;
			$ret[] = readCell($o);
			$ii++;
		}
	}
	return $ret;
}

function WM1_getfileinfo($of) {
	$c = (readCell($of+2) >> 16) & 0xFFFF;
	if ($c==0) return array();
	return array(
		"name"=>byte2str(readCell($of),0).byte2str(readCell($of+1),0).byte2str(readCell($of+2) & 0xFFFF,2),
		"ext"=>byte2str(readCell($of+3) & 0xFFFFFF,0),
		"attr"=>(readCell($of+3) >> 24) & 0xFF,
		"time_create"=>readCell($of+4),
		"time_access"=>readCell($of+5),
		"time_modify"=>readCell($of+6),
		"cluster"=>$c,
		"size"=>readCell($of+7),
		"data_sector"=>$of,
	);
}

function WM1_mkfile(&$fi,$sd,$name,$ext,$is_dir,$dir) {
	$tmp = WM1_direxists($fi,$dir);
	if (!$tmp) return 0; // dir not exists
	$c = WM1_findfreecluster($fi);
	if ($c==0) return -1; // not enough space
	$o = WM1_findfreediroffset($fi,$dir);
	if (!$o) {
		$l = WM1_findlastcluster($fi,$dir);
		WM1_updatefat($fi,$l,$c);
		WM1_updatefat($fi,$c,0xFFFF);
		$o = WM1_findsectorbycluster($fi,$c);
		$c = WM1_findfreecluster($fi);
		if ($c==0) return -1;
	}
	//if (!$is_dir) $c = 1; // don't reserv cluster for empty file EDIT: glitch with fileexists so disabled
	$name = substr($name,0,12);
	writeCell($o,str2byte(substr($name,0,4)));						// [4 bytes - name part1]
	writeCell($o+1,str2byte(substr($name,4,4)));           			// [4 bytes - name part2]
	writeCell($o+2,str2byte(substr($name,8,2)) + ($c << 16)); 		// [2 bytes - name part3] [2 bytes - first cluster]
	writeCell($o+3,str2byte(substr($ext,0,3)) + ($is_dir << 28));	// [3 bytes - file extension] [1 byte - attributes]
	writeCell($o+4,time());											// [4 bytes - creation time]
	writeCell($o+5,time());											// [4 bytes - last access time]
	writeCell($o+6,time());											// [4 bytes - modify time]
	writeCell($o+7,0);		 										// [4 bytes - file size]

	// Update FATs
	WM1_updatefat($fi,$c,0xFFFF);

	if ($dir!=$fi['root_dir_cluster']) {
		writeCell($sd+5,time());
		writeCell($sd+6,time());
	}

	// mk sys dirs
	if ($is_dir) WM1_mksysdirs($fi,$dir,$c);
	return $o;
}

?>
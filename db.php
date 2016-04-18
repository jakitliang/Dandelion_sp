<?php
function get_cache($key)
{
	$fp_idx = ROOT.'cache'.'/'.'cache_idx.dat';
	$fp_dat = ROOT.'cache'.'/'.'cache_dat.dat';
	if (is_file($fp_idx)) {
		$file = fopen($fp_idx, 'rb');
		for ($i=0; $i < 128; $i++) {
			fseek($file, $i*44, SEEK_SET);
			$block = fread($file, 44);
			$ckey = substr($block, 0, 32);
			$time = unpack('L', substr($block, 32, 4));
			$ckey = trim($ckey);
			if ($time[1] == 0 || (time() - $time[1]) > CACHE_TIME) {
				break;
			}
			if ($key == $ckey) {
				$off = unpack('L', substr($block, 36, 4));
				$off = $off[1];
				$len = unpack('L', substr($block, 40, 4));
				$len = $len[1];
				$fdat = fopen($fp_dat, 'rb');
				fseek($fdat, $off, SEEK_SET);
				$value_data = fread($fdat, $len);
				fclose($fdat);
				break;
			}
		}
		fclose($file);
	}
	return empty($value_data) ? false : $value_data;
}

function set_cache($key, $value)
{
	$fp_idx = ROOT.'cache'.'/'.'cache_idx.dat';
	$fp_dat = ROOT.'cache'.'/'.'cache_dat.dat';
	$byte = pack('C', 0x00);
	$index = $data = $bytes = '';
	if (!is_file($fp_idx)) {
		$file = fopen($fp_idx, 'w+b');
		for ($i=0; $i < 5632; $i++) {
			$bytes .= $byte;
		}
		fwrite($file, $bytes);
		fclose($file);
		unset($bytes);
		$file = fopen($fp_dat, 'w+b');
		fclose($file);
	}
	$file = fopen($fp_idx, 'r+b');
	$off = $len = $idx = 0;
	for ($i=0; $i < 127; $i++) {
		fseek($file, $i*44, SEEK_SET);
		$block = fread($file, 44);
		$time = unpack('L', substr($block, 32, 4));
		if ($time[1] == 0 || (time() - $time[1]) > CACHE_TIME) {
			break;
		}
		$idx++;
	}
	//write index
	fseek($file, 0, SEEK_SET);
	$index = $idx ? fread($file, $idx*44) : '';
	$space = 32 - strlen($key);
	for ($i=0; $i < $space; $i++) {
		$key .= $byte;
	}
	$key .= pack('L', time());
	$key .= pack('L', filesize($fp_dat));
	$key .= pack('L', strlen($value));
	$index = $key.$index;
	$space = 5632 - strlen($index);
	for ($i=0; $i < $space; $i++) {
		$index .= $byte;
	}
	fseek($file, 0, SEEK_SET);
	fwrite($file, $index);
	fclose($file);
	$file = fopen($fp_dat, 'a+b');
	fwrite($file, $value);
	fclose($file);
}
?>
<?php
/**
 * ZIP类，支持压缩及解压
 * @package phpok\libs
 * @作者 qinggan <admin@phpok.com>
 * @版权 2015-2016 深圳市锟铻科技有限公司
 * @主页 http://www.phpok.com
 * @版本 4.x
 * @授权 http://www.phpok.com/lgpl.html PHPOK开源授权协议：GNU Lesser General Public License
 * @时间 2016年12月16日
**/


if(!defined("PHPOK_SET")){exit("<h1>Access Denied</h1>");}
class phpzip_lib
{
	private $ctrl_dir = array();
	private $datasec = array();
	public $old_offset = 0;
	public $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
	public $dir_root = '';
	private $obj;

	public function __construct()
	{
		$this->dir_root = $GLOBALS['app']->dir_root;
	}

	public function set_root($dir)
	{
		$this->dir_root = $dir;
	}

	public function filelist(&$filelist,$path)
	{
		$path = str_replace("\\", "/", $path);
		$fdir = dir($path);
		while(($file = $fdir->read()) !== false){
			if($file == '.' || $file == '..'){
				continue;
			}
			$path_sub = preg_replace("*/{2,}*", "/", $path."/".$file);
			$filelist[] = is_dir($path_sub) ? $path_sub."/" : $path_sub;
			if(is_dir($path_sub)){
				$this->filelist($filelist,$path_sub);
			}
		}
		$fdir->close();
	}
	
	private function unix2DosTime($unixtime = 0)
	{
		$timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
		if($timearray['year'] < 1980){
			$timearray['year']    = 1980;
			$timearray['mon']     = 1;
			$timearray['mday']    = 1;
			$timearray['hours']   = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		}
		return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16)	| ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
	}
	 
	private function addFile($data, $filename, $time = 0)
	{
		$filename = str_replace('\\', '/', $filename);
		$dtime    = dechex($this->unix2DosTime($time));
		$hexdtime = '\x' . $dtime[6] . $dtime[7]
				  . '\x' . $dtime[4] . $dtime[5]
				  . '\x' . $dtime[2] . $dtime[3]
				  . '\x' . $dtime[0] . $dtime[1];
		eval('$hexdtime = "' . $hexdtime . '";');
		$fr       = "\x50\x4b\x03\x04";
		$fr      .= "\x14\x00";
		$fr      .= "\x00\x00";
		$fr      .= "\x08\x00";
		$fr      .= $hexdtime;
		$unc_len  = strlen($data);
		$crc      = crc32($data);
		$zdata    = gzcompress($data);
		$c_len    = strlen($zdata);
		$zdata    = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
		$fr      .= pack('V', $crc);
		$fr      .= pack('V', $c_len);
		$fr      .= pack('V', $unc_len);
		$fr      .= pack('v', strlen($filename));
		$fr      .= pack('v', 0);
		$fr      .= $filename;
		$fr      .= $zdata;
		$fr      .= pack('V', $crc);
		$fr      .= pack('V', $c_len);
		$fr      .= pack('V', $unc_len);
		$this->datasec[] = $fr;
		$new_offset      = strlen(implode('', $this->datasec));
		$cdrec  = "\x50\x4b\x01\x02";
		$cdrec .= "\x00\x00";
		$cdrec .= "\x14\x00";
		$cdrec .= "\x00\x00";
		$cdrec .= "\x08\x00";
		$cdrec .= $hexdtime;
		$cdrec .= pack('V', $crc);
		$cdrec .= pack('V', $c_len);
		$cdrec .= pack('V', $unc_len);
		$cdrec .= pack('v', strlen($filename) );
		$cdrec .= pack('v', 0 );
		$cdrec .= pack('v', 0 );
		$cdrec .= pack('v', 0 );
		$cdrec .= pack('v', 0 );
		$cdrec .= pack('V', 32 );
		$cdrec .= pack('V', $this->old_offset );
		$this->old_offset = $new_offset;
		$cdrec .= $filename;
		$this->ctrl_dir[] = $cdrec;
	}
	 
	 
	
	private function file()
	{
		$data    = implode('', $this->datasec);
		$ctrldir = implode('', $this->ctrl_dir);
		return   $data.$ctrldir.$this->eof_ctrl_dir.pack('v', sizeof($this->ctrl_dir)).pack('v', sizeof($this->ctrl_dir)).pack('V', strlen($ctrldir)).pack('V', strlen($data)). "\x00\x00";
	}

	/**
	 * 制作压缩包
	 * @参数 $dir，支持单个文件，目录及数组
	 * @参数 $saveName，保存的ZIP文件名
	**/
	public function zip($dir, $saveName)
	{
		if(@!function_exists('gzcompress')){
			return false;
		}
		ob_end_clean();
		$filelist = array();
		if(is_array($dir)){
			$filelist = $dir;
		}else{
			if(!file_exists($dir)){
				return false;
			}
			if(is_file($dir)){
				$filelist = array($dir);
			}else{
				$this->filelist($filelist,$dir);
			}
		}
		if(count($filelist) < 1){
			return false;
		}
		if(class_exists('ZipArchive')){
			$obj = new ZipArchive();
			$obj->open($saveName,ZipArchive::OVERWRITE|ZipArchive::CREATE);//创建一个空的zip文件
			foreach($filelist as $file){
				if(!file_exists($file) || !is_file($file)){
					continue;
				}
				$name = substr($file,strlen($this->dir_root));
				$obj->addFile($file,$name);
			}
			$obj->close();
			return true;
		}
		foreach($filelist as $file){
			if(!file_exists($file) || !is_file($file)){
				continue;
			}
			$fd = fopen($file, "rb");
			$content = @fread($fd, filesize($file));
			fclose($fd);
			$file = substr($file, strlen($this->dir_root));
			if(substr($file, 0, 1) == "\\" || substr($file, 0, 1) == "/"){
				$file = substr($file, 1);
			}
			$this->addFile($content, $file);
		}
		$out = $this->file();
		$fp = fopen($saveName, "wb");
		fwrite($fp, $out, strlen($out));
		fclose($fp);
	}
	 
	private function ReadCentralDir($zip, $zipfile)
	{
		$size     = filesize($zipfile);
		$max_size = ($size < 277) ? $size : 277;
		 
		@fseek($zip, $size - $max_size);
		$pos   = ftell($zip);
		$bytes = 0x00000000;
		 
		while($pos < $size){
			$byte  = @fread($zip, 1);
			$bytes = ($bytes << 8) | Ord($byte);
			$pos++;
			if($bytes == 0x504b0506){ break; }
		}
		 
		$data = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', fread($zip, 18));

		$centd['comment']      = ($data['comment_size'] != 0) ? fread($zip, $data['comment_size']) : '';  // 注释
		$centd['entries']      = $data['entries'];
		$centd['disk_entries'] = $data['disk_entries'];
		$centd['offset']       = $data['offset'];
		$centd['disk_start']   = $data['disk_start'];
		$centd['size']         = $data['size'];
		$centd['disk']         = $data['disk'];
		return $centd;
	}
	 
	 
	private function ReadCentralFileHeaders($zip)
	{
		$binary_data = fread($zip, 46);
		$header      = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);

		$header['filename'] = ($header['filename_len'] != 0) ? fread($zip, $header['filename_len']) : '';
		$header['extra']    = ($header['extra_len']    != 0) ? fread($zip, $header['extra_len'])    : '';
		$header['comment']  = ($header['comment_len']  != 0) ? fread($zip, $header['comment_len'])  : '';
		if($header['mdate'] && $header['mtime']){
			$hour    = ($header['mtime']  & 0xF800) >> 11;
			$minute  = ($header['mtime']  & 0x07E0) >> 5;
			$seconde = ($header['mtime']  & 0x001F) * 2;
			$year    = (($header['mdate'] & 0xFE00) >> 9) + 1980;
			$month   = ($header['mdate']  & 0x01E0) >> 5;
			$day     = $header['mdate']   & 0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		} else {
			$header['mtime'] = time();
		}
		$header['stored_filename'] = $header['filename'];
		$header['status'] = 'ok';
		if(substr($header['filename'], -1) == '/'){
			$header['external'] = 0x41FF0010;
		}
		return $header;
	}
 
 
	private function ReadFileHeader($zip)
	{
		$binary_data = fread($zip, 30);
		$data        = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);
 
		$header['filename']        = fread($zip, $data['filename_len']);
		$header['extra']           = ($data['extra_len'] != 0) ? fread($zip, $data['extra_len']) : '';
		$header['compression']     = $data['compression'];
		$header['size']            = $data['size'];
		$header['compressed_size'] = $data['compressed_size'];
		$header['crc']             = $data['crc'];
		$header['flag']            = $data['flag'];
		$header['mdate']           = $data['mdate'];
		$header['mtime']           = $data['mtime'];
 
		if($header['mdate'] && $header['mtime']){
			$hour    = ($header['mtime']  & 0xF800) >> 11;
			$minute  = ($header['mtime']  & 0x07E0) >> 5;
			$seconde = ($header['mtime']  & 0x001F) * 2;
			$year    = (($header['mdate'] & 0xFE00) >> 9) + 1980;
			$month   = ($header['mdate']  & 0x01E0) >> 5;
			$day     = $header['mdate']   & 0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		}else{
			$header['mtime'] = time();
		}
 
		$header['stored_filename'] = $header['filename'];
		$header['status']          = "ok";
		return $header;
	}
 
 
	private function ExtractFile($header, $to, $zip)
	{
		$header = $this->readfileheader($zip);
		 
		if(substr($to, -1) != "/"){ $to .= "/"; }
		if(!@is_dir($to)){ @mkdir($to, 0777); }
		 
		$pth = explode("/", dirname($header['filename']));
		for($i=0; isset($pth[$i]); $i++){
			if(!$pth[$i]){ continue; }
			$pthss .= $pth[$i]."/";
			if(!is_dir($to.$pthss)){ @mkdir($to.$pthss, 0777); }
		}
		 
		if(!($header['external'] == 0x41FF0010) && !($header['external'] == 16))
		{
			if($header['compression'] == 0){
				$fp = @fopen($to.$header['filename'], 'wb');
				if(!$fp){ return(-1); }
				$size = $header['compressed_size'];
				 
				while($size != 0){
					$read_size   = ($size < 2048 ? $size : 2048);
					$buffer      = fread($zip, $read_size);
					$binary_data = pack('a'.$read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size       -= $read_size;
				}
				fclose($fp);
				touch($to.$header['filename'], $header['mtime']);
			 
			}else{
				 
				$fp = @fopen($to.$header['filename'].'.gz', 'wb');
				if(!$fp){ return(-1); }
				$binary_data = pack('va1a1Va1a1', 0x8b1f, Chr($header['compression']), Chr(0x00), time(), Chr(0x00), Chr(3));
				 
				fwrite($fp, $binary_data, 10);
				$size = $header['compressed_size'];
				 
				while($size != 0)
				{
					$read_size   = ($size < 1024 ? $size : 1024);
					$buffer      = fread($zip, $read_size);
					$binary_data = pack('a'.$read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size       -= $read_size;
				}
				 
				$binary_data = pack('VV', $header['crc'], $header['size']);
				fwrite($fp, $binary_data, 8);
				fclose($fp);
				 
				$gzp = @gzopen($to.$header['filename'].'.gz', 'rb') or die("Cette archive est compress!");
				 
				if(!$gzp){
					return(-2);
				}
				$fp = @fopen($to.$header['filename'], 'wb');
				if(!$fp){ return(-1); }
				$size = $header['size'];
				 
				while($size != 0){
					$read_size   = ($size < 2048 ? $size : 2048);
					$buffer      = gzread($gzp, $read_size);
					$binary_data = pack('a'.$read_size, $buffer);
					@fwrite($fp, $binary_data, $read_size);
					$size       -= $read_size;
				}
				fclose($fp); gzclose($gzp);
				 
				touch($to.$header['filename'], $header['mtime']);
				@unlink($to.$header['filename'].'.gz');
			}
		}
		return true;
	}
	 
	private function Extract($zipfile, $to, $index = array(-1))
	{
		$ok  = 0;
		$zip = @fopen($zipfile, 'rb');
		if(!$zip){
			return false;
		}
		$cdir      = $this->ReadCentralDir($zip, $zipfile);
		$pos_entry = $cdir['offset'];
		if(!is_array($index)){
			$index = array($index);
		}
		for($i=0; $index[$i]; $i++){
			if(intval($index[$i]) != $index[$i] || $index[$i] > $cdir['entries']){
				return(-1);
			}
		}
		for($i=0; $i<$cdir['entries']; $i++){
			@fseek($zip, $pos_entry);
			$header          = $this->ReadCentralFileHeaders($zip);
			$header['index'] = $i;
			$pos_entry       = ftell($zip);
			@rewind($zip);
			fseek($zip, $header['offset']);
			if(in_array("-1", $index) || in_array($i, $index)){
				$stat[$header['filename']] = $this->ExtractFile($header, $to, $zip);
			}
		}
		 
		fclose($zip);
		return $stat;
	}

	/**
	 * 解压缩，支持解压的类有：ZipArchive > zip_open > 自写PHP
	 * @参数 $file，要解压的ZIP文件，完整的路径
	 * @参数 $to，要解压到的目标文件，如果为空，将解压到当前文件夹
	 * @返回 
	 * @更新时间 
	**/
	public function unzip($file,$to='')
	{
		if(class_exists('ZipArchive')){
			$zip = new ZipArchive;
			$zip->open($file);
			$zip->extractTo($to);
			$zip->close();
			return true;
		}
		if(function_exists('zip_open') && function_exists('zip_close')){
			$zip = zip_open($file);
			if($zip){
				while ($zip_entry = zip_read($zip)) {
					$file = basename(zip_entry_name($zip_entry));
					$fp = fopen($to.basename($file), "w+");
					if (zip_entry_open($zip, $zip_entry, "r")) {
						$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
						zip_entry_close($zip_entry);
					}
					fwrite($fp, $buf);
					fclose($fp);
				}
				zip_close($zip);
				return true;
			}
		}
		return $this->Extract($file,$to);
	}

	public function zip_info($zipfile)
	{
		$zip = @fopen($zipfile, 'rb');
		if(!$zip){
			return false;
		}
		$centd = $this->ReadCentralDir($zip, $zipfile);
		 
		@rewind($zip);
		@fseek($zip, $centd['offset']);
		$ret = array();
		for($i=0; $i<$centd['entries']; $i++){
			$header          = $this->ReadCentralFileHeaders($zip);
			$header['index'] = $i;
			$info = array(
				'filename'        => $header['filename'],                   // 文件名
				'stored_filename' => $header['stored_filename'],            // 压缩后文件名
				'size'            => $header['size'],                       // 大小
				'compressed_size' => $header['compressed_size'],            // 压缩后大小
				'crc'             => strtoupper(dechex($header['crc'])),    // CRC32
				'mtime'           => date("Y-m-d H:i:s",$header['mtime']),  // 文件修改时间
				'comment'         => $header['comment'],                    // 注释
				'folder'          => ($header['external'] == 0x41FF0010 || $header['external'] == 16) ? 1 : 0,  // 是否为文件夹
				'index'           => $header['index'],                      // 文件索引
				'status'          => $header['status']                      // 状态
			);
			$ret[] = $info;
			unset($header);
		}
		fclose($zip);
		return $ret;
	}

	public function zip_comment($zipfile)
	{
		$zip = @fopen($zipfile, 'rb');
		if(!$zip){
			return false;
		}
		$centd = $this->ReadCentralDir($zip, $zipfile);
		fclose($zip);
		return $centd[comment];
	}
}
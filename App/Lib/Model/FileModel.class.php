<?php
class FileModel extends Model
{
	private $file = array();
	public function getFile($name = '')
	{
		$this->file["status"] = 0;
		if (!$name) {
			return false;
		}
		if (!is_uploaded_file($_FILES[$name]["tmp_name"])) {
			return false;
		}
		$upfile = $_FILES[$name];
		$name = $upfile["name"];
		$type = $upfile["type"];
		$size = $upfile["size"];
		$tmp_name = $upfile["tmp_name"];
		$error = $upfile["error"];
		if ($error) {
			$this->file["error"] = "文件上传出错,系统出错";
			return $this->file;
		}
		if (C("fileSize") < $size / 1024 / 1024) {
			$this->file["error"] = "文件大小被限制";
			return $this->file;
		}
		$this->file["size"] = $size;
		$suffix = substr(strrchr($name, "."), 1);
		if (is_array(C("fileSuffix"))) {
			if (!in_array($suffix, C("fileSuffix"))) {
				$this->file["error"] = "文件类型被禁止";
				return $this->file;
			}
		}
		$this->file["suffix"] = $suffix;
		$savePath = APP_PATH . "../Public/Upload/";
		$loadTime = time();
		if (!file_exists($savePath . date("Ymd", $loadTime))) {
			if (!mkdir($savePath . date("Ymd", $loadTime))) {
				$this->file["error"] = "文件写入出错";
				return $this->file;
			}
		}
		$saveName = $savePath . date("Ymd", $loadTime) . "/" . md5($tmp_name . time()) . "." . $suffix;
		$this->file["url"] = date("Ymd", $loadTime) . "/" . md5($tmp_name . time()) . "." . $suffix;
		if (!move_uploaded_file($tmp_name, $saveName)) {
			$this->file["error"] = "文件保存失败";
			return $this->file;
		}
		$fileMd5 = md5_file($saveName);
		$result = $this->add(array("load_time" => $loadTime, "url" => $this->file["url"], "suffix" => $suffix, "filemd5" => $fileMd5, "filesize" => $this->file["size"]));
		if (!$result) {
			$this->file["error"] = "数据保存失败";
			return $this->file;
		}
		$this->file["status"] = 1;
		return $this->file;
	}
}
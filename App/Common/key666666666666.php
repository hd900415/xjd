		/*$domain = $_SERVER["SERVER_NAME"];
		$domain = substr($domain, 0, 4) == "www." ? substr($domain, 0, 4) : $domain;
		$version = CVPHP_VERSION;
		$key_def = base64_encode(sha1(base64_encode(md5($domain . $version))));
		if (!file_exists(COMMON_PATH . "key.php")) {
			$result = file_get_contents("http://www.xauguo.cn/Api/Index/index/?domain=" . $domain . "&version=" . $version);
			if ($result == "noAuth") {
				echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">";
				exit("您的程序为盗版，请立即删除所有文件并购买正版程序");
			}
			file_put_contents(COMMON_PATH . "key.php", $key_def);
		}
		$key = file_get_contents(COMMON_PATH . "key.php");
		$keyTime = filectime(COMMON_PATH . "key.php");
		if ($key != $key_def) {
			echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">";
			exit("您的程序为盗版，请立即删除所有文件并购买正版");
		}
		if ($keyTime <= time() - 4 * 60 * 60) {
			$result = file_get_contents("http://www.xauguo.cn/Api/Index/index/?domain=" . $domain . "&version=" . $version);
			if ($result == "noAuth") {
				echo "<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\">";
				exit("您的程序为盗版，请立即删除所有文件并购买正版");
			}
			unlink(COMMON_PATH . "key.php");
			file_put_contents(COMMON_PATH . "key.php", $key_def);
		}*/
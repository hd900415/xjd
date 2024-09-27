<?php
class AdminModel extends Model
{
	public function str2pass($str = '')
	{
		$str = md5($str);
		return sha1($str);
	}
}
<?php
class CommonAction extends Action
{
	protected function _initialize()
	{
		$action = strtoupper(MODULE_NAME);
		if ($action != "INDEX" && !$this->isLogin()) {
			$this->redirect("Index/login");
			exit(0);
		}
		$user= $this->isLogin();
		if($user['type'] == 1){
			define('ISADMIN',1);
		}
		if($user['type'] == 2){
			define('ISADMIN',0);
		}
		$m = array('Loan','Info','Index');
		//var_dump(ISADMIN);
		if(!ISADMIN){
			if(!in_array(MODULE_NAME, $m)){
					$this->error("没有操作权限");
			}
		}
		//var_dump(ISADMIN);
	}
	protected function isLogin()
	{
		@($info = session("manage"));
		return empty($info) ? false : $info;
	}
	protected function setLogin($info)
	{
		if (empty($info)) {
			session("manage", NULL);
			return true;
		}
		if (!empty($info['password'])) {
			unset($info['password']);
		}
		session("manage", $info);
		return true;
	}
}
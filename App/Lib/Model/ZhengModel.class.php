<?php
class ZhengModel extends Model
{	
	private function getSign($arr){
		ksort($arr);
		$str = '';
		foreach($arr as $key => $v){
			$str.="$key=".$v."&";
		}
		
		$str = substr($str,0,-1);
		//var_dump($str);die;
		//立木征信密钥
		$str = $str."OOOOOOOOOOTAnPXMOdpgc4ClhHAOMPny4RXVABdNdHKOOOOOOOOOOOOOOOO";
		return sha1($str);
	}
	public function ihttpPost($arr){
		$url = "https://api.limuzhengxin.com/api/gateway";
		$sign = $this->getSign($arr);
		$signn = array('sign'=>$sign);
		$params = array_merge($arr,$signn);
		//var_dump($this->request_post($url,$params));die;
		$res = $this->request_post($url,$params);
		return $res;
	}
    private function request_post($url = '', $post_data = array()) {//url为必传  如果该地址不需要参数就不传  
		 if (empty($url)) {  
			 return false;  
		 }  		   
		if(!empty($post_data)){  
		 $params = '';  
		  foreach ( $post_data as $k => $v )   
		  {   
			  $params.= "$k=" . $v. "&" ;  
			 // $params.= "$k=" . $v. "&" ;  
		  }  
		  $params = substr($params,0,-1);  
		}   		
		 $ch = curl_init();//初始化curl  
		 curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页  
		 curl_setopt($ch, CURLOPT_HEADER, 0);//设置header  
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上  
		 curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		 
		 if(!empty($post_data))curl_setopt($ch, CURLOPT_POSTFIELDS, $params);  
		 $data = curl_exec($ch);//运行curl
		 //var_dump($data);die;
		 curl_close($ch);  
		 return $data;  
	}  
}
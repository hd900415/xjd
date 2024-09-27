<?php
$config=array(
    //'配置项'=>'配置值'
    'APP_GROUP_LIST' 		=>	'Wchat,Manage',
    'DEFAULT_GROUP'  		=>	'Wchat',
    'URL_MODEL'		 		=>	2,
    'URL_HTML_SUFFIX'		=>	'.shtml',
    'URL_404_REDIRECT'		=>	'404.html',
    'OUTPUT_ENCODE'			=>	true,
    'PAY_ORDER_EMAIL'			=>	'admin@gfpay199.com',
    'TMPL_ACTION_ERROR'		=>	APP_PATH . 'Tpl/error.php',
    'TMPL_ACTION_SUCCESS'	=>	APP_PATH . 'Tpl/success.php',
    'TMPL_EXCEPTION_FILE'   =>	APP_PATH . 'Tpl/phpfeid.php',
    'TMPL_PARSE_STRING' 	=>	array(
        '__PUBLIC__' 	=> 	'/Public',
        '__UPLOAD__' 	=> 	'/Uploads',
    ),
    'LOAD_EXT_CONFIG' 		=>	'database,domain,site,api,loan,contract,settings,keywords',
    'SHOW_PAGE_TRACE' 		=>	false,
    'TMPL_FILE_DEPR'		=>	'_',
    'TAGLIB_LOAD'			=>	true,
    'APP_AUTOLOAD_PATH'		=>	'@.TagLib',
    'TAGLIB_BUILD_IN'		=>	'Cx,Cvphp',
    //------------------------------------
    //分页部分设置
    //------------------------------------
    'PAGE_NUM_ONE'			=>	50,
//			'PAGE_STYLE'			=>	'<div class="pagination-info">共 %totalRow% %header%</div> %first% %upPage% %linkPage% %downPage% %end%',
    'PAGE_STYLE'			=>	'<div class="pagination-info">%totalRow% %header% %nowPage%/%totalPage% 页</div> %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%',
    'DATA_CACHE_TYPE' =>  'Redis',
    'REDIS_HOST' =>  '127.0.0.1',
    'REDIS_PORT' =>  6379,
    'REDIS_DB' =>  6,
    'redis'=>array(
        'host'=>'127.0.0.1',
        'port'=>6379,
        // 'pass'=>'',
        'db'=>6
    )

);
$settings=include_once __DIR__.'/settings.php';
return array_merge($config,$settings);

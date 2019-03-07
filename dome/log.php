<?php
use LSYS\Loger\Handler\Folder;
use LSYS\Loger;
use LSYS\Exception;
include __DIR__."/Bootstarp.php";
//reg error log
register_shutdown_function(function(){
	if ($error = error_get_last() AND in_array($error['type'], array(E_PARSE, E_ERROR, E_USER_ERROR))){
		\LSYS\Loger\DI::get()->loger()->addError($error['message']);
	}
});

//此段代码移动到bootstarp文件中
//文件方式记录日志
\LSYS\Loger\DI::get()->loger()->addHandler(new Folder(__DIR__."/logs",Loger::DEBUG));
//SeasLog 扩展方式记录日志,有条件的话建议使用此方式..
// \LSYS\Loger\DI::get()->loger()->addHandler(new LSYS\Loger\Handler\SeasLog(Loger::DEBUG));


//手动记录日志,测试开启以下代码
// \LSYS\Loger\DI::get()->loger()->addDebug("this is debug log");


class b{
	public function s(){
		throw new Exception("test log exception...");
	}
}
$b= new b();
$b->s(); 

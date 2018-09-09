#日志处理


//更多设置参阅 dome/log.php
//设置日志处理 handler
\LSYS\Loger\DI::get()->loger()->add_handler(new Folder(__DIR__."/logs"));

//出问题 ,记录日志
$log=\LSYS\Loger\DI::get()->loger();
$log->add_debug("hehe");
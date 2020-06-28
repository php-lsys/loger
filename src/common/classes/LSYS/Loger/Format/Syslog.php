<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Loger\Format;
use LSYS\Loger\Format;
use LSYS\Loger;
class Syslog implements Format{
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Format::format()
	 */
	public function format(array $message):string{
		$string="{$message['body']}";
		if ($message['exception'] instanceof \ErrorException){
			$string.="\n".$message['exception']->getTraceAsString();
		}else{
			foreach ($message['trace'] as $k=>$v){
				$msg="#{$k} ";
				if (isset($v['file'])){
					$msg.="{$v['file']}({$v['line']}) :";
				}else $msg.=":";
				if(isset($v['class'])){
					$msg.="{$v['class']}{$v['type']}{$v['function']}";
				}else if(isset($v['function'])){
					$msg.="{$v['function']}";
				}
				if (isset($v['args'])){
					$args=array();
					foreach ($v['args'] as $vv){
						if(is_object($vv)){
							$args[]=get_class($vv)." ...";
						}else if (is_array($vv)){
							$args[]="array ...";
						}else{
							$vv=strval($vv);
							if(strlen($vv)>512)$vv=substr($vv, 0,512)."...";
							$args[]=gettype($vv)." ".$vv;
						}
					}
					$msg.='('.implode(",", $args).')';
				}else $msg.="()";
				$context[]=$msg;
			}
			if (isset($context)) $string.="\n".implode("\n", $context);
		}
		return $string;
	}
}
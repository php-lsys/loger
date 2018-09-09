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
class Trace implements Format{
	//level string map
	protected $_str_levels = array(
		Loger::EMERGENCY	=> 'EMERGENCY',
		Loger::ALERT		=>'ALERT',
		Loger::CRITICAL		=> 'CRITICAL',
		Loger::ERROR		=> 'ERROR',
		Loger::WARNING		=> 'WARNING',
		Loger::NOTICE		=> 'NOTICE',
		Loger::INFO			=> 'INFO',
		Loger::DEBUG		=> 'DEBUG',
	);
	/**
	 * @return \DateTimeZone
	 */
	protected function _zone(){
		static $_zone;
		if ($_zone==null)$_zone=new \DateTimeZone( date_default_timezone_get());
		return $_zone;
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Format::format()
	 */
	public function format(array $message){
		$format = "time --- level: body in file:line";
		$datetime_str = '@'.$message['time'];
		$tz   = $this->_zone();
		$time = new \DateTime($datetime_str, $tz);
		if ($time->getTimeZone()->getName() !== $tz->getName())
		{
			$time->setTimeZone($tz);
		}
		$message['time'] = $time->format('Y-m-d H:i:s');
		$message['level'] = $this->_str_levels[$message['level']];
		$trace=$message['trace'];
		unset($message['trace']);
		$string = @strtr($format, $message);
		if ($message['exception'] instanceof \ErrorException){
			$string.="\n".$message['exception']->getTraceAsString();
		}else{
			foreach ($trace as $k=>$v){
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
		return $string.PHP_EOL;
	}
}
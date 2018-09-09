<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS;
use LSYS\Loger\Handler;
class Loger{
	/**
	 * Detailed debug information
	 */
	const DEBUG = 1;
	/**
	 * Interesting events
	 */
	const INFO = 2;
	/**
	 * Uncommon events
	 */
	const NOTICE = 3;
	
	/**
	 * Exceptional occurrences that are not errors
	 */
	const WARNING = 4;
	/**
	 * Runtime errors
	 */
	const ERROR = 5;
	/**
	 * Critical conditions
	 */
	const CRITICAL = 6;
	/**
	 * Action must be taken immediately
	 */
	const ALERT = 7;
	/**
	 * Urgent alert.
	 */
	const EMERGENCY = 8;
	/**
	 * @var Handler
	 */
	protected $_handler=array();
	/**
	 * @var bool
	 */
	protected $_batch=false;
	/**
	 * @var array
	 */
	protected $_records=array();
	/**
	 * add loger handler
	 * @param Handler $handler
	 * @return \LSYS\Loger
	 */
	public function add_handler(Handler $handler){
		foreach ($this->_handler as $v){
			if ($v===$handler)return $this;
		}
		$this->_handler[]=$handler;
		return $this;
	}
	/**
	 * clear loger headler
	 * @return \LSYS\Loger
	 */
	public function del_handler(Handler $handler){
		foreach ($this->_handler as $k=>$v){
			if ($v===$handler){
				unset($this->_handler[$k]);
				return $this;
			}
		}
		return $this;
	}
	/**
	 * clear all loger headler
	 * @return \LSYS\Loger
	 */
	public function clear_handler(){
		$this->_handler=array();
		return $this;
	}
	/**
	 * @return Handler[]
	 */
	public function get_handler(){
		return $this->_handler;
	}
	/**
	 * start batch log
	 * @return \LSYS\Loger
	 */
	public function batch_start(){
		if (count($this->_records)>0) $this->batch_end();
		$this->_batch=true;
		return $this;
	}
	/**
	 * end batch log
	 * @return \LSYS\Loger
	 */
	public function batch_end(){
		if (count($this->_records)>0){
			foreach ($this->_handler as $handler){
				$records=array();
				foreach ($this->_records as $record){
					if ($handler->get_level()>$record['level'])continue;
					$records[]=$record;
				}
				if (count($records)>0) $handler->handle_batch($records);
			}
			$this->_records=[];
		}
		$this->_batch=false;
		return $this;
	}
	/**
	 * add log to handler 
	 * @param int $level
	 * @param mixed $message
	 * @return void|\LSYS\Loger
	 */
	public function add($level,$message){
		if (count($this->_handler)==0) return;
		if ($message instanceof \Exception){
			$msg=$message->getMessage();
		}else $msg=strval($message);
		if ($message instanceof \ErrorException){
			$trace =$message->getTrace();
		}else if ($message instanceof \Exception){
			$trace=array(
				array(
					'file'=>$message->getFile(),
					'line'=>$message->getLine(),
				)	
			);
		}else{
			if ( ! defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
			{
				$trace = array_slice(debug_backtrace(FALSE), 1);
				foreach ($trace as &$v){
					unset($v['args']);
				}
			}
			else
			{
				$trace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1);
			}
		}
		$record = array
		(
			'time'       => time(),
			'level'      => $level,
			'body'       => $msg,
			'trace'      => $trace,
			'file'       => isset($trace[0]['file']) ? $trace[0]['file'] : NULL,
			'line'       => isset($trace[0]['line']) ? $trace[0]['line'] : NULL,
			'class'      => isset($trace[0]['class']) ? $trace[0]['class'] : NULL,
			'function'   => isset($trace[0]['function']) ? $trace[0]['function'] : NULL,
			'exception'  => $message instanceof \Exception?$message:null,
		);
	
		if ($this->_batch){
			$this->_records[]=$record;
			return $this;
		}
		
		foreach ($this->_handler as $handler){
			if ($handler->get_level()>$level){
				continue;
			}
			$handler->handle($record);
		}
		return $this;
	}
	/**
	 * Adds a log record at the DEBUG level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_debug($message){
		return $this->add(self::DEBUG,$message);
	}
	/**
	 * Adds a log record at the INFO level.
	 * @param  string  $message The log message
	 */
	public function add_info($message){
		return $this->add(self::INFO,$message);
	}
	/**
	 * Adds a log record at the NOTICE level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_notice($message){
		return $this->add(self::NOTICE,$message);
	}
	/**
	 * Adds a log record at the WARNING level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_warning($message){
		return $this->add(self::WARNING,$message);
	}
	/**
	 * Adds a log record at the ERROR level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_error($message){
		return $this->add(self::ERROR,$message);
	}
	/**
	 * Adds a log record at the CRITICAL level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_critical($message){
		return $this->add(self::CRITICAL,$message);
	}
	/**
	 * Adds a log record at the ALERT level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_alert($message){
		return $this->add(self::ALERT,$message);
	}
	/**
	 * Adds a log record at the EMERGENCY level.
	 *
	 * @param  string  $message The log message
	 */
	public function add_emergency($message){
		return $this->add(self::EMERGENCY,$message);
	}
	
	/**
	 * destruct
	 */
	public function __destruct(){
	    if (count($this->_records)>0)$this->batch_end();
	    $this->_records=[];
	    $this->_batch=false;
	}
}
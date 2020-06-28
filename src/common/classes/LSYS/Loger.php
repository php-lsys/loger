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
	public function addHandler(Handler $handler){
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
	public function delHandler(Handler $handler){
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
	public function clearHandler(){
		$this->_handler=array();
		return $this;
	}
	/**
	 * @return Handler[]
	 */
	public function getHandler(){
		return $this->_handler;
	}
	/**
	 * start batch log
	 * @return \LSYS\Loger
	 */
	public function batchStart(){
		if (count($this->_records)>0) $this->batchEnd();
		$this->_batch=true;
		return $this;
	}
	/**
	 * end batch log
	 * @return \LSYS\Loger
	 */
	public function batchEnd(){
		if (count($this->_records)>0){
			foreach ($this->_handler as $handler){
				$records=array();
				foreach ($this->_records as $record){
					if ($handler->getLevel()>$record['level'])continue;
					$records[]=$record;
				}
				if (count($records)>0) $handler->handleBatch($records);
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
	public function add(int $level,$message){
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
			if ($handler->getLevel()>$level){
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
	public function addDebug($message){
		return $this->add(self::DEBUG,$message);
	}
	/**
	 * Adds a log record at the INFO level.
	 * @param  string  $message The log message
	 */
	public function addInfo($message){
		return $this->add(self::INFO,$message);
	}
	/**
	 * Adds a log record at the NOTICE level.
	 *
	 * @param  string  $message The log message
	 */
	public function addNotice($message){
		return $this->add(self::NOTICE,$message);
	}
	/**
	 * Adds a log record at the WARNING level.
	 *
	 * @param  string  $message The log message
	 */
	public function addWarning($message){
		return $this->add(self::WARNING,$message);
	}
	/**
	 * Adds a log record at the ERROR level.
	 *
	 * @param  string  $message The log message
	 */
	public function addError($message){
		return $this->add(self::ERROR,$message);
	}
	/**
	 * Adds a log record at the CRITICAL level.
	 *
	 * @param  string  $message The log message
	 */
	public function addCritical($message){
		return $this->add(self::CRITICAL,$message);
	}
	/**
	 * Adds a log record at the ALERT level.
	 *
	 * @param  string  $message The log message
	 */
	public function addAlert($message){
		return $this->add(self::ALERT,$message);
	}
	/**
	 * Adds a log record at the EMERGENCY level.
	 *
	 * @param  string  $message The log message
	 */
	public function addEmergency($message){
		return $this->add(self::EMERGENCY,$message);
	}
	
	/**
	 * destruct
	 */
	public function __destruct(){
	    if (count($this->_records)>0)$this->batchEnd();
	    $this->_records=[];
	    $this->_batch=false;
	}
}
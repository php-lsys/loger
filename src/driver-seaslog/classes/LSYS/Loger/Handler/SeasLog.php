<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Loger\Handler;
use LSYS\Loger\Handler;
use LSYS\Exception;
use LSYS\Loger;
use LSYS\Loger\Format;
use LSYS\Loger\Format\Trace;
class SeasLog implements Handler
{
	protected $_level;
	protected $_format;
	public function __construct($level=Loger::DEBUG){
		$this->_level=$level;
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Handler::getLevel()
	 */
	public function getLevel():int{
    	return $this->_level;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::setLevel()
     */
    public function setLevel($level){
    	$this->level = $level;
    	return $this;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::handle()
     */
	public function handle(array $record){
		$message=$this->getFormat()->format($record);
		switch ($record['level']){
			case Loger::DEBUG: \SeasLog::debug($message);break;
			case Loger::NOTICE: \SeasLog::notice($message);break;
			case Loger::WARNING: \SeasLog::warning($message);break;
			case Loger::INFO: \SeasLog::info($message);break;
			case Loger::ERROR: \SeasLog::error($message);break;
			case Loger::CRITICAL: \SeasLog::critical($message);break;
			case Loger::ALERT: \SeasLog::alert($message);break;
			case Loger::EMERGENCY: \SeasLog::emergency($message);break;
		}
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Handler::handleBatch()
	 */
	public function handleBatch(array $records){
		foreach ($records as $record) $this->handle($record);
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Handler::setFormat()
	 */
 	public function setFormat(Format $formatter){
    	$this->_format=$formatter;
    	return $this;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::getFormat()
     */
    public function getFormat(){
    	if ($this->_format==null) $this->_format= new Trace();
    	return $this->_format;
    }
}

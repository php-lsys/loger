<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Loger\Handler;
use LSYS\Loger\Handler;
use LSYS\Loger;
use LSYS\Loger\Format;
use LSYS\Loger\Format\Trace;
class Stdout implements Handler
{
    //handler level
    protected $_level;
    /**
     * @var Format
     */
    protected $_format;
    protected $_file;
    protected $_time;
    /**
     * @param string   $folder		   log folder
     * @param int      $level          The minimum logging level at which this handler will be triggered
     * @param Boolean  $bubble         Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($level = Loger::DEBUG)
    {
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
    	$msg=$this->getFormat()->format($record);
    	print $msg;
    	flush();
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::handleBatch()
     */
    public function handleBatch(array $records){
    	$msg='';
    	$format=$this->getFormat();
    	foreach ($records as $record){
    		$msg.=$format->format($record);
    	}
    	print $msg;
    	flush();
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
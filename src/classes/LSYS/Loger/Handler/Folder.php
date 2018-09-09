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
use LSYS\Exception;
class Folder implements Handler
{
	//save log dir
    protected $_folder;
    //handler level
    protected $_level;
    //save file ext
    protected $_ext;
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
    public function __construct($folder,$level = Loger::DEBUG,$ext='.log')
    {
        $this->_folder = rtrim($folder,'/\\').'/';
        $this->_level=$level;
        $this->_ext=$ext;
    }
    /**
     * Return the currently active stream if it is open
     *
     * @return resource|null
     */
    private function _file()
    {
    	if ($this->_file&&$this->_time>=strtotime("today")) return $this->_file;
    	// may be more to : __construct ??
    	if (!is_writable($this->_folder)) throw new Exception("can't be write to :".$this->_folder);
    	// Set the yearly directory name
    	$directory = $this->_folder.date('Ym');
    	
    	if ( ! is_dir($directory))
    	{
    		// Create the yearly directory
    		mkdir($directory, 02777);
    		
    		// Set permissions (must be manually set to fix umask issues)
    		chmod($directory, 02777);
    	}
    	// Set the name of the log file
    	$filename = $directory.DIRECTORY_SEPARATOR.date('d').$this->_ext;
		$this->_file=$filename;
		$this->_time=time();
    	return $filename;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::get_level()
     */
    public function get_level(){
    	return $this->_level;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::set_level()
     */
    public function set_level($level){
    	$this->level = $level;
    	return $this;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::handle()
     */
    public function handle(array $record){
    	$msg=$this->get_format()->format($record);
    	$filename=$this->_file();
    	return file_put_contents($filename, $msg, FILE_APPEND);
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::handle_batch()
     */
    public function handle_batch(array $records){
    	$filename=$this->_file();
    	$msg='';
    	$format=$this->get_format();
    	foreach ($records as $record){
    		$msg.=$format->format($record);
    	}
    	return file_put_contents($this->_file(), $msg, FILE_APPEND);
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::set_format()
     */
    public function set_format(Format $formatter){
    	$this->_format=$formatter;
    	return $this;
    }
    /**
     * {@inheritDoc}
     * @see \LSYS\Loger\Handler::get_format()
     */
    public function get_format(){
    	if ($this->_format==null) $this->_format= new Trace();
    	return $this->_format;
    }
}
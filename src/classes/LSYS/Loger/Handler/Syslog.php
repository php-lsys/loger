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
use LSYS\Exception;
class Syslog implements Handler
{
	/**
	 * Translates Monolog log levels to syslog log priorities.
	 */
	protected $logLevels = array(
			Loger::DEBUG     => LOG_DEBUG,
			Loger::INFO      => LOG_INFO,
			Loger::NOTICE    => LOG_NOTICE,
			Loger::WARNING   => LOG_WARNING,
			Loger::ERROR     => LOG_ERR,
			Loger::CRITICAL  => LOG_CRIT,
			Loger::ALERT     => LOG_ALERT,
			Loger::EMERGENCY => LOG_EMERG,
	);
	
	/**
	 * List of valid log facility names.
	 */
	protected $facilities = array(
			'auth'     => LOG_AUTH,
			'authpriv' => LOG_AUTHPRIV,
			'cron'     => LOG_CRON,
			'daemon'   => LOG_DAEMON,
			'kern'     => LOG_KERN,
			'lpr'      => LOG_LPR,
			'mail'     => LOG_MAIL,
			'news'     => LOG_NEWS,
			'syslog'   => LOG_SYSLOG,
			'user'     => LOG_USER,
			'uucp'     => LOG_UUCP,
	);
	protected $facility;
	protected $_level;
	protected $_bubble;
	protected $_format;
	protected $ident;
	protected $logopts;
	protected $_is_open=false;
	
	/**
	 * @param string  $ident
	 * @param mixed   $facility
	 * @param int     $level    The minimum logging level at which this handler will be triggered
	 * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not
	 * @param int     $logopts  Option flags for the openlog() call, defaults to LOG_PID
	 */
	public function __construct($ident, $level=Loger::DEBUG, $facility = LOG_USER, $bubble = true, $logopts = LOG_PID)
	{
		if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
			$this->facilities['local0'] = LOG_LOCAL0;
			$this->facilities['local1'] = LOG_LOCAL1;
			$this->facilities['local2'] = LOG_LOCAL2;
			$this->facilities['local3'] = LOG_LOCAL3;
			$this->facilities['local4'] = LOG_LOCAL4;
			$this->facilities['local5'] = LOG_LOCAL5;
			$this->facilities['local6'] = LOG_LOCAL6;
			$this->facilities['local7'] = LOG_LOCAL7;
		} else {
			$this->facilities['local0'] = 128; // LOG_LOCAL0
			$this->facilities['local1'] = 136; // LOG_LOCAL1
			$this->facilities['local2'] = 144; // LOG_LOCAL2
			$this->facilities['local3'] = 152; // LOG_LOCAL3
			$this->facilities['local4'] = 160; // LOG_LOCAL4
			$this->facilities['local5'] = 168; // LOG_LOCAL5
			$this->facilities['local6'] = 176; // LOG_LOCAL6
			$this->facilities['local7'] = 184; // LOG_LOCAL7
		}
		// convert textual description of facility to syslog constant
		if (array_key_exists(strtolower($facility), $this->facilities)) {
			$facility = $this->facilities[strtolower($facility)];
		} elseif (!in_array($facility, array_values($this->facilities), true)) {
			throw new \UnexpectedValueException('Unknown facility value "'.$facility.'" given');
		}
		$this->_level=$level;
		$this->_bubble=$bubble;
		$this->facility = $facility;
		$this->ident = $ident;
		$this->logopts = $logopts;
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
	protected function _openlog(){
		if (openlog($this->ident, $this->logopts, $this->facility))return;
		throw new Exception('Can\'t open syslog for ident "'.$this->ident.'" and facility "'.$this->facility.'"');
	}	
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Handler::handle()
	 */
	public function handle(array $record){
		$this->_openlog();
		$message=$this->get_format()->format($record);
		syslog($this->logLevels[$record['level']],$message);
		closelog();
		return $this;
	}
	/**
	 * {@inheritDoc}
	 * @see \LSYS\Loger\Handler::handle_batch()
	 */
	public function handle_batch(array $records){
		$this->_openlog();
		foreach ($records as $record) {
			$message=$this->get_format()->format($record);
			syslog($this->logLevels[$record['level']],$message);
		}
		closelog();
		return $this;
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
		if ($this->_format==null) $this->_format= new \LSYS\Loger\Format\Syslog();
		return $this->_format;
	}
}
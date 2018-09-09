<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Loger;
interface Handler{
	/**
	 * get filter level
	 */
	public function get_level();
	/**
	 * Sets minimum logging level at which this handler will be triggered.
	 *
	 * @param  int
	 * @return self
	 */
	public function set_level($level);
	/**
	 * Adds a log record at the DEBUG level.
	 * $record:
	 *  'time'       => 
	 *	'level'      => 
	 *	'body'       => 
	 *	'trace'      => 
	 *	'file'       => 
	 *	'line'       => 
	 *	'class'      => 
	 *	'function'   =>
	 *  'exception'  =>
	 * @param  string  $message The log message
	 */
	public function handle(array $record);
	/**
	 * Adds a log records at the DEBUG level.
	 * @param array $record
	 */
	public function handle_batch(array $records);
	/**
	 * Sets the formatter.
	 *
	 * @param  Format $formatter
	 * @return self
	 */
	public function set_format(Format $formatter);
	
	/**
	 * Gets the formatter.
	 *
	 * @return Format
	 */
	public function get_format();
	
}
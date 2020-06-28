<?php
/**
 * lsys core
 * @author     Lonely <shan.liu@msn.com>
 * @copyright  (c) 2017 Lonely <shan.liu@msn.com>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
namespace LSYS\Loger;
interface Format
{
	/**
	 * format log to string
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
	 * @param array $record
	 */
	public function format(array $record):string;
}

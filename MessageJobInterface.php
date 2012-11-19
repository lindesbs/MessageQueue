<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

abstract class MessageJobInterface extends Controller
{
	
	protected static $objInstance;



	public function __construct()
	{
		$this->import("Environment");
		$this->Environment->httpUserAgent = 'MessageQueue';	
	}
	
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new MessageQueue();
		}

		return self::$objInstance;
	}

	public function runJob($objData,$jobData)
	{
		$this->log(__METHOD__,__FUNCTION__,TL_INFO);
	}
	
	public function finalizeJob($objData,$jobData)
	{
		$this->log(__METHOD__,__FUNCTION__,TL_INFO);
	}
	
	public function finishedAllJobs($objGroup)	
	{
		$this->log(__METHOD__,__FUNCTION__,TL_INFO);
	}

	public function exceptionHandler($objData,$jobData,$strOutput,$strFunc)
    {
		$this->log(__METHOD__,__FUNCTION__,TL_INFO);
		$this->log($strFunc,$strOutput,TL_INFO);
	}

}

?>
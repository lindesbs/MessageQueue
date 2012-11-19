<?php

define('TL_MODE', 'FE');
require_once('../../../system/initialize.php');

class MessageQueueCron extends Frontend
{

	/**
	 * Initialize object (do not remove)
	 */
	public function __construct()
	{
		parent::__construct();

		$this -> initTime();
	}

	/**
	 * Run the controller
	 */
	public function run()
	{
		
		$this -> import("Database");

		$bNoJobs = false;

		while (($this -> stillOnTime()) && (!$bNoJobs))
		{
			$objQueue = $this -> Database -> prepare("SELECT * FROM tl_messagequeue WHERE status=?") -> limit(1) -> executeUncached(MESSAGEJOB_NEW);

			if ($objQueue -> numRows == 1)
			{
				$objResult = $objQueue -> fetchAssoc();

				$arrUpdate = array(
					'tstamp' => time(),
					'status' => MESSAGEJOB_WORKING,
				);

				$objQueueUpdateWorking = $this->Database->prepare("UPDATE tl_messagequeue %s WHERE id=?")->set($arrUpdate)->executeUncached($objResult['id']);


				ob_start();
				$objClass = new $objResult['objClass'];

				$startTime = microtime(true);



				try
				{
					
					$return = $objClass -> runJob(deserialize($objResult['objData']), $objResult);
				}
				catch (Exception $e)
				{
					$strBuffer = ob_get_contents();
					
					$objClass -> exceptionHandler(deserialize($objResult['objData']), $objResult, $e -> getMessage().'<br>'.$strBuffer, 'runJob');
					
					ob_end_clean();
				}

				
				ob_end_clean();
				
				
				$endTime = microtime(true);

				$arrUpdate = array(
					'tstamp' => time(),
					'status' => MESSAGEJOB_FINISHED,
					'objDuration' => $endTime - $startTime
				);

					$objQueueUpdateFinished = $this->Database->prepare("UPDATE tl_messagequeue %s WHERE id=?")->set($arrUpdate)->executeUncached($objQueue->id);

				$objNewQueue = $this -> Database -> prepare("SELECT * FROM tl_messagequeue WHERE id=?") -> limit(1) -> executeUncached($objQueue -> id);

				try
				{
					$return = $objClass -> finalizeJob($objResult['objData'], $objNewQueue -> fetchAssoc());
				}
				catch (Exception $e)
				{
					$objClass -> exceptionHandler(deserialize($objResult['objData']), $objResult, $e -> getMessage(), 'finalizeJob');
				}

			}
			else
			{
				$bNoJobs = true;
			}
		}

		$objFinishedJobs = $this -> Database -> prepare("SELECT COUNT(id),objClass,objGroup,status FROM `tl_messagequeue` GROUP BY objGroup,status") -> executeUncached();
		$arrFinalJobs = array();

		while ($objFinishedJobs -> next())
		{
			$arrFinalJobs[$objFinishedJobs -> objGroup][$objFinishedJobs -> objClass][$objFinishedJobs -> status] = true;
		}

		foreach ($arrFinalJobs as $keyGroup => $valueGroup)
		{
			foreach ($valueGroup as $keyClass => $valueStatus)
			{
				if ((count($valueStatus) == 1) && (array_key_exists(MESSAGEJOB_FINISHED, $valueStatus)))
				{
					$objClass = new $keyClass();

					try
					{
						$return = $objClass -> finishedAllJobs($keyGroup);
					}
					catch (Exception $e)
					{
						$objClass -> exceptionHandler(deserialize($objResult['objData']), $objResult, $e -> getMessage(), 'finishedAllJobs');
					}

					

					$objCleanFinishedJobs = $this -> Database -> prepare("DELETE FROM `tl_messagequeue` WHERE objGroup=?") -> executeUncached($keyGroup);

				}
			}

		}
		
		
				
					
	}

	protected function initTime()
	{
		$this -> maxtime = (function_exists('ini_get')) ? (int)@ini_get('max_execution_time') : (int)@get_cfg_var('max_execution_time');

		if ($this -> maxtime === 0)
			$this -> maxtime = 50;
		$this -> startTime = microtime(true);
	}

	protected function stillOnTime($timeNeeded = 5)
	{
		return (ceil(microtime(true) - ($this -> startTime - $timeNeeded)) < $this -> maxtime) ? true : false;
	}

}

/**
 * Instantiate controller
 */
 
 
$objMessageQueueCron = new MessageQueueCron();
$objMessageQueueCron -> run();
?>
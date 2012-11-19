<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

class MQSample extends  MessageJobInterface
{

	public function runJob($objData,$jobData)
	{
		usleep(2);
		
	}
	
	public function finalizeJob($objData,$jobData)
	{
		$this->log("finalizeJob ".$jobData['id'],$jobData['objGroup'],TL_INFO);
		
	}
	
	public function finishedAllJobs($objGroup)
	{
		$this->log("FINISHED ".$objGroup,$objGroup,TL_INFO);
		
	}
	
}

?>
<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

class MessageQueue extends Controller
{
/**
	 * Current object instance (Singleton)
	 * @var object
	 */
	protected static $objInstance;

	public function __construct()
	{
	}
	
	
	public function __destruct()
	{
		
	}

	final private function __clone() {}


	/**
	 * Return the current object instance (Singleton)
	 * @return object
	 */
	public static function getInstance()
	{
		if (!is_object(self::$objInstance))
		{
			self::$objInstance = new MessageQueue();
		}

		return self::$objInstance;
	}

	public function runOnce()
	{
		
	}
	
	public function add($objClass,$objData,$objGroup = 'MessageQueue')
	{
		$this->import("Database");
				
		$objTableExists = $this->Database->prepare("SHOW TABLES LIKE 'tl_messagequeue'")->executeUncached();
		
		if ($objTableExists->numRows!=0)
		{

			$arrData = array(
				'tstamp' => time(),
				'objClass'	=> $objClass,
				'objData'	=> $objData,
				'objGroup'	=> $objGroup,
				'status'	=> MESSAGEJOB_NEW
			);
			
			$objQueue = $this->Database->prepare("INSERT INTO tl_messagequeue %s")->set($arrData)->executeUncached();
		}
		
	}
	
	
}

?>
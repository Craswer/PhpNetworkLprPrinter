<?php
/*
 * Class PhpLprPrinter
 * Print your files via PHP with LPR network printer
 * (C) Copyright 2011 Pedro Villena <craswer@gmail.com>
 * Licensed under the GNU GPL v3 license. See file COPYRIGHT for details. 
 */
 
class PhpNetworkLprPrinter{

	/**
	 * Printer's host. Initialize by constructor
	 * 
	 * @var 	string
	 * @access 	protected
	 * @since	1.0
	 */
	var $_host;
	
	/**
	 * Printer's Port. Default port 515 (see constructor), but it can change with the function setPort
	 * 
	 * @var 	integer
	 * @access 	protected
	 * @since	1.0
	 */
	var $_port;
	
	/**
	 * Max seconds to connect to the printer. Default 20, but it can change with the function setTimeOut
	 * 
	 * @var 	integer
	 * @access 	protected
	 * @since	1.0
	 */
	var $_timeout = 30;
	
	/**
	 * Error number if connection fails
	 * 
	 * @var 	integer
	 * @access 	protected
	 * @since	1.0
	 */
	private $_error_number;
	
	/**
	 * Error message if connection fails
	 * 
	 * @var 	integer
	 * @access 	protected
	 * @since	1.0
	 */
	private $_error_msg;
	
	/**
	 * Debug message
	 * 
	 * @var 	string
	 * @access 	protected
	 * @since	1.0
	 */
	private $_debug = array();
	
	/**
	 * Class constructor.
	 *
	 * @param	string	The printer's host
	 * @param	integer	The printer's port
	 * @since	1.0
	 */
	public function __construct($host, $port=515) {
		$this->_host = $host;
		$this->_port = $port;
	}
   
	/**
	 * Sets a message in the array $_debug
	 *
	 * @access	public
	 * @param	string 	$message 	Message
	 * @param	string  $type	 	Message's type, for example "message" or "error"
	 * @since	1.0
 	 */
	private function setMessage($message="", $type="message"){
		$this->_debug[]=array("message"=>$message, "time"=>time(), "type"=>$type);
	}
   
	/**
	 * Sets an error message in the array $_debug
	 *
	 * @access	public
	 * @param	string 	$error 	Error message
	 * @since	1.0
 	 */
	private function setError($error=""){
		$this->_debug[]=array("message"=>$error, "time"=>time(), "type"=>"error");
		$this->_error_msg=$error;
	}
   
	/**
	 * Sets the port
	 *
	 * @access	public
	 * @param	integer	$port 	Printer's port
	 * @since	1.0
 	 */
	public function setPort($port){
		$this->_port = $port;
		$this->setMessage("Setting port: ".$this->_port);
	}

	/**
	 * Sets the time out in seconds
	 *
	 * @access	public
	 * @param	integer	$timeout Timeout in seconds
	 * @since	1.0
 	 */	
	public function setTimeOut($timeout){
		$this->_timeout = $timeout;
		$this->setMessage("Setting time out: ".$this->_timeout);
	}
	
	/**
	 * Gets the error number
	 *
	 * @access	public
	 * @return	integer	Error number
	 * @since	1.0
 	 */ 
	public function getErrNo(){
		return $this->_errNo;
	}

	/**
	 * Gets the error message
	 *
	 * @access	public
	 * @return	string	Error message
	 * @since	1.0
 	 */	 
	public function getErrStr(){
		return $this->_errStr;
	}	

	/**
	 * Gets the debug message
	 *
	 * @access	public
	 * @return	array	Debug message array
	 * @since	1.0
 	 */	 
	public function getDebug(){
		return $this->_debug;
	}	
   
	/**
	 * Connect to printer
	 *
	 * @access	private
	 * @return	socket	Connection
	 * @since	1.0
 	 */  
	private function connect(){
		$this->setMessage("Connecting... Host: ".$this->_host.", Port: ".$this->_port);
		return stream_socket_client("tcp://".$this->_host.":".$this->_port, $this->_error_number, $this->_error_msg, $this->_timeout);
	}
   
	/**
	 * Makes de cfA (control string)
	 *
	 * @access	private
	 * @return	string	cfA control String
	 * @since	1.0
 	 */  
	private function makecfA($jobid, $user){
		$this->setMessage("Setting cfA control String");
			
		$hostname = $_SERVER['REMOTE_ADDR'];
		$cfa  = "";
		$cfa .= "H" . $hostname . "\n"; //hostname
		$cfa .= "P" . $user . "\n"; //user
		$cfA .= "fdfA" + $jobid + $hostname + "\n";
		//TODO: Add more parameters. See http://www.faqs.org/rfcs/rfc1179.html 
		
		return $cfa;
	}
   
	/**
	 * Print a text message on network lpr printer
	 *
	 * @access	public
	 * @param	string 	$text 	The name of the property
	 * @return	boolean	True if success
	 * @since	1.0
 	 */  
	public function printText($text=""){

		//Connecting to the network printer
		$connection = $this->connect();

		//If fail, exit with false 
		if(!$connection){
			return false;
		}else{
			
			$queue="defaultQueue"; //TODO: Change default queue
			$jobid=001; //TODO: Autoincrement $jobid
			
			//Starting printer
			fwrite($connection, chr(2).$queue."\n");
				$this->setMessage("Starting printer...");
			
				//Checking errors
				if (ord(fread($connection, 1)) != 0) {
					$this->setError("Error while start printing on queue");
					return false;
				}
				
			//Write control file		
			$user="PhpNetworkLprPrinter";	
			$ctrl = $this->makecfA($jobid, $user);
			fwrite($connection, chr(2).strlen($ctrl)." cfA".$jobid.$user."\n");
			
				$this->setMessage("Sending control file...");
			
				//Checking errors
				if (ord(fread($connection, 1)) != 0) {
					$this->setError("Error while start sending control file");
					return false;
				}
				
				fwrite($connection, $ctrl.chr(0));
				//Checking errors
				if (ord(fread($connection, 1)) != 0) {
					$this->setError("Error while sending control file");
					return false;
				}
			
				
			//Send data string
			fwrite($connection, chr(3).strlen($text)." dfA".$jobid."\n");   
				$this->setMessage("Sending data...");
			
				//Checking errors
				if (ord(fread($connection, 1)) != 0) {
					$this->setError("Error while sending control file");
					return false;
				}
				
				
				fwrite($connection, $text.chr(0));
				//Checking errors
				if (ord(fread($connection, 1)) != 0) {
					$this->setError("Error while sending control file");
					return false;
				}else{
					$this->setMessage("Data received!!!");
				}
			
		}   


	}
	
}

?>

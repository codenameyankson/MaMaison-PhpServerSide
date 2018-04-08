<?php 
/**
 * 7/04/2018
 * @author : Kingsley Yankson
 * MaMaison 
 */


require("database/databaseconnection.php");

class dbScript extends DatabaseConnection {

private $dbcon= null;

/**
 * A function to performs the connection to the MaMaison database
 * @return boolean true if successful
 */
private function getInstance(){
//create new connection if its doesn't exist already
        if ($this->dbcon == null) {
            $this->dbcon = mysqli_connect('localhost','root','king','MaMaison');
        }
        //exit if database connection fails
        if ($this->dbcon->connect_error) {
            die("The connection to the database failed");
        }
        return true; //return connection for use

}
/**
 * this function retireves the deviceId from the given device name 
 * @param $deviceName
 * @return string of the results if successful and false if device name doesnt exist 
 */

public function getDeviceId($deviceName){

	if ($this->dbcon == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }
		$sql = " SELECT `DeviceId` FROM `Devices` WHERE `DeviceName` = '{$deviceName}' " ; 
		
		$execute = $this->dbcon->query($sql);

		var_dump($execute->num_rows);
		$res = $execute->fetch_assoc();
		
    if ($res)
    	{
    	$res['DeviceId'];
        return $res['DeviceId'];
	    } 
		 return false;
		}


/**
 * A function to perform the generation of a deviceId  given a deviceName
 * @param $deviceName
 * @return string of the results if successful and false if device name doesnt exist 
 */
public function genDeviceId ($deviceName){
	if ($this->dbcon == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }

		$sql = " SELECT `DeviceId` FROM `Devices` WHERE `DeviceName` = '{$deviceName}' " ; 
		//$sql = " SELECT * FROM  Devices ";
		$execute = $this->dbcon->query($sql);

		if (($execute->num_rows) < 1){

	$dID = "MaMaison/".$deviceName;

	return $dID;

	}else{

		return false;
	}

}


/**
 * A function to perform the addittion of a device to the db
 * @param $DeviceName : string ,$weeklyT : int,$monthlyT: int
 * @return true if successful and false if device name already exist 
 */
public function addDevice($DeviceName,$weeklyT,$monthlyT){
	if ($this->dbcon == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }

	$sql = "INSERT INTO `Devices`(`DeviceId`,`DeviceName`, `WeeklyThreashold`, `MonthlyThreshold`) VALUES (?,?,?,?)";
	
	$params = array($this->genDeviceId($DeviceName),$DeviceName,$weeklyT,$monthlyT);
	
	$dbexec = $this->prepareSql($sql,"ssii", $params); 

	if($dbexec == true){
        
        return true; 
      } 
        else{
        echo "Addition Failed/ device may already exist";
        return false;
      }
}

/**
 * A function to perform the recording of total consumption 
 * @param date: String (dd/mm/yyyy),$time : String (15:00),$coonsumption : int
 * @return true if successful and false if addittion was made
 */
public function recordTotalConsumption($date,$time,$coonsumption){
	if ($this->dbcon == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }
        $sql = "INSERT INTO `TotalConsumption`(`Date`, `Time`, `Consumption`) VALUES (?,?,?)";

        $params = array($date,$time,$coonsumption);

        $dbexec = $this->prepareSql($sql,"ssi", $params); 

		if($dbexec == true){
	        
	        return true; 
	      } 
	        else{
	        echo "Failed to record consumption";
	        return false;
	      }


}

/**
 * A function to perform the recording of total consumption 
 * @param deviceId , event , date: String (dd/mm/yyyy),$time : String (15:00),$coonsumption : int
 * @return true if successful and false if addittion was made
 */
public function logAppliance($deviceId,$event,$date,$time){
	if ($this->dbcon == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }
        $sql = "INSERT INTO `Appliance_Logsheet`(`DeviceId`,`Event`,`Time`,`Date`) VALUES (?,?,?,?)";

        $params = array($deviceId,$event,$time,$date);

        $dbexec = $this->prepareSql($sql,"ssss", $params); 

		if($dbexec == true){
	        
	        return true; 
	      } 
	        else{
	        echo "Failed to log";
	        return false;
	      }


}


/**
 * A function to perform the recording of individual appliance consumption 
 * @param consumption : int,  date: String (dd/mm/yyyy),$time : String (15:00) deviceId 
 * @return true if successful and false if addittion was made
 */
public function recordApplianceConsumption($consumption,$date,$time,$deviceId){
	if ($this->dbcon == null) {
            $this->getInstance(); //if connection has not been created, create one
            //perform connection
        }
       $sql =  "INSERT INTO `ApplianceConsumption`(`Consumption`, `Date`, `Time`, `Entry`, `DeviceId`) VALUES (?,?,?,?,?)";

        $params = array($consumption,$date,$time,0,$deviceId);

        $dbexec = $this->prepareSql($sql,"issis", $params); 

		if($dbexec == true){
	        
	        return true; 
	      } 
	        else{
	        echo "Failed to log appliance consumption";
	        return false;
	      }


}







}
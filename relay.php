<?
/**
 * 7/04/2018
 * @author : Kingsley Yankson
 * MaMaison 
 */

require("phpMQTT.php");
require("database/dbScript.php");


$server = "test.mosquitto.org";     // change if necessary
$port = 1883;                     // change if necessary
$username = "";                   // set your username
$password = "";                   // set your password
$client_id = "phpMQTT-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()
global $dbcon;

$GLOBALS['$dbcon'] = new dbScript();

	 $topic1['King/MaMaison/'] = array("qos" => 0, "function" => "onMessage");
	 $topic2['King/MaMaison/light'] = array("qos" => 0, "function" => "onMessage");
	 $topic3['King/MaMaison/switch1'] = array("qos" => 0, "function" => "onMessage");
	 $topic4['King/MaMaison/switch2'] = array("qos" => 0, "function" => "onMessage");
	 $topic5['King/MaMaison/mains'] = array("qos" => 0, "function" => "onMessage");
	 $topic6['King/MaMaison/light/event'] = array("qos" => 0, "function" => "onMessage");
	 $topic7['King/MaMaison/switch1/event'] = array("qos" => 0, "function" => "onMessage");
	 $topic8['King/MaMaison/switch2/event'] = array("qos" => 0, "function" => "onMessage");
	 $topic9['King/MaMaison/TT_consumption'] = array("qos" => 0, "function" => "onMessage");


$mqtt = new phpMQTT($server, $port, $client_id);

	if ($mqtt->connect(true, NULL, $username, $password))
		{
				$mqtt->subscribe($topic1,0);
				$mqtt->subscribe($topic2,0);
				$mqtt->subscribe($topic3,0);
				$mqtt->subscribe($topic4,0);
				$mqtt->subscribe($topic5,0);
				$mqtt->subscribe($topic6,0);
				$mqtt->subscribe($topic7,0);
				$mqtt->subscribe($topic8,0);
				$mqtt->subscribe($topic9,0);

				$mqtt->publish("King/MaMaison/","Connected and Ready");
				$mqtt->publish("King/MaMaison/light","lights turnned on");
		}
	else 
		{
	    echo "Time out!\n";

		}

   
function record($topic,$message)
	{
		$dbcon = $GLOBALS['$dbcon'];
		if ($topic == "King/MaMaison/light")
			{	
				$msg = (int)$message;
				$deviceId= $dbcon -> getDeviceId("Lamp"); 
				$dbcon -> recordApplianceConsumption($msg,date("d-m-Y"),date("H:i"),$deviceId);
			}
		elseif ($topic == "King/MaMaison/switch1")
			{
				$msg = (int)$message;
				$deviceId= $dbcon -> getDeviceId("Switch1"); 
				$dbcon -> recordApplianceConsumption($msg,date("d-m-Y"),date("H:i"),$deviceId);
			}
		elseif($topic == "King/MaMaison/switch2")
			{	
				$msg = (int)$message;
				$deviceId= $dbcon -> getDeviceId("Switch2"); 
				$dbcon -> recordApplianceConsumption($msg,date("d-m-Y"),date("H:i"),$deviceId);		
			}
		elseif($topic == "King/MaMaison/light/event") 
			{
				$deviceId= $dbcon -> getDeviceId("Lamp"); 
				$dbcon -> logAppliance($deviceId,$message,date("d-m-Y"),date("H:i"));
			}
		elseif($topic == "King/MaMaison/switch1/event") 
			{
				$deviceId= $dbcon -> getDeviceId("Switch1"); 
				$dbcon -> logAppliance($deviceId,$message,date("d-m-Y"),date("H:i"));
			}
		elseif($topic == "King/MaMaison/switch2/event") 
			{
				$deviceId= $dbcon -> getDeviceId("Switch2"); 
				$dbcon -> logAppliance($deviceId,$message,date("d-m-Y"),date("H:i"));
			}
		elseif($topic == "King/MaMaison/TT_consumption") 
			{
				$msg = (int)$message;
				$dbcon ->recordTotalConsumption(date("d-m-Y"),date("H:i"),$msg);
			}
		else
			{

			}	
	}




function onMessage($topic, $msg)
	{
		record($topic,$msg);
		echo "Message Recieved: " . date("d-m-Y") ."  ".date("H:i"). "\n";
		echo "Topic: {$topic}\n\n";
		echo "\t$msg\n\n";
	}


 while($mqtt->proc())
		{

		 
		}
$mqtt->close();

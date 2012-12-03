<?php
session_start();
ob_start();

$_SESSION['addressInput'] = $_REQUEST['zip'];
$host = $_SERVER['HTTP_HOST'];

function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
} 

function getObjVars($xmlObject, $name) {
    if(empty($xmlObject[$name])) {
        return;
    }
    if(is_string($xmlObject[$name])) {
        return $xmlObject[$name];
    }
    $rtn = get_object_vars($xmlObject[$name]);
    return $rtn;
}

// Get parameters from URL
if(isset($_GET["lat"])) { $center_lat = $_GET["lat"]; }
if(isset($_GET["lng"])) { $center_lng = $_GET["lng"]; }
if(isset($_GET["radius"])) { $radius = $_GET["radius"]; }

//$_REQUEST['zip']="60626";
$request_url = $host."/psclist.php?zip=".$_REQUEST['zip']."&source=ppmd";

$ch = curl_init();
$timeout = 30;

curl_setopt($ch, CURLOPT_URL, $request_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$data = curl_exec($ch);
curl_close($ch);

$xmlobj = new SimpleXMLElement($data);
$xmlArray = get_object_vars($xmlobj);
$xmlData = $xmlArray['patient-service-center'];

header("Content-type: text/xml");

if(isset($_REQUEST['locid'])) { $locid = $_REQUEST['locid'];}
$addressArray = array();

// Start XML file, echo parent node
echo "<markers>\n";
// Iterate through the rows, printing XML nodes for each
$labCount=0;
for($i=0; $i<count($xmlData); $i++)
{
	$xml = get_object_vars($xmlData[$i]);
	//$fax = get_object_vars($xml['fax']);
	$fax = getObjVars($xml, 'fax');
	//$hours = get_object_vars($xml['hours']);
	$hours = getObjVars($xml, 'hours');
	//$landmark = get_object_vars($xml['landmark']);
	$landmark = getObjVars($xml, 'landmark');
	//$telephone = get_object_vars($xml['telephone']);
	$telephone = getObjVars($xml, 'telephone');
	//$typeofservice = get_object_vars($xml['type-of-service']);
	$typeofservice = getObjVars($xml, 'type-of-service');
	
		$name = parseToXML($xml['name']);
		$name = strtolower($name);
		$name = ucwords($name);
		
		$address= parseToXML($xml['address']);
		$address = strtolower($address);
		$address = ucwords($address);
		
		if (!in_array ($address,$addressArray ))
		{
      $labCount++;
      $addressArray[$i] = $address;
    
      $city= parseToXML($xml['city']);
  		$city = strtolower($city);
  		$city = ucfirst($city);
  		
  		$hours= parseToXML($xml['hours']);
  		$hours = str_ireplace("monday", "M", $hours);
  		$hours = str_ireplace("mon", "M", $hours);
  		$hours = str_ireplace("tuesday", "T", $hours);
  		$hours = str_ireplace("tues", "T", $hours);
  		$hours = str_ireplace("wednesday", "W", $hours);
  		$hours = str_ireplace("wed", "W", $hours);
  		$hours = str_ireplace("thursday", "TH", $hours);
  		$hours = str_ireplace("thurs", "TH", $hours);
  		$hours = str_ireplace("thur", "TH", $hours);
  		$hours = str_ireplace("friday", "F", $hours);
  		$hours = str_ireplace("fri", "F", $hours);
  		$hours = str_ireplace("fr", "F", $hours);
  		$hours = str_ireplace("saturday", "Sat", $hours);
  		$hours = str_ireplace("sunday", "Sun", $hours);
  		$hours = str_ireplace("|", " | ", $hours);
  		//$hours = strtoupper($hours);
  		//$hours = ucwords($hours);
      
      // ADD TO XML DOCUMENT NODE
                // only a combination of MA & 129 should be omitted -- JH 11/28/2012
                $xmlState = $xml['state'];
                $xmlLabId = $xml['lab-id'];
                $testXML_state_labid = $xmlState.$xmlLabId;
                if( $testXML_state_labid != 'MA129' ){
  		echo '<marker ';
  		echo 'id="' . parseToXML($xml['id']) . '" ';
  		echo 'name="' . $name . '" ';
  		echo 'address="' . $address . '" ';
  		echo 'city="' . $city . '" ';
  		echo 'code="' . parseToXML($xml['code']) . '" ';
  		echo 'lat="' . $xml['lat'] . '" ';
  		echo 'lng="' . $xml['lng'] . '" ';
  		echo 'created-at="' . parseToXML($xml['created-at']) . '" ';
  		echo 'state="' . parseToXML($xml['state']) . '" ';
  		echo 'zip="' . parseToXML($xml['zip']) . '" ';
  		echo 'lab-id="' . parseToXML($xml['lab-id']) . '" ';
  		echo 'fax="' . $fax . '" ';
  		echo 'hours="' . $hours . '" ';
  		echo 'telephone="' . parseToXML($xml['telephone']) . '" ';
  		echo 'landmark="' . parseToXML($landmark['@attributes']['nil']) . '" ';
  		echo 'type-of-service="' . $typeofservice . '" ';
  		echo 'distance="' . $xml['distance'] . '" ';
  		echo "/>\n";
                }		
  	
  	 if ($labCount==11) break;
    }
}

// End XML file
echo "</markers>\n";


// Select all the rows in the markers table
/*if($_REQUEST['locid'] != '')
{
	$query = sprintf("SELECT * FROM markers WHERE id = ".$_REQUEST['locid'],
  	mysql_real_escape_string($center_lat),
  	mysql_real_escape_string($center_lng),
  	mysql_real_escape_string($center_lat));
}
else
{
	$query = sprintf("SELECT * FROM markers",
  	mysql_real_escape_string($center_lat),
  	mysql_real_escape_string($center_lng),
  	mysql_real_escape_string($center_lat),
  	mysql_real_escape_string($radius));
}
$result = mysql_query($query);
if (!$result) {
  die('Invalid query: ' . mysql_error());
}

// Start XML file, echo parent node
echo "<markers>\n";
// Iterate through the rows, printing XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  // ADD TO XML DOCUMENT NODE
  echo '<marker ';
  echo 'id="' . parseToXML($row['id']) . '" ';
  echo 'name="' . parseToXML($row['name']) . '" ';
  echo 'address="' . parseToXML($row['address']) . '" ';
  echo 'lat="' . $row['lat'] . '" ';
  echo 'lng="' . $row['lng'] . '" ';
  echo 'distance="' . $row['distance'] . '" ';
  echo "/>\n";
}

// End XML file
echo "</markers>\n";*/
?>

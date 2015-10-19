<?php
header("Content-type: text/json");

$html = file_get_contents('http://www.fh-potsdam.de/studieren/design/studium/vorlesungsverzeichnis/1-studienabschnitt-ba-design/');

$doc = new DOMDocument();
libxml_use_internal_errors(true);
$doc->loadHTML($html); // loads your HTML
$xpath = new DOMXPath($doc);
// returns a list of all links with rel=nofollow
$entries = $xpath->query('//tr[@class="short"]');

$baseDateString = '2015-10-18';

$courses = [];
$modules = [];

    	$translate = ["Montag" => "monday", "Dienstag" => "tuesday", "Mittwoch" => "wednesday", "Donnerstag" => "thursday", "Freitag" => "friday"];

foreach ($entries as $entry) {
    $ro = preg_replace('/\s\s+/', "\t",trim($entry->nodeValue));
    //echo $ro ."\n";
    $rawData = explode("\t", $ro);
    $courseData = [];
    if (trim($rawData[0]) !== '')
    	$courseData["modul"] = $rawData[0];
    
    if (trim($rawData[1]) !== '')
    	$courseData["titel"] = $rawData[1];
    
    if (isset($rawData[2]) && trim($rawData[2]) !== '')
    	$courseData["prof"] = $rawData[2];
    
    if (isset($rawData[3]) && in_array(trim($rawData[3]), ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag']))
    	$courseData["tag"] = $rawData[3];

    $reZeit = "/((?:(?:[0-1][0-9])|(?:[2][0-3])|(?:[0-9])):(?:[0-5][0-9])(?::[0-5][0-9])?(?:\\s?(?:am|AM|pm|PM))?)\\s+\\S+\\s+((?:(?:[0-1][0-9])|(?:[2][0-3])|(?:[0-9])):(?:[0-5][0-9])(?::[0-5][0-9])?(?:\\s?(?:am|AM|pm|PM))?)/is";
    $result = preg_match($reZeit, str_replace('.',':',$ro),$matches);
    if ($result) {
    	$courseData["zeit"] = $matches[1].' - '.$matches[2];	
    }

    if (isset($courseData["tag"]) && isset($courseData["zeit"])) {
    	$baseDate = new DateTime( $baseDateString );
    	$baseDate->modify("next ".$translate[$rawData[3]]." ".$matches[1]);
    	$courseData["begin"] = $baseDate->format('Y-m-d\TH:i:s+01:00');
    	
    	$baseDate = new DateTime( $baseDateString );
    	$baseDate->modify("next ".$translate[$rawData[3]]." ".$matches[2]);
    	$courseData["end"] = $baseDate->format('Y-m-d\TH:i:s+01:00');
    } else {
    	$baseDate = new DateTime( $baseDateString );
    	$baseDate->modify("+ 1 day");
    	$courseData["begin"] = $baseDate->format('Y-m-d');
    	$baseDate->modify("+ 5 days");
    	$courseData["end"] = $baseDate->format('Y-m-d');
    }
    $modulSplit = explode('-',$courseData["modul"]);
    $baseModule = $modulSplit[0];
    if (!in_array($baseModule, $modules)) {
    	array_push($modules, $baseModule);
    }

    $courseData["colorId"] = array_search($baseModule, $modules);

    $courseData["raum"] = $rawData[count($rawData)-1];
    
    array_push($courses,$courseData);
}

//$types = array_column($curses, 1);
//print_r($types);

//print_r($courses);

echo json_encode($courses);
?>
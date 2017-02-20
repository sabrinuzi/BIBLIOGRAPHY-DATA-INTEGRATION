<html>
<head>
   <link href="styles.css" rel="stylesheet" type="text/css" />
 <title>System</title>
</head>
<body><form method="post" action="<?php echo "elsevier.php";?>"> 
 Type in a search:<input type="text" id="searchText" name="searchText" value="<?php 
 if (isset($_POST['searchText'])){
  echo($_POST['searchText']); }
  else { echo('Mathematics');}
  ?>"/>
        <input type="submit" value="Search!" name="submit" id="searchButton" />
<?php
if (isset($_POST['submit'])) {


$eidArray = array('PUBLICATION_EID');
$thisCount = 0;
$continueCt = 0;
$eidCount = count($eidArray);
// Loop through each eID, one by one
foreach($eidArray as $eid) {
	$thisCount++;
					
	$offset = 0;
	$countTotal = 0;
	// Let's pull a maximum of 50 publication results per API call	
	$countIncrement = 50;
	$loopThrough = 1;
	$totalResults = null;
	$pubCtr = 0;
	
	while($loopThrough == 1) {
		$query = urlencode('eid(' . $eid . ')');
		
		$url = 'http://api.elsevier.com/content/search/scopus?query=' . urlencode( $_POST["searchText"]). '&view=COMPLETE&count=' . $countIncrement . '&start=' . $offset;
		$openCurl = curl_init();
		$u = 'http://api.elsevier.com/content/search/index:SCIDIR?query=' . urlencode( $_POST["searchText"]). '&APIKey=cfa8ba30cdf0d24033753673a016c3af&count=100' ;

		curl_setopt_array($openCurl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $u,
			CURLOPT_HTTPHEADER => array(
					// Specify the API key -- replace with your own once registered					
					'X-ELS-APIKey: cfa8ba30cdf0d24033753673a016c3af',
					'Accept: application/json'
				)
		));
		// Store the data returned by the API in a variable $result
		$result = curl_exec($openCurl);
		$httpCode = curl_getinfo($openCurl, CURLINFO_HTTP_CODE); // Retrieve HTTP Response
		// If the cURL call returns an error...
		if($result === false) {
			echo 'Curl error: ' . curl_error($openCurl);
		// If the cURL call is successful, but returns an HTTP error code...
		} else if($httpCode !== 200) {
			print "HTTP Response Error - Code: " . $httpCode . "\n";
		// Otherwise, proceed with returned results
		} else {
			// The query response returns data in JSON format, but we need to encode it as such
			// so PHP can know how to read it.
			// You could print out the $json variable to see the returned data in its entirety
			$json = json_decode($result,true);
			//var_dump($json);
			// The query response returns a lot of different data in a structured format.
			// Here, we're defining a variable $pubs that holds all of the PUBLICATION data,
			// which is represented in the JSON under search-results -> entry
			$pubs = $json['search-results']['entry'];
			//var_dump($pubs);
			$pubsCount = count($pubs);
			$countTotal += count($pubs);
			
			
			if(is_null($totalResults)) {
				// Grab the total number of results returned from the query
				$totalResults = $json['search-results']['opensearch:totalResults'];
				if($totalResults == 0) {
					// If the query returns 0 results, then quit looping through this publication eID
					$loopThrough  = 0;
					continue;
				} else {
					print "\t <b>Total results: " . $totalResults . "</b>\n";
				}
			}
		
			// Let's walk through each publication result stored in $pubs, one by one,
			// and display the returned data for each;
			// since the example here is performing the search by eID, the query should
			// only return one result
			foreach($pubs as $key => $pubInfo) {
				$pubCtr++;
						//	var_dump($pubInfo);echo "<br/>-- "; 
							
					$authors="";	
					$i=0;
					foreach( $pubInfo['authors'] as $creator){
					for($x=0;$x<sizeof($creator);$x++){
						//echo "<br/>"; var_dump();
						$authors.=$creator[$x]['given-name']." ".$creator[$x]['surname']. " / ";
					
					}
					
					$i++;
					}
				if(isset($pubInfo['error'])) {
				} else {
					$ur=$pubInfo['link'][1];
					 
					echo " <br/> ". ($pubCtr) ." - <a href='" . $ur['@href'] . "'> ". $pubInfo['dc:title']." </a> By (".$authors.") - (Elsevier)";
					echo "<br/>";
					
					//print_r($pubInfo['link'][1]);
					
				} // End if($pubInfo['error']) structure
			} // End foreach($pubs) structure
		} // End if($result === false) structure
		// Check to see if we need to keep looping through this particular publication search
		// and retrieve additional records
		if($totalResults - $countTotal > 0) {
			$offset += $countIncrement;
		} else {
			$loopThrough = 0;
		}
	} // End LOOPTHROUGH control structure
	
	// Close the cURL connection	
	curl_close($openCurl);
}
}
?>



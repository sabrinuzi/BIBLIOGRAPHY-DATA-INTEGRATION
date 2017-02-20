<html>
<head>
   <link href="styles.css" rel="stylesheet" type="text/css" />
 <title>System</title>
</head>
<body><form method="post" action="<?php echo "search.php";?>"> 
 Type in a search:<input type="text" id="searchText" name="searchText" value="<?php 
 if (isset($_POST['searchText'])){
  echo($_POST['searchText']); }
  else { echo('Mathematics');}
  ?>"/>
        <input type="submit" value="Search!" name="submit" id="searchButton" />
<?php
if (isset($_POST['submit'])) {
	$results_springer=array();
	$results_elsevier=array();
	$results_cinii=array();
	
	$idx=0;
	
	$offset = 0;
	$countTotal = 0;
	// Let's pull a maximum of 50 publication results per API call	
	$countIncrement = 50;
	$loopThrough = 1;
	$totalResults = null;
	$pubCtr = 0;
		
  $request_sp = 'http://api.springer.com/metadata/json?&api_key=82de5935b837e1940ae61325f7f24b53&q=' . urlencode( $_POST["searchText"])."&s=1&p=100";
  $request_cinii ='http://ci.nii.ac.jp/opensearch/search?q=' . urlencode( $_POST["searchText"]).'&count=100&start=1&lang=en&title=library&author=&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';
 	
  
  $response_sp  = file_get_contents($request_sp);
  $jsonobj_sp  = json_decode($response_sp);
  
  $response_cinii = file_get_contents($request_cinii);
  $jsonobj_cinii  = json_decode($response_cinii);
  
  //$response_el  = file_get_contents($request_el);
 // $jsonobj_el  = json_decode($response_el);
 
 
 // get Springer results
 
   for($i=0;$i<sizeof($jsonobj_sp->records);$i++)
  	{
	$authors="";	
	//var_dump($jsonobj2->records[$i]->creators);
	foreach( $jsonobj_sp->records[$i]->creators as $creator){
	$authors.=$creator->creator. " / ";
	
	}
	
  	$url=$jsonobj_sp->records[$i]->url;
	$item=array();
	//var_dump($jsonobj->records[$i]);
	$item=array("url"=>$url[0]->value,"title"=>$jsonobj_sp->records[$i]->title,"authors"=>$authors,"abstract"=>$jsonobj_sp->records[$i]->abstract,"publisher"=>"Springer");
	array_push($results_springer,$item);
  	}
	
 
 //end springer
 
  // get Cinii results_cinii
 
   foreach( $jsonobj_cinii as $ob){
	//  echo "<br/> OB ->> ";  var_dump($ob); 


	if(is_array($ob)){
		 $res_array=$ob; 
		 foreach( $ob[0] as $item){
			
			$c=0;
			if(is_array ($item)){
				
				 foreach( $item as $i){  
				 // echo "<br/> i ->> ";  var_dump($i); 
						foreach( $i as $itm=>$info){
							
						  if(is_object($info)){
							  $exit=true;
							   foreach( $info as $b){
								     //echo "<br/> vard ->> ";  var_dump($b); 
									 $url=$b;  
									 if($exit) break 2;
							   }
							  
							 
							 
						  }
					  }
					  
					  // insert into array
					  
					$item=array();
	
					$item=array("url"=>$url,"title"=>$i->title,"authors"=>"","abstract"=>"","publisher"=>"CiNii");
					array_push($results_cinii,$item);

					 }
				}
			 }
		}

	  }
 
 // cinii
 
 // Get search results Elsevier
		
 
		// Define the query string for the API in a variable. This string can be written in the
		// same way as an advanced search string on the Scopus online database, with some field names changed.
		// Here, I will do a Scopus search for the publication identified by the given eID.
		// NOTE: The query string must be URL-encoded
		//$query = urlencode('eid(' . $eid . ')');
		
		// Define the URL of the API to query. The API is RESTful and also allows other parameters beyond the query,
		// such as limiting which fields to return and the number of results to return, that are defined
		// in the API documentation		
		//$url = 'http://api.elsevier.com/content/search/scopus?query=' . urlencode( $_POST["searchText"]). '&view=COMPLETE&count=' . $countIncrement . '&start=' . $offset;
		// Since this script is written in PHP, the API call will be executed via cURL
		$openCurl = curl_init();
		$request_el = 'http://api.elsevier.com/content/search/index:SCIDIR?query=' . urlencode( $_POST["searchText"]). '&APIKey=cfa8ba30cdf0d24033753673a016c3af&count=100' ;
		//$request_el = 'http://api.elsevier.com/content/search/scopus?query=' . urlencode( $_POST["searchText"]). '&view=COMPLETE&count=' . $countIncrement . '&start=' . $offset;
	
		curl_setopt_array($openCurl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $request_el,
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
			//print "HTTP Response Error - Code: " . $httpCode . "\n";
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
					print "\tNo publications recorded with this ID.\n";
					// If the query returns 0 results, then quit looping through this publication eID
					$loopThrough  = 0;
					continue;
				} else {
					//print "\tTotal results: " . $totalResults . "\n";
				}
			}
		
			// Let's walk through each publication result stored in $pubs, one by one,
			// and display the returned data for each;
			// since the example here is performing the search by eID, the query should
			// only return one result
			foreach($pubs as $key => $pubInfo) {
				$pubCtr++;
						//	var_dump($pubInfo);
					$authors="";	
					$i=0;
					foreach( $pubInfo['authors'] as $creator){
					for($x=0;$x<sizeof($creator);$x++){
						//echo "<br/>"; var_dump();
						$authors.=$creator[$x]['given-name']." ".$creator[$x]['surname']. " / ";
					
					}
					
					$i++;
					}
				//print "\tPublication " . $pubCtr . "/" . $totalResults . "\n";
				// If the publication entry has an error...
				if(isset($pubInfo['error'])) {
					//$thisError = $pubInfo['error'];
					//if($thisError !== "Result set was empty") {
						//print "Error message: " . $thisError . "\n";
					//}
				// Otherwise, proceed with publication entry					
				} else {
					// This is where you'd take the JSON data and do what you want with it,
					// such as dump it into a database, do some analyses, echo it out, etc.
					// NOTE: See get-publication-data_mysql.php for an example of this
					$ur=$pubInfo['link'][1];
					
					// update array of results
					$item=array();
	
					$item=array("url"=>$ur['@href'],"title"=>$pubInfo['dc:title'],"authors"=>$authors,"abstract"=>"","publisher"=>"Elsevier");
					array_push($results_elsevier,$item) ;
					
					
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
	
	// Close the cURL connection	
	curl_close($openCurl);
 
 
 
 // end elsevier
 
 
 
 
 
 // echo $jsonobj->query;
  echo "\tTotal results: "; 
  echo count($results_springer) + count($results_cinii)+count($results_elsevier);
 
  echo('<ul ID="resultList">');                    
  $sizes_of_results=array(count($results_springer),count($results_cinii),count($results_elsevier));
 // echo count($results_springer) ." -- ".count($results_cinii)." -- ".count($results_elsevier);
  //sort($sizes_of_results);
 //var_dump($jsonobj->records[0]);
 $increment=0;
  for($i=0;$i<max($sizes_of_results);$i++)
  	{
	if($i<count($results_springer)){
		echo " <br/> ". ($increment+1) ." - <a target='_blank' href='" . $results_springer[$i]['url'] . "'> ". $results_springer[$i]['title']. " </a> - By (". $results_springer[$i]['authors'] .")  (Springer)";
		echo "<br/>";
		echo "<b>Abstract: </b> ".$results_springer[$i]['abstract'] ;
		 $increment+=1;
	}
	if($i<count($results_elsevier)){
		echo " <br/> ". ($increment+1) ." - <a target='_blank'  href='" . $results_elsevier[$i]['url'] . "'> ". $results_elsevier[$i]['title']. " </a> - By (". $results_elsevier[$i]['authors'] .")  (Elsevier)";
		echo "<br/>";
		echo "<b>Abstract: </b> ".$results_elsevier[$i]['abstract'] ;
		 $increment+=1;
	}
	if($i<count($results_cinii)){
		echo " <br/> ". ($increment+1) ." - <a target='_blank' href='" . $results_cinii[$i]['url'] . "'> ". $results_cinii[$i]['title']. " </a> - By (". $results_cinii[$i]['authors'] .")  (Cinii)";
		echo "<br/>";
		echo "<b>Abstract: </b> ".$results_cinii[$i]['abstract'] ;
		 $increment+=1;
	}
  	
  
	}
  	echo "<br/>";

echo("</ul>");
} ?>
</form>
</body>
</html>
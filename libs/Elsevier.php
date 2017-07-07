<?php

class Elsevier {

 
 private $API_KEY='cfa8ba30cdf0d24033753673a016c3af'; 
 private $searchTerm;
  
 private $idx=0;
 private $offset = 0;
 private  $countTotal = 0;
  // Let's pull a maximum of 50 publication results per API call	
 private $countIncrement = 50;
private  $loopThrough = 1;
 private $totalResults = null;
 private  $pubCtr = 0;

 public function setSearchTerm($searchTerm){
    $this->searchTerm=$searchTerm;
    
 }
 function __construct($searchTerm){
   $this->searchTerm=$searchTerm;
 }

  public function result(){
    $return_result=array();
    
    
		$openCurl = curl_init();
		$request_el = 'http://api.elsevier.com/content/search/index:SCIDIR?query=' . urlencode( $this->searchTerm ). '&APIKey='.$this->API_KEY.'&count=100' ;

		curl_setopt_array($openCurl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER => 0,
			CURLOPT_URL => $request_el,
			CURLOPT_HTTPHEADER => array(
					// Specify the API key -- replace with your own once registered					
					'X-ELS-APIKey:'.$this->API_KEY,
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
			// The query response returns a lot of different data in a structured format.
			// Here, we're defining a variable $pubs that holds all of the PUBLICATION data,
			// which is represented in the JSON under search-results -> entry
			$pubs = $json['search-results']['entry'];
			//var_dump($pubs);
			$this->pubsCount = count($pubs);
			$this->countTotal += count($pubs);
			
			if(is_null($this->totalResults)) {
				// Grab the total number of results returned from the query
				$this->totalResults = $json['search-results']['opensearch:totalResults'];
				if($this->totalResults == 0) {
					print "\tNo publications recorded with this ID.\n";
					// If the query returns 0 results, then quit looping through this publication eID
					$this->loopThrough  = 0;
					continue;
				} else {
					
				}
			}
      $return_result=$this->toArray($pubs);

		} // End if($result === false) structure
		// Check to see if we need to keep looping through this particular publication search
		// and retrieve additional records
		if($this->totalResults - $this->countTotal > 0) {
			$this->offset += $this->countIncrement;
		} else {
			$this->loopThrough = 0;
		}
	
    // Close the cURL connection	
    curl_close($openCurl);
    return $return_result;
  }

    private function toArray($obj){
      $results_elsevier=array();
      foreach($obj as $key => $pubInfo) {
				$this->pubCtr++;
					$authors="";	
					$i=0;
					if(isset( $pubInfo['authors'])){
						foreach( $pubInfo['authors'] as $creator){
							for($x=0;$x<sizeof($creator);$x++){
								//echo "<br/>"; var_dump();
								if(isset( $creator[$x]['given-name'])){
									$authors.=$creator[$x]['given-name']." ";
								}
								if(isset( $creator[$x]['surname'])){
									$authors.=$creator[$x]['surname']. " / ";
							
								}
								
						}
					}
					
					$i++;
					}
				
				if(isset($pubInfo['error'])) {
				// Otherwise, proceed with publication entry					
				} else {
					$ur=$pubInfo['link'][1];
					// update array of results
					$item=array();
					$item=array("url"=>$ur['@href'],"title"=>$pubInfo['dc:title'],"authors"=>$authors,"abstract"=>"","publisher"=>"Elsevier");
					array_push($results_elsevier,$item) ;
				} // End if($pubInfo['error']) structure
			} // End foreach($pubs) structure
    return $results_elsevier;
  }

}

?>
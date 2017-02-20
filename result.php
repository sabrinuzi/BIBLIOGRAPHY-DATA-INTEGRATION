<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="assets/img/favicon.png">

    <title>Search Publications</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="assets/css/main.css" rel="stylesheet">

    <!-- Fonts from Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html"><b>Search Publications</b></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">About</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>

	
	
<br/>
	<div class="container">
		<hr>
		<div class="row left">
			<div class="col-lg-8 col-lg-offset-3">
				<form class="form-inline" role="form" action="result.php"  method="post" >
				  <div class="form-group">
				    <input class="form-control" type="text" id="searchText" name="searchText" value="<?php 
					 if (isset($_POST['searchText'])){
					  echo($_POST['searchText']); }
					 
					  ?>"
				  </div>
				  <div class="form-group">
					    <select id="searchType" name="searchType" class="form-control">
						 <option value="all" <?php 
						 if (isset($_POST['searchType']) && $_POST['searchType']=='all'){
						  echo 'selected="selected"'; }
						 
						  ?> > All</option>
					  	<option value="title"
						<?php 
						 if ( isset($_POST['searchType']) && $_POST['searchType']=='title'){
						  echo 'selected="selected"'; }
						 
						  ?> 
						>By Title</option>
						 <option value="author"
						 <?php 
						 if (isset($_POST['searchType']) && $_POST['searchType']=='author'){
						  echo 'selected="selected"'; }
						  else { echo(' ');}
						  ?> 
						 >By Author</option>
		
						</select>
					  </div>
				  <button type="submit" name="submit" id="searchButton" class="btn btn-warning btn-lg">Search again!</button>
				</form>					
			</div>
			<div class="col-lg-3"></div>
		</div><!-- /row -->
		
	</div><!-- /container -->
	
	<div class="container">
	<hr>
		<div class="row mt left">
			<div class="col-lg-8">
			 <ul  >
			 <?php
if (isset($_POST['submit'])) {
	
	$searchType=$_POST['searchType'];

	
	
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
  
  // DBLP
  
  $results_dblp=array();
		
		if($_POST['searchType']=="author"){
			
			 $request = 'http://dblp.uni-trier.de/search/author/api?q='. urlencode( $_POST["searchText"]).'&h=1000&c=0&rd=1a&format=json';
			 $response  = file_get_contents($request);
			 $jsonobj  = json_decode($response);
			 $results_author=array();
			
			if(isset($jsonobj->result->hits->hit)){
			  if(is_array( $jsonobj->result->hits->hit)){
				$infos=$jsonobj->result->hits->hit;
				for($idx=0;$idx<sizeof($infos);$idx++){
					$username=explode("/", $infos[$idx]->info->url);
					
					array_push($results_author,$username[sizeof($username) - 1]);
					
					 $request = 'http://dblp.uni-trier.de/pers/xx/'.$username[sizeof($username) - 2].'/'.$username[sizeof($username) - 1];
					$res = simplexml_load_file($request);
					
					foreach ($res->r as $r) {
						
						if(isset($r->article )){
							$item=array("url"=>$r->article->url,"title"=>$r->article->title,"authors"=>$infos[$idx]->info->author,"abstract"=>"","publisher"=>"DBLP");
							array_push($results_dblp,$item);
						}
						else if(isset($r->inproceedings)){
							$item=array("url"=>$r->inproceedings->url,"title"=>$r->inproceedings->title,"authors"=>$infos[$idx]->info->author,"abstract"=>"","publisher"=>"DBLP");
							array_push($results_dblp,$item);
						}
						$aut=$r->inproceedings->author;
						
					}
				}
				
				
				
			  }
			 
			}
			 
			 
		}
		else{
			$request = 'http://dblp.uni-trier.de/search/publ/api?q='. urlencode( $_POST["searchText"]).'&h=1000&c=0&rd=1a&format=json';
			  $response  = file_get_contents($request);
			  $jsonobj  = json_decode($response);
			  $hits=$jsonobj->result->hits->hit;
			 
					 $i=0;
		  foreach($hits as $hit){
			
			$authors="";
			
			$a;
			if(isset($hit->info->authors->author)){
			
			if( is_array($a=$hit->info->authors->author) ){
			for($c=0; $c<sizeof($a); $c++){
			 $authors.=$a[$c]. " / ";
			
			}
			}
			else{
				 $authors.=$a. " / ";
			}
			}
			
		 
			

			$item=array("url"=>$hit->info->url ,"title"=>$hit->info->title,"authors"=>$authors,"abstract"=>"","publisher"=>"DBLP");
			array_push($results_dblp,$item);
		  $i++;
		  }
			 
		}
  
  
  
 // springer 
	if($searchType=='all') {
		$request_sp = 'http://api.springer.com/metadata/json?&api_key=82de5935b837e1940ae61325f7f24b53&q=' . urlencode( $_POST["searchText"])."&s=1&p=100";
	}
	else if($searchType=='title') {
		$request_sp = 'http://api.springer.com/metadata/json?&api_key=82de5935b837e1940ae61325f7f24b53&q=title:' . urlencode( $_POST["searchText"])."&s=1&p=100";
	
	}
	else if($searchType=='author') {
		$request_sp = 'http://api.springer.com/metadata/json?&api_key=82de5935b837e1940ae61325f7f24b53&q=name:' . urlencode( $_POST["searchText"])."&s=1&p=100";
	
	}
	
	
  // cinii
  if($searchType=='all') {
		 $request_cinii ='http://ci.nii.ac.jp/opensearch/search?q=' . urlencode( $_POST["searchText"]).'&count=100&start=1&lang=en&title=&author=&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';
 }
	else if($searchType=='title') {
		  $request_cinii ='http://ci.nii.ac.jp/opensearch/search?q=&count=100&start=1&lang=en&title=' . urlencode( $_POST["searchText"]).'&author=&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';

	}
	else if($searchType=='author') {
		 $request_cinii ='http://ci.nii.ac.jp/opensearch/search?q=&count=100&start=1&lang=en&title=&author='.urlencode( $_POST["searchText"]).'&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';

	}
 	
  
  $response_sp  = file_get_contents($request_sp);
  $jsonobj_sp  = json_decode($response_sp);
  
  $response_cinii = file_get_contents($request_cinii);
  $jsonobj_cinii  = json_decode($response_cinii);
  
 
 // get Springer results
 
   for($i=0;$i<sizeof($jsonobj_sp->records);$i++)
  	{
	$authors="";	
	
	foreach( $jsonobj_sp->records[$i]->creators as $creator){
	$authors.=$creator->creator. " / ";
	
	}
	
  	$url=$jsonobj_sp->records[$i]->url;
	$item=array();
	
	$item=array("url"=>$url[0]->value,"title"=>$jsonobj_sp->records[$i]->title,"authors"=>$authors,"abstract"=>$jsonobj_sp->records[$i]->abstract,"publisher"=>"Springer");
	array_push($results_springer,$item);
  	}
	
 
 //end springer
 
  // get Cinii results_cinii
 
   foreach( $jsonobj_cinii as $ob){
	
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
					
				}
			}

			foreach($pubs as $key => $pubInfo) {
				$pubCtr++;
						//	var_dump($pubInfo);
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
 
  echo "\t <b> Total results: "; 
  echo count($results_springer) + count($results_cinii)+count($results_elsevier)+count($results_dblp)." </b> <br/> <br/>";
                    
  $sizes_of_results=array(count($results_springer),count($results_cinii),count($results_elsevier),count($results_dblp));

 $increment=0;
  for($i=0;$i<max($sizes_of_results);$i++)
  	{
	if($i<count($results_springer)){
		echo "   ". ($increment+1) ." - <a target='_blank' href='" . $results_springer[$i]['url'] . "'> ". $results_springer[$i]['title']. " </a> - By (". $results_springer[$i]['authors'] .")  (Springer)";
		echo "<br/>";
		echo "<b>Abstract: </b> ".  substr( $results_springer[$i]['abstract'], 0, 50)." ... " ;
		 $increment+=1;
	 echo "<hr/>";
	}
	 
	if($i<count($results_elsevier)){
		echo "  ". ($increment+1) ." - <a target='_blank'  href='" . $results_elsevier[$i]['url'] . "'> ". $results_elsevier[$i]['title']. " </a> - By (". $results_elsevier[$i]['authors'] .")  (Elsevier)";
		echo "<br/>";
		echo "<b>Abstract: </b> ".  substr( $results_elsevier[$i]['abstract'], 0, 50)." ..." ;
		 $increment+=1;
		  echo "<hr/>";
	}
	 
	if($i<count($results_cinii)){
		echo " ". ($increment+1) ." - <a target='_blank' href='" . $results_cinii[$i]['url'] . "'> ". $results_cinii[$i]['title']. " </a> - By (". $results_cinii[$i]['authors'] .")  (Cinii)";
		echo "<br/>";
		echo "<b>Abstract: </b> ". substr($results_cinii[$i]['abstract'], 0, 50)." ..." ;
		 $increment+=1;
	}
	if($i<count($results_dblp)){
	echo " <br/> ". ($increment+1) ." - <a target='_blank'  href='http://dblp.uni-trier.de/" . $results_dblp[$i]['url'] . "'> ". $results_dblp[$i]['title']. " </a> - By (".  $results_dblp[$i]['authors'] .")  (DBLP)";
	echo "<br/>";
	echo "<b>Abstract: </b> ...";
  	 $increment+=1;
	 echo "<hr/>";
	 }
  
	}
  	echo "<br/> ";


} ?>

  
				
				</div>
		</div><!-- /row -->
	

	</div><! --/container -->

	<div class="container">
	
		
		<hr>
		<p class="centered">by S.Nuzi -2015</p>
	</div><!-- /container -->

	
	
	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
  </body>
</html>

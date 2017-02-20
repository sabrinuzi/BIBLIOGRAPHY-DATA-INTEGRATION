<html>
<head>
   <link href="styles.css" rel="stylesheet" type="text/css" />
 <title>System</title>
</head>
<body><form method="post" action="<?php echo "plos.php";?>"> 
 Type in a search:<input type="text" id="searchText" name="searchText" value="<?php 
 if (isset($_POST['searchText'])){
  echo($_POST['searchText']); }
  else { echo('Mathematics');}
  ?>"/>
        <input type="submit" value="Search!" name="submit" id="searchButton" />
<?php
if (isset($_POST['submit'])) {

  $request = 'http://api.plos.org/search?q='. urlencode( $_POST["searchText"]).'&api_key=5rok31nkzhGX2yL6WYAz';
  
  $response  = file_get_contents($request);
  //$xml = new SimpleXMLElement($request);
//print_r($xml);

$ch = curl_init();  

curl_setopt($ch,CURLOPT_URL,$request);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

$output = curl_exec($ch);

curl_close($ch);

$xml = simplexml_load_string($output);
//  $xml = simplexml_load_file($response);
      //var_dump($xml->result);
	  $i=1;
foreach($xml->result->doc as $item){
	// echo "<br/> <br/>";
      //var_dump($item);
	   
	   echo "<br/>";
	   echo "<br/>";
	   //var_dump($item->str);
	   echo $i." <a href='#' > ".$item->str[4]." </a> ";
	   
	   $i++;
      }
  //var_dump($xml->result);
  // echo "Parent Tag = ". $xml->getName() . "<br />";
  //$jsonobj  = json_decode($response);
 
 // echo $jsonobj->query;
  echo('<ul ID="resultList">');                    
   
 //var_dump($jsonobj->records[0]);
  //for($i=0;$i<sizeof($jsonobj->records);$i++)
  //	{
	$authors="";	
	//foreach( $jsonobj->records[$i]->creators as $creator){
	//$authors.=$creator->creator. " / ";
	//var_dump($creator);
		
	//}
	
  	//$url=$jsonobj->records[$i]->url;
  
	//var_dump($url);
	
  	//echo " <br/> ". ($i+1) ." - <a href='" . $url[0]->value . "'> ". $jsonobj->records[$i]->title. " </a> - By (". $authors .")  (Springer)";
	//echo "<br/>";
  //	echo "<br/>".$i." -  Title: ".$jsonobj->records[$i]->title." Url: ".$url->value;
  //	}
 
echo("</ul>");
} ?>
</form>
</body>
</html>
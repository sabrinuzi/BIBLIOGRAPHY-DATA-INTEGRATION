	<html>
	<head>
	   <link href="styles.css" rel="stylesheet" type="text/css" />
	 <title>System</title>
	</head>
	<body><form method="post" action="<?php echo "springer.php";?>"> 
	 Type in a search:<input type="text" id="searchText" name="searchText" value="<?php 
	 if (isset($_POST['searchText'])){
	  echo($_POST['searchText']); }
	  else { echo('Mathematics');}
	  ?>"/>
			<input type="submit" value="Search!" name="submit" id="searchButton" />
	<?php
	if (isset($_POST['submit'])) {

  $request = 'http://api.springer.com/metadata/json?&api_key=82de5935b837e1940ae61325f7f24b53&q=' . urlencode( $_POST["searchText"])."&s=1&p=100";

  $response  = file_get_contents($request);
  $jsonobj  = json_decode($response);
 
  echo('<ul ID="resultList">');                    
  for($i=0;$i<sizeof($jsonobj->records);$i++)
  	{
	$authors="";	
	foreach( $jsonobj->records[$i]->creators as $creator){
	$authors.=$creator->creator. " / ";
	
	}
	
  	$url=$jsonobj->records[$i]->url;
  	echo " <br/> ". ($i+1) ." - <a href='" . $url[0]->value . "'> ". $jsonobj->records[$i]->title. " </a> - By (". $authors .")  (Springer)";
	echo "<br/>";
	echo "<b>Abstract: </b> ".$jsonobj->records[$i]->abstract;
  	}
 	echo "<br/>";
  
echo("</ul>");
} ?>
</form>
</body>
</html>
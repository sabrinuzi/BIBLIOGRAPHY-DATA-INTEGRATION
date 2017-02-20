<html>
<head>
   <link href="styles.css" rel="stylesheet" type="text/css" />
 <title>System</title>
</head>
<body><form method="post" action="<?php echo "cinii.php";?>"> 
 Type in a search:<input type="text" id="searchText" name="searchText" value="<?php 
 if (isset($_POST['searchText'])){
  echo($_POST['searchText']); }
  else { echo('Mathematics');}
  ?>"/>
        <input type="submit" value="Search!" name="submit" id="searchButton" />
<?php
if (isset($_POST['submit'])) {

 // $request = 'http://api.springer.com/metadata/json?&api_key=82de5935b837e1940ae61325f7f24b53&q=&' . urlencode( $_POST["searchText"]).'s=1&p=100';
 $request ='http://ci.nii.ac.jp/opensearch/search?q=' . urlencode( $_POST["searchText"]).'&count=20&start=1&lang=en&title=&author=&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';
  $response  = file_get_contents($request);
  $jsonobj  = json_decode($response);
  $res_array=array();
  
  echo('<ul ID="resultList">'); 

  foreach( $jsonobj as $ob){
	
	if(is_array($ob)){
		 $res_array=$ob; 
		 foreach( $ob[0] as $item){
			
			$c=0;
			if(is_array ($item)){
				
				 foreach( $item as $i){  
				 		foreach( $i as $itm=>$info){
							
						  if(is_object($info)){
							  $exit=true;
							   foreach( $info as $b){
									 $url=$b;  
									 if($exit) break 2;
							   }
							  
							 
							 
						  }
					  }
					echo " <br/> ". ($c+1) ." - <a href='".$url."'> ". $i->title. " </a> - By ()  (CiNii)";
				$c++;
	echo "<br/>";
				 }
			}
		 }
	}
	 
echo("</ul>");
  }

} ?>
</form>
</body>
</html>
	<html>
	<head>
	   <link href="styles.css" rel="stylesheet" type="text/css" />
	 <title>System</title>
	</head>
	<body><form method="post" action="<?php echo "dblp.php";?>"> 
	 Type in a search:<input type="text" id="searchText" name="searchText" value="<?php 
	 if (isset($_POST['searchText'])){
	  echo($_POST['searchText']); }
	  else { echo('Mathematics');}
	  ?>"/>
	   <select id="searchType" name="searchType" class="form-control">
						 <option value="all" selected="selected">By Publication</option>
						 <option value="author">By Author</option>
		
						</select>
			<input type="submit" value="Search!" name="submit" id="searchButton" />
	<?php
	
	if (isset($_POST['submit'])) {
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
					//echo "<br>-usr- ". $username[sizeof($username) - 1];
					 $request = 'http://dblp.uni-trier.de/pers/xx/'.$username[sizeof($username) - 2].'/'.$username[sizeof($username) - 1];
					$res = simplexml_load_file($request);
					
					foreach ($res->r as $r) {
						//echo "<br><br> vardump: ";
						//var_dump($r);
						//echo " <br/>";
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
			 //var_dump($hit->info->authors );
			  
			$authors="";
			//echo "<br> ++ ".var_dump($hit);
			
			//echo '<br>---';
			$a;
			if(isset($hit->info->authors->author)){
				//var_dump($hit->info->authors->author);
			if( is_array($a=$hit->info->authors->author) ){
			for($c=0; $c<sizeof($a); $c++){
			 $authors.=$a[$c]. " / ";
			
			}
			}
			else{
				 $authors.=$a. " / ";
			}
			}
			
		 
			//var_dump($jsonobj->records[$i]); echo "<br/>";

			$item=array("url"=>$hit->info->url ,"title"=>$hit->info->title,"authors"=>$authors,"abstract"=>"","publisher"=>"DBLP");
			array_push($results_dblp,$item);
		  $i++;
		  }
			 
		}
	



for($i=0;$i<sizeof($results_dblp);$i++)
  	{
	echo " <br/> ". ($i+1) ." - <a href='" . $results_dblp[$i]['url'] . "'> ". $results_dblp[$i]['title']. " </a> - By (".  $results_dblp[$i]['authors'] .")  (DBLP)";
	echo "<br/>";
	echo "<b>Abstract: </b> ...";
		
	}
} 
	


?>
</form>
</body>
</html>

<?php

class Cinii {

 public $AUTHOR_TYPE='name';
 public $TITLE_TYPE='title';
 public $ALL_TYPE='all';
 
 private $API_KEY='82de5935b837e1940ae61325f7f24b53'; 
 private $searchTerm;
 private $type;

 public function setSearchTerm($searchTerm){
    $this->searchTerm=$searchTerm;
 }
 function __construct($searchTerm, $type='all'){
    $this->searchTerm=$searchTerm;
    $this->setType($type);
 }

  public function setType($type){
    $this->type=$type;
  }

  public function result(){
    if( $this->type=='title')
      $request ='http://ci.nii.ac.jp/opensearch/search?q=' . urlencode(  $this->searchTerm ).'&count=100&start=1&lang=en&title=&author=&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';
    else if( $this->type=='author') 
      $request ='http://ci.nii.ac.jp/opensearch/search?q=&count=100&start=1&lang=en&title=' . urlencode(  $this->searchTerm  ).'&author=&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';

    else 
       $request ='http://ci.nii.ac.jp/opensearch/search?q=&count=100&start=1&lang=en&title=&author='.urlencode(  $this->searchTerm  ).'&affiliation=&journal=&issn=&volume=&issue=&page=&publisher=&references=&year_from=&year_to=&range=&sortorder=&format=json';

      $response  = file_get_contents($request);
      $jsonobj  = json_decode($response);
      return $this->toArray($jsonobj);
    }

    private function toArray($obj){
      $results=array();
      foreach( $obj as $ob){
	
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
              $item=array();
              $item=array("url"=>$url,"title"=>$i->title,"authors"=>"","abstract"=>"","publisher"=>"CiNii");
              array_push($results,$item);
               }
            }
           }
        }
	   }
    return $results;
  }

}

?>
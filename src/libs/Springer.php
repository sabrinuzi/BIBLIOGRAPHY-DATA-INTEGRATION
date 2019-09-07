<?php

class Springer {

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
      $request = 'http://api.springer.com/metadata/json?&api_key='.$this->API_KEY.'&q='.$this->type.':' . urlencode( $this->searchTerm)."&s=1&p=100";
    else if( $this->type=='author') 
      $request = 'http://api.springer.com/metadata/json?&api_key='.$this->API_KEY.'&q='.$this->type.':' . urlencode( $this->searchTerm )."&s=1&p=100";
    else 
      $request = 'http://api.springer.com/metadata/json?&api_key='.$this->API_KEY.'&q=' . urlencode( $this->searchTerm )."&s=1&p=100";

      $response  = file_get_contents($request);
      $jsonobj_sp  = json_decode($response);
      return $this->toArray($jsonobj_sp);
    }

    private function toArray($obj){
      $results_springer=array();
      for($i=0;$i<sizeof($obj->records);$i++)
      {
        $authors="";	
        foreach( $obj->records[$i]->creators as $creator){
        $authors.=$creator->creator. " / ";
        }
        $url=$obj->records[$i]->url;
        $item=array();
        $item=array("url"=>$url[0]->value,"title"=>$obj->records[$i]->title,"authors"=>$authors,"abstract"=>$obj->records[$i]->abstract,"publisher"=>"Springer");
        array_push($results_springer,$item);
    }
    return $results_springer;
  }

}

?>
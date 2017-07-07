<?php

class Dblp {

 private $searchTerm;

 public function setSearchTerm($searchTerm){
    $this->searchTerm=$searchTerm;
 }
 function __construct($searchTerm){
    $this->searchTerm=$searchTerm;
 }

  public function result(){
    $request = 'http://dblp.uni-trier.de/search/publ/api?q='. urlencode( $this->searchTerm).'&h=1000&c=0&rd=1a&format=json';
    $response  = file_get_contents($request);
    $jsonobj  = json_decode($response);
    return $this->toArray($jsonobj);
 }

 private function toArray($obj){
    $results=array();
    $hits=$obj->result->hits->hit;
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
      array_push($results,$item);
      $i++;
    }
    return $results;
  }

}

?>
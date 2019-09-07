<?php
include('libs/Springer.php');
include('libs/Cinii.php');
include('libs/Dblp.php');
include('libs/Elsevier.php');
?>
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
		<link type="text/css" rel="stylesheet" href="assets/css/waitMe.css">
    <!-- Fonts from Google Fonts -->
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400,900' rel='stylesheet' type='text/css'>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>

  <body id='body'>

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
				<form class="form-inline" role="form" action="result.php"  method="get" >
				    <div class="form-group">
				        <input class="form-control" type="text" id="q" name="q" value="<?php  if (isset($_GET['q']))  echo($_GET['q']);	 ?>">
                    </div>
				     <div class="form-group">
					    <select id="type" name="type" class="form-control">
						 <option value="all" <?php 
						    if (isset($_GET['type']) && $_GET['type']=='all') {
                                echo 'selected="selected"'; 
                            }
						  ?>
                          >All</option>
					  	<option value="title"
                            <?php 
                                if (isset($_GET['type']) && $_GET['type']=='title'){
                                    echo 'selected="selected"'; 
                                }
                            ?> 
                        >
                            By Title
                        </option>
						<option value="author"
                            <?php 
                            if (isset($_GET['type']) && $_GET['type']=='author'){
                                echo 'selected="selected"';
                            } else { 
                                echo(' ');
                            }
                            ?> 
						>
                            By Author
                        </option>
						</select>
					  </div>
				  <button type="submit" id="searchButton" class="btn btn-warning btn-lg">Search again!</button>
				</form>					
			</div>
			<div class="col-lg-3"></div>
		</div>

	</div><!-- /container -->
		<div class="container">
			<hr>
			<div class="row mt left">
				<div class="col-lg-8">
			 <ul>
			 <?php
                if (isset($_GET['q'])) {
                    $type = $_GET['type'];

                    $results_springer = array();
                    $results_elsevier = [];
                    $results_cinii = [];

                    $results_dblp = [];

                    $dblp = new Dblp($_GET["q"]);
                    $results_dblp = $dblp->result();

                    $springer = new Springer($_GET["q"],$type);
                    $results_springer = $springer->result();

                    $cinii = new Cinii($_GET["q"]);
                    $results_cinii = $cinii->result();

                    $elsevier = new Elsevier($_GET["q"]);
                    $results_elsevier = $elsevier->result();

                    echo "\t <b> Total results: "; 
                    echo count($results_springer) + count($results_cinii)+count($results_elsevier)+count($results_dblp)." </b> <br/> <br/>";

                    $sizes_of_results = [
                        count($results_springer),
                        count($results_cinii),
                        count($results_elsevier),
                        count($results_dblp)
                    ];

                    $increment = 0;
                    for ($i = 0;$i < max($sizes_of_results); $i++) {

                        if($i<count($results_springer)){

                            echo "   " . ($increment + 1) . " - <a target='_blank' href='" . $results_springer[$i]['url'] . "'> " . $results_springer[$i]['title'] . " </a> - By (" . $results_springer[$i]['authors'] . ")  (Springer)";
                            echo "<br/>";
                            echo "<b>Abstract: </b> " .  substr( $results_springer[$i]['abstract'], 0, 50)." ... " ;
                                $increment += 1;
                            echo "<hr/>";
                        }

                        if ($i < count($results_elsevier)) {
                            echo "  " . ($increment+1) . " - <a target='_blank'  href='" . $results_elsevier[$i]['url'] . "'> " . $results_elsevier[$i]['title']. " </a> - By (" . $results_elsevier[$i]['authors'] . ")  (Elsevier)";
                            echo "<br/>";
                            echo "<b>Abstract: </b> ".  substr( $results_elsevier[$i]['abstract'], 0, 50)." ..." ;
                                $increment += 1;
                                echo "<hr/>";
                        }

                        if($i < count($results_cinii)) {
                            echo " " . ($increment+1) . " - <a target='_blank' href='" . $results_cinii[$i]['url'] . "'> ". $results_cinii[$i]['title'] . " </a> - By (" . $results_cinii[$i]['authors'] . ")  (Cinii)";
                            echo "<br/>";
                            echo "<b>Abstract: </b> ". substr($results_cinii[$i]['abstract'], 0, 50)." ..." ;
                                $increment+=1;
                                echo "<hr/>";
                        }
                        if ($i < count($results_dblp)) {
                            echo " <br/> " . ($increment+1) . " - <a target='_blank'  href='http://dblp.uni-trier.de/" . $results_dblp[$i]['url'] . "'> " . $results_dblp[$i]['title'] . " </a> - By (" .  $results_dblp[$i]['authors'] . ")  (DBLP)";
                            echo "<br/>";
                            echo "<b>Abstract: </b> ...";
                            $increment+=1;
                            echo "<hr/>";
                        }
                    }
                    echo "<br/> ";
                } 
            ?>
		    </div>
		</div><!-- /row -->
		</div>
		<div class="container">
			<hr>
			<p class="centered">by S.Nuzi -2015</p>
		</div><!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
	<script src="assets/js/waitMe.js"></script>
		
    <script>
        $(document).ready(function() {
            var currentEffect = 'bounce';
            $('#searchButton').click(function(){
                runWaitMe(currentEffect);
            });

            function runWaitMe(effect){
                $('#body').waitMe({
                    effect: 'bounce',
                    text: 'Searching publications...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    sizeW: '',
                    sizeH: '',
                    source: '',
                    onClose: function() {}
                });
            }
        });
    </script>
    </body>
</html>

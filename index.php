<!DOCTYPE html>
<html lang="en">
	<head>
		
<?php
	require_once("ESCall.php");
?>


		<meta charset="utf-8">
		<title>
			SugarES
		</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link href="bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
		
	    <style type="text/css">
			/* Large desktop */
			@media (min-width: 980px) { 
				body {
					padding-top: 60px;
				}
				.linediv-l {
					border-right: 1px white solid;
				}
				.linediv-r {
					border-left: 1px white solid;
				}
				
				.container{
					padding-left:10px;
				}
			}

			/* Landscape phones and down */
			@media (max-width: 480px) { 
				.copy {
					padding: 2.5% 10%;
				}
				.linediv-l {
					border-bottom: 1px white solid;
				}
				.linediv-r {
					border-top: 1px white solid;
				}
			}

	      	/* All form factors */
			/* Main body and headings */
			body{
				font-family: 'Open Sans', Helvetica, Arial, sans-serif;
			}
			.heading, .subheading {
				font-family: 'Ubuntu', Helvetica, Arial, sans-serif;
				text-align: center;
			}
			
			ul.treeAction{
				list-style-type: none;
				padding: 0px;
			}
			
			label{
				cursor:default;
			}
		</style>
	</head>
	<body>
		<!-- nav bar -->
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container" style="width:100%;">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
            			<span class="icon-bar"></span>
            			<span class="icon-bar"></span>
          			</button>
          			<a class="brand" href="#">SugarES</a>
          			<div class="nav-collapse collapse">
            			<ul class="nav">
              				<li class="active"><a href="#">Home</a></li>
              				<li><a href="#about">About</a></li>
              				<li><a href="#contact">Contact</a></li>
            			</ul>
          			</div><!--/.nav-collapse -->
        		</div>
      		</div>
    	</div>
		
		<!-- body -->
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3 well">
					<!--Sidebar content-->
					<!-- Connection Form -->
					<form action='/' class="form-inline" id="serverConnection">
						<div class="row-fluid">
							<label class="span4" for="inputServerName">Server</label>
  							<input class="span8" type="text" id="inputServerName" name="inputServerName" value="localhost" />
						</div>
						
						<div class="row-fluid">
							<label class="span4" for="inputPort">Port</label>
  							<input class="span8" type="text" id="inputPort" name="inputPort" value="9200" />
						</div>
						
						<div class="row-fluid">
							<label class="span4" for="inputIndex">Index</label>
  							<input class="span8" type="text" id="inputIndex" name="inputIndex" placeholder="(optional)" />
						</div>
						
						<div class="row-fluid">
							<a class="btn btn-primary span6" id="serverConnectionSubmit" style="align:center;"><i class="icon-refresh icon-white"></i> Load</a>
						</div>
					</form>
					<!-- Actions Tab Pane -->
					<div class="row-fluid">
						<div class="span12 well well-small">
							<div class="tabbable" style="margin-bottom: 18px;">
								<ul class="nav nav-tabs">
                					<li class="active"><a href="#treeContent" data-toggle="tab">Tree</a></li>
                					<li><a href="#tab2" data-toggle="tab">Inject</a></li>
                					<li><a href="#tab3" data-toggle="tab">Search</a></li>
              					</ul>
								<div class="tab-content" style="padding-bottom: 9px; border-bottom: 1px solid #ddd;">
                					<div class="tab-pane active" id="treeContent">
                						Enter in the ElasticSearch Server Properties and click Load.
                  						<!--
                  							<ul class="treeAction"><li><i class="icon-minus-sign" data-toggle="collapse" data-target="#tab1_cd802dbad42214ba41081d088c177c4c"></i>cd802dbad42214ba4108...<ul id="tab1_cd802dbad42214ba41081d088c177c4c" class="treeAction collapse in"><li><i class="icon-plus-sign"></i>Cases</li><li><i class="icon-plus-sign"></i>Accounts</li><li><i class="icon-plus-sign"></i>Notes</li><li><i class="icon-plus-sign"></i>Meetings</li><li><i class="icon-plus-sign"></i>Documents</li><li><i class="icon-plus-sign"></i>Contacts</li><li><i class="icon-plus-sign"></i>Leads</li><li><i class="icon-plus-sign"></i>Calls</li><li><i class="icon-plus-sign"></i>Opportunities</li></ul></li><li><i class="icon-minus-sign" data-toggle="collapse" data-target="#tab1_d3f30aab421a8c6f49b61918f326b5a4"></i>d3f30aab421a8c6f49b6...<ul id="tab1_d3f30aab421a8c6f49b61918f326b5a4" class="treeAction collapse in"><li><i class="icon-plus-sign"></i>Accounts</li><li><i class="icon-plus-sign"></i>Cases</li><li><i class="icon-plus-sign"></i>Notes</li><li><i class="icon-plus-sign"></i>Meetings</li><li><i class="icon-plus-sign"></i>Contacts</li><li><i class="icon-plus-sign"></i>Leads</li><li><i class="icon-plus-sign"></i>Calls</li><li><i class="icon-plus-sign"></i>Opportunities</li></ul></li></ul>
                  						-->
                					</div>
                					<div class="tab-pane" id="tab2">
                  						<p>Howdy, I'm in Section 2.</p>
                					</div>
                					<div class="tab-pane" id="tab3">
                  						<p>What up, this is Section 3.</p>
                					</div>
              					</div>
            				</div> <!-- /tabbable -->
						</div>
					</div>
				</div>
				<div class="span8">
					<!--Body content-->
					
					<div class="row-fluid">
						
						<!-- Server Stats -->
						<div class="span6" id="serverStatsContent">
							<fieldset>
    							<legend>Server Stats</legend>
    							<div class="row-fluid">
    								<label class="span12">No Server Loaded.</label>
    							</div>
  							</fieldset>
						</div>
						
						<!-- Index Stats -->
						<div class="span6" id="activeIndexStatsContent">
							<fieldset>
    							<legend>Index Stats</legend>
    							<div class="row-fluid">
    								<label class="span12">No Active Index Selected.</label>
    							</div>
  							</fieldset>
						</div>
					</div>
					
					<div class="row-fluid">
						<!-- Record Results -->
						
						<fieldset>
    						<legend>Record Results</legend>
    						<!-- rows and labels
							<div class="row-fluid">
								<label class="span6">Index Name: </label>
								<label class="span6">d3f30aab421a8c6f49b61918f326b5a4</label>
							</div>
							<div class="row-fluid">
								<label class="span6">Type: </label>
								<label class="span6">Accounts</label>
							</div>
							<div class="row-fluid">
								<label class="span6">Id: </label>
								<label class="span6">a3867037-aa48-4b7d-4170-520b85aad5b9</label>
							</div>
							<div class="row-fluid">
								<label class="span6">Score: </label>
								<label class="span6">1.0</label>
							</div>
							<br />
							<div class="row-fluid">
								<label class="span6"><strong>Data: </strong></label>
							</div>
							<!-- end rows and labels-->
							
							<!-- Tables -->
								<table class="table table-striped table-condensed table-bordered">
									<tr>
										<td>
											Index Name
										</td>
										<td>
											d3f30aab421a8c6f49b61918f326b5a4
										</td>
									</tr>
									<tr>
										<td>
											Type
										</td>
										<td>
											Accounts
										</td>
									</tr>
									<tr>
										<td>
											Id
										</td>
										<td>
											a3867037-aa48-4b7d-4170-520b85aad5b9
										</td>
									</tr>
									<tr>
										<td>
											Score
										</td>
										<td>
											1.0
										</td>
									</tr>
								</table>
							<!-- End Tables -->
							<div class="row-fluid">
								<dl class="dl-horizontal">
									<dt>name:</dt>
									<dd>Waverly Trading House</dd>
									<dt>phone_office:</dt>
									<dd>(517) 856-1556</dd>
									<dt>email1:</dt>
									<dd>the.the.info@example.co.jp</dd>
									<dt>module:</dt>
									<dd>Accounts</dd>
									<dt>team_set_id:</dt>
									<dd>5c8817c8099600398510520b851be2d3</dd>
									<dt>user_favorites:</dt>
									<dd>[]</dd>
									<dt>doc_owner:</dt>
									<dd>seed_jim_id</dd>
								</dl>
							</div>
  						</fieldset>
  						
					</div>
					<div class="row-fluid" id="errorResultsContent">
						<!-- Error Results -->
    				</div>
				</div>
			</div>
		</div>

<?php


?>

		<script src="bootstrap/js/jquery-2.0.3.min.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script src="bootstrap/js/bootstrap-transition.js"></script>
		<script src="bootstrap/js/bootstrap-tab.js"></script>
		<script src="bootstrap/js/bootstrap-button.js"></script>
		<script src="bootstrap/js/bootstrap-collapse.js"></script>

		<script type="text/javascript">
			//bind the anchor tag to the form submit 
			$(document).ready(function() {
				$('#serverConnectionSubmit').bind('click', function(){
	      			$('#serverConnection').submit();
				});
	 		});

			//capture the form submit and process the ajax
			/* attach a submit handler to the form */
			$("#serverConnection").submit(function serverConnectionSubmit(event) {

  				/* stop form from submitting normally */
  				event.preventDefault();

  				/* get some values from elements on the page: */
  				var $form = $( this ),
  					action = "serverConnection",
      				inputServerName = $form.find( 'input[name="inputServerName"]' ).val(),
      				inputPort = $form.find( 'input[name="inputPort"]' ).val(),
      				inputIndex = $form.find( 'input[name="inputIndex"]' ).val(),
      				url = "http://localhost/_test/SugarES/controller.php";

  				/* Send the data using post */
  				var posting = $.post( url, { action: action, inputServerName: inputServerName, inputPort: inputPort, inputIndex: inputIndex } );

  				/* Put the results in a div */
  				posting.done(function( data ) {
  					
    				var treeContent = $(data).find('#treeHTML');
    				$("#treeContent").empty().append(treeContent);
    				
    				var serverStatsHTML = $(data).find('#serverStatsHTML');
    				$("#serverStatsContent").empty().append(serverStatsHTML);
    				
    				var indexStatsHTML = $(data).find('#indexStatsHTML');
    				var activeIndexId = $(data).find('#activeIndexId').val();
    				
    				//put all index stats into hidden div, to be revealed when selecting an index
    				$("#activeIndexStatsContent").empty().append(indexStatsHTML);
    				$("#"+activeIndexId).css("display","inline");
    				
    				var errorHTML = $(data).find('#errorHTML');
    				$("#errorResultsContent").empty().append(errorHTML);
    				
    				//$( "#treeContent" ).append("test!!!!");
  				});
			});
			
			function changeActiveIndex(newActiveIndex){
				var oldActiveIndexId = $("#activeIndexId").val();
				$("#"+oldActiveIndexId).css("display","none");
				$("#activeIndexId").val("index_stats_"+newActiveIndex);
				$("#index_stats_"+newActiveIndex).css("display","inline");
			}
			
		</script>
		
	</body>
</html>
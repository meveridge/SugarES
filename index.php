<!DOCTYPE html>
<html lang="en">
	<head>
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
		
		<input type="hidden" id="activeDocRecordIndex" />
		<input type="hidden" id="activeDocRecordType" />
		<input type="hidden" id="activeDocRecordId" />
		
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
  							<input class="span8" type="text" id="inputServerName" name="inputServerName" value="localhost" tabindex="1" />
						</div>
						
						<div class="row-fluid">
							<label class="span4" for="inputPort">Port</label>
  							<input class="span8" type="text" id="inputPort" name="inputPort" value="9200" tabindex="2" />
						</div>
						<!--
						<div class="row-fluid">
							<label class="span4" for="inputIndex">Index</label>
  							<input class="span8" type="text" id="inputIndex" name="inputIndex" placeholder="(optional)" />
						</div>
						-->
						<input type="hidden" id="inputIndex" name="inputIndex" />
						
						<div class="row-fluid">
							<div class="form-actions">
								<button type="submit" class="btn btn-primary pull-right" tabindex="3"><i class="icon-refresh icon-white"></i> Load</button>
							<!-- <a class="btn btn-primary span6" id="serverConnectionSubmit" style="align:center;" tabindex="3"><i class="icon-refresh icon-white"></i> Load</a> -->
							<!-- <input type="submit" style="border: none;width: 0;height: 0;line-height: 0;padding:0;margin: 0;" /> -->
							</div>
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
                					</div>
                					<div class="tab-pane" id="tab2">
                  						<p>Inject section still to be built...</p>
                					</div>
                					<div class="tab-pane" id="tab3">
                  						<p>Search section still to be built...</p>
                					</div>
              					</div>
            				</div> <!-- /tabbable -->
						</div>
					</div>
				</div>
				<div class="span8">
					<!--Body content-->
					
					<div class="row-fluid hidden-phone">
						
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
						<div class="span6 hidden-phone" id="activeIndexStatsContent">
							<fieldset>
    							<legend>Index Stats</legend>
    							<div class="row-fluid">
    								<label class="span12">No Active Index Selected.</label>
    							</div>
  							</fieldset>
						</div>
					</div>
					
					<div class="row-fluid" id="mainContent">
						<!-- Main Content -->
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
			
			function changeTreeIcon(inputIndex,inputType){
				if(inputType==""){
					var iconId = inputIndex + "_icon";
				}else{
					var iconId = inputIndex + "_" + inputType + "_icon";
				}
				var newClass = ""; 
				if($("#"+iconId).hasClass("icon-plus-sign")){
					$("#"+iconId).removeClass("icon-plus-sign");
					$("#"+iconId).addClass("icon-minus-sign",true);
					newClass = "icon-minus-sign";
				}else{
					$("#"+iconId).addClass("icon-plus-sign",true);
					$("#"+iconId).removeClass("icon-minus-sign",true);
					newClass = "icon-plus-sign";
					
					if(inputType!=""){
						var activeDocRecordIndex = $("#activeDocRecordIndex").val();
	  					var activeDocRecordType = $("#activeDocRecordType").val();
	  					
	  					if(activeDocRecordIndex==inputIndex && activeDocRecordType == inputType){
	  						$("#mainContent").empty();
	  					}
					}
					
				}
				return newClass;
			}
			
			function retrieveDocsByIndexAndType(inputIndex,inputType){
				
				var targetId = inputIndex + "_" + inputType + "_child";
				var iconId = inputIndex + "_" + inputType + "_icon";
				
				var newClass = changeTreeIcon(inputIndex,inputType);
				
				if(newClass == "icon-minus-sign"){
					//only run ajax if icon is minus sign, meaning the section is open
					//get some values from elements on the page:
	  				var $form = $("#serverConnection"),
	  					action = "retrieveDocsByIndexAndType",
	      				inputServerName = $form.find( 'input[name="inputServerName"]' ).val(),
	      				inputPort = $form.find( 'input[name="inputPort"]' ).val(),
	      				url = "http://localhost/_test/SugarES/controller.php";
	
	  				//Send the data using post
	  				var posting = $.post( url, { action: action, inputServerName: inputServerName, inputPort: inputPort, inputIndex: inputIndex, inputType: inputType } );
	
	  				//Put the results in a div
	  				posting.done(function(data) {
	
	  					var docTreeHTML = $(data).find('#docTreeHTML');
	    				$("#"+targetId).empty().append(docTreeHTML);
	    				
	    				var errorHTML = $(data).find('#errorHTML');
	    				$("#errorResultsContent").empty().append(errorHTML);
	  				});
  				
  				}else{
  					//collapse tree, clear the results
  					//but only if we collapse the active record section
  					
  					var activeDocRecordIndex = $("#activeDocRecordIndex").val();
  					var activeDocRecordType = $("#activeDocRecordType").val();
  					
  					if(activeDocRecordIndex==inputIndex && activeDocRecordType == inputType){
  						$("#mainContent").empty();
  					}
  				}
			}
			
			function retrieveDocById(inputIndex,inputType,inputId){
				var $form = $("#serverConnection"),
					activeDocRecord = $("#activeDocRecord").val(),
	  				action = "retrieveDocById",
	      			inputServerName = $form.find( 'input[name="inputServerName"]' ).val(),
	      			inputPort = $form.find( 'input[name="inputPort"]' ).val(),
	      			url = "http://localhost/_test/SugarES/controller.php";
				
				//tree record highlighting
				if(activeDocRecordId!=""){
					$("#"+$("#activeDocRecordIndex").val()+"_"+$("#activeDocRecordType").val()+"_tree_"+$("#activeDocRecordId").val()).removeClass("muted");
					$("#"+$("#activeDocRecordIndex").val()+"_"+$("#activeDocRecordType").val()+"_tree_"+$("#activeDocRecordId").val()+"_icon").removeClass("icon-arrow-right");
					//$("#tree_"+activeDocRecord+"_icon").removeClass("icon-white");
				}
				$("#activeDocRecordIndex").val(inputIndex);
				$("#activeDocRecordType").val(inputType);
				$("#activeDocRecordId").val(inputId);
				
				$("#"+inputIndex+"_"+inputType+"_tree_"+inputId).addClass("muted");
				$("#"+inputIndex+"_"+inputType+"_tree_"+inputId+"_icon").addClass("icon-arrow-right");
				//$("#tree_"+inputId+"_icon").addClass("icon-white");
				
				
	  			//Send the data using post
	  			var posting = $.post( url, { action: action, inputServerName: inputServerName, inputPort: inputPort, inputIndex: inputIndex, inputType: inputType, inputId: inputId } );
	
	  			//Put the results in a div
	  			posting.done(function(data) {
	
	  				var docHTML = $(data).find('#docHTML');
	   				$("#mainContent").empty().append(docHTML);
	   				var errorHTML = $(data).find('#errorHTML');
	   				$("#errorResultsContent").empty().append(errorHTML);
				});
			}
		</script>
		
	</body>
</html>
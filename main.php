<?php
session_start();
$canShare = false;
if(isset($_SESSION["uid"]) && isset($_SESSION["CREATED"]))
{
	if( (time() - $_SESSION["CREATED"]) > 3600 )
    {
        //Session too old
        session_unset();
        session_destroy();
    }
	else
	{
		$canShare = true;
	}
}
?>
<!DOCTYPE html>
<html leng="en">
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<style>
	.anyClass {
  	height:300px;
  	width:400px;
  	overflow-y: scroll;
	}
	textarea {
	resize: none;
	}
	.col-1
	{
	}
	.col-2
	{
	}
	.col-3
	{
	}

	.mem-0
	{
		background: #eeeeee;
	}

	.mem-1
	{
		background: #dddddd;
	}
</style>

<script type="text/javascript" src="stack8.js"></script>
<script type="text/javascript" src="assembler.js"></script>

<link href="jquery.numberedtextarea.css" rel="stylesheet" type="text/css">

</head>
<body onload="pageInit();">

<script>
var testMemory = new Memory();
var testAssembler = new Assembler();
var testStack = new Stack();
var testALU = new ALU(testStack);
var testCPU = new CPU(testMemory,testALU,testStack);
var runState = 0;
</script>

<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">Stack8</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item active">
        <a class="nav-link" href="#">Home</a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">Manual</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="community.php">Shared Programs</a>
      </li>
      <li class="nav-item">
        <a class="nav-link disabled" href="#">My Stack</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
	<div class="row">
	<br>
	</div>

	<div class="row d-flex align-items-stretch">

		<div class="col-md-5 col-1 flex-wrap">
		<ul class="nav nav-tabs" role="tablist">
  			<li class="nav-item">
   			<a class="nav-link active" href="#code" role="tab" data-toggle="tab"><i class="fa fa-code" aria-hidden="true"></i> Code</a>
  			</li>
  			<li class="nav-item">
    		<a class="nav-link" href="#memory" role="tab" data-toggle="tab"><i class="fa fa-list" aria-hidden="true"></i> Memory</a>
  			</li>
		</ul>

		<div class="tab-content">
  		<div role="tabpanel" class="tab-pane active text-center" id="code">
  			<form class="text-center">
  				<div class="form-group">
    			<textarea class="form-control" id="codeArea" rows="14" cols="20" wrap="off"></textarea>
  				</div>
  			</form>
  			<button type="button" class="btn btn-outline-danger" onclick="doAssemble();"><i class="fa fa-wrench" aria-hidden="true"></i> Assemble</button>
  		</div>
  		<div role="tabpanel" class="tab-pane fade" id="memory">
  			<div id="memoryArea" class="anyClass">
  			
  			</div>
  		</div>
		</div>
		</div>

		<div class="col-md-2 align-self-center">
		<div class="text-center">
		<button type="button" class="btn btn-info btn-block" onclick="doStep();"><i class="fa fa-step-forward" aria-hidden="true"></i> Step</button><br>
		<button type="button" id="runButton" class="btn btn-primary btn-block" onclick="doRun();"><i class="fa fa-play" aria-hidden="true"></i> Run</button><br>
		<button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#shareModal"><i class="fa fa-share-alt" aria-hidden="true"></i> Share</button>
		</div>
		</div>

		<div class="col-md-5 col-3">
		<ul class="nav nav-tabs" role="tablist">
  			<li class="nav-item">
   			<a class="nav-link active" href="#screen" role="tab" data-toggle="tab"><i class="fa fa-picture-o" aria-hidden="true"></i> Screen</a>
  			</li>
  			<li class="nav-item">
    		<a class="nav-link" href="#debugger" role="tab" data-toggle="tab"><i class="fa fa-bug" aria-hidden="true"></i> Debugger</a>
  			</li>
		</ul>

		<div class="tab-content">
  		<div role="tabpanel" class="tab-pane active" id="screen">
			<canvas id="myCanvas" width="400" height="300" style="border:1.5px solid #d3d3d3;">
			Your browser does not support the HTML5 canvas tag.</canvas>
  		</div>
  		<div role="tabpanel" class="tab-pane fade" id="debugger">
  			<div class="row">
  				<div class="col d-flex flex-column align-items-stretch text-center" style="border-right: 2px solid #cccccc;">
  				<!-- <div class="d-flex flex-column"> -->
  				<div class="p-1">Internal Stack</div>
  				<div class="p-0" id="stack0" style="background:#eeeeee;">?</div>
  				<div class="p-0" id="stack1" style="background:#dddddd;">?</div>
  				<div class="p-0" id="stack2" style="background:#eeeeee;">?</div>
  				<div class="p-0" id="stack3" style="background:#dddddd;">?</div>
  				<div class="p-0" id="stack4" style="background:#eeeeee;">?</div>
  				<div class="p-0" id="stack5" style="background:#dddddd;">?</div>
  				<div class="p-0" id="stack6" style="background:#eeeeee;">?</div>
  				<div class="p-0" id="stack7" style="background:#dddddd;">?</div>
				<!-- </div> -->
  				</div>
  				<div class="col d-flex flex-column align-items-stretch text-center">
  				<div class="p-1">Program Counter</div>
  				<div class="p-0" id="pc" style="background:#eeeeee;">?</div>
  				<div class="p-1">Jump Flag</div>
  				<div class="p-0" style="background:#eeeeee;">Not implemented</div>
  				</div>
  			</div>
  		</div>
		</div>
	</div>
</div>

<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Share your Program</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
			<div class="row">
			<div class="col">
				<div class="alert alert-success collapse" id="successShareAlert" role="alert"></div>			
				<div class="alert alert-info collapse" id="infoShareAlert" role="alert"></div>			
				<div class="alert alert-warning collapse" id="warningShareAlert" role="alert"></div>			
				<div class="alert alert-danger collapse" id="dangerShareAlert" role="alert"></div>			
			</div>
			</div>
			<div class="row">
				<div class="col-md-6">
				Program Name:<br>
				<input type="text" class="form-control" id="programName"><br>
				Share privately  <input type="checkbox" id="sharePrivate" <?php if(!$canShare){echo "disabled";}?>>
				</div>
			</div>
			<div class="row">
			<div class="col">
			<hr>
			<small><i>You can only share compilable programs<br>You must be logged in to share privately</i></small>
			</div>
			</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="doShare();">Share</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>


<script>
$('#shareModal').on('hidden.bs.modal', function (e) {
  	$("#infoShareAlert").hide();
	$("#warningShareAlert").hide();
})

function doShare()
{	
	$("#infoShareAlert").hide();
    $("#warningShareAlert").hide();
	var programName = document.getElementById("programName").value.trim();
	if(programName.length == 0 || programName.length > 32)
	{
		//alert("Invalid Name");
		document.getElementById("infoShareAlert").innerHTML = "<strong>Info!</strong> Your name is not valid (>0 and <32 characters)";
		$("#infoShareAlert").show();
		return;
	}
	
	var sharePrivate = 0;
	if(document.getElementById("sharePrivate").checked === true)
	{
		sharePrivate = 1;
	}
	
	
	var asmIn = document.getElementById("codeArea").value;
	if(asmIn.trim().length == 0)
	{
		//alert("Empty Program");
		document.getElementById("infoShareAlert").innerHTML = "<strong>Info!</strong> Your program is empty";
        $("#infoShareAlert").show();
		return;
	}
	var tempMemory = new Memory();
    var asmResult = testAssembler.assemble(asmIn,tempMemory);
    if(asmResult !== "Success")
    {
        //alert("Does not compile!");
        document.getElementById("warningShareAlert").innerHTML = "<strong>Warning!</strong> Your program does not compile";
        $("#warningShareAlert").show();
		return;
    }

	var request = new XMLHttpRequest();
	request.open("POST","ajax/store.php",false);
	request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	var encodedProgram = encodeURIComponent(asmIn);
	var encodedName	= encodeURIComponent(programName);
	var requestContent = "program="+encodedProgram+"&name="+encodedName+"&private="+sharePrivate;
	request.send(requestContent);
	alert(request.responseText);
}
</script>
<script src="jquery.numberedtextarea.js"></script>
<script>
$('textarea').numberedtextarea({
  //https://www.jqueryscript.net/form/jQuery-Plugin-To-Display-Line-Numbers-In-Textarea-numberedTextarea.html
  color:        null,   // Font color
  borderColor:	null,   // Border color
  class:        null,   // Add class to the 'numberedtextarea-wrapper'
  allowTabChar: false,  // If true Tab key creates indentation
});
</script>

<script>
$(document).ready(function(){
	//https://stackoverflow.com/questions/6501043/limit-number-of-lines-in-textarea-and-display-line-count-using-jquery
    var lines = 8191;
    
    $('textarea').keydown(function(e) {
        
        newLines = $(this).val().split("\n").length;
        
        if(e.keyCode == 13 && newLines >= lines) {
            return false;
        }
        else {
        }
    });

});
</script>

<script>
function memInit(memory)
{
	var currentDiv = document.getElementById("memLocation");
	var mem = "";
	for(var i = 0; i < 8192; i++)
	{
		mem = "memLocation"+i;
		document.getElementById(mem).innerHTML = ("00"+memory.memArr[i].toString(16)).substr(-2);
	}
}
function doAssemble()
{
	for(var i = 0; i < 8191; i++)
	{
		testMemory.memArr[i] = 0;
	}
	var asmIn = document.getElementById("codeArea").value;
	var asmResult = testAssembler.assemble(asmIn,testMemory);
	if(asmResult !== "Success")
	{
		alert(asmResult);
		return;
	}
	memInit(testMemory);
	testCPU.pc = 0;
	wrapperDrawScreen();
	for(var i = 0; i < 8; i++)
	{
		testStack.pop();
	}
	updateStackDebugger(testStack.stackObject);
	updatePCDebugger(testCPU.pc);
}

function updateStackDebugger(stack)
{
	var element = "";
	for(var i = 0; i < 8; i++)
	{
		element = "stack"+i;
		//console.log(element+" "+stack[i]);
		var stackelement = stack[i];
		if(stackelement < 0)
		{
			stackelement = 256 + stackelement;
		}
		document.getElementById(element).innerHTML = ("00"+stackelement.toString(16)).substr(-2);
	}
}

function updatePCDebugger(pc)
{
	document.getElementById("pc").innerHTML = ("0000"+pc.toString(16)).substr(-4);
}

function updateMemoryDebugger(location)
{
	mem = "memLocation"+location;
	document.getElementById(mem).innerHTML = ("00"+testMemory.memArr[location].toString(16)).substr(-2);
}

function doStep()
{
	var CPUaction = testCPU.step();
	if(CPUaction == -2)
	{
		console.log("CPU step error");
		return;
	}
	if(CPUaction > -1)
	{
		updateMemoryDebugger(CPUaction);
	}
	if(CPUaction > 6990)
	{
		wrapperDrawScreen();
	}
	updateStackDebugger(testStack.stackObject);
	updatePCDebugger(testCPU.pc);
}

function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

async function doRun()
{
	runState = runState ^ 1;
	if(runState == 0)
	{
		document.getElementById("runButton").innerHTML = "<i class=\"fa fa-play\" aria-hidden=\"true\"></i> Run";
	}
	else
	{
		document.getElementById("runButton").innerHTML = "<i class=\"fa fa-stop\" aria-hidden=\"true\"></i> Stop";
	}
	while(runState == 1)
	{
		await doStep();
		await sleep(10);
	}
}

function drawScreen(screen,memory)
{
	var red = 0;
	var green = 0;
	var blue = 0;
	var memIndex = 0;
	for(var i = 0; i < 30; i++)
	{
		for(var j = 0; j < 40; j++)
		{
			red = (memory[memIndex] >> 5) * 32;
			green = ( (memory[memIndex] & 28) >> 2) * 32;
			blue = (memory[memIndex] & 3) * 64;
			memIndex++;
			screen.fillStyle = "rgb("+red+","+green+","+blue+")";
			screen.fillRect(10*j,10*i,10,10);
		}
	}

}

function wrapperDrawScreen()
{
	var c = document.getElementById("myCanvas");
	var ctx = c.getContext("2d");

	//var colors = Array.from({length: 40*30}, () => Math.floor(Math.random() * 255));
	drawScreen(ctx,testMemory.memArr.subarray(6991,8192));	
}
</script>

<script>
function populateCode(){
	var getString = window.location.search.substr(1);
	if( getString  !== "")
	{
		if(getString.indexOf("phash=") == 0)
		{
			var phash = getString.split("=")[1];
			var request = new XMLHttpRequest();
			var url = "ajax/load.php?phash="+phash;
			request.open("GET",url,false);
			request.send();
			if(request.responseText.length != 0)
			{
				document.getElementById("codeArea").value = request.responseText;
			}
		}
	}
}

function pageInit()
{
	populateCode();
	var currentDiv = document.getElementById("memoryArea");

	var newRow;
	var rowmodifier = 1;

	for(var i = 0; i < 8192; i++)
	{
		if((i%8) == 0)
		{
			if(i != 0)
			{
				currentDiv.appendChild(newRow);
			}
			newRow = document.createElement("div");
			newRow.className = "row no-gutters";
			rowmodifier = (rowmodifier+1)%2;

			var line = document.createElement("div");
			line.className =  "col";
			line.innerHTML = ("0000"+i.toString(16)).substr(-4);
			newRow.appendChild(line);

		}
		var newColumn = document.createElement("div");
		newColumn.id = "memLocation"+i;
		var newClass = "col text-center mem-"+((i+rowmodifier)%2);
		newColumn.className = newClass;
		newColumn.innerHTML = "00";//("00"+i.toString(16)).substr(-2);
		newRow.appendChild(newColumn)
	}
	currentDiv.appendChild(newRow); 
}
</script>

</body>
</html>

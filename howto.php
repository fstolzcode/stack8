<?php
session_start();
if(isset($_SESSION["uid"]) && isset($_SESSION["CREATED"]))
{
    if( (time() - $_SESSION["CREATED"]) > 3600 )
    {
        //Session too old
        session_unset();
        session_destroy();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="#">Stack8</a>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="#">Manual</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="community.php">Shared Programs</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="mystack.php">My Stack</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
	<div class="row p-2">
	<div class="col">
    <div class="card">
	<div class="card-block">
	<h1 class="display-4">How to use Stack8</h2>
	<p class="lead">
	A primitive and ever so limited stack machine, yet it can compute everything! Learn how to utilize its power.
	</p>
	<p>
	Stack8 emulates a primitive stack machine in your browser. By itself it cannot do much. It can move items into its
	internal stack and can do only three operations on them. But that should not stop you from trying your luck and creating a 
	fully funtional program. In fact, it is possible to do just about any computation possible with Stack8 that fits into its limited storage.
	You are able to go beyond the limitations imposed by the instruction set and synthesize additional instructions for just about anything.
	Substraction, exclusive-or, you name it. But before you dive into all of this, you should learn the basics here.
	</p>
	<p>
	<h4>Specifications</h4>
	<table class="table">
  <thead>
    <tr>
      <th>Machine</th>
      <th>Word length</th>
      <th>Instructions</th>
      <th>Speed</th>
	  <th>RAM</th>
      <th>Storage</th>
      <th>Graphics</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Stack8</th>
      <td>8 Bit</td>
      <td>8</td>
      <td>A few hertz</td>
	  <td>8 KB</td>
      <td>0 B</td>
      <td>4:3 8-bit color</td>
    </tr>
    <tr>
      <th scope="row">Typical PC</th>
      <td>64 Bit</td>
      <td>~1000</td>
      <td>2-4 GHz</td>
      <td>~8 GB</td>
      <td>1 TB</td>
      <td>Dedicated GPU</td>
    </tr>
  </tbody>
	</table>
	</p>
	<p>
	<h4> How to write a program? </h4><br>
	First of all, you should know all existing instructions. So here is a quick list:<br>
	<code>push OPERAND</code> pushes a value at the specified address OPERAND on the stack<br>
	<code>pushi OPERAND</code> pushes the value on the stack, that the pointer at address OPERAND points to<br>
	<code>pop OPERAND</code> stores a value at the specified address OPERAND in memory<br>
	<code>popi OPERAND</code> stores a value at the address the pointer stored in OPERAND points to<br>
	<code>push OPERAND</code> pushes a value at the specified address on the stack<br>
	<code>add</code> adds the two values at the top of the stack together and pushes the result<br>
	<code>nand</code> nands the two values at the top of the stack together and pushes the result<br>
	<code>jmple OPERAND</code> jumps to the operand if the first stack element is less or equal to the second stack elemend<br>
	<code>jmpule OPERAND</code> same as above, but performs a unsigned comparison<br><br>
	But what is in operand? Let's see:<br>
	<code>SomeName</code> for example. So it can point to a so called label<br>
	<code>SomeName+X</code> for example. It points to a label plus an offset<br>
	<code>#Address</code> for example. This points to the address specified behind #<br><br>
	Wait, how do I define a label?<br>
	<code>SomeName:</code> in its own line, refers to the address of the instruction or data below it<br><br>
	How can tell the Assembler what is data?<br>
	<code>db X[,Y]</code> does the job. You can define up to two 8 bit values with one <code>db</code> statement<br>
	<code>dp X</code> let's you store a pointer at this location. A pointer is 13-bits wide.<br><br>
	So what is the memory layout?<br>
	You can use all memory from address 0 to 8191 for your program. The machine starts executing at 0. Everything above 6990 gets displayed on the screen.<br><br>
	I think, I understood the basic idea. But how do I make my program dynamic?<br>
	Data is code, and code is data. You can freely write to memory. For example, you stored a pointer somewhere. You can push the lower byte on the stack and increment it. After popping it to the same location, the stored pointer was advanced. You can use this to write compact loops.<br><br>
	I want to draw some fancy graphics, how can I do it?<br>
	Pixel 0 is located at address 6991, and pixel 1200 is located at 8191. You have 40px:30px to work with. The display uses the <code>RRRGGGBB</code> format for color.<br><br>
	But I want to subtract something?<br>
	Well, you have to think of a way to use only <code>add</code> and <code>nand</code> to do subtraction. It is doable. In fact, with a bit of thinking you can do anything you want<br><br>
	</p>
	</div>
	</div>
	</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>

</body>
</html>

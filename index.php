<?php
//MathApp/index.php
//Math Web App by Casey Yardley
//Copyright 2013 yardleyc.com


//Autoload classes
function __autoload($class_name) {
	include $class_name . '.php';
}

//Init Session
session_start();
$mathapp = NULL;
$v;
if(!isset($_SESSION['mathapp'])){
	$mathapp = new MathApp();
}else{
	date_default_timezone_set('UTC');
	$mathapp = unserialize($_SESSION['mathapp']);
	if(isset($_GET['answer'])){
		if($_GET['answer']!="")$mathapp->submitAnswer($_GET['answer']);
	}
	if(isset($_GET['selectFirstNDigits']))$mathapp->setFirstNDigits($_GET['selectFirstNDigits']);
	if(isset($_GET['selectSecondNDigits']))$mathapp->setSecondNDigits($_GET['selectSecondNDigits']);	
	if(isset($_GET['selectOperator']))$mathapp->setOperator($_GET['selectOperator']);
}

//Page Layout
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
<HEAD>
<TITLE>Math App</TITLE>
</HEAD>
<BODY>

<h1>Math App</h1>

<form name="questionForm" action="index.php" method="get">
<span id="firstn"><?php echo $mathapp->getFirstN(); ?></span>
&nbsp;
<span id="operator"><?php echo $mathapp->getOperator(); ?></span>
&nbsp;
<span id="secondn"><?php echo $mathapp->getSecondN(); ?></span>
=&nbsp;<input id="answerbox" name="answer" type="text" /><br/>
<input type="submit" value="Submit" /><br/>
</form>

<hr>
Correct:&nbsp;<?php echo $mathapp->getCorrect();?> 
&nbsp;|&nbsp;
Incorrect:&nbsp;<?php echo $mathapp->getIncorrect();?>

<form name="settingsForm" action="index.php" method="get">
<label for="selectFirstNDigits">First Digits</label>
<select name="selectFirstNDigits">
<?php for($i=1; $i<=$mathapp->getMaxDigits(); $i++){
	if($i==$mathapp->getFirstNDigits()) echo "<option selected=\"true\">";
	else echo "<option>";
	echo $i . "</option>";
} ?>
</select>
<label for="selectOperator">Operator</label>
<select name="selectOperator">
<?php foreach($mathapp->getOperators() as $op){ 
	if($op==$mathapp->getOperator()) echo "<option selected=\"true\">";
	else echo "<option>";
	echo $op . "</option>";
} ?>
</select>
<label for="selectSecondNDigits">Second Digits</label>
<select name="selectSecondNDigits">
<?php for($i=1; $i<=$mathapp->getMaxDigits(); $i++){ 
	if($i==$mathapp->getSecondNDigits()) echo "<option selected=\"true\">";
	else echo "<option>";
	echo $i . "</option>";
} ?>
</select>
<input type="submit" value="Apply" />
</form>
<hr>
<small><a href="index.php?about">About</a></small>
<?php 
if(isset($_GET['about'])){
	echo "<hr><b>Math Web App</b> by Casey Yardley<br/>";
	echo "Copyright 2013 yardleyc.com<br/>";
	echo "<small>Version " . $mathapp->getVersion() . "</small>";
	echo "<p><i>An equation generator built for mental math practice.</i></p>";
	echo "Supports addition, subtraction, and multiplication up to 14 by 14 digits.<br/>";
	echo "Supports division up to 9 by 9 digits, and evaluates answers to at least two significant decimal places.<br/></br>";
}
//Auto-focus the textbox ?>
<script>window.onload = function() { document.getElementById("answerbox").focus();};</script>
</BODY>
</HTML>

<?php
//Save Session
$_SESSION['mathapp'] = serialize($mathapp);
?>
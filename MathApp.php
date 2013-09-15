<?php
//MathApp/MathApp.php
//Math Web App by Casey Yardley
//Copyright 2013 yardleyc.com

class MathApp{
//PUBLIC INTERFACE

	//Constructor
	public function __construct(){
		date_default_timezone_set('UTC');
		//Initalize Application Data
		$this->firstNDigits = 2;
		$this->firstN = $this->getNewFirstN();
		$this->secondNDigits = 2;
		$this->secondN = $this->getNewSecondN();
		$this->operator = "+";
		$this->correct = 0;
		$this->incorrect = 0;
	}
	
	//Set Options
	public function setFirstNDigits($d){
		if($d<1 || $d>$this->MAX_DIGITS)return;
		$this->firstNDigits = $d;
		$this->getNewFirstN();
	}
	public function setSecondNDigits($d){
		if($d<1 || $d>$this->MAX_DIGITS)return;
		$this->secondNDigits = $d;
		$this->getNewSecondN();
	}
	public function setOperator($op){
		if(!in_array($op, $this->OPERATORS)) return;
		$this->operator = $op;
		if($op=="/"){
			if($this->firstNDigits>9){
				$this->firstNDigits=9;
				$this->getNewFirstN();
			}
			if($this->secondNDigits>9){
				$this->secondNDigits=9;
				$this->getNewSecondN();
			}
		}
	}
		
	//Getters
	public function getVersion(){ return $this->VERSION; }
	public function getMaxDigits(){ return $this->MAX_DIGITS; }
	public function getOperators(){ return $this->OPERATORS; }
	public function getFirstN(){ return $this->firstN; }
	public function getFirstNDigits(){ return $this->firstNDigits; }
	public function getSecondN(){ return $this->secondN; }
	public function getSecondNDigits(){ return $this->secondNDigits; }
	public function getOperator(){ return $this->operator; }
	public function getCorrect(){ return $this->correct; }
	public function getIncorrect(){ return $this->incorrect; }
	
  //Public Helper Functions
	
	//Set and Return a new FirstN
	public function getNewFirstN(){
		$this->firstN = $this->getRandomInteger($this->firstNDigits);
		return $this->firstN;
	}
	
	//Set and Return a new SecondN
	public function getNewSecondN(){
		$this->secondN = $this->getRandomInteger($this->secondNDigits);
		if($this->secondN->equals(new Math_BigInteger(0))) $this->secondN = new Math_BigInteger(1);
		return $this->secondN;
	}
	
	//Submit the answer and update the interface
	public function submitAnswer($answer){
		if($this->evaluate($answer)){
			$this->correct++;
			$this->firstN = $this->getNewFirstN();
			$this->secondN = $this->getNewSecondN();
		}else{
			$this->incorrect++;
		}
	}
	
//PRIVATE INTERFACE

	//Constants
	private $VERSION = 0.1;
	private $MAX_DIGITS = 14;
	private $OPERATORS = array("+", "-", "*", "/");
	private $SIGNIFICANT_DECIMALS = 2;
	
	//Application Data
	private $firstN;
	private $firstNDigits;
	private $secondN;
	private $secondNDigits;
	private $operator;
	private $correct;
	private $incorrect;
	
  //Private Helper Functions
		
	//Evaluates the answer
	//Uses Math_BigInteger lGPL library licenced library by Jim Wigginton, Math_BigInteger.php
	private function evaluate($answer){
		$a = new Math_BigInteger($answer);
		$f = $this->firstN;
		$s = $this->secondN;
		switch($this->operator){
			case "+":
				$r = $f->add($s);
				return $a->equals($r);
			case "-":
				$r = $f->subtract($s);
				return $a->equals($r);
			case "*":
				$r = $f->multiply($s);
				return $a->equals($r);
			case "/":
				if($this->secondN==new Math_BigInteger(0)){ $this->secondN = new Math_BigInteger(1); return false; }
				$fs = $f->toString();
				$ss = $s->toString();
				return $this->matchDecimal(((int)$fs / (int)$ss), $answer, $this->SIGNIFICANT_DECIMALS);
			default:
				$this->operator = "+";
				return false;
		}
	}
	
	//Returns true if $input matches $answer up to $decimalsToMatch decimal places
    private function matchDecimal($input, $answer, $decimalsToMatch){
        $sb = "/";
        $decimal = false;
        foreach(str_split($input) as $c){
        	if(!$decimal || $decimalsToMatch>0){
        		if($decimal) $decimalsToMatch--;
        		if($c=='.'){
        			$decimal = true;
        			$sb .= "\\.";
        		}else{
        			$sb .= $c;
        		}
        	}else{
        		  $sb .= $c;
        		  $sb .= "?+"; //match is optional
        	}
        }
        $sb .= "/";
    	return 0 != preg_match($sb, $answer);
      }
      
      //Returns a random integer with $digits number of digits
	  //Uses Math_BigInteger lGPL library licenced library by Jim Wigginton, Math_BigInteger.php
    private function getRandomInteger($digits){
      	if($digits<1 || $digits > $this->MAX_DIGITS) return -1;
      	$lower=0;
      	$upper=0;
      	if($digits==1){ $lower=0; $upper=9; }
      	if($digits==2){ $lower=10; $upper=99; }
      	if($digits==3){ $lower=100; $upper=999; }
      	if($digits==4){ $lower=1000; $upper=9999; }
      	if($digits==5){ $lower=10000; $upper=99999; }
      	if($digits==6){ $lower=100000; $upper=999999; }
      	if($digits==7){ $lower=1000000; $upper=9999999; }
      	if($digits==8){ $lower=10000000; $upper=99999999; }
      	if($digits==9){ $lower=100000000; $upper=999999999; }
      	if($digits==10){ $lower=1000000000; $upper=9999999999; }
      	if($digits==11){ $lower=10000000000; $upper=99999999999; }
      	if($digits==12){ $lower=100000000000; $upper=999999999999; }
      	if($digits==13){ $lower=1000000000000; $upper=9999999999999; }
      	if($digits==14){ $lower=10000000000000; $upper=99999999999999; }
      	$r = new Math_BigInteger();
      	return $r->random(new Math_BigInteger($lower), new Math_BigInteger($upper));
      }
      
}
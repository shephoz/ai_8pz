<?php

class EightPz{
	private $nums;

	public function __construct($nums=[0,1,2,3,4,5,6,7,8]){
		$this->nums = $nums;
	}

	public static function equals(EightPz $a, EightPz $b){
		return $a->toString() == $b->toString();
	}

	public function getNums(){
		return $this->nums;
	}


	//----------------------------------------------------


	public function toString(){
		return implode("",$this->nums);
	}


	//----------------------------------------------------


	public function shuffle($howmany){
		$pz = $this;
		for($i=0;$i<$howmany;$i++){
			$move = $pz->move();
			$pz = $move[rand(0,count($move)-1)];
		}
		$this->nums = $pz->nums;
	}


	private function findNum($num){
		return array_search($num, $this->nums);
	}


	//----------------------------------------------------


	private function listDir(){
		switch($this->findNum(0)){
			case 0: return [1,3]; break;
			case 1: return [-1,1,3]; break;
			case 2: return [-1,3]; break;
			case 3: return [-3,1,3]; break;
			case 4: return [-3,-1,1,3]; break;
			case 5: return [-3,-1,3]; break;
			case 6: return [-3,1]; break;
			case 7: return [-3,-1,1]; break;
			case 8: return [-3,-1]; break;
		}
	}

	public function move(){
		$zero = $this->findNum(0);
		return array_map(function($dir) use($zero){
			return $this->makeSwapped($zero,$zero+$dir);
		},$this->listDir());
	}

	private function makeSwapped($swap1,$swap2){
		$nums = $this->nums;
		$tmp = $nums[$swap1];
		$nums[$swap1] = $nums[$swap2];
		$nums[$swap2] = $tmp;
		return new EightPz($nums);
	}

	//----------------------------------------------------


	public function cost($costFunc){
		switch ($costFunc) {
			case "howmanyWrong":
				return $this->howmanyWrong();
				break;
			case "manhattan" :
				return $this->manhattan();
				break;
		}
	}

	private function howmanyWrong(){
		$r = 0;
		$nums = $this->nums;
		for($i=0;$i<9;$i++){
			if($nums[$i] != $i) $r++;
		}
		return $r;
	}

	private function manhattan(){
		$r = 0;
		foreach($this->nums as $ideal => $real){
			$mh = abs(floor($ideal/3) - floor($real/3)) + abs($ideal%3 - $real%3);
			$r += $mh;
		}
		return $r;
	}

	public function isGoal(){
		return $this->howmanyWrong() == 0;
	}

}

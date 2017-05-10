<?php

class EightPz{
	private $numbers;
	private $howToGetCost;
	private $parent = null;

	public function __construct($numbers=[0,1,2,3,4,5,6,7,8],$howToGetCost="howmanyWrong"){
		$this->numbers = $numbers; 
		$this->howToGetCost = $howToGetCost; 
	}

	public function setHowToGetCost($howToGetCost){
		$this->howToGetCost = $howToGetCost; 
	}
	public function shuffle($howmany){
		$pz = $this;
		for($i=0;$i<$howmany;$i++){
			$move = $pz->move();
			$pz = $move[rand(0,count($move)-1)];
		}
		$this->numbers = $pz->numbers;
	}

	public function show(){
		$r = "";
		for($i=0;$i<9;$i++){
			$r .= $this->numbers[$i];
			if($i%3 == 2) $r .= "<br />"; 
		}
		return $r;
	}

	private function findNum($num){
		return array_search($num, $this->numbers);
	}

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
		$r = [];
		$zero = $this->findNum(0);
		foreach($this->listDir() as $dir){
			$swaped = $this->swap($zero,$zero+$dir);
			$same = true;
			for($i=0;$i<9;$i++){
				if($this->numbers[$i] != $swaped->numbers[$i]){
					$same = false;
				}
			}
			if(!$same){
				$r[] = $swaped;
			}
		}
		return $r;
	} 

	private function swap($swap1,$swap2){
		$numbers = $this->numbers;
		$tmp = $numbers[$swap1];
		$numbers[$swap1] = $numbers[$swap2];
		$numbers[$swap2] = $tmp;
		return new EightPz($numbers,$this->howToGetCost);
	}

	public function cost(){
		switch ($this->howToGetCost) {
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
		$numbers = $this->numbers;
		for($i=0;$i<9;$i++){
			if($numbers[$i] != $i) $r++;
		}
		return $r;
	}

	private function manhattan(){
		$r = 0;
		foreach($this->numbers as $ideal => $real){
			$mh = abs(floor($ideal/3) - floor($real/3)) + abs($ideal%3 - $real%3);
			$r += $mh;
		}
		return $r;
	}

	public function setParent($parent){
		$this->parent = $parent;
	}

	public function trail(){
		?>
		<div style='margin:10px;'>
			<div style="display:inline-block;">
				<?php echo $this->show();?>
			</div>
			<div style="display:inline-block;">
				cost:<?php echo $this->cost();?><br />
			</div>
		</div>
		<p>&uarr;</p>
		<?php
		$parent = $this->parent;
		if($parent !== null){
			$parent->trail();
		}else{
		echo "start";	
		}
	}


}


<?php

class Solver{
	private $start = null;
	private $open = [];
	private $opened = [];
	private $costFunc = "manhattan";
	//private $costFunc = "howmanyWrong";
	private $isIda = true;
	//private $isIda = false;

	private $time = 0;
	private $memory = 0;
	private $isSolved = false;

	private $cutoff = 1;
	private $openedOrder = 0;

	public function __construct(EightPz $start){
		$this->start = $start;
		$this->pushToOpen($start);
	}

	public function open(){
		$best_index = null;
		$best_item  = null;
		$best_cost  = null;

		if($this->isIda){
			// like a stack
			$best_index = count($this->open) - 1;
			echo "\n--- cutoff : ".$this->cutoff." ---\n";
			if($best_index < 0){
				echo "failed in this cutoff\n";
				$this->cutoff++;
				$this->pushToOpen($this->start);
				$this->opened = [];
				$this->memory = 0;
				if($cutoff > 100) fgets(STDIN);
				return;
			}
			$best_item  = $this->open[$best_index];
			$best_cost = $best_item->cost($this->costFunc) + $best_item->getHowmanyMoved();
		}else{
			foreach($this->open as $index => $item){
				$cost = $item->cost($this->costFunc) +  $item->getHowmanyMoved();
				if($best_item === null || $best_cost > $cost){
					$best_index = $index;
					$best_item  = $item;
					$best_cost  = $cost;
				}
			}
			if($best_item === null){
				echo "failed\n";
				$this->isSolved = true;
				return;
			}
		}


		echo "\n--- the best one in open is ... ---\n";
		$best_item->display();
		$this->popFromOpen($best_index);

		$best_item->setWhenOpened($this->openedOrder);
		$this->openedOrder++;

		if($best_item->isGoal()){
			echo "--- this is goal ---\n";
			$this->isSolved = true;
			$best_item->trail($this->costFunc);
			return;
		}

		$this->time++;

		echo "\n--- added to open ---\n";
		foreach($best_item->move() as $moved){
			$this->pushToOpen($moved);
			$moved->display($this->costFunc);
		}
		// echo "\n--- open items is ... ---\n";
		// foreach($this->open as $item){
		// 	$item->display($this->costFunc);
		// }

	}


	private function pushToOpen($pushing){
		$duplicating = false;
		foreach($this->open as $comparing){
			if($pushing->toString() == $comparing->toString())
				$duplicating = true;
		} //ここいるか？
		foreach($this->opened as $comparing){
			if($pushing->toString() == $comparing->toString())
				$duplicating = true;
		} //ここいるか？

		$cost = $pushing->cost($this->costFunc) +  $pushing->getHowmanyMoved();
		echo "cost::".$pushing->cost($this->costFunc)." + ". $pushing->getHowmanyMoved()."\n";
		if(
			( !$this->isIda || $cost <= $this->cutoff)
			 && !$duplicating
		){
			$this->open[] = $pushing;
			if($this->memory < count($this->open)) $this->memory = count($this->open);
		}
	}

	private function popFromOpen($index){
		$this->opened[] = $this->open[$index];
		array_splice($this->open,$index);
	}

	public function isSolved(){
		return $this->isSolved;
	}

	public function evaluate(){
		echo "time   : ".$this->time."\n";
		echo "memory : ".$this->memory."\n";
	}

}

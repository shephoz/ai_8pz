<?php

class Solver{
	private $open = [];
	private $opened = [];
	private $costFunc = "manhattan";
	//private $costFunc = "howmanyWrong";

	private $time = 0;
	private $memory = 0;
	private $isSolved = false;

	public function __construct(EightPz $start){
		$this->pushToOpen($start);
	}

	public function open(){
		$best_index = null;
		$best_item  = null;
		$best_cost  = null;
		foreach($this->open as $index => $item){
			$cost = $item->cost($this->costFunc);
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

		echo "\n--- the best one in open is ... ---\n";
		$best_item->display();
		$this->popFromOpen($best_index);
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

		if(!$duplicating){
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

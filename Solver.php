<?php

class Solver{
	private $open = [];
	private $costFunc = "howmanyWrong";

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

		echo "\n--- the best one in open is ... ---\n";
		$best_item->display();
		$this->popFromOpen($best_index);
		if($best_item->isGoal()){
			echo "--- this is goal ---\n";
			$this->isSolved = true;
			$best_item->trail($this->costFunc);
			return;
		}

		echo "\n--- added to open ---\n";
		foreach($best_item->move() as $moved){
			$this->pushToOpen($moved);
			$moved->display($this->costFunc);
		}
		echo "\n--- open items is ... ---\n";
		foreach($this->open as $item){
			$item->display($this->costFunc);
		}

	}


	private function pushToOpen($pushing){
		$duplicating = false;
		// foreach($this->open as $comparing){
		// 	if($pushing->toString() == $comparing->toString())
		// 		$duplicating = true;
		// } //ここいるか？
	 	if(!$duplicating){
			$this->open[] = $pushing;
		}
	}

	private function popFromOpen($index){
		array_splice($this->open,$index);
	}

	public function isSolved(){
		return $this->isSolved;
	}

}

<?php
require_once  'Image/GraphViz.php';

class Solver{
	private $start = null;
	private $costFunc;
	private $isIda;

	private $open = [];
	private $opened = [];

	private $goal  = null;

	private $totalTime = 0;
	private $time      = 0;
	private $memory    = 0;
	private $isSolved  = false;

	private $cutoff = 5;
	private $openedOrder = 1;

	public function __construct(EightPz $start, $costFunc, $isIda){
		$start_node = new SolverNode($start,$this,0);
		$this->start = $start_node;
		$this->pushToOpen($start_node);
		$this->costFunc = $costFunc;
		$this->isIda    = $isIda;
	}

	public function getCostFunc(){
		return $this->costFunc;
	}


	public function run(){
		$this->start->display();
		while(!$this->isSolved()){
			echo "\n--- loop ".$this->totalTime."---\n";
			$this->open();
			$this->totalTime++;
			if($this->totalTime > 100) break; //fgets(STDIN);
		}
	}


	public function open(){
		$best_index = null;
		$best_node  = null;
		$best_f     = null;

		if($this->isIda){
			// like a stack
			$best_index = count($this->open) - 1;
			if($best_index < 0){
				echo "\nfailed in this cutoff\n";
				echo "--- cutoff : ".($this->cutoff)." -> ".($this->cutoff + 1)." ---\n";
				$this->cutoff++;
				$this->opened = [];
				$this->memory = 0;

				// __constructor
				$this->pushToOpen($this->start);

				if($this->cutoff > 100) fgets(STDIN);
				return;
			}
			$best_node  = $this->open[$best_index];
			$best_f     = $best_node->calcF($this->costFunc);
		}else{
			foreach($this->open as $index => $node){
				$f = $node->calcF($this->costFunc);
				if($best_node === null || $best_f > $f){
					$best_index = $index;
					$best_node  = $node;
					$best_f     = $f;
				}
			}
			if($best_node === null){
				echo "failed\n";
				$this->isSolved = true;
				return;
			}
		}

		echo "\n--- now, open this node (at order ".$this->openedOrder.") ---\n";
		$best_node->display();
		$this->popFromOpen($best_index);

		if($best_node->isGoal()){
			echo "--- this is goal ---\n";
			$this->isSolved = true;
			return;
		}

		$this->time++;

		$best_node->extract($this->openedOrder);
		$this->openedOrder++;

		echo "\n--- There're ".count($this->open)." items to open. They are ... ---\n";
		SolverNode::displayList($this->open);

		// echo "\n--- There're ".count($this->opened)." items already opened. They are ... ---\n";
		// SolverNode::displayList($this->opened);

	}


	public function pushToOpen($pushing){

		$duplicating = false;
		foreach($this->open as $comparing){
			if(SolverNode::equals($pushing,$comparing)){
				$duplicating = true;
			}
		} //ここいるか？
		foreach($this->opened as $comparing){
			if(SolverNode::equals($pushing,$comparing)){
				$duplicating = true;
			}
		} //ここいるか？

		if($this->isIda){
			$f = $pushing->calcF();
			if(!$duplicating && $f <= $this->cutoff){
				$this->open[] = $pushing;
				if($this->memory < count($this->open)) $this->memory = count($this->open);
			}else{
				$pushing->removePointer();
			}
		}else{
			if(!$duplicating){
				$this->open[] = $pushing;
				if($this->memory < count($this->open)) $this->memory = count($this->open);
			}else{
				$pushing->removePointer();
			}
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

	public function makeTree(){
		$g = new Image_GraphViz();
		$this->start->makeTree($g);
		file_put_contents("tree.png",$g->fetch('png'));
	}

}

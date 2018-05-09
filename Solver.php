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
	private $time      = 1;
	private $memory    = 0;

	private $cutoff = 5;
	private $timeLimit = 10000;

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


	public function run($showMessage=true){
		if(!$showMessage) ob_start();
		$this->start->display();
		while(!$this->isSolved()){
			echo "\n--- loop ".$this->totalTime."---\n";
			$this->open();
			$this->totalTime++;
			if($this->totalTime > $this->timeLimit) break;
		}
		if(!$showMessage) ob_end_clean();
	}


	public function open(){
		$best_index = null;
		$best_node  = null;
		$best_f     = null;

		foreach($this->open as $index => $node){
			$f = $node->calcF($this->costFunc);
			if($best_node === null || $f <= $best_f){
			// $fと$best_fの比較を"<="にするとスタック、"<"にするとキュー
				$best_index = $index;
				$best_node  = $node;
				$best_f     = $f;
			}
		}
		if($best_node === null){
			if($this->isIda){
				echo "\nfailed in this cutoff\n";
				echo "--- cutoff : ".($this->cutoff)." -> ".($this->cutoff + 1)." ---\n";
				$this->cutoff++;
				$this->opened = [];
				$this->memory = 0;
				// __constructor
				$this->pushToOpen($this->start);
			}else{
				echo "failed\n";
				$this->isSolved = true;
			}
			return;
		}

		echo "\n--- now, open this node (at order ".$this->time.") ---\n";
		$best_node->display();
		$this->popFromOpen($best_index);

		if($best_node->isGoal()){
			echo "--- this is goal ---\n";
			$this->goal = $best_node;
			return;
		}
		$best_node->extract($this->time);
		$this->time++;

		echo "\n--- There're ".count($this->open)." items to open. They are ... ---\n";
		SolverNode::displayList($this->open);
	}


	public function pushToOpen($pushing){

		$duplicating = false;
		foreach($this->open as $comparing){
			if(SolverNode::equals($pushing,$comparing)){
				$duplicating = true;
			}
		} //ここいるか？
		// foreach($this->opened as $comparing){
		// 	if(SolverNode::equals($pushing,$comparing)){
		// 		$duplicating = true;
		// 	}
		// } //ここいるか？

		if($this->isIda){
			$f = $pushing->calcF();
			if(!$duplicating && $f <= $this->cutoff){
				$this->open[] = $pushing;
				$this->memory = max($this->memory, count($this->open));
			}else{
				$pushing->removePointer();
			}
		}else{
			if(!$duplicating){
				$this->open[] = $pushing;
				$this->memory = max($this->memory, count($this->open));
			}else{
				$pushing->removePointer();
			}
		}
	}

	private function popFromOpen($index){
		$this->opened[] = $this->open[$index];
		array_splice($this->open,$index,1);
	}

	public function isSolved(){
		return $this->goal !== null;
	}

	public function evaluate(){
		echo "time   : ".$this->time."\n";
		echo "memory : ".$this->memory."\n";
	}

	public function makeTree($filename){
		$g = new Image_GraphViz();
		$this->start->makeTree($g);
		file_put_contents($filename.".png",$g->fetch('png'));
	}

	public function trail($filename){
		$g = new Image_GraphViz();
		$this->goal->trail($g);
		file_put_contents($filename.".png",$g->fetch('png'));
	}
}

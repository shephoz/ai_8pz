<?php

require_once  "Solver.php";
require_once "EightPz.php";

class SolverNode{
	private $solver = null;
	private $puzzle = null;

	private $parent = null;
	private $children = [];

	private $howmanyMoved = null;
	private $whenOpened   = [];

	private $viewString;
	private $draw = true;

	public function __construct(EightPz $puzzle, Solver $solver, int $howmanyMoved){
		$this->puzzle = $puzzle;
		$this->solver = $solver;
		$this->howmanyMoved = $howmanyMoved;
	}

	public static function equals(SolverNode $a, SolverNode $b){
		return EightPz::equals($a->puzzle,$b->puzzle);
	}


	// ----------------------------------------------------

	public function toViewString($showMeta = false){
		$r = "";
		for($i=0;$i<9;$i++){
			$nums = $this->puzzle->getNums();
			$r .= $nums[$i]." ";
			if($i == 8 && $showMeta)   $r .= "(".implode(",",$this->whenOpened).")";
			if($i%3 == 2) $r .= "\n";
		}
		if($showMeta){
			$cost  = $this->puzzle->cost($this->solver->getCostFunc());
			$moved = $this->howmanyMoved;
			$r .= "f = ".$cost." + ".$moved."\n";
			$r .= "  = ".($cost+$moved)."\n";
		}
		return $r;
	}

	public function display($showMeta = false){
		echo $this->toViewString();
	}

	public static function displayList($list){
		$lines = ["| ","| ","| ","| ","| ","| "];
		foreach($list as $index => $sn){
			$lines[0] .= $index.".";
			for($i=0;$i<9;$i++){
				$nums = $sn->puzzle->getNums();
				$lines[($i/3 + 1)] .= $nums[$i]." ";
				if($i == 8) $lines[3] .= "(".implode(",",$sn->whenOpened).")";
			}
			$cost  = $sn->puzzle->cost($sn->solver->getCostFunc());
			$moved = $sn->howmanyMoved;
			$lines[4] .= "f = ".$cost." + ".$moved;
			$lines[5] .= "  = ".($cost+$moved);

			$pad_len = max(array_map(function($line){
				return strlen($line);
			},$lines));
			$lines = array_map(function($line) use($pad_len){
				return str_pad($line,$pad_len)." | ";
			},$lines);
		}
		echo implode("\n",$lines)."\n";
	}


	// ----------------------------------------------------


	private function setChild(SolverNode $child){
		$this->children[] = $child;
		$child->parent = $this;
		$this->draw = true;
	}

	public function removePointer(){
		$removing_index = null;
		$parent = $this->parent;
		if($parent == null) return;

		foreach($parent->children as $i => $child){
			if(SolverNode::equals($child,$this)) $removing_index = $i;
		}
		if($removing_index)  array_splice($parent->children,$removing_index,1);
		$this->parent = null;
		$this->draw = false;
	}

	public function getHowmanyMoved(){
		return $this->howmanyMoved;
	}
	public function setWhenOpened($val){
		$this->whenOpened[] = $val;
	}


	// ----------------------------------------------------

	public function extract($whenOpened){
		$this->setWhenOpened($whenOpened);
		foreach($this->puzzle->move() as $moved){
			$already_exists = null;
			foreach($this->children as $child){
				if(EightPz::equals($moved,$child->puzzle)){
					$already_exists = $child;
				}
			}
			if($already_exists === null){
				if($this->parent === null){
					$child = new SolverNode($moved,$this->solver,$this->howmanyMoved + 1);
					$this->setChild($child);
					$this->solver->pushToOpen($child);
				}else{
					// ２個前と被ってないかチェック
					// （openedの重複チェックと同じことじゃないか？）
					if(true && !EightPz::equals($moved,$this->parent->puzzle)){
						$child = new SolverNode($moved,$this->solver,$this->howmanyMoved + 1);
						$this->setChild($child);
						$this->solver->pushToOpen($child);
					}
				}
			}else{
				$this->solver->pushToOpen($already_exists);
				$this->setChild($already_exists);
			}
		}
	}

	public function calcF(){
		return $this->puzzle->cost($this->solver->getCostFunc()) + $this->getHowmanyMoved();
	}

	public function isGoal(){
		return $this->puzzle->isGoal();
	}

	//----------------------------------------------------

	public function makeTree($g){
		if(!$this->draw) return;
		if($this->viewString !== "") $this->viewString = str_replace("\n","\l",$this->toViewString(true));
		$style = ['shape'=>'box','fontname'=>'MigMix 2M'];
		if($this->isGoal())
			$style = array_merge($style, ['style'=>'filled','fillcolor'=>'red']);
		$g->addNode($this->viewString,$style);
		if($this->parent !== null){
			$g->addEdge([$this->parent->viewString => $this->viewString]);
		}
		foreach($this->children as $child){
			$child->makeTree($g);
		}
	}

	public function trail($g){
		if($this->viewString !== "") $this->viewString = str_replace("\n","\l",$this->toViewString());
		$g->addNode($this->viewString,['shape'=>'box','fontname'=>'MigMix 2M']);
		if($this->parent !== null){
			if($this->parent->viewString !== "") $this->parent->viewString = str_replace("\n","\l",$this->parent->toViewString());
			$g->addEdge([$this->parent->viewString => $this->viewString]);
			$this->parent->trail($g);
		}else{
			$g->addNode("start",['shape'=>'box']);
			$g->addEdge(["start" => $this->viewString]);

		}
	}

}

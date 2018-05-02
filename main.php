<?php
require_once     "EightPz.php";
require_once      "Solver.php";
require_once  "SolverNode.php";

$eightPz = new EightPz(
[
1,5,4,
3,2,8,
6,7,0,
]
);
//$eightPz->shuffle(8);
$eightPz->display();

$solver = new Solver($eightPz);
$i = 1;
while(!$solver->isSolved()){
	echo "\n--- loop ".$i."---\n";
	$solver->open();
	$i++;
	//if($i>1000) break;
	//fgets(STDIN);
}

$solver->evaluate();

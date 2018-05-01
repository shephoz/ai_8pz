<?php
require_once     "EightPz.php";
require_once      "Solver.php";
require_once  "SolverNode.php";

$eightPz = new EightPz([
	0,4,2,
	1,3,5,
	6,7,8,
]);
//$eightPz->shuffle(8);
$eightPz->display();

$solver = new Solver($eightPz);
$i = 1;
while(!$solver->isSolved()){
	echo "\n--- loop ".$i."---\n";
	$solver->open();
	$i++;
	fgets(STDIN);
}

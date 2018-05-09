<?php
require_once  "Image/GraphViz.php";
require_once         "EightPz.php";
require_once          "Solver.php";
require_once      "SolverNode.php";

$eightPz = new EightPz(
//[4,3,2,6,1,5,0,7,8]
);
$eightPz->shuffle(10);

echo json_encode($eightPz->getNums())."\n";

foreach([
	"manhattan-a"   => new Solver($eightPz,"manhattan",false),
	"manhattan-ida" => new Solver($eightPz,"manhattan",true),
	"howmany-a"     => new Solver($eightPz,"howmanyWrong"  ,false),
	"howmany-ida"   => new Solver($eightPz,"howmanyWrong"  ,true),
] as $label => $solver){
	$solver->run(false);
	echo "\n".$label." : ";
	echo $solver->isSolved()? "solved\n":"failed\n";
	$solver->evaluate();
	$solver->makeTree("tree-".$label);
	//$solver->Trail("trail-".$label);
}

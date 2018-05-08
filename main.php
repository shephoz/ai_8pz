<?php
require_once  "Image/GraphViz.php";
require_once         "EightPz.php";
require_once          "Solver.php";
require_once      "SolverNode.php";

$eightPz = new EightPz(
[1,5,4,0,2,8,6,7,3]
);
//$eightPz->shuffle(4);

echo json_encode($eightPz->getNums())."\n";

echo "\nman,a*\n";
$solver = new Solver($eightPz,"manhattan",false);
$solver->run(false);
$solver->evaluate();
$solver->makeTree("manhattan-a");

echo "\nman,ida*\n";
$solver = new Solver($eightPz,"manhattan",true);
$solver->run(false);
$solver->evaluate();
$solver->makeTree("manhattan-ida");

echo "\nhowmany,a*\n";
$solver = new Solver($eightPz,"howmanyWrong",false);
$solver->run(false);
$solver->evaluate();
$solver->makeTree("howmany-a");

echo "\nhowmany,ida*\n";
$solver = new Solver($eightPz,"howmanyWrong",true);
$solver->run(false);
$solver->evaluate();
$solver->makeTree("howmany-ida");

<?php
require_once  "Image/GraphViz.php";
require_once         "EightPz.php";
require_once          "Solver.php";
require_once      "SolverNode.php";

$eightPz = new EightPz(
[
3, 1, 4,
5, 0, 2,
6, 7, 8,
]
);
//$eightPz->shuffle(8);


//$solver = new Solver($eightPz,"manhattan",false);
//$solver = new Solver($eightPz,"manhattan",true);
//$solver = new Solver($eightPz,"howmanyWrong",false);
$solver = new Solver($eightPz,"howmanyWrong",true);

$solver->run();
$solver->evaluate();
$solver->makeTree();

<?php
ini_set("display_errors",1);

function debug($var){
	echo "<textarea>";
	var_dump($var);
	echo "</textarea><br />";
}

require_once "EightPz.php";

$limit = 99;


//$pz = new EightPz([0,4,2,1,3,5,6,7,8]);
//$pz = new EightPz([1,4,2,6,3,5,0,7,8]);
$pz = new EightPz([1,4,0,3,5,2,6,7,8]);
//$pz = new EightPz();$pz->shuffle(8);

echo "<table><tr>";

foreach(["howmanyWrong","manhattan"] as $howToGetCost){
	echo "<td style='vertical-align:top;'>";
	echo "<h1>A*(".$howToGetCost.")</h1>";
	$pz->setHowToGetCost($howToGetCost);
	echo $pz->show();
	echo "<p>cost:".$pz->cost()."</p>";
	aStar($pz);
	echo "</td>";
}
foreach(["howmanyWrong","manhattan"] as $howToGetCost){
	echo "<td style='vertical-align:top;'>";
	echo "<h1>IDA*(".$howToGetCost.")</h1>";
	$pz->setHowToGetCost($howToGetCost);
	echo $pz->show();
	echo "<p>cost:".$pz->cost()."</p>";
	idaStar($pz);
	echo "</td>";
}

echo "</tr></table>";


function aStar($pz){
	global $limit;
	$open = [["pz"=>$pz,"cost"=>0]];
	$depth = 0;
	$howmany_opened = 0;
	$max_memory_spend = 0;
	$goal = null;
	while(count($open)>0){
		usort($open,function($a,$b){
			return $a["cost"] - $b["cost"];
		});

		$n = array_shift($open);
		$n_pz = $n["pz"];

		if($n_pz->cost()==0) {
			$goal = $n_pz;
			break;
		} 

		foreach($n_pz->move() as $suc){
			echo "(".$howmany_opened.")<br />";
			echo $suc->show();
			echo "cost: ".$suc->cost()." + ".$depth." = ".($suc->cost()+$depth)."<br /><br />";
			$howmany_opened++;
		
			$suc->setParent($n_pz);
			$cost = $suc->cost() + $depth;
			$open[] = ["pz"=>$suc,"cost"=>$cost];

		}
		if($max_memory_spend < count($open)) $max_memory_spend = count($open);


		if($goal !== null || $depth > $limit) break;
		$depth++;
	}

	if($goal == null){
		echo "overflow<br />";
	}else{
		echo "<h2>goal</h2>";
		//echo "depth:".$depth."<br />";
		echo "howmany_opened:".$howmany_opened."<br />";
		echo "max_memory_spend:".$max_memory_spend."<br />";
		echo $goal->trail();
	}
}


function idaStar($pz){
	global $limit;	
	$cutoff = 0;
	$howmany_opened = 0;
	$max_memory_spend = 0;
	$goal = null;
	
	while($cutoff < $limit){
		$depth = 0;
		$open = [["pz"=>$pz,"cost"=>0]];
		$cutoff++;
		echo "<p>cutoff:".$cutoff."</p>";
		while(count($open)>0){
			
			$n = array_pop($open);
			$n_pz = $n["pz"];

			if($n_pz->cost()==0) {
				$goal = $n_pz;
				break;
			} 

			foreach($n_pz->move() as $suc){
				//echo "f()=".($suc->cost()+$depth)."<br />";
				if($suc->cost()+$depth <= $cutoff){
					echo "(".$howmany_opened.")<br />";
					echo $suc->show();
					echo "cost: ".$suc->cost()." + ".$depth." = ".($suc->cost()+$depth)."<br /><br />";
					$howmany_opened++;
				
					$suc->setParent($n_pz);
					$cost = $suc->cost() + $depth;
					$open[] = ["pz"=>$suc,"cost"=>$cost];
				}
			}
			if($max_memory_spend < count($open)) $max_memory_spend = count($open);


			$depth++;
		}
		if($goal !== null) break;
	}
	if($goal == null){
		echo "overflow<br />";
	}else{
		echo "<h2>goal</h2>";
		//echo "depth:".$depth."<br />";
		echo "howmany_opened:".$howmany_opened."<br />";
		echo "max_memory_spend:".$max_memory_spend."<br />";
		echo $goal->trail();
	}
}
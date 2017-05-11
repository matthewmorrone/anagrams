<?
function diff($a, $b) {
	$first = strpos($b, $a);

	if($first == -1) {
		return -1;
	}
	else {
		if ($first == 0) {
			$res = substr($b, strlen($a));
		}
		else {
			$res = substr($b, 0, $first);
			$res .= substr($b, $first + strlen($a));
		}

		return $res;
	}
}
function expand($lines) {
	$iter = [];
	foreach($lines as $line):
		$line = preg_replace('/[\x00-\x1F\x7F]/u', '', $line);
		$i = preg_match_all("/(\[.+?\])/", $line, $duo, PREG_PATTERN_ORDER&PREG_OFFSET_CAPTURE);
		if ($i) {
			foreach(range(1, $i) as $j) {
				$pair = array_values(array_filter(preg_split("/[\[ \] ]/", $duo[0][$j-1])));
				$iter[] = trim(substr_replace(diff($duo[0][$j-1], $line), $pair[0], strpos($line, $duo[0][$j-1]), 0));
				$iter[] = trim(substr_replace(diff($duo[0][$j-1], $line), $pair[1], strpos($line, $duo[0][$j-1]), 0));
			}
		}
		else {
			$iter[] = $line;
		}
	endforeach;
	return $iter;
}
$prev = [];
$iter = file($argv[1]);

while(count($prev) !== count($iter)) {
	$prev = $iter;
	$iter = expand($iter);
}
foreach($iter as $line):
	echo $line."\n";
endforeach;
<?php

	require_once __DIR__ ."/config.php";
	require_once __DIR__ ."/MPI.php";
	require_once __DIR__ ."/NPI.php";
	require_once __DIR__ ."/vendor/autoload.php";

	use mpyw\Co\Co;
	use mpyw\Co\CURLException;
	use mpyw\Cowitter\Client;
	use mpyw\Cowitter\HttpException;

	$MPOSsData = [];
	foreach ($Pools as $value)
		array_push($MPOSsData, new MPI($value));
	$MPOSsGetData = [
		'public' => 'public'
	];

	$NOMPsData = [];
	foreach ($NPools as $value)
		array_push($NOMPsData, new NPI($value));
	$NOMPsGetData = [
		'stats' => 'stats'
	];

	$MPOS = Util::RUN($MPOSsData, $MPOSsGetData);
	$NOMP = Util::NRUN($NOMPsData, $NOMPsGetData);

	$DATA = [];

	echo PHP_EOL;
	echo "[MPOS]" .PHP_EOL;

	$DATA['MPOS'] = [];
	foreach ($MPOS as $name => $value) {
		echo PHP_EOL;
		if($value === null) {
			var_dump($name, $value);
			continue;
		}

		if(!isset($DATA['MPOS']['hashrate']))
			$DATA['MPOS']['hashrate'] = 0.0;
		if(!isset($DATA['NOMP']['workers']))
			$DATA['MPOS']['workers'] = 0.0;
		if(!isset($DATA['NOMP']['fee']))
			$DATA['MPOS']['fee'] = [];

		echo '  ' .$value['public']->pool_name .PHP_EOL;
		echo '    ' ."Hashrate: ". Util::NumberStyle($value['public']->hashrate *1000) .PHP_EOL;
		$DATA['MPOS']['hashrate'] += $value['public']->hashrate *1000;
		echo '    ' ."workers: ". Util::NumberStyle($value['public']->workers) .PHP_EOL;
		$DATA['MPOS']['workers'] += $value['public']->workers;
		if(isset($value['public']->fee)) {
			echo '    ' ."fee: ". $value['public']->fee .PHP_EOL;
			$DATA['MPOS']['fee'][] = $value['public']->fee;
		}

	}



	echo PHP_EOL;
	echo "[NOMP]" .PHP_EOL;
	echo PHP_EOL;
	$DATA['NOMP'] = [];
	foreach ($NOMP as $name => $value) {
		echo '  ' .$name .PHP_EOL;
		foreach ($value['stats']->algos as $key => $value) {
			if(!isset($DATA['NOMP'][$key]))
				$DATA['NOMP'][$key] = [];
			if(!isset($DATA['NOMP'][$key]['hashrate']))
				$DATA['NOMP'][$key]['hashrate'] = 0.0;
			if(!isset($DATA['NOMP'][$key]['workers']))
				$DATA['NOMP'][$key]['workers'] = 0.0;
			echo '    ' ."[$key]Hashrate: ". Util::NumberStyle($value->hashrate) .PHP_EOL;
			$DATA['NOMP'][$key]['hashrate'] += $value->hashrate;
			echo '    ' ."[$key]workers: ". Util::NumberStyle($value->workers) .PHP_EOL;
			$DATA['NOMP'][$key]['workers'] += $value->workers;
		}
		//echo '    ' ."fee: ". $value['public']->fee .PHP_EOL;
	}

	if(!isset($DATA['Pool-hashrate']))
		$DATA['Pool-hashrate'] = 0.0;
	if(!isset($DATA['Pool-workers']))
		$DATA['Pool-workers'] = 0.0;

	echo PHP_EOL;
	echo "[MPOS-ALLData]" .PHP_EOL;
	echo '  Hashrate: ' .Util::NumberStyle($DATA['MPOS']['hashrate']) .'Hash' .PHP_EOL;
	$DATA['Pool-hashrate'] += $DATA['MPOS']['hashrate'];
	echo '  Workers: ' .Util::NumberStyle($DATA['MPOS']['workers']) .PHP_EOL;
	$DATA['Pool-hashrate'] += $DATA['MPOS']['workers'];
	echo '  MaxFee: ' .max($DATA['MPOS']['fee']) .PHP_EOL;

	echo PHP_EOL;
	echo "[NOMP-ALLData]" .PHP_EOL;
	foreach ($DATA['NOMP'] as $name => $arg) {
		echo '  Hashrate: ' .Util::NumberStyle($arg['hashrate']) .'Hash' .PHP_EOL;
		$DATA['Pool-hashrate'] += $arg['hashrate'];
		echo '  Workers: ' .Util::NumberStyle($arg['workers']) .PHP_EOL;
		$DATA['Pool-workers'] += $arg['workers'];
	}

	echo PHP_EOL;
	echo "[POOL-ALLData]" .PHP_EOL;
	echo '  Hashrate: ' .Util::NumberStyle($DATA['Pool-hashrate']) .'Hash' .PHP_EOL;
	echo '  Workers: ' .Util::NumberStyle($DATA['Pool-workers']) .PHP_EOL;
?>

<?php

	require_once __DIR__ ."/config.php";
	require_once __DIR__ ."/MPI.php";
	require_once __DIR__ ."/vendor/autoload.php";

	use mpyw\Co\Co;
	use mpyw\Co\CURLException;
	use mpyw\Cowitter\Client;
	use mpyw\Cowitter\HttpException;

	$TWU = new Client($TwitterUser);
	//Titterの名前取得
	try {
		if($TwitterUserID === "") {
			throw new Exception("Not Set TwitterUserID!!!", 1);
		}
		$UserName = $TWU->get("users/show",[
				'user_id' => $TwitterUserID,
				'include_entities' => true,
			])->name;
		echo "User: {$UserName}\n";
	} catch (Exception $e) {
		echo "やらかしました->";
		var_dump($e);
		exit();
	}
	$PoolsData = array();
	foreach ($Pools as $value) {
		array_push($PoolsData, new MPI($value));
	}
	$GetData = array(
		//name	Action
		'PUB' => 'public',
		'GUB' => 'getuserbalance',
		'GUS' => 'getuserstatus'
	);
	$URLS = [];
	$REX = [];
	$Data = [];
	foreach ($PoolsData as $pool) {
		/*
		$URLS[$pool->getName()] = [];
		$REX[$pool->getName()] = [];
		*/
		foreach ($GetData as $name => $action) {
			$pool->selecter($action, $pool->getName() ."_" .$name, $pool, $REX, $URLS);
			//$pool->selecter($action, $name, $pool, $REX[$pool->getName()], $URLS[$pool->getName()]);
			//$pool->selecter($action, $pool->getName() ."_" .$name, $pool, $REX, $URLS);
		}
	}
	//$TWU->post('statuses/update', ['status' => "久しぶりにプログラムからのツイート"]);

	$Data = Co::wait($REX);
	$D = [];
	foreach ($Data as $key => $value) {
		$names = explode("_", $key);
		if(!isset($D[$names[0]]))
			$D[$names[0]] = [];
		$D[$names[0]] = array_merge($D[$names[0]], array($names[1] => $value));
	}
	$Data = $D;
	/*
	foreach ($REX as $key => $rex) {
		try {
			echo "[Co] {$key} Start...\n";
			//$Data[$key] = Co::wait($rex);
			echo "[Co] {$key} Done...\n";
		} catch (CURLException\SSL_ERROR_SYSCALL $e) {
			var_dump($e, $rex);
			exit();
		} catch (Exception $e) {
			var_dump($e, $rex);
			exit();
		}
	}*/
	/*	last Data Broken...
	foreach ($Data as &$pool) {
		foreach ($pool as &$action) {
			$action = json_decode($action);
		}
	}
	*/
	foreach ($Data as $pKey => $pool) {
		foreach ($pool as $aKey => $action) {
			$Data[$pKey][$aKey] = json_decode($action);
		}
	}

//	var_dump($Data);

	$tweet = "{$UserName} のPoolInfo\n\n";
	$hashRate = "ハッシュレート\n";
	$ConfirmedBalance = "残高\n";
	$ALLBalance = array('confirmed' => 0, 'unconfirmed' => 0,);
	$i = 0;
	foreach ($Data as $pKey =>$pool) {
		//$PoolPubName = $pool["PUB"]->pool_name;
		$PoolPubName = $PoolsData[$i]->getName();
		foreach ($pool as $key => $value) {
			if($key == "GUS") {
				$hash = $value->getuserstatus->data->hashrate;
				$hash = number_format($hash,3);
				$hash = preg_replace("/\.?0+$/","",$hash);
				if($hash>0)
					$hashRate .= $PoolPubName ." ：" .$hash ." k/Hs\n";
				//var_dump($hash ." k/Hs");
			}
			if($key == "GUB") {
				$balance = $value->getuserbalance->data;
				foreach ($balance as $key => $value) {
					$value = number_format($value,3);
					$value = preg_replace("/\.?0+$/","",$value);
					$balance->$key = $value;
				}
				$confirmed = $balance->confirmed;
				$ALLBalance['confirmed'] += $confirmed;
				$unconfirmed = $balance->unconfirmed;
				$ALLBalance['unconfirmed'] += $unconfirmed;
				/*
				if($confirmed > 0 || $unconfirmed > 0)
					$ConfirmedBalance .= $PoolPubName ." :{$confirmed}ZNY ({$unconfirmed}ZNY)\n";
					*/
			}
		}
		$i++;
	}

	$ConfirmedBalance .= "{$ALLBalance['confirmed']} ZNY (unconfirmed：{$ALLBalance['unconfirmed']} ZNY)\n";

	$tweet .= $hashRate ."\n";
	$tweet .= $ConfirmedBalance ."\n";

	$date = MPI::nowtime("Y/m/d H:i:s");
	$tweet .= "#MultiPoolInfo {$date}";
	try {
		$T = $TWU->post('statuses/update', ['status' => $tweet]);
	} catch (Exception $e) {
		var_dump($e);
	}
	var_dump($tweet);
?>

<?php
/**
 *
*/

use mpyw\Co\Co;
use mpyw\Co\CURLException;
class Util {
    public static function nowtime($format = 'Y-m-d H:i:s', $timezone = 'Asia/Tokyo') {
        $date = new DateTime("", new DateTimeZone($timezone));
        return $date->format($format);
    }

    public static function ADD(&$SAVE, $name, $Query) {
		$SAVE = array_merge($SAVE, array($name => $Query ));
	}

	public static function ADDF(&$SAVE, $name, $Query) {
		$func = function($n, $Q) {
				echo "[{$n}] Start\n";
				$data = yield $Q;
				echo "[{$n}] Done\n";
				return $data;
			};
		$SAVE = array_merge($SAVE, array($name => $func($name,$Query) ));
	}

    public static function RUN($PoolsData, $GetData) {

    	$URLS = [];
    	$REX = [];
    	$Data = [];
    	foreach ($PoolsData as $pool) {
    		foreach ($GetData as $name => $action) {
    			$pool->selecter($action, $pool->getName() ."_" .$name, $pool, $REX, $URLS);
    		}
    	}

    	$Data = Co::wait($REX);
    	$D = [];
    	foreach ($Data as $key => $value) {
    		$names = explode("_", $key);
    		if(!isset($D[$names[0]]))
    			$D[$names[0]] = [];
    		$D[$names[0]] = array_merge($D[$names[0]], array($names[1] => $value));
    	}
    	$Data = $D;
    	foreach ($Data as $pKey => $pool) {
    		foreach ($pool as $aKey => $action) {
    			$Data[$pKey][$aKey] = json_decode($action);
    		}
    	}
    	return $Data;
    }

    public static function NRUN($PoolsData, $GetData) {

    	$URLS = [];
    	$REX = [];
    	$Data = [];
    	foreach ($PoolsData as $pool) {
    		foreach ($GetData as $name => $action) {
    			$pool->selecter($action, null, $pool->getName() ."_" .$name, $pool, $REX, $URLS);
    		}
    	}

    	$Data = Co::wait($REX);
    	$D = [];
    	foreach ($Data as $key => $value) {
    		$names = explode("_", $key);
    		if(!isset($D[$names[0]]))
    			$D[$names[0]] = [];
    		$D[$names[0]] = array_merge($D[$names[0]], array($names[1] => $value));
    	}
    	$Data = $D;
    	foreach ($Data as $pKey => $pool) {
    		foreach ($pool as $aKey => $action) {
    			$Data[$pKey][$aKey] = json_decode($action);
    		}
    	}
    	return $Data;
    }

    public static function NumberStyle($input) {

        $str = $input;
        $inputChar = ['K','M','G','T','P'];
        $inputStr = number_format($input);
        $inputCount = substr_count($inputStr, ',');
        $inputArray = explode(',', $inputStr);
        $inputArrayCount = count($inputArray);
        if($inputCount !== 0) {
            if(isset($inputChar[$inputCount])) {
                $str = $inputArray[0];
                if($inputArrayCount > 0) {
                    if($inputArrayCount-1 > 0)
                        $str .= '.' .$inputArray[1];
                }
                $str .= ' '.$inputChar[$inputCount-1];
            }

        }return $str;
    }
}

?>

<?php
require_once __DIR__ ."/Util.php";
/**
 *
 */
class NPI {

	private $name;
	private $hostname;
	private $ssl;

	function __construct($arg = null) {
		$this->name = $arg['NAME'];
		$this->hostname	= $arg['HOSTNAME'];
		$this->ssl	= $arg['SSL'];
	}

	public function getName() {
		return $this->name;
	}

	public function get($url, $ops = []) {
		$ch = curl_init();
		$ops = array_replace([
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
		], $ops);
		curl_setopt_array($ch, $ops);
		return $ch;
	}

    public function getUrl($name, $ops = []) {
        $pams = [];
		$pams = array_merge($pams, $ops);
        $url  = "http" .($this->ssl ? "s":"") ."://" .$this->hostname ."/api/" .$name .($ops!==null ? '?'.http_build_query($pams):'');
		return $url;
    }

    public function selecter($acction = 'stats', $adr = null ,$name, $exc, &$CURLs = [] ,&$URLs = []) {
        $ops = [];
        switch ($acction) {
            case 'stats':
            case 'blocks':
            case 'pool_stats':
            case 'payments':
            case 'stats':
				Util::ADD($URLs, $name, $this->getUrl($acction,$ops));
				Util::ADDF($CURLs,$name, $this->get($this->getUrl($acction,$ops)));
                break;

            case 'worker_stats':
            if($adr !== null) {
                $ops = [
                    'taddr' => $adr
                ];

				Util::ADD($URLs, $name, $this->getUrl($acction,$ops));
				Util::ADDF($CURLs,$name, $this->get($this->getUrl($acction,$ops)));
                break;
            }

            default:
                break;
        }
    }
/*
    public function selecter($acction = 'stats', $adr = null ) {
        $ops = [];
        switch ($acction) {
            case 'stats':
            case 'blocks':
            case 'pool_stats':
            case 'payments':
            case 'stats':
                break;

            case 'worker_stats':
            if($adr !== null) {
                $ops = [
                    'taddr' => $adr
                ];
                break;
            }

            default:
                # code...
                break;
        }
        return $this->getUrl($acction,$ops);
    }
    public function all() {
        $list = [
            'stats',
            'blocks',
            'pool_stats',
            'payments',
        ];
        $data = [];
        foreach ($list as $value) {
             $data[] = $this->getUrl($acction,$ops);
        }
        return $data;
    }
	*/
}

?>

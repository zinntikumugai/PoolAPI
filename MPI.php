<?php

require_once __DIR__ ."/Util.php";
class MPI {

	private $name;
	private $hostname;
	private $apikey;
	private $userid;
	private $ssl;

	function __construct($arg = null) {
		$this->name = $arg['NAME'];
		$this->hostname	= $arg['HOSTNAME'];
		$this->apikey	= $arg['APIKEY'];
		$this->userid	= $arg['USERID'];
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

	public function getUrl($ops = []) {
		$pams = array(
			'page' => "api"
		);
		$pams = array_merge($pams, $ops);
		$url  = "http" .($this->ssl ? "s":"") ."://" .$this->hostname ."/index.php?" .http_build_query($pams);
		return $url;
	}

	public function pub($action = "public", $id = false) {
		$ops = array('action' => $action );
		if($action !== "public")
			$ops = array_merge($ops, array('api_key' => $this->apikey ));
		if($id)
			$ops = array_merge($ops, array('id' => $this->userid ));
		return $this->getUrl($ops);
	}

	public function selecter($action = "public",$name, $exc, &$CURLs = [] ,&$URLs = []) {
		$id = false;
		switch ($action) {
			//public
			case 'public':
				Util::ADD($URLs, $name, $exc->pub());
				Util::ADDF($CURLs, $name, $exc->get($exc->pub()));
				break;
			//user
			case 'getdashboarddata':
			case 'getuserbalance':
			case 'getuserhashrate':
			case 'getusersharerate':
			case 'getuserstatus':
			case 'getusertransactions':
			case 'getuserworkers':
				Util::ADD($URLs, $name, $exc->pub($action, true));
				Util::ADDF($CURLs, $name, $exc->get($exc->pub($action, true)));
				break;
			//pool
			case 'getblockcount':
			case 'getblocksfound':
			case 'getblockstats':
			case 'getcurrentworkers':
			case 'getdifficulty':
			case 'getestimatedtime':
			case 'gethourlyhashrates':
			case 'getnavbardata':
			case 'getpoolhashrate':
			case 'getpoolinfo':
			case 'getpoolsharerate':
			case 'getpoolstatus':
			case 'gettimesincelastblock':
			case 'gettopcontributors':
				Util::ADD($URLs, $name, $exc->pub($action));
				Util::ADDF($CURLs, $name, $exc->get($exc->pub($action)));
				break;

			default:
				break;
		}
	}

}
?>

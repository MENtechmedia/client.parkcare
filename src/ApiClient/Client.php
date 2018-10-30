<?php namespace MENtechmedia\ApiClient;

use Exception;
use GuzzleHttp;
use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\HandlerStack;
use kamermans\OAuth2\GrantType\PasswordCredentials;

class Client
{
    private $baseUrl;
	private $client;
	private $uploadClient;
	private $oAuth;
	private $headers;
	private $tenantId;
	private static $instance = null;
	public $exceptions = false;
    public $lastError = "";
    
	private function __construct($auhUrl, $baseUrl, $clientId, $clientSecret, $username, $password, $tenantId)
	{
		$this->baseUrl = $baseUrl;
		$this->tenantId = $tenantId;
		$this->headers = [];
		$this->client = $this->getClient($auhUrl, $clientId, $clientSecret, $username, $password);
		// use a different client for file uploads
		$this->uploadClient = new GuzzleHttp\Client(['debug' => false, 'exceptions' => false]);
		$this->addHeader('Content-Type', 'application/json');
		$this->addHeader('Accept', 'application/json');
    }
    
    public function getLastError() 
    {
		return $this->lastError;
    }
    
    public static function init($auhUrl, $baseUrl, $clientId, $clientSecret, $username, $password, $tenantId) 
    {
		if(!self::$instance) {
			self::$instance = new self($auhUrl, $baseUrl, $clientId, $clientSecret, $username, $password, $tenantId);
		}
		return self::$instance;
    }
    
    public static function clearInstance() {
		self::$instance = null;
	}
	public static function getInstance() {
		if(!self::$instance) {
			throw new Exception("Client not initialized, run init() first");
		}
		return self::$instance;
    }
    
    private function getHeaders() 
    {
		return $this->headers;
    }
    
    private function addHeader($header, $value) 
    {
		$this->headers[$header] = [$value];
    }

    public function call($method, $endpoint, $data = [], $includes = [], $perPage = 15, $currentPage = 1)
    {
        $url = $this->prepareUrl($endpoint, $includes, $perPage, $currentPage);
        try {
            $response = $this->client->request($method, $url, [
                'headers' => $this->getHeaders(),
                'json' => $data
            ]);
        } catch (Exception $e) {
            // api exception ?
            if ($e instanceof GuzzleHttp\Exception\ClientException) {
                $contents = $e->getResponse()->getBody(true)->getContents();
                $message = $this->parseErrors($contents);
                if(isset($contents['status_code'])) {
                    if ($this->exceptions) {
                        throw new ApiException($endpoint . ": " . $message, $contents['status_code']);
                    }
                }
            } else if($e instanceof GuzzleHttp\Exception\RequestException) {
                $contents = $e->getResponse()->getBody(true)->getContents();
                $message = $this->parseErrors($contents);
            }
            // general exception
            if($this->exceptions) {
                throw new Exception($endpoint . ": " . $e->getMessage());
            }
            return null;
        }
        $data = $response->getBody()->getContents();
        return $this->decodeResponseData($data);
    }

    private function decodeResponseData($data) 
    {
		return json_decode($data, true);
	}
    
	private function getClient($auhUrl, $clientId, $clientSecret, $username, $password)
	{
		$reauth_client = new GuzzleHttp\Client([
			'base_uri' => $auhUrl,
		]);
		$grant_type = new PasswordCredentials($reauth_client, [
			'client_id' => $clientId,
			'client_secret' => $clientSecret,
			'username' => $username,
			'password' => $password,
		]);
		$this->oAuth = new OAuth2Middleware($grant_type);
		$stack = HandlerStack::create();
		$stack->push($this->oAuth);
		return new GuzzleHttp\Client([
			'handler' => $stack,
			'auth' => 'oauth',
			'headers' => [
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
			]
		]);
    }

    private function prepareUrl($endpoint, $includes, $perPage = 15, $currentPage = 1) 
    {
        $url = $this->baseUrl."/".$endpoint;
        if(isset(parse_url($url)['query'])){
            $sign = '&';
        } else {
            $sign = '?';
        }
        if(count($includes)) {
            $url .= $sign."include=".implode(",", $includes);
            $url .= "&limit=". $perPage ."&page=". $currentPage;
        }else{
            $url .= $sign."limit=". $perPage ."&page=". $currentPage;
        }
        return $url;
    }

    public function getToken() 
    {
		return $this->oAuth->getAccessToken();
    }
    
    private function parseErrors($contents) 
    {
		$contents = $this->decodeResponseData($contents);
		if (isset($contents['errors'])) {
			$message = GuzzleHttp\json_encode($contents['errors']);
		} else {
			$message = $contents['message'];;
		}
		$this->lastError = $message;
		return $message;
	}

    
    public function get($endpoint, $includes = [], $perPage = 15, $currentPage = 1) {
        return $this->call('GET', $endpoint, [], $includes, $perPage, $currentPage);
    }
	public function patch($endpoint, $data = [], $includes = []) {
		return $this->call('PATCH', $endpoint, $data, $includes);
	}
	public function post($endpoint, $data = [], $includes = []) {
		return $this->call('POST', $endpoint, $data, $includes);
	}
	public function delete($endpoint) {
		return $this->call('DELETE', $endpoint);
	}
}
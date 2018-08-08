<?php

namespace NotificationChannels\CorpSMS;

use Illuminate\Support\Arr;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\CorpSMS\Exceptions\CouldNotSendNotification;

class CorpSMSApi
{
    const FORMAT_JSON = 3;

    /** @var HttpClient */
    protected $client;

    /** @var string */
    protected $endpoint;

    /** @var string */
    protected $login;

    /** @var string */
    protected $secret;

    /** @var string */
    protected $sender;

    /** @var string */
    protected $tid;

    public function __construct(array $config)
    {
        $this->login = Arr::get($config, 'login');
        $this->secret = Arr::get($config, 'secret');
        $this->sender = Arr::get($config, 'sender','CorpSMS');
        $this->endpoint = Arr::get($config, 'host', 'http://203.151.230.33/CorporateSMS_API/');
        $this->headers = [
            'Authorization' => 'Basic '.base64_encode($this->login.':'.$this->secret),
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];
		$this->client = new HttpClient([
            'base_uri' => $this->endpoint,
            'timeout' => 5,
            'connect_timeout' => 5,
            'headers' => $this->headers
        ]);

    }

    public function send($params)
    {
        if(!is_array($params['phones'])){
            $params['phones']= explode(',',$params['phones']);
        }

	    $key = md5($params['phones'][0] . $params['tid']);
	    $lang="E";
	    if (!preg_match('/^[a-zA-Z0-9 .!?"-]+$/', $params['msg'])){
		    $lang="E";
	    }else{
		    $lang="T";
        }
        $params['sender'] = $params['sender'] ?? $this->sender;
	    $xml =   '<?xml version="1.0" encoding="tis-620" ?>'
				.'<corpsms_request>'
				.'<key>'.$key.'</key>'
				.'<header>null</header>'
				.'<sender>'.$params['sender'].'</sender>'
				.'<mtype>'.$lang.'</mtype>'
				.'<msg>'.$params['msg'].'</msg>'
				.'<tid>'.$params['tid'].'</tid>'
				.'<recipients>';
		foreach($params['phones'] as $phone){
			$xml 	.='<msisdn>'.$phone.'</msisdn>';
		}


		$xml .='</recipients>'
				.'</corpsms_request>';
        try {
            $request = $this->client->get('APIReceiver', [
	            'body' => $xml,
	            'headers' => $this->headers,
	            'verify' => false
	        ]);
	        $response = new \SimpleXMLElement($request->getBody()->getContents());

            if (!isset($response->STATUS)) {
                throw new \DomainException('Unknown SMS Gateway Error', 500);
            }
            if (intval($response->STATUS) != 000) {
                throw new \DomainException($response->DETAIL, 500);
            }
            //dd($response);
            return $response;
        } catch (\DomainException $exception) {
            throw CouldNotSendNotification::smscRespondedWithAnError($exception);
        } catch (\Exception $exception) {
            throw CouldNotSendNotification::couldNotCommunicateWithSmsc($exception);
        }
    }
}

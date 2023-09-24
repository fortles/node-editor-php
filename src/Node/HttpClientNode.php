<?php
namespace NodeEditor\Node;

use Cake\Http\Client;
use Cake\Http\Client\Request;
use Exception;
use NodeEditor\Utility\OutputNode;

class HttpClientNode extends OutputNode{
    
    public $in = [
        'method' => [
            'type' => 'select',
            'values' => [
                Request::METHOD_POST => 'POST',
                Request::METHOD_GET  => 'GET',
            ]
        ],
        'fail' => [
            'type' => 'select',
            'values' => [
                0 => 'Allow error',
                1 => 'Abort error'
            ]
        ],
        'url' => 'string',
        'headers' => 'array',
        'body' => 'mixed'
    ];
    
    public $out = [
        'headers' => 'mixed',
        'body'    => 'mixed',
        'status'  => 'int',
        'success'  => 'bool'
    ];


    private $client;
    
    public function init(array $inputs) {
        $this->client = new Client();
        parent::init($inputs);
    }
    
    public function method(array $inputs) {
        //Dummy output for init
        return [
            'status' => 200,
            'headers' => [],
            'body' => '',
            'success' => true
        ];
    }

    public function setData(array $inputs) {
        $headers = $inputs['headers'];
        if(is_array($headers)){
            $headers = $headers;
        }else if(is_string($headers)){
            $headers = explode(':',$headers);
            $headers = [$headers[0] => $headers[1]];
        }else{
            $headers = [];
        }
        $request = new Request($inputs['url'], $inputs['method'], $headers);
        $request->body($inputs['body']);
        $response = $this->client->send($request,['timeout' => 180]);
        if($inputs['fail'] && !$response->isOk()){
            throw new Exception((string)$response->getBody());
        }
        $this->outputBuffer = [
            'status' => $response->getStatusCode(),
            'headers' => $response->getHeaders(),
            'body' => $response->getBody(),
            'success' => $response->isOk()
        ];
    }
}

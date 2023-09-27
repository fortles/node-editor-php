<?php
namespace Fortles\NodeEditor\Node;
use Exception;
use Fortles\NodeEditor\Node;

class HttpClientNode extends Node{
    
    public $in = [
        'method' => [
            'type' => 'select',
            'values' => [
                'POST',
                'GET',
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
    
    public function init(array $inputs) {
        parent::init($inputs);
    }
    
    public function method(array $inputs) {
        //TODO POST with get data
        return $this->fetch($inputs);
    }

    public function fetch(array $inputs) {
        $url = $inputs['url'];
        $method = $inputs['method'];
        $headers = $inputs['headers'];
        $body = $inputs['body'];
    
        $ch = curl_init();
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
        // Set headers
        if(is_array($headers)) {
            $headerArray = [];
            foreach($headers as $key => $value) {
                $headerArray[] = "{$key}: {$value}";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        } elseif(is_string($headers)) {
            $headerParts = explode(':', $headers);
            if(count($headerParts) === 2) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, [trim($headerParts[0]) . ': ' . trim($headerParts[1])]);
            }
        }
    
        // Set HTTP method and body
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;
            case 'GET':
                curl_setopt($ch, CURLOPT_HTTPGET, 1);
                break;
            // Add other HTTP methods as needed
        }
    
        $responseBody = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        $success = $responseCode >= 200 && $responseCode < 300;
    
        if ($inputs['fail'] && !$success) {
            throw new Exception($responseBody);
        }
    
        curl_close($ch);

        return [
            'status' => $responseCode,
            'headers' => [], // cURL doesn't parse headers by default, you might want to extract them separately
            'body' => $responseBody,
            'success' => $success
        ];
    }    
}

<?php
namespace Loader\Utility\Model;
class StreamSoapClient extends \SoapClient{
    protected $options;
    protected  $file;
    public function __construct($wsdl, array $options = null) {
        $this->options = $options;
        parent::SoapClient($wsdl, $options);
    }
    public function __doRequest($request, $location, $action, $version, $one_way = 0) {
        if(empty($this->file)){
            return parent::__doRequest($request, $location, $action, $version);
        }
        // xml post structure
        $headers = [
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: $action", 
            "Content-length: ".strlen($request),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $location);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        if($this->options['login']){
            curl_setopt($ch, CURLOPT_USERPWD, $this->options['login'].':'.$this->options['password']);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        }
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if(isset($this->file)){
            $file = fopen($this->file, 'w');
            curl_setopt($ch, CURLOPT_FILE, $file);
        }
        curl_exec($ch); 
        if(curl_error($ch)){
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
        if(isset($file)){
            fclose($file);
        }
        return '';
    }
    public function __soapCall($function_name, $arguments, $options = null, $input_headers = null, &$output_headers = null) {
        if(isset($options['file'])){
            $this->file = $options['file'];
        }else{
            unset($this->file);
        }
        return parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
    }
}

<?php

namespace NodeEditor\Node;

use Cake\Core\Exception\CakeException;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use DateTime;
use NodeEditor\Utility\Node;

class CacheNode extends Node{
    public $in = [
        'Mode' => [
            'type' => 'select',
            'values' => [
                'URL','Content',
            ]
        ],
        'url' => 'url',
        'timeout' => 'time'
    ];
    public $out = [
        'File' => 'file'
    ];
    protected $path;
    public function __construct(array $data, $path = null) {
        $this->path = "input/".(isset($path) ? "$path/" : null);
        parent::__construct($data);
    }
    public function method(array $inputs) {
        if(!isset($inputs['url']) && $inputs['Mode'] != 1){
            throw new Exception("$this->name: Hiányzó url");
        }
        if(!isset($inputs['timeout'])){
            throw new CakeException("$this->name: Hiányzó idő");
        }
        if(!$time_out = strtotime($inputs['timeout'].' UTC',0)){
            throw new CakeException("$this->name: Szabálytalani idő");
        }
        //todo file  mentés

        if(empty($inputs['Mode'])) {
            $target_path = $this->cache($inputs['url'], $this->path . $this->name, $inputs['timeout']);
        }else{
            $target_path = $this->cache($inputs['url'], $this->path . $this->name, $inputs['timeout'], [$this, 'saveContent']);
        }


        return ['File' => $target_path];
    }

    protected function save($source, $target){
        if(strncmp($source, 'zip://', 6) == 0){
            $end = strpos($source, '#');
            copy(substr($source,6, $end - 6), $target.'.zip');
            copy('zip://'.$target.'.zip'.substr($source, $end), $target);
            unlink($target.'.zip');
        }else{
            copy($source, $target);
        }
    }

    protected function saveContent($source, $target){
        $file = new File($target, TRUE);
        $file->write($source);
    }

    function cache($source, $target, $timeout, $callback = null){
        if(!$time_out = strtotime($timeout.' UTC',0)){
            throw new CakeException("Wrong timeout format");
        }
        /*
        $extension = pathinfo($target,PATHINFO_EXTENSION);
        if(!empty($extension)){
            $target = substr($target, 0, -1 - strlen($extension));
        }else{
            $extension = pathinfo($source,PATHINFO_EXTENSION);
        }
        if(!empty($extension)){
            $extension = '.'.$extension;
        }*/
        //check if cache valid
        //  $extension = '';
        $target = TMP . '/cache/loader/cache/' . $target;
        $files = glob("$target-*");
        $target_path = "$target-".date('YmdHi').'-'.dechex(crc32($source));
        if(empty($files)){
            $path = substr($target, 0, strrpos($target, '/')+1);
            if(!is_dir($path)){
                mkdir($path, 0777, true);
            }
            $callback ? $callback($source, $target_path) : $this->save($source, $target_path);
        }else{
            $fileparts = explode('-',substr($files[0], strrpos($files[0], '/')+1));

            $last_modified = DateTime::createFromFormat('YmdHi',$fileparts[1]);
            if(!$last_modified){
                throw new CakeException("Illformed filepath: '$files[0]'");
            }
            if((time() - $last_modified->getTimestamp() > $time_out) || dechex(crc32($source)) != $fileparts[2]){
                unlink($files[0]);
                $callback ? $callback($source, $target_path) :  $this->save($source, $target_path);
            }else{
                $target_path = $files[0];
            }
        }
        return $target_path;
    }
}

class LoaderCacheNode extends CacheNode{
    public $out = [
        'File' => 'filename'
    ];
    public function __construct($data){
        $path = $data['editor']->id;
        parent::__construct($data, $path);
    }
}

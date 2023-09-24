<?php
namespace Loader\Utility\Model;
class CacheModel{
    static function cache($source, $target, $timeout, $callback = null){
        if(!$time_out = strtotime($timeout.' UTC',0)){
            throw new Exception("Wrong timeout format");
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
        $target = "cache/$target";
        $files = glob("$target-*");
        $target_path = "$target-".date('YmdHi').'-'.dechex(crc32($source));
        if(empty($files)){
            $path = substr($target, 0, strrpos($target, '/')+1);
            if(!is_dir($path)){
                mkdir($path, 0777, true);
            }
            $callback ? $callback($source, $target_path) : static::save($source, $target_path);
        }else{
            $fileparts = explode('-',substr($files[0], strrpos($files[0], '/')+1));
            
            $last_modified = \DateTime::createFromFormat('YmdHi',$fileparts[1]);
            if(!$last_modified){
                throw new Exception("Illformed filepath: '$files[0]'");
            }
            if((time() - $last_modified->getTimestamp() > $time_out) || dechex(crc32($source)) != $fileparts[2]){
                unlink($files[0]);
                $callback ? $callback($source, $target_path) : static::save($source, $target_path);
            }else{
                $target_path = $files[0];
            }
        }
        return $target_path;
    }
    protected static function save($source, $target){
        if(strncmp($source, 'zip://', 6) == 0){
            $end = strpos($source, '#');
            copy(substr($source,6, $end - 6), $target.'.zip');
            copy('zip://'.$target.'.zip'.substr($source, $end), $target);
            unlink($target.'.zip');
        }else{
            copy($source, $target);
        }
    }
}
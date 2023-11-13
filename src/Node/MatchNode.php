<?php
namespace Fortles\NodeEditor\Node;
use Fortles\NodeEditor\Node;

class MatchNode extends Node{

    public $description = 'Matches if the string contains an anouther one or matches against a regexp. If matches the mathing output will return true. If replacement provided, the replacement output will emit it.';

    const CONTAINS_CASE_INSENSITIVE = 0;
    const CONTAINS_CASE_SENSITIVE = 1;
    const REGEXP = 2;
    public $in = [
        'mode' => [
            'type' => 'select',
            'values' => [
                self::CONTAINS_CASE_INSENSITIVE => 'Case Insensitive',
                self::CONTAINS_CASE_SENSITIVE => 'Case Sensitive',
                self::REGEXP => 'Regexp'
            ]
        ],
        'pattern' => 'string',
        'replacement' => 'string',
        'subject' => 'string'
    ];

    public $out = [
        'matching' => 'boolean',
        'replaced' => 'string'
    ];

    public function method(array $inputs){

        $matching = false;
        $replaced = '';
        $pattern = $inputs['pattern'];
        $subject = $inputs['subject'];
        $replacement = $inputs['replacement'];
        $mode = $inputs['mode'];

        if($mode == self::REGEXP){
            $matching = !!preg_match($pattern, $subject);
            if($replacement){
                $replaced = preg_replace($pattern, $replacement, $subject);
            }
        }else{
            if($mode == self::CONTAINS_CASE_SENSITIVE){
                $pattern = strtolower($pattern);
                $subject = strtolower($subject);
            }
            $matching = str_contains($subject , $pattern);
            if($replacement){
                $replaced = str_replace($pattern, $replacement, $subject);
            }
        }

        return [
            'matching' => $matching,
            'replaced' => $replaced
        ];
    }
}
<?php
namespace Bottel;

class Quote {
    /**
     * HTML
     *
     * @param  string $string
     * @return string
     */
    public static function HTML(string $string): string {
        return strtr($string, [
            '&' => '&amp;',
            '<' => '&lt;',
            '>' => '&gt;',
        ]);
    }

    /**
     * MarkDown
     *
     * @param  string $string
     * @return string
     */
    public static function MarkDown(string $string): string {
        return strtr($string, [
            '\\' => '\\\\',
            '_' => '\_',
            '*' => '\*',
            '`' => '\`',
            '[' => '\[',
        ]);
    }
    
    /**
     * MarkDownV2
     *
     * @param  string $string
     * @return string
     */
    public static function MarkDownV2(string $string): string {
        return strtr($string, [
            '\\' => '\\\\',
            '_' => '\_',
            '*' => '\*',
            '[' => '\[',
            ']' => '\]',
            '(' => '\(',
            ')' => '\)',
            '~' => '\~',
            '`' => '\`',
            '>' => '\>',
            '#' => '\#',
            '+' => '\+',
            '-' => '\-',
            '=' => '\=',
            '|' => '\|',
            '{' => '\{',
            '}' => '\}',
            '.' => '\.',
            '!' => '\!',
        ]);
    }
    
    /**
     * format
     *
     * @param  string $string
     * @param  ?string $type
     * @return string
     */
    public static function format(string $string, string $type = 'raw'): string {
        switch(strtolower($type)){
            case 'html':
                return self::HTML($string);
            case 'markdown':
                return self::MarkDown($string);
            case 'markdownv2':
                return self::MarkDownV2($string);
            default:
                return $string;
        }
    }
}
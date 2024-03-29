<?php
/*
 * Copyright 2017 The LazyJsonMapper Project
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Bottel\Utils\DelayedJsonMapper\Magic;

/**
 * Compatibility translation layer for illegal operator characters.
 *
 * Takes care of encoding/decoding illegal characters (special PHP operators)
 * in JSON property names, to ensure that the resulting function name is legal.
 *
 * @copyright 2017 The LazyJsonMapper Project
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author SteveJobzniak (https://github.com/SteveJobzniak)
 *
 * @see FunctionTranslation
 * @see PropertyTranslation
 */
class SpecialOperators
{
    /**
     * Table of special operator translations.
     *
     * @var array|null
     */
    private static $_translations;

    /**
     * Encode special operators.
     *
     * @param string $str Input string.
     *
     * @return string Output string.
     */
    public static function encodeOperators(
        $str)
    {
        if (self::$_translations === null) {
            self::_buildTranslations();
        }

        return strtr($str, self::$_translations['encode']);
    }

    /**
     * Decode special operators.
     *
     * @param string $str Input string.
     *
     * @return string Output string.
     */
    public static function decodeOperators(
        $str)
    {
        if (self::$_translations === null) {
            self::_buildTranslations();
        }

        // NOTE: preg_replace_callback() was over 6x slower than strtr() here.
        return strtr($str, self::$_translations['decode']);
    }

    /**
     * Build the table of special operator translations.
     *
     * PHP doesn't allow these special operators in function names, so we cannot
     * legally generate such function names. Instead, we'll encode the operators
     * with a sequence that will never appear in our normal algorithm's output.
     *
     * For example, if the user has a property named `en-US`, our special
     * operator translation will give it a function name of `getEn_x2D_US()`
     * to safely encode the arithmetic minus symbol.
     *
     * This encoding scheme was deliberately chosen thanks to the fact that it
     * will never conflict with anything else that's legitimately generated by
     * our algorithm. Because if the user has a property that's _actually_ named
     * `en_x2D_US`, then that one would be encoded as `getEnX2D_US()` (by the
     * main `PropertyTranslation` algorithm), which is obviously not the same
     * thing and will never confuse our parser in decoding later. And if they
     * sent us `en__x2D_US`, it would be encoded as `getEn_X2D_US()`, which is
     * yet again different (the `X` is uppercase in that situation too).
     *
     * Therefore, our special encoding of `_x[HEX]_` will never be generated by
     * any legitimate user property names, even if those names do contain that
     * sequence on-disk. They'll all be encoded to something else. The only
     * thing that can put a lowercase `x` in this particular arrangement is our
     * own operator symbol translator here.
     *
     * As for why we need that long sequence instead of something shorter, it's
     * unfortunately the only way that we can guarantee uniqueness. It relies on
     * the principle that `_x` in the user's own properties would ALWAYS be
     * encoded as `X`. Most programmers are familiar with hex codes and will
     * recognize the sequences we've chosen: `getEn_x2D_US()`, where `x` stands
     * for "hex", and `2D` is the hex character value for the minus symbol.
     *
     * This particular encoding also allows us to easily encode future illegal
     * characters of any length (even multiple bytes such as `_xFFFF_` if ever
     * necessary) without needing to change the encoding scheme again.
     */
    private static function _buildTranslations()
    {
        // This operator list was based on the following lists:
        // http://php.net/manual/en/language.operators.precedence.php
        // http://php.net/manual/en/language.operators.execution.php
        // and general PHP code structures (`$` and `;` operators, etc).
        $operators = [
            // General code structures.
            '$', // Variables.
            ',', // Comma (statement separator).
            ';', // End of statement.
            '`', // Execution backticks.
            '=', // Assignment operator.
            '[', // Array left.
            ']', // Array right.
            '(', // Function left.
            ')', // Function right.
            '{', // Code block left.
            '}', // Code block right.
            '.', // String concatenation.
            '~', // Tilde (not allowed in names).
            '@', // At-symbol (not allowed in names).
            '?', // Comparison and ternary.
            ':', // Ternary.
            ' ', // Space.
            "\t", // Tab.
            '\\', // Backslash (namespace separator).
            '#', // Hash (alternative comment operator).
            "'", // Single quote (string component operator).
            '"', // Double quote (string component operator).
            // Arithmetic operators.
            '+',
            '-',
            '*',
            '/',
            '%',
            // Logical operators.
            '&',
            '|',
            '^',
            '!',
            '<',
            '>',
        ];

        $translations = ['encode' => [], 'decode' => []];
        foreach ($operators as $chr) {
            $hex = str_pad(strtoupper(dechex(ord($chr))), 2, '0', STR_PAD_LEFT);
            $encoded = sprintf('_x%s_', $hex);
            if ($chr === '.') {
                $translations['encode'][$chr] = '';
                $translations['decode'][$encoded] = $chr;
            } else {
                $translations['encode'][$chr] = $encoded;
                $translations['decode'][$encoded] = $chr;
            }
        }

        self::$_translations = $translations;
    }
}

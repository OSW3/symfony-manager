<?php 
namespace OSW3\Manager\Utils;

class StringUtil
{
    public static function camelToSlug(string $input): string {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }

    public static function addArticle(string $word): string {
        $vowelSounds = ['a', 'e', 'i', 'o', 'u', 'h'];
    
        $word = trim($word);
        $lowerWord = strtolower($word);
    
        if (in_array($lowerWord, ['hour', 'honest', 'honor', 'heir'])) {
            return "an $word";
        }
    
        return in_array($lowerWord[0], $vowelSounds) ? "an $word" : "a $word";
    }
}
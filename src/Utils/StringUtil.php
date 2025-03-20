<?php 
namespace OSW3\Manager\Utils;

class StringUtil
{
    public static function camelToSlug(string $input): string {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }
}
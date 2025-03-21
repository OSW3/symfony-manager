<?php
namespace OSW3\Manager\Enum;

use OSW3\Manager\Trait\EnumTrait;

enum RequestOperators: string 
{
    use EnumTrait;

    case EQUAL          = 'equal';           // =
    case IS_NOT         = 'is-not';          // <> ou !=
    case LIKE           = 'like';            // LIKE %val%
    case LEFT_LIKE      = 'left-like';       // LIKE %val (ending by val)
    case RIGHT_LIKE     = 'right-like';      // LIKE val% (starting by val)
    case NOT_LIKE       = 'not-like';        // NOT LIKE
    case NOT_LEFT_LIKE  = 'not-left-like';   // NOT LIKE %val
    case NOT_RIGHT_LIKE = 'not-right-like';  // NOT LIKE val%
    case GREATER        = 'greater';         // >
    case LESS           = 'less';            // <
    case GREATER_EQUAL  = 'greater-equal';   // >=
    case LESS_EQUAL     = 'less-equal';      // <=
    case IN             = 'in';              // IN
    case BETWEEN        = 'between';         // BETWEEN a AND b
    case IS_NULL        = 'is-null';         // IS NULL
    case IS_NOT_NULL    = 'is-not-null';     // IS NOT NULL
}
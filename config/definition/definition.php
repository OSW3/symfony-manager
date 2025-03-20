<?php 

return static function($definition)
{
    $definition->rootNode()->children()

        ->append( (include __DIR__."/parts/homepage.php")() )

    ->end();
};
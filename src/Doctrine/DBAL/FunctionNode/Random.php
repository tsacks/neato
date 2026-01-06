<?php

// App/Doctrine/DBAL/FunctionNode/Random.php

namespace App\Doctrine\DBAL\FunctionNode;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\TokenType;

/**
 * RandFunction ::= "RANDOM" "(" ")".
 */
final class Random extends FunctionNode
{
    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker): string
    {
        return 'RAND()';
    }
}
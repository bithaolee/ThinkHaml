<?php

namespace ThinkHaml\Support\Twig;

use ThinkHaml\Environment;

/**
 * Example integration of ThinkHaml with Twig, by proxying the Lexer
 *
 * This lexer will parse Twig templates as HAML if their filename end with
 * `.haml`, or if the code starts with `{% haml %}`.
 *
 * Alternatively, use ThinkHaml\Support\Twig\Loader.
 *
 * <code>
 * $lexer = new \ThinkHaml\Support\Twig\Lexer($mthaml);
 * $lexer->setLexer($twig->getLexer());
 * $twig->setLexer($lexer);
 * </code>
 */
class Lexer implements \Twig_LexerInterface
{
    protected $env;
    protected $lexer;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function setLexer(\Twig_LexerInterface $lexer)
    {
        $this->lexer = $lexer;
    }

    public function tokenize($code, $filename = null)
    {
        if (preg_match('#^\s*{%\s*haml\s*%}#', $code, $match)) {
            $padding = str_repeat(' ', strlen($match[0]));
            $code = $padding . substr($code, strlen($match[0]));
            $code = $this->env->compileString($code, $filename);
        } else if (null !== $filename && 'haml' === pathinfo($filename, PATHINFO_EXTENSION)) {
            $code = $this->env->compileString($code, $filename);
        }
        return $this->lexer->tokenize($code, $filename);
    }
}

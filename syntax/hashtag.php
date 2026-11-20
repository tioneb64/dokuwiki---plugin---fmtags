<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

/**
 * #hashtag dans le texte : devient un lien vers la liste des fiches portant ce tag.
 *
 * Un # collé au mot qui suit (jamais « # Titre »), précédé d'un non-mot (donc pas page#ancre),
 * commençant par une lettre (donc ni #1 ni &#39;). Le code, les liens et les adresses ne sont
 * pas concernés : le lexer les traite avant.
 */
class syntax_plugin_fmtags_hashtag extends SyntaxPlugin
{
    /** @inheritdoc */
    public function getType()
    {
        return 'substition';
    }

    /** @inheritdoc */
    public function getPType()
    {
        return 'normal';
    }

    /** @inheritdoc */
    public function getSort()
    {
        return 300;
    }

    /** @inheritdoc */
    public function connectTo($mode)
    {
        if (!plugin_load('helper', 'fmeta')) return;      // sans fmeta, #mot reste du texte
        // Le lexer travaille en octets : les lettres accentuées UTF-8 sont les octets C3 80-BF.
        // Le / s'écrit \x2F : le lexer double les / à l'intérieur d'une classe de caractères.
        $this->Lexer->addSpecialPattern(
            '\B#(?:[A-Za-z]|\xC3[\x80-\xBF])(?:[A-Za-z0-9_\x2F-]|\xC3[\x80-\xBF])*',
            $mode,
            'plugin_fmtags_hashtag'
        );
    }

    /** @inheritdoc */
    public function handle($match, $state, $pos, Handler $handler)
    {
        return rtrim(substr($match, 1), '-/_');
    }

    /** @inheritdoc */
    public function render($format, Doku_Renderer $renderer, $data)
    {
        if ($format === 'xhtml') {
            $renderer->doc .= plugin_load('helper', 'fmeta')->tagLink($data);
            return true;
        }
        if ($format === 'text' || $format === 'odt') {
            $renderer->cdata('#' . $data);
            return true;
        }
        return false;
    }
}

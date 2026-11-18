<?php

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\Parsing\Handler;

/**
 * <tagcloud>              nuage de tous les tags
 * <taglist ia projet>      pages ayant TOUS ces tags
 * <taglist ia status=validee nature=projet>   avec filtres supplémentaires
 */
class syntax_plugin_fmtags extends SyntaxPlugin
{
    /** @inheritdoc */
    public function getType()
    {
        return 'substition';
    }

    /** @inheritdoc */
    public function getPType()
    {
        return 'block';
    }

    /** @inheritdoc */
    public function getSort()
    {
        return 251;
    }

    /** @inheritdoc */
    public function connectTo($mode)
    {
        $this->Lexer->addSpecialPattern('<tagcloud\s*/?>', $mode, 'plugin_fmtags');
        $this->Lexer->addSpecialPattern('<taglist\b[^>]*>', $mode, 'plugin_fmtags');
    }

    /** @inheritdoc */
    public function handle($match, $state, $pos, Handler $handler)
    {
        if (str_starts_with($match, '<tagcloud')) return ['cloud'];

        $tags = [];
        $filters = [];
        preg_match_all('/(?:[^\s=>]+=(?:"[^"]*"|\S+)|[^\s>]+)/u', substr($match, 8, -1), $m);
        foreach ($m[0] as $token) {
            if (str_contains($token, '=')) {
                [$k, $v] = explode('=', $token, 2);
                $filters[mb_strtolower($k)] = trim($v, '"');
            } elseif (trim($token, '/') !== '') {
                $tags[] = ltrim($token, '#');
            }
        }
        return ['list', $tags, $filters];
    }

    /** @inheritdoc */
    public function render($format, Doku_Renderer $renderer, $data)
    {
        if ($format !== 'xhtml') return false;
        // le contenu dépend de l'index et des droits de lecture
        $renderer->nocache();
        /** @var helper_plugin_fmeta $fm */
        $fm = plugin_load('helper', 'fmeta');

        if ($data[0] === 'cloud') {
            $renderer->doc .= $this->cloud($fm);
            return true;
        }

        [, $tags, $filters] = $data;
        $rows = $fm->query($filters);
        foreach ($tags as $t) {
            $t = mb_strtolower($t);
            $rows = array_values(array_filter($rows, static fn($e) => in_array($t, array_map('mb_strtolower', $e['tags']), true)));
        }
        $renderer->doc .= $fm->table($rows, false);
        return true;
    }

    /** Nuage : la taille de chaque tag suit son nombre de fiches */
    public function cloud(helper_plugin_fmeta $fm): string
    {
        $counts = $fm->tagCounts();
        if (!$counts) return '<p>' . hsc($this->getLang('no_tags')) . '</p>';

        $max = max($counts);
        $h = '<ul class="plugin_fmtags_cloud">';
        foreach ($counts as $tag => $n) {
            $size = 0.85 + 0.9 * ($n / $max);
            $h .= '<li style="font-size:' . round($size, 2) . 'em"><a class="fm-tag" href="' . $fm->url('tag', $tag) . '">#' .
                hsc($tag) . '</a> <span class="fmt-n">' . $n . '</span></li>';
        }
        return $h . '</ul>';
    }
}

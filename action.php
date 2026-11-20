<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\Event;
use dokuwiki\Extension\EventHandler;

/** Page ?do=fmtags : nuage de tous les hashtags */
class action_plugin_fmtags extends ActionPlugin
{
    /** Le plugin fmeta, dont ce plugin dépend, manque : message pour les gestionnaires du wiki */
    public function missingBase(Event $event)
    {
        if (auth_ismanager()) msg(sprintf($this->getLang('need_fmeta'), 'fmtags'), -1);
    }

    /** @inheritdoc */
    public function register(EventHandler $controller)
    {
        if (!plugin_load('helper', 'fmeta')) {
            // le plugin de base manque : on prévient les gestionnaires et on ne branche rien d'autre
            $controller->register_hook('DOKUWIKI_STARTED', 'AFTER', $this, 'missingBase');
            return;
        }
        $controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'actPreprocess');
        $controller->register_hook('TPL_ACT_UNKNOWN', 'BEFORE', $this, 'tplUnknown');
    }

    public function actPreprocess(Event $event)
    {
        if ($event->data === 'fmtags') {
            $event->preventDefault();
            $event->stopPropagation();
        }
    }

    public function tplUnknown(Event $event)
    {
        if ($event->data !== 'fmtags') return;
        $event->preventDefault();

        /** @var helper_plugin_fmeta $fm */
        $fm = plugin_load('helper', 'fmeta');
        /** @var syntax_plugin_fmtags $syntax */
        $syntax = plugin_load('syntax', 'fmtags');
        echo '<h1>' . hsc($this->getLang('title')) . '</h1>';
        echo $syntax->cloud($fm);
    }
}

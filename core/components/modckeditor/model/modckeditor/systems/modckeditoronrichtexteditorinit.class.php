<?php

class modCKEditorOnRichTextEditorInit extends modCKEditorPlugin
{
    public function run()
    {
        $useEditor = $this->modx->getOption('use_editor', false);
        $whichEditor = $this->modx->getOption('which_editor', '');
        /** @var modResource $resource */
        $resource = $this->modx->getOption('resource', $this->scriptProperties);

        if (
            !$useEditor
            OR
            $whichEditor != 'modckeditor'
            OR
            ($resource AND !$richtext = $resource->get('richtext'))
        ) {
            return;
        }

        /** @var modManagerController $controller */
        $controller = &$this->modx->controller;
        $this->modCKEditor->loadControllerJsCss($controller, array(
            'css'      => true,
            'config'   => true,
            'tools'    => true,
            'ckeditor' => true
        ));

    }

}
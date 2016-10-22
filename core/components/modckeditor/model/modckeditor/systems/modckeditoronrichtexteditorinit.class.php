<?php

class modCKEditorOnRichTextEditorInit extends modCKEditorPlugin
{
    public function run()
    {
        /** @var modResource $resource */
        $resource = $this->modx->getOption('resource', $this->scriptProperties);
        if (
            !$this->initEditor
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
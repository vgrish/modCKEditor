<?php

class modCKEditorOnRichTextEditorInit extends modCKEditorPlugin
{
    public function run()
    {
        if (!$this->initEditor) {
            return;
        }

        /** @var modManagerController $controller */
        $controller = &$this->modx->controller;
        $this->modCKEditor->loadControllerJsCss($controller, array(
            'css'      => true,
            'config'   => true,
            'tools'    => true,
            'ckeditor' => true,
        ), $this->scriptProperties);

    }

}
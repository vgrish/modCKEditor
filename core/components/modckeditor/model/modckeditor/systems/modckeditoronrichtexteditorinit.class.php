<?php

class modCKEditorOnRichTextEditorInit extends modCKEditorPlugin
{
    public function run()
    {
        if (!$this->initEditor) {
            return;
        }

        $this->modCKEditor->loadControllerJsCss($this->modx->controller, array(
            'css'      => true,
            'config'   => true,
            'tools'    => true,
            'ckeditor' => true,
        ), $this->scriptProperties);

    }

}
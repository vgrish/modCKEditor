<?php

class modCKEditorOnRichTextEditorInit extends modCKEditorPlugin
{
    public function run()
    {
        if (!$this->modCKEditor->initEditor) {
            return;
        }

        $output = $this->modCKEditor->loadControllerJsCss($this->modx->controller, array(
            'css'      => true,
            'config'   => true,
            'tools'    => true,
            'ckeditor' => true,
        ), $this->scriptProperties);

        $this->modx->event->output($output);
    }

}
<?php

class modCKEditorOnRichTextEditorInit extends modCKEditorPlugin
{
    public function run()
    {
        $this->modx->log(1, print_r('modCKEditorOnRichTextEditorInit', 1));

        $this->modx->log(1, print_r(array_keys($this->scriptProperties), 1));

        /** @var modResource $resource */
        if (
            !$resource = $this->modx->getOption('resource', $this->scriptProperties)
            OR
            !$richtext = $resource->get('richtext')
        ) {
            return;
        }

        $editor = $this->modx->getOption('editor', $this->scriptProperties);

        $this->modx->log(1, print_r($editor, 1));
        //$this->modx->log(1, print_r($resource->toArray(), 1));

        if ($editor != 'modckeditor') {
            return;
        }

        /** @var modManagerController $controller */
        $controller = &$this->modx->controller;
        $this->modCKEditor->loadControllerJsCss($controller, array(
            'css'      => true,
            'config'   => true,
            'tools'    => true,
            'ckeditor' => true,
        ));

        $this->modx->invokeEvent('modCKEditorOnLoadControllerJsCss',
            array('controller' => $controller, 'resource' => $resource));

    }

}
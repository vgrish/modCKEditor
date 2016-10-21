<?php

class modCKEditorOnRichTextEditorRegister extends modCKEditorPlugin
{
    public function run()
    {
        $this->modx->log(1, print_r('modCKEditorOnRichTextEditorRegister', 1));


        $this->modx->event->output('modckeditor');
    }

}
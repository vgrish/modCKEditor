<?php

class modCKEditorOnRichTextBrowserInit extends modCKEditorPlugin
{

    public function run()
    {
        if (!$this->modCKEditor->initEditor) {
            return;
        }

        $funcNum = isset($_REQUEST['CKEditorFuncNum']) ? $_REQUEST['CKEditorFuncNum'] : null;
        $this->modx->event->output("function(data){
        window.parent.opener.CKEDITOR.tools.callFunction({$funcNum}, data.fullRelativeUrl);
        }");
    }

}
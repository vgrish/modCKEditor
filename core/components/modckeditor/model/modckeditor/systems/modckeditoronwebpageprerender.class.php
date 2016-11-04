<?php

class modCKEditorOnWebPagePrerender extends modCKEditorPlugin
{
    public function run()
    {
        if ($this->modCKEditor->getOption('remove_devtags', null, false, true)) {
            $output = $this->modx->resource->_output;
            $output = preg_replace('/<pre>(.*)<devtags[^>]*>|<\/devtags>(.*)<\/pre>/Usi', '\\1', $output);
            $this->modx->resource->_output = $output;
        }
    }

}
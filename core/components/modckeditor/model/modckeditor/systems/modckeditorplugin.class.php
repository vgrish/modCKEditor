<?php

abstract class modCKEditorPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var modCKEditor $modCKEditor */
    protected $modCKEditor;
    /** @var array $scriptProperties */
    protected $scriptProperties;


    public function __construct(& $modx, &$scriptProperties)
    {
        $this->scriptProperties =& $scriptProperties;
        $this->modx = &$modx;
        $this->modCKEditor = &$this->modx->modckeditor;

        if (!$this->modCKEditor) {
            return;
        }

        $this->modCKEditor->initialize($this->modx->context->key);
    }

    abstract public function run();
}
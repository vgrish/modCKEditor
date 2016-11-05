<?php


class modCKEditorFileBrowserUploadProcessor extends modProcessor
{
    /** @var modCKEditor $modCKEditor */
    public $modCKEditor;

    /** @var modMediaSource $source */
    public $source;
    /** @var xPDOObject $_mo */
    public $_mo = null;

    public function checkPermissions()
    {
        return $this->modx->hasPermission('file_upload');
    }

    public function getLanguageTopics()
    {
        return array('file');
    }

    public function initialize()
    {
        $this->setDefaultProperties(array(
            'source'   => 1,
            'filePath' => false,
            'fileName' => false,
        ));

        $this->modCKEditor = $this->modx->getService('modckeditor');
        $this->modCKEditor->initialize($this->getProperty('wctx'));

        return true;
    }

    public function process()
    {
        if (!$this->getSource()) {
            return $this->_failure($this->modx->lexicon('permission_denied'));
        }

        if (!$path = $this->getFilePath()) {
            return $this->_failure($this->modx->lexicon('file_folder_err_ns'));
        }

        if (!$files = $this->getFiles()) {
            return $this->_failure($this->modx->lexicon('file_folder_err_ns'));
        }

        $success = $this->source->uploadObjectsToContainer($path, $files);
        if (empty($success)) {
            $errors = $this->source->getErrors();

            return $this->_failure($errors, $files);
        }

        return $this->_success('', $files);
    }

    /**
     * Get the active Source
     * @return modMediaSource|boolean
     */
    public function getSource()
    {
        $this->modx->loadClass('sources.modMediaSource');
        $this->source = modMediaSource::getDefaultSource($this->modx, $this->getProperty('source'));
        if (empty($this->source) || !$this->source->getWorkingContext()) {
            return false;
        }

        $this->source->setRequestProperties($this->getProperties());
        $this->source->initialize();
        if (!$this->source->checkPolicy('create')) {
            return $this->_failure($this->modx->lexicon('permission_denied'));
        }

        return $this->source;
    }


    public function getFilePath()
    {
        if (empty($this->source)) {
            return false;
        }

        $path = $this->getProperty('filePath');
        if (empty($path)) {
            $tmp = $this->_getFilePath();
            $tmp = $this->_process($tmp);

            $path = '';
            foreach (explode('/', rtrim($tmp, '/')) as $dir) {
                $path .= $dir . '/';
                $this->source->createContainer($path, '/');
            }

            $this->source->createContainer($path, '/');
            $this->source->errors = array();
            $this->setProperty('filePath', $path);
        }

        return $path;
    }


    public function getFiles()
    {
        $files = (array)$_FILES;
        foreach ($files as $k => $row) {
            $type = $this->getFileType($row);
            $name = $this->getFileName($row);

            $files[$k]['name'] = $name . '.' . $type;
        }

        return $files;
    }

    public function getFileType(array $row = array())
    {
        $name = $this->modx->getOption('name', $row);

        $type = explode('.', $name);
        $type = end($type);
        $this->setProperty('type', $type);

        return $type;
    }

    public function getFileName(array $row = array())
    {
        $tmp = $this->modx->getOption('tmp_name', $row);
        $name = $this->modx->getOption('name', $row);

        $type = explode('.', $name);
        $type = end($type);
        $name = rtrim(str_replace($type, '', $name), '.');

        $nameType = $this->_getFileNameType();
        switch ($nameType) {
            case 'friendly':
                /** @var  modResource $resource */
                $resource = $this->modx->newObject('modResource');
                $name = $resource->cleanAlias($name, array(
                    'friendly_alias_lowercase_only' => true
                ));
                break;
            case 'hash':
            default:
                $name = hash_file('sha1', $tmp);
                break;
        }
        $name = $this->_process($name);
        $this->setProperty('name', $name);

        return $name;
    }

    public function _getFileName()
    {
        $value = $this->modCKEditor->getOption('source_fileName', null, '{name}.{ext}', true);
        if (!empty($this->source)) {
            $value = $this->modCKEditor->getOption('fileName', $this->source->properties, $value, true);
        }

        return $value;
    }

    public function _getFilePath()
    {
        $value = $this->modCKEditor->getOption('source_filePath', null, '{class_key}/{id}/', true);
        if (!empty($this->source)) {
            $value = $this->modCKEditor->getOption('filePath', $this->source->properties, $value, true);
        }

        return $value;
    }

    public function _getFileNameType()
    {
        $value = $this->modCKEditor->getOption('source_fileNameType', null, 'friendly', true);
        if (!empty($this->source)) {
            $value = $this->modCKEditor->getOption('fileNameType', $this->source->properties, $value, true);
        }

        return $value;
    }

    public function _getMainObject()
    {
        if (is_null($this->_mo)) {
            $this->_mo = $this->modx->getObject($this->getProperty('class_key'), $this->getProperty('id'));
        }

        return $this->_mo;
    }

    public function _getMainArray()
    {
        $array = array(
            'id'        => 0,
            'class_key' => 'default',
            'session'   => session_id(),
            'createdby' => $this->modx->user->id,
            'rand'      => strtr(base64_encode(openssl_random_pseudo_bytes(2)), "+/=", "zzz"),
            'source'    => $this->getProperty('source'),
            'name'      => $this->getProperty('name'),
            'alias'     => $this->getProperty('alias'),
            'ext'       => $this->getProperty('type')
        );

        if ($this->_getMainObject()) {
            $array = array_merge($array, $this->_getMainObject()->toArray());
        }

        return $array;
    }

    public function _getPls()
    {
        $pls = array(
            'pl' => array(),
            'vl' => array()
        );

        $array = $this->_getMainArray();
        foreach ($array as $k => $v) {
            $pls['pl'][] = '{' . $k . '}';
            $pls['vl'][] = $v;
        }

        return $pls;
    }

    public function _process($tmp = '')
    {
        $pls = $this->_getPls();
        $result = strtolower(str_replace($pls['pl'], $pls['vl'], $tmp));

        return $result;
    }

    public function _success($message = '', array $data = array())
    {
        $response = array(
            'url'      => '',
            'fileName' => '',
            'uploaded' => true,
            'data'     => $data,
            'error'    => array(
                'message' => is_array($message) ? implode('<br>', $message) : $message
            )
        );

        $upload = isset($data['upload']) ? $data['upload'] : $data['file'];
        if (isset($upload['name'])) {
            $response['fileName'] = $upload['name'];
            $response['url'] = $this->source->getBaseUrl() . ltrim($this->getProperty('filePath'),
                    '/') . $upload['name'];
        }

        $funcNum = $this->getProperty('CKEditorFuncNum');
        if (is_null($funcNum)) {
            return json_encode($response, 1);
        }

        return "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction('{$funcNum}', '{$response['url']}', '');</script>";
    }

    public function _failure($message = '', array $data = array())
    {
        $response = array(
            'url'      => '',
            'fileName' => '',
            'uploaded' => false,
            'data'     => $data,
            'error'    => array(
                'message' => is_array($message) ? implode('<br>', $message) : $message
            )
        );

        return json_encode($response, 1);
    }
}

return 'modCKEditorFileBrowserUploadProcessor';
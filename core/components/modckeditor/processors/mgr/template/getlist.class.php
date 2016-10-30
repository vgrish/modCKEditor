<?php

class modCKEditorTemplateGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modTemplate';
    public $languageTopics = array('template', 'category');
    public $defaultSortField = 'templatename';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modCategory', 'Category');
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select(array(
            'category_name' => 'Category.category',
        ));

        $categories = $this->modx->getOption('mcked_template_categories', null, '');
        $categories = array_map('trim', explode(',', $categories));
        if (!empty($categories)) {
            $c->where(array(
                'Category.category:IN' => $categories,
            ));
        }

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'templatename:LIKE' => "$query%"
            ));
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        return $array;
    }

}

return 'modCKEditorTemplateGetListProcessor';
<?php

class ESaveGreedViewState extends CActiveRecordBehavior {

    public $defaults=array();

    public $defaultStickOnClear=false;

	private $_rememberScenario=null;


    private function getStatePrefix() {
	    $modelName = get_class($this->owner);
	    if ($this->_rememberScenario!=null) {
	        return $modelName.$this->_rememberScenario;
	    } else {
	        return $modelName;
	    }
	}

    private function returnGridValues() {
        $modelName = get_class($this->owner);
        $attributes = $this->owner->getSafeAttributeNames();
        if (is_array($this->defaults) && (null==Yii::app()->user->getState($modelName . __CLASS__. 'defaultsSet', null))) {
            foreach ($this->defaults as $attribute => $value) {
                if (null == (Yii::app()->user->getState($this->getStatePrefix() . $attribute, null))) {
                    Yii::app()->user->setState($this->getStatePrefix() . $attribute, $value);
                }
            }
            Yii::app()->user->setState($modelName . __CLASS__. 'defaultsSet', 1);
        }
        foreach ($attributes as $attribute) {
            if (null != ($value = Yii::app()->user->getState($this->getStatePrefix() . $attribute, null))) {
                try
                {
                    $this->owner->$attribute = $value;
                }
                catch (Exception $e) {
                }
            }
        }
        $_GET[get_class($this->owner).'_page']=Yii::app()->user->getState($this->getStatePrefix().'_page');
        $_GET[get_class($this->owner).'_sort']=Yii::app()->user->getState($this->getStatePrefix().'_sort');
    }

    private function doSave() {

        $modelName = get_class($this->owner);
        $attributes = $this->owner->getSafeAttributeNames();
        foreach ($attributes as $attribute) {
            if (isset($this->owner->$attribute)) {
                Yii::app()->user->setState($this->getStatePrefix() . $attribute, $this->owner->$attribute);
            }
        }
    }

    public function afterConstruct($event) {
        $this->saveGridValues();
        $this->returnGridValues();
    }

    private function saveGridValues() {

        if ($this->owner->scenario == 'search') {
            $this->owner->unsetAttributes();
            if (isset($_GET[get_class($this->owner)])) {
                $this->owner->attributes = $_GET[get_class($this->owner)];
                $this->doSave();
            }
                $this->savePageSort();
        }
    }
    private function savePageSort()
    {
        if (isset($_GET[get_class($this->owner).'_page']))
        {
            Yii::app()->user->setState($this->getStatePrefix().'_page', $_GET[get_class($this->owner).'_page']);
        }
        elseif(isset($_GET['ajax']))
        {
            Yii::app()->user->setState($this->getStatePrefix().'_page', 1);
        }
        if (isset($_GET[get_class($this->owner).'_sort']))
        {
            Yii::app()->user->setState($this->getStatePrefix().'_sort', $_GET[get_class($this->owner).'_sort']);
        }
        elseif(isset($_GET['ajax']))
        {
            Yii::app()->user->setState($this->getStatePrefix().'_sort', "");
        }
    }


    public function unsetFilters() {
        $modelName = get_class($this->owner);
        $attributes = $this->owner->getSafeAttributeNames();

        foreach ($attributes as $attribute) {
            if (null != ($value = Yii::app()->user->getState($this->getStatePrefix() . $attribute, null))) {
                Yii::app()->user->setState($this->getStatePrefix() . $attribute, 1, 1);
            }
        }
        if ($this->defaultStickOnClear) {
            Yii::app()->user->setState($modelName . __CLASS__. 'defaultsSet', 1,1);
        }
        Yii::app()->user->setState($this->getStatePrefix().'_sort', "");
        Yii::app()->user->setState($this->getStatePrefix().'_page', 1);
        return $this->owner;
    }
}
?>
ESaveGreedViewState
===================

Extensions to store filters, sorting and pages

В Вашей модели:

    public function behaviors() {
        return array(
            'ESaveGreedViewState' => array(
                'class' => 'common.modules.YOnixCommon.behaviors.ESaveGreedViewState.ESaveGreedViewState',
            ),
        );
    }
	
В Вашем экшене контролера заменить:
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Articles']))
			$model->attributes=$_GET['Articles']; 
на: 
		if (isset($_GET['unsetFilter']) && $_GET['unsetFilter']=='yes') {
            $model->unsetFilters();
            $this->redirect(array('action_name'));
        }
		
Когда нужно сбросить фильтры, просто передайте в Ваш экшн переменную 'unsetFilter'=>'yes' методом GET.
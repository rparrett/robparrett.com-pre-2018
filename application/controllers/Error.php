<?php
class ErrorController extends Yaf_Controller_Abstract {
	private $_layout;

    public function init(){
		$this->_layout = new LayoutPlugin('layout.phtml');
		Yaf_Dispatcher::getInstance()->autoRender(false);
    }

    public function errorAction($exception) {
		if (Yaf_Application::app()->getConfig()->application->showDetailedErrors) {
			$this->_view->message = $exception->getMessage();
        	$this->_view->trace = $exception->getTraceAsString();
		}

		switch ($exception->getCode()) {
			case YAF_ERR_NOTFOUND_MODULE:
			case YAF_ERR_NOTFOUND_CONTROLLER:
			case YAF_ERR_NOTFOUND_ACTION:
			case YAF_ERR_NOTFOUND_VIEW:
				header('HTTP/1.0 404 Not Found');
				$this->_view->error = '404 Not Found';
				break;
			default:
				header('HTTP/1.0 500 Internal Server Error');
				$this->_view->error = '500 Internal Server Error';
		}

		// Something rather strange is happening with auto-rendering in this
		// ErrorController which is preventing the LayoutPlugin from working
		// correctly. Workaround:

		$this->getResponse()->setBody($this->_view->render("error/error.phtml"));
		$this->_layout->postDispatch($this->getRequest(), $this->getResponse());
    }
}

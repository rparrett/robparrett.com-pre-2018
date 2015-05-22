<?php
class LayoutPlugin extends Yaf_Plugin_Abstract
{
    private $layoutDir;
    private $layoutFile;
    private $layoutVars = array();

    public function __construct($layoutFile, $layoutDir = null)
    {
        $this->layoutFile = $layoutFile;
        $this->layoutDir = ($layoutDir) ? $layoutDir : APPLICATION_PATH . '/application/views/';
    }

    public function __set($name, $value)
    {
        $this->layoutVars[$name] = $value;
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $body = $response->getBody();
        
        $response->clearBody();
        
        $layout = new Yaf_View_Simple($this->layoutDir);
        $layout->content = $body;
        $layout->assign('layout', $this->layoutVars);
        
        $response->setBody($layout->render($this->layoutFile));
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }

    public function preResponse(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
    }
}

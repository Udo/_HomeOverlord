<?php
/*
 * The H2Dispatcher coordinates the server's response 
 */
 
class H2Dispatcher
{

  function __construct(&$req)
  {
    $this->req = &$req;
    profile_point('H2Dispatcher');
  }
  
  /*
   * set up the request environment
   */
  function initEnvironment()
  {
    o(new H2Configuration(), config)
      ->load();
    load_l10n('');
    o(new H2Database(), db)
      ->connect();
    profile_point('H2Dispatcher.initEnvironment()');
    date_default_timezone_set($GLOBALS['config']['geo']['timezone']);
    return($this);
  }
  
  /*
   * receive and parse request data
   */
  function receiveData()
  {
    return($this);
  }
  
  /*
   * create the controller object
   */
  function initController($controllerName)
  {
    $controllerName = first($controllerName, cfg('service/defaultcontroller'));
  	$this->controllerName = safeName($controllerName);
  	$this->controllerFile = 'mvc/'.strtolower($this->controllerName).'/'.strtolower($this->controllerName).'.controller.php';
  	if(!file_exists($this->controllerFile))
  	{
	    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	    header('Status: 404 Not Found');	
	    die('File not found at: '.$_SERVER['REQUEST_URI'].'<br/>Controller: '.$this->controllerName);     
  	}
  	require_once($this->controllerFile);
    $this->controllerClassName = $this->controllerName.'Controller';
  	$this->controller = o(new $this->controllerClassName($this->controllerName), 'controller');
  	if (is_callable(array($this->controller, '__init'))) $this->controller->__init();
  	$_REQUEST['controller'] = $this->controllerName;
    profile_point('H2Dispatcher.initController('.$this->controllerName.')');
  	return($this);
  }
  
  /*
   * execute the action
   */
  function invokeAction($action)
  {
  	$_REQUEST['action'] = basename(first($action, 'index'));
    ob_start();
    $this->controller->invokeAction($_REQUEST['action'], $this->params);
    $this->controller->lastAction = $_REQUEST['action'];
    $this->actionOutput = trim(ob_get_clean());
    profile_point('H2Dispatcher.invokeAction('.$_REQUEST['action'].')');
    return($this);
  }
  
  /*
   * render the view
   */
  function invokeView($outletName)
  {
    $GLOBALS['content'][$outletName] = 
      $this->actionOutput.
      $this->controller->invokeView($this->controller->lastAction);
    profile_point('H2Dispatcher.invokeView('.$outletName.')');
  	return($this);
  }
  
  /*
   * render the template
   */
  function invokeTemplate($templateName)
  {
  	switch($templateName)
  	{
  		case('blank'): {
  			print($GLOBALS['content']['main']);
  			break;
  		}		
  		default: {
  			header('content-type: text/html;charset=UTF-8');
        require('themes/'.cfg('theme/name', 'default').'/'.$templateName.'.php');
        break;
  		}
  	}
    profile_point('H2Dispatcher.invokeTemplate('.$templateName.')');
    return($this);
  }
    
  /*
   * clean up request objects
   */
  function cleanup()
  {
    return($this);
  }
  

}
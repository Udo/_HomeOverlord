<?php

class H2Controller
{
  function __construct($name)
  {
    $this->name = $name;
    load_l10n($name.'/');
  }
  
  function access($policy)
  {
    if(!is_array($policy))
      $policy = explode(',', $policy);
    $canAccess = false;
    
    foreach($policy as $p)
    {
      switch($p)
      {
        case('local'):
        {
          if($_SERVER['REMOTE_ADDR'] == '127.0.0.1') $canAccess = true;
          break;
        }
        case('internal'):
        {
          $i = $_SERVER['REMOTE_ADDR'];
          $f = CutSegment('.', $i);
          if($f == '10' && $_SERVER['HTTP_X_FORWARDED_FOR'] == '') $canAccess = true;
          break;
        }
        case('auth'):
        {
          if($_SESSION['uid'] > 0) $canAccess = true;
          break;
        }
      }
    }
    
    if(!$canAccess)
    {
      header('location: '.actionUrl('signin', 'account'));
      die();
    }
    
    return($canAccess);
  }
  
	function invokeAction($action, $params = null)
  {
    $action = first($action, cfg('service/defaultaction'));
		$this->lastAction = $action;
    if($params == null) $params = &$_REQUEST;

    // by convention, actions starting with "ajax_" don't return the whole page template
    // since they're intended to be partial content 
    if(substr($action, 0, 5) == 'ajax_')
    {
      $this->skipView = true;
      $GLOBALS['config']['page']['template'] = 'blank';
      //$this->accessPolicy('origin');
    }
        
    if(is_callable(array($this, $action)))
      $output = $this->$action($params);
    else 
      logError('Action not defined: '.get_class($this).'->'.$action.'()');
      
    if($output != null) print($output);
      
    $GLOBALS['config']['page']['title'] = $action;      
    
    return($this);
  }

	function invokeView($action)
	{
    ob_start();
    $action = first($action, cfg('service/defaultaction'));
		if(!$this->skipView)
		{
      require('mvc/'.strtolower($this->name).'/'.strtolower($this->name).'.'.first($this->viewName, $action).'.php');
		} 

    $this->pageTitle = first($this->pageTitle, l10n($action.'.title', true), l10n($this->name.'.'.$action));
    cfg('page/title', $this->pageTitle, true);
    $output = ob_get_clean();

    return($output);			
	}
	


}
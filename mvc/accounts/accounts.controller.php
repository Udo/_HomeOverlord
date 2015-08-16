<?php

class AccountsController extends H2Controller
{
  function index()
  {
  }
  
  function signin()
  {
    if($_POST['fid'] == sha1($_SESSION['fid']))
    {
      $uds = o(db)->getDSMatch('accounts', array(
        'a_username' => trim($_POST['username']),
        'a_password' => sha1($_POST['password']),
        ));
      if($uds['a_key'] > 0)
      {
        WriteToFile('log/account.log', date('Y-m-d H:i:s').' (i) '.$_SERVER['HTTP_X_FORWARDED_FOR'].' : '.$_SERVER['REMOTE_ADDR'].' sign in with '.
          trim($_POST['username']).'/***'.chr(10));
        $_SESSION['uid'] = $uds['a_key'];
        $_SESSION['ds'] = $uds;
        header('location: ./?');
        die();
      }
      else
      {
        WriteToFile('log/account.log', date('Y-m-d H:i:s').' (f) '.$_SERVER['HTTP_X_FORWARDED_FOR'].' : '.$_SERVER['REMOTE_ADDR'].' sign in failed with '.
          trim($_POST['username']).'/***'.chr(10));
        ?><div style="text-align: center">Error signing in, try again.</div><?
      }
    }
    else
    {
      WriteToFile('log/account.log', date('Y-m-d H:i:s').' (a) '.$_SERVER['HTTP_X_FORWARDED_FOR'].' : '.
        gethostbyaddr($_SERVER['HTTP_X_FORWARDED_FOR']).' signin page '.chr(10));
    }
  }

}


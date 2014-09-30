<?
if($_POST['mode'])
{
  $mode = new H2Mode();
  $mode->set($_REQUEST['mode']);
}

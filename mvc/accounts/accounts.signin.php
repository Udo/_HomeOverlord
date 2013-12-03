<?


$_SESSION['fid'] = first($_SESSION['fid'], md5(mt_rand(1, 100000).time()));  

?><h1 style="text-align: center;">Sign In</h1>
<form action="./?" method="post">
<input type="hidden" name="controller" value="accounts"/>
<input type="hidden" name="action" value="signin"/>
<input type="hidden" name="fid" value="<?= sha1($_SESSION['fid']) ?>"/>
<table width="400" align="center" class="pane">
  <tr>
    <td>User</td>
    <td><input type="text" name="username" value="<?= htmlspecialchars($_POST['username']) ?>" id="f_name" placeholder="user name"/></td>
  </tr>
  <tr>
    <td>Password</td>
    <td><input type="password" name="password" value="<?= htmlspecialchars($_POST['password']) ?>" placeholder="password"/></td>
  </tr>
  <tr>
    <td></td>
    <td><input type="submit" value="Sign In"/></td>
  </tr>

</table>
<script>
  document.getElementById('f_name').focus();
</script>
</form>
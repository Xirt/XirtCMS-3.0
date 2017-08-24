<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">


<head>

  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

</head>

<body bgcolor="#8A8A8A">

<div align="center">

<table bgcolor="#ffffff" cellpadding="15" cellspacing="0" width="600" border="0">
<tr>
	<td style="color: #153643; font-family: Arial, sans-serif; font-size: 11px;">


	<p align="center"><img src="<?php echo $target; ?>/assets/images/cms/logo.png" alt="" /></p>

	<hr />
	

	<p>Dear <?php echo $name; ?>,</p>

	<p>A new password has been created for your account at <a href='<?php echo $target; ?>' target='_blank'><?php echo $target; ?></a>. In this e-mail you will find your new login details for this account.</p>

	<table>
	<tr>
		<td width="150"><font size="2"><b>Username</b></font></td>
		<td><font size="2"><?php echo $username; ?></font></td>
	</tr>
	<tr>
		<td width="150"><font size="2"><b>Password</b></font></td>
		<td><font size="2"><?php echo $password; ?></font></td>
	</tr>
	</table>

    	
    <p>You cannot react to this e-mail; it has been automatically generated.</p>

	
	<hr />


	</td>
</tr>
</table>

</div>

</body>

</html>
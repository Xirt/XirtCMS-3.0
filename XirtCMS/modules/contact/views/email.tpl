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
		

		<p>Dear recipient,</p>

		<p>A form was submitted on <?php echo $target; ?> containing the following information:</p>

		<table>
		<tr>
			<td width="150"><font size="2"><b>Name</b></font></td>
			<td><font size="2"><?php echo $data->name; ?></font></td>
		</tr>
		<tr>
			<td width="150"><font size="2"><b>Company</b></font></td>
			<td><font size="2"><?php echo $data->company; ?></font></td>
		</tr>
		<tr>
			<td width="150"><font size="2"><b>E-mail address</b></font></td>
			<td><font size="2"><?php echo $data->email; ?></font></td>
		</tr>
		<tr>
			<td width="150"><font size="2"><b>Phone number</b></font></td>
			<td><font size="2"><?php echo $data->phone; ?></font></td>
		</tr>
		<tr>
			<td width="150"><font size="2"><b>Subject</b></font></td>
			<td><font size="2"><?php echo $data->subject; ?></font></td>
		</tr>
		<tr>
			<td width="150"><font size="2"><b>Content</b></font></td>
			<td><font size="2"><?php echo $data->content; ?></font></td>
		</tr>
		</table>

		<p>Please do not respond to this this e-mail; it has been automatically generated.</p>

		<hr />

		</td>
	</tr>
	</table>

</div>

</body>

</html>
<HTML>
	<BODY>
		<form enctype="multipart/form-data" action="" method="POST">
	Send this file: <input name="userfile" type="file" />
					<input type="submit" value="Send File" />
</form>
<?php
echo "<PRE>";
print_r([$_POST, $_FILES]);
echo "</PRE>";
?>
	</BODY>
</HTML>

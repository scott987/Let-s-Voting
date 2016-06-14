<!DOCTYPE>
<html>
	<head>
		<title>try</title>
	</head>
	<body>
		<p>try signUp</p>
		<?php
			require_once("MemberDB.php");
			$db = new MemberDB("localhost","MyDatabase","root","xbc3608a");
			
			//echo "this account has been used</br>";
			echo "(121,122,123,124)</br>";
			$js=$db->signUp("121","122","123","124");
			var_dump($js);
			//echo "</br>this example can be signed up</br>";
			echo "</br>(ddd,456,aaa,bbb)</br>";
			$js=$db->signUp("ddd","456","aaa","bbb");
			var_dump($js);
			echo "</br>(ccc,789,kkk,lll)</br>";
			$js=$db->signUp("ccc","789","kkk","lll");
			var_dump($js);
		?>
		<p>try another function getUserInformation</p>
		<?php
			echo "acount=123 password=123</br>";
			$js=$db->getUserInformation("123","123");
			var_dump($js);
			echo "</br>account=456 password=456789</br>";
			$js=$db->getUserInformation("456","456789");
			var_dump($js);
			echo "</br>account =ooo password=0806</br>";
			$js=$db->getUserInformation("ooo","0806");
			var_dump($js);
		?>
		<p>try another function existThisUser</p>
		<?php
			$js=$db->existThisUser("123","122");
			echo "wrong example</br>";
			var_dump($js);
			echo "</br>correct example</br>";
			$js=$db->existThisUser("123","123");
			var_dump($js);
		?>
		<p>try another function getPassword</p>
		<?php
			$js=$db->getPassword("121","123");
			var_dump($js);
			echo "</br>";
			$js=$db->getPassword("xxx","ooo");
			var_dump($js);
		?>
	</body>
</html>
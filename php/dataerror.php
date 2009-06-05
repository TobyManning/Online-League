<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Data error</title>
</head>
<body>
<h1>Data or program error.</h1>
<?php
$qmess = htmlspecialchars($mess);
print <<<EOT
<p>
Sorry but there has been a database or program error.
Message was $qmess.
</p>
EOT;
?>
<p>
Please tell John Collins
<a href="mailto:jmc@xisl.com">jmc@xisl.com</a> about this.
</p>
</body>
</html>

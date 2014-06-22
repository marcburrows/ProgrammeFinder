<?php
// include Programme Finder Class
include "priv/classes/search.class.php";
$search = new search;
?>
<!DOCTYPE html >
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Programme Finder Search Results</title>
<link rel="stylesheet" type="text/css" href="priv/css/style.css" media="screen" />

</head>

<body>
<?php
$search->get_query($_GET['q'], $_GET['page']);
?>

</body>
</html>
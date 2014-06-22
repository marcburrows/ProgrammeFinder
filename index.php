<!DOCTYPE html>
<html>
<head>
<title>Programme Finder</title>
<link rel="stylesheet" type="text/css" href="priv/css/style.css">
</head>
<body>
<form id="form" method="GET" action="results.php">
  <label for="programmeFinder" >Find a programme...</label>
  <input name="q" id="programmeFinder" value="" type="text" placeholder="Find a programme..." autocomplete="off" maxlength="128">
	<input type="hidden" id="page" value="1" name="page">
  <input name="submit" id="submit" type="submit" value="Submit">

</form>
<div id="results">

</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="priv/js/inline-search.js"></script>
<script>

		$('#programmeFinder').keyup(function(){
			if($('#programmeFinder').val().length > 2){
				$.doSearch();
			}
		});


</script> 

</body>
</html>
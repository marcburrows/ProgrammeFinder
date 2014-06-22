$.doSearch = function(){
	$.ajax({
			url: 'priv/ajx/xhr.search.php',
			data: { 
				query: $("#programmeFinder").val()
			},	
			type: 'POST',
			success: function(data) {
				data = eval(data);
				if(data.errors != ''){
					console.log('Error: '+data.errors);	
				} else {
					$("#results").html(data.programmes);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(jqXHR+' '+textStatus+' '+errorThrown);
			},
			dataType: 'json'
		});	
}

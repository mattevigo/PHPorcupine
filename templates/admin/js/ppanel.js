$(function()
{				
	$('#datepicker').datepicker({
		inline: true,
		dateFormat: 'yymmdd',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		dayNamesMin: ['Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa'],
		firstDay: 1,
		gotoCurrent: true,
		onSelect: function(dateText, inst) 
		{ 
			document.location.replace("admin.php?option=mgmt_articles&date="+dateText);
			return false;
		}
	});
	
	//alert(jQuery.url.param('date'));
	$('#datepicker').datepicker('setDate', jQuery.url.param('date'));
	
	$('button').button();
});
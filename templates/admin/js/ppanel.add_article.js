$(document).ready(function()
	{
		// Datepicker style
		$("#article_date").datepicker({ 
			dateFormat: 'dd/mm/yy',
			monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
			dayNamesMin: ['Do', 'Lu', 'Ma', 'Me', 'Gi', 'Ve', 'Sa'],
			firstDay: 1,
			gotoCurrent: true
		});
		
		// Rounded corners
		$("input").addClass("ui-corner-all");
		$("fieldset").addClass("ui-corner-all");
		$("textarea").addClass("ui-corner-all");
	}
);
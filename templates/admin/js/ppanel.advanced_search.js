$(document).ready(function()
{
	$("#search-text-option").buttonset();
	
	$("span#categories-toggle").click(function(){
		$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
		$(".categories").toggle();
		$("#end-search").toggleClass("cleared");
	});
	
	$("span#newspapers-toggle").click(function(){
		$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
		$(".newspapers").toggle();
	});
	
	$("span#date-range-toggle").click(function(){
		$( this ).toggleClass( "ui-icon-minusthick" ).toggleClass( "ui-icon-plusthick" );
		$("div.date-range").toggle();
	});
	
	$( "input.date-range" ).datepicker({
		showOn: "button",
		buttonImage: "images/calendar.gif",
		buttonImageOnly: true
	});

});
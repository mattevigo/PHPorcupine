$(function() {

	// Address of single delete
	var delselected = "http://yahoo.com";

	// Dialog///////////////////////////////////////////////
	$( "#dialog" ).dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Cancella Articolo": function() {
				$( this ).dialog( "close" );
				document.location.replace(delselected);
			},
			"Non Cancellare": function() {
				$( this ).dialog( "close" );
			}
		}
	});

	$("#dialog").attr("class", "ui-helper-reset");

	// Actions //////////////////////////////////////////////
	
	// Delete (single article)
	$(".delbutton").click(function(){
		$("#dialog").dialog("open");
		delselected = $(this).attr("href");
		return false;
	});
	
	// Delete (multiple selected) 
	
	// Print All (multiple selected)
	$("button#printall").show();
	
	$("button#printall").click(function(){
		var post = $('form').serialize(); 
		
		$.post( 'admin.php?script=printall', post, function(data, textStatus, XMLHttpRequest){
			document.location.replace(data);
		});
		
		return false;
	});
});
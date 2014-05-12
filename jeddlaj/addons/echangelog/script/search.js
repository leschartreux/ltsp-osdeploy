function makeOSListe()
{    
    nomLogiciel = $('#nomLogiciel').val();
    
    $.post('index.php?module=echangelog&action=search&view=listeOS', 
    {
	nomLogiciel:nomLogiciel
    },
    function(data) {

        if(data.length >0 ) {
            $('#os').html(data);

        } else {
            $('#os').empty();
        }
    });
}

function makeVersionListe()
{    
    nomLogiciel = $('#nomLogiciel').val();

    os = $('#os').val();
    
    $.post('index.php?module=echangelog&action=search&view=listeVersion', 
    {
	nomLogiciel:nomLogiciel,
	os:os
    },
    function(data) {

        if(data.length >0 ) {

            $('#version').html(data);

        } else {

            $('#version').empty();
        }
    });
}




$(document).ready(function() {

    $('#autoSuggestionsList_nomLogiciel').click(function() {

	makeOSListe();

	$('#os').change(function() {

	    makeVersionListe();

	});


    });

    
    
});



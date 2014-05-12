/* gestion de l'affichage des commentaires des fichiers de log */
$('#toggleLog').click(function () {

    $('#comLog').toggle('slow');

    if ($('#comLog .comLog').length > 1) {


	if ($('#toggleLog').html() == 'Afficher les commentaires') {

	    $('#toggleLog').html('Cacher les commentaires');

	} else {

	    $('#toggleLog').html('Afficher les commentaires');
	}

    } else {

	if ($('#toggleLog').html() == 'Afficher le commentaire') {

	    $('#toggleLog').html('Cacher le commentaire');

	} else {

	    $('#toggleLog').html('Afficher le commentaire');
	}

    }
});

/* gestion de l'affichage des commentaires des pis */
$('#togglePis').click(function () {

    $('#comPis').toggle('slow');

    if ($('#comPis .comPis').length > 1) {

	if ($('#togglePis').html() == 'Afficher les commentaires') {

	    $('#togglePis').html('Cacher les commentaires');

	} else {

	    $('#togglePis').html('Afficher les commentaires');
	}

    } else {

	if ($('#togglePis').html() == 'Afficher le commentaire') {

	    $('#togglePis').html('Cacher le commentaire');

	} else {

	    $('#togglePis').html('Afficher le commentaire');
	}
    }
});

/* gestion de l'affichage des commentaires des pdis */
$('#togglePdis').click(function () {

    $('#comPdis').toggle('slow');

    if ($('#comPdis .comPdis').length > 1) {

	if ($('#togglePdis').html() == 'Afficher les commentaires') {

	    $('#togglePdis').html('Cacher les commentaires');

	} else {

	    $('#togglePdis').html('Afficher les commentaires');
	}

    } else {
	
	if ($('#togglePdis').html() == 'Afficher le commentaire') {

	    $('#togglePdis').html('Cacher le commentaire');

	} else {

	    $('#togglePdis').html('Afficher le commentaire');
	}
    }
});

/* gestion de l'affichage du formulaire de téléchargement du pis */
$('#poserPis').click(function() {

    $('#downloadPis').css('display','block');

});

/* gestion de l'affichage du formulaire de téléchargement du pdis */
$('#poserPdis').click(function() {

    $('#downloadPdis').css('display','block');

});
$('form').submit(function() {

    message = '';
    $('.info').remove();

    //on vérifie que le login n'est pas vide
    if ($('#login').val() == '') {
	message += 'Veuillez saisir un login<br />';

	$('form').before('<div class="info">'+message+'</div>');

	return false;
    }

    //on vérifie que le nom n'est pas vide
    if ($('#nom').val() == '') {
  message += 'Veuillez saisir votre nom<br />';

  $('form').before('<div class="info">'+message+'</div>');

  return false;
    }

    //on vérifie que le prénom n'est pas vide
    if ($('#prenom').val() == '') {
  message += 'Veuillez saisir votre prénom<br />';

  $('form').before('<div class="info">'+message+'</div>');

  return false;
    }

if ($('#adresseIP').val() == '') {
  message += 'Veuillez renseigner votre adresse IP sortante<br />';

  $('form').before('<div class="info">'+message+'</div>');
  return false;
    }



    if ($('#email').val() == '') {
	message += 'Veuillez saisir une adresse email<br />';

	$('form').before('<div class="info">'+message+'</div>');
	return false;
    }
});

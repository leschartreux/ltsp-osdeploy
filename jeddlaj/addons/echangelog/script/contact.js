$('form').submit(function() {

    message = '';
    $('.info').remove();

    //on v�rifie que le login n'est pas vide
    if ($('#login').val() == '') {
	message += 'Veuillez saisir un login<br />';

	$('form').before('<div class="info">'+message+'</div>');

	return false;
    }

    //on v�rifie que le nom n'est pas vide
    if ($('#nom').val() == '') {
  message += 'Veuillez saisir votre nom<br />';

  $('form').before('<div class="info">'+message+'</div>');

  return false;
    }

    //on v�rifie que le pr�nom n'est pas vide
    if ($('#prenom').val() == '') {
  message += 'Veuillez saisir votre pr�nom<br />';

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

//Code adapter de l'exemple http://nodstrum.com/2007/09/19/autocompleter/

function lookup(fieldId, inputString, url) {

    if(inputString.length == 0) {
        /* Hide the suggestion box. */
        $('#suggestions_'+fieldId).hide();
    } else {

        /* détail de la fonction $.post(url, [data], [callback]) 
        url est l'adresse du script php qui va faire le traitement 
        (chercher dans la bdd tout les élément qui commence par data 
        data est la chaine de caractère rentrer par l'utilisateur dans le champs de saisie
        callback fonction executer après le traitement du script PHP. 
        Elle va contruire la liste des suggestion sous le champ de saisie */
        $.post(url, {queryString: ""+inputString+""}, function(data){

            if(data.length >0 ) {

                $('#suggestions_'+fieldId).show();
                $('#autoSuggestionsList_'+fieldId).html(data);
            
            } else {
                $('#suggestions_'+fieldId).hide();
            }
        });
    }
} // lookup

function fill(thisValue, fieldId) {
    $('#'+fieldId).val(thisValue);
    $('#suggestions_'+fieldId).hide();

}

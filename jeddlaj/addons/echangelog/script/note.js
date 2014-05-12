//gestion de la "sélection" de la note
$('.note').hover(
    // mouseover
    function() {
        $(this).prevAll().andSelf().addClass('noteOver');
	$(this).nextAll().andSelf().removeClass('noteVote');
    },

    // mouseout
    function() {

        $(this).prevAll().andSelf().removeClass('noteOver');
    }
);

$('.note').click(function() {

    note = $(this).find('img').attr('alt');

    logiciel = $(this).parent().next('p').html();

    os = $(this).parent().next('p').next('p').html();

    version = $(this).parent().next('p').next('p').next('p').html();

    idScript = $(this).parent().attr('id');

    $.post('index.php?module=echangelog&action=install&logiciel='+logiciel+'&os='+os+'&version='+version+'&task=rate', 
	   {idScript:idScript, note:note}, 
	 function(data)
	 {
	     setVotes($('.note'));
	 });

});

// - récupère la moyenne et le nombre de note
// - ajoute la classe noteVote pour colorer les J
function setVotes(widget)
{
    logiciel = widget.parent().next('p').html();

    os = widget.parent().next('p').next('p').html();

    version = widget.parent().next('p').next('p').next('p').html();

    idScript = widget.parent().attr('id');

    $.post('index.php?module=echangelog&action=install&logiciel='+logiciel+'&os='+os+'&version='+version+'&task=displaynote',
	   {idScript:idScript},
	   function(data)
	   {
	       array = data.split(':');
	       avg = array[0];
	       nbNote = array[1];
	       
	       $('.totalVotes').html('Note : '+avg+'/5 ('+nbNote+' votes)');

	       nbEtoile = Math.round(parseFloat(avg));	       

	       $('.noteScript').find('img').slice(0,nbEtoile).addClass('noteVote');
	   });
}

$(document).ready(function() {

    setVotes($('.note'));
});
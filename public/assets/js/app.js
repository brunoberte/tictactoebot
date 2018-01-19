
var global = {
    debug: true,
    base_url: '/'
};

log = function(s){
    if(global.debug) {
        if ( typeof(console.log) !== 'undefined' ) {
            console.log(s);
        }
    }
};

var app = (function($){

    var character;
    var turn;

    function init(){

        $('.board .btn').click(function(e){
            if ($(this).html() == '' && turn == 'player') {
                $(this).html(character);
                $('#b' + $(this).data('position')).val(character);
                afterPlayer();
            }
            e.preventDefault();
        });

        $('#character-choose .btn').click(function(e){

            $('#character-choose .btn').removeClass().addClass('btn btn-secondary');

            $(this).removeClass().addClass('btn btn-primary');
            character = $(this).html();

            afterChooseCharacter();

            e.preventDefault();
        });
    }

    function afterPlayer(){
        turn = 'computer';
        updatePlayerStatus();
        getNextMoveFromAPI();
    }

    function afterComputer(){
        turn = 'player';
        updatePlayerStatus();
    }

    function getNextMoveFromAPI(){
        var jqxhr = $.post( global.base_url + 'api.php', $('#form-board').serialize(), function( data ) {

            if (data.move != undefined) {

                var column = data.move.shift();
                var row = data.move.shift();
                var playerUnit = data.move.shift();

                $('#b' + row + column).val(playerUnit);
                $('.btn' + row + column).html(playerUnit);

                afterComputer();
            } else {
                alert(data.message);
            }
        }, "json");
        jqxhr.fail(function(){
            alert("error");
        })
    }

    function updatePlayerStatus(){
        $('#state2 .player-1, #state2 .player-2').removeClass('active disabled');
        if (turn == 'player') {
            $('#state2 .player-2').removeClass('active').addClass('disabled');
            $('#state2 .player-1').removeClass('disabled').addClass('active');
        } else {
            $('#state2 .player-1').removeClass('active').addClass('disabled');
            $('#state2 .player-2').removeClass('disabled').addClass('active');
        }
    }

    function afterChooseCharacter(){
        $('#state2 .player-1 span').html(character);
        if (character == 'X') {
            $('#state2 .player-2 span').html('O');
            $('#playerUnit').val('O');
        } else {
            $('#state2 .player-2 span').html('X');
            $('#playerUnit').val('X');
        }

        $('#state1').hide();
        $('#state2').show();

        turn = 'player';
    }

    return {
        init: init,
    }

})(jQuery);

$(document).ready(function() {
    app.init();
});

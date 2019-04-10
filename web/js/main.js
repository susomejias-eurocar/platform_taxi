{

    let init = function(){

        $('#imgLogout').mouseenter(function () { 
            $(this).addClass('animated').addClass('swing');
        });

        $('#imgLogout').mouseleave(function () { 
            setTimeout(function(){ 
                $('#imgLogout').removeClass('animated').removeClass('swing');
            }, 1100);
        });

    }

    $(init);
}
{

    let init = function(){

        
        $('.option').mouseenter(function () { 
            $(this).addClass('animated').addClass('flipInX');
        });

        $('.option').mouseleave(function () { 
            setTimeout(function(){ 
                $('.option').removeClass('animated').removeClass('flipInX');
            }, 2000);
            
            //$(this).removeClass('animated jackInTheBox');

        });


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
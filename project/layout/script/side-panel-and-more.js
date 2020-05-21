$(document).ready(function(){

///////////Setting Popup///////////

    $('#popup').hide();
    $('.user-profile-setting').click(function(){
        $('#popup').toggle(100);
    });
    
////////////SIDE PANEL/////////////
    $('#show-panel').click(function(){
        showSidePanel();
    });
    $('#hide-panel').click(function(){
        hideSidePanel();
    });
    $(".side-panel").click(function(e){
        e.stopPropagation();
    });
                
    $(document).on('click touch', function(event) {            
        if(!$(event.target).parents().addBack().is('#show-panel')) {
            hideSidePanel();
        }  
        else
        if(!$(event.target).parents().addBack().is('#hide-panel')) {
            showSidePanel();
        }
        if(!$(event.target).parents().addBack().is('.popup_show') && !$(event.target).parents().addBack().is('#popup')) {
            $('#popup').hide(100);
        }
    });
                
//////////SEARCH/////////////
                
                

                
//////////////////////

//////Sub-Header//////
                      
    var header = $('#sub-header');
    
    $(window).scroll(function(){

        var winpos = $(window).scrollTop();
        if(winpos > 64){
            header.addClass("sub-header-fixed");
        }
        else
            header.removeClass("sub-header-fixed");
    
    });
    
//////////////////////
        
//////////SCroll/////////////
                 
    $('#popup-text').slimscroll({
        railDraggable: true,
        height: '70%',
        allowPageScroll: true,
        size: '5px',
        borderRadius: '0',
        color: '#364C56',
    });            
//////////////////////

});               

function showSidePanel(){
    $('.side-panel').animate({left:"0px"},{duration:300,easing:'easeInOutQuart'});           
}
function hideSidePanel(){
    $('.side-panel').animate({left:"-310px"},{duration:200,easing:'easeInQuint'});
}
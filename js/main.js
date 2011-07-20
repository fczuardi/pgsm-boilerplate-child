jQuery(document).ready(function($) {
    // $() will work as an alias for jQuery() inside of this function
    // alert('foo!');
    $('.collapsible').bind('click', function(event){
      event.preventDefault();
      if ($(event.currentTarget).hasClass('closed')){
        $('.collapsible').each(function(i, e){
          if (e === event.currentTarget){
            $(e).removeClass('closed');
          }else {
            $(e).addClass('closed');
          }
        });
      } else{
        $('.collapsible').each(function(i, e){
            $(e).addClass('closed');
        });
      }
    });
});
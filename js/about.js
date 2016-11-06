(function() {
  var $text = $('.js-text');


  $("#whoweare").click(function() {
      $('html, body').animate({
          scrollTop: $("#textwhoweare").offset().top
      }, 2000);
  });
  $("#achievements").click(function() {
      $('html, body').animate({
          scrollTop: $("#textacheivements").offset().top
      }, 2000);
  });
  $("#outreach").click(function() {
      $('html, body').animate({
          scrollTop: $("#textoutreach").offset().top
      }, 2000);
  });
  $("#alumni").click(function() {
      $('html, body').animate({
          scrollTop: $("#textalumni").offset().top
      }, 2000);
  });
})();
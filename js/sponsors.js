(function() {


  $("#sponsorus").click(function() {
      $('html, body').animate({
          scrollTop: $("#SponsorText").offset().top
      }, 2000);
  });
  $("#oursponsors").click(function() {
      $('html, body').animate({
          scrollTop: $("#namesaketext").offset().top
      }, 2000);
  });
})();
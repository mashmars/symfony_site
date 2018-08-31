$(document).foundation()

/***首页个人资料 */
// more click
$('.card-profile-stats-more-link').click(function(e){
    e.preventDefault();
    if ( $(".card-profile-stats-more-content").is(':hidden') ) {
      $('.card-profile-stats-more-link').find('i').removeClass('fa-angle-down').addClass('fa-angle-up');
    } else {
      $('.card-profile-stats-more-link').find('i').removeClass('fa-angle-up').addClass('fa-angle-down');
    }
    $(this).next('.card-profile-stats-more-content').slideToggle();
  });
  /***首页个人资料end */
  
(function ($) {

  // Convert svg to png equivalents if not supported
  Drupal.behaviors.svgFallback = {
    attach: function (context, settings) {
      svgeezy.init(false, 'png');
    }
  };

  Drupal.behaviors.alerts = {
    attach: function(context, settings) {
      $(".region-alert .alert", context).each(function(){
        var $alert = $(this);
        var cookie_id = 'alert-'+$alert.data('nid');
        if ($.cookie(cookie_id) !== 'closed') {
          $alert.show();
          $('body').addClass('show-alert');
        }
        $(this).find(".alert-close").click(function(e){
          e.preventDefault();
          $.cookie(cookie_id, 'closed', {path: '/'});
          $alert.slideUp();
          $('body').removeClass('show-alert');
        });
      });
    }
  };

  Drupal.behaviors.InView = {
    attach : function(context) {
      $('.animated').bind('inview', function (event, isVisible) {
        if (isVisible) {
          $(this).addClass('fadeInUp');
        }
      });
    }
  };

  Drupal.behaviors.matchHeights = {
    attach: function(context) {
      $('.view-cu-teams .views-row .node').matchHeight();
      $('.view-cu-news .views-row .node').matchHeight();
      $('.view-cu-ideas-topics .views-row .node').matchHeight();
      $('.view-cu-newsletters .views-row .node').matchHeight();
    }
  };


  Drupal.behaviors.ViewsAutoSubmit = {
    attach: function(context) {
      function triggerSubmit (e) {
        var $this = $(this);
        if (!$this.hasClass('views-ajaxing')) {
          $this.find('.views-auto-submit-click').click();
        }
      }

      $(once('views-auto-submit', 'form.views-auto-submit-full-form', context)).each(function() {
        $(this).find('.nav-pills a')
          .click(function (e) {
            e.preventDefault();
            $('[name=media]').val($(e.target).data('term-id'));
            triggerSubmit.call($(this).closest('form'));
          });
      });
    }
  };


  Drupal.behaviors.videoControl = {
    attach: function(context, settings) {
      if ($('.bg-video-player-control').length > 0) {
        var id, parent, vid;
        $('.bg-video-play, .bg-video-pause').click(function(e) {
          e.preventDefault();
          id = $(this).attr('id').replace('video-play-', '').replace('video-pause-', '');
          vid = document.getElementById('bg-video-' + id);
          parent = $(this).closest('section');
          if ($(this).hasClass('bg-video-play')) {
            parent.removeClass('video-paused').addClass('video-playing');
            vid.play();
          } else {
            parent.removeClass('video-playing').addClass('video-paused');
            vid.pause();
          }
        });
      }
    }
  };

  Drupal.behaviors.pageLoaded = {
    attach : function(context) {
      $(window).on('load', function() {
        $('body').addClass('is-loaded');
      });
    }
  };

})(jQuery);

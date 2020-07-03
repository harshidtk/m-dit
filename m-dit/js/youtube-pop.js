(function ($) {

  'use strict';

  var $document = $(document);

  var getYoutubeId = function (_link) {
    if (_link.match(/(youtube.com)/)) {
      var _split = "v=";
      var _split_length = 1;
    } else if (_link.match(/(youtu.be)/) || _link.match(/(vimeo.com\/)+[0-9]/)) {
      var _split = "/";
      var _split_length = 3;
    } else if (_link.match(/(vimeo.com\/)+[a-zA-Z]/)) {
      var _split = "/";
      var _split_length = 5;
    }
    _link = _link.split(_split)[_split_length];
    return _link;
  };

  $.fn.YouTubePopUp = function (options) {

    var opts = $.extend({
      autoplay: 1 },
    options);

    var $this = $(this);

    $this.on('click', function (e) {

      e.preventDefault();

      var _link = $this.attr("href");

      var getYouTubeVideoID = getYoutubeId(_link),
      cleanVideoID = getYouTubeVideoID.replace(/(&)+(.*)/, "");

      if (_link.match(/(youtu.be)/) || _link.match(/(youtube.com)/)) {
        var videoEmbedLink = "https://www.youtube.com/embed/" + cleanVideoID + "?autoplay=" + opts.autoplay + "";
      } else if (_link.match(/(vimeo.com\/)+[0-9]/) || _link.match(/(vimeo.com\/)+[a-zA-Z]/)) {
        var videoEmbedLink = "https://player.vimeo.com/video/" + cleanVideoID + "?autoplay=" + opts.autoplay + "";
      }

      var _html = '<div class="YouTubePopUp-Wrap YouTubePopUp-animation"><div class="YouTubePopUp-Content"><span class="YouTubePopUp-Close"></span><iframe src="' + videoEmbedLink + '" allowfullscreen></iframe></div></div>';

      $("body").append(_html);

      if ($('.YouTubePopUp-Wrap').hasClass('YouTubePopUp-animation')) {
        setTimeout(function () {
          $('.YouTubePopUp-Wrap').removeClass("YouTubePopUp-animation");
        }, 600);
      }

      $document.on('click', '.YouTubePopUp-Wrap, .YouTubePopUp-Close', function () {
        $(".YouTubePopUp-Wrap").addClass("YouTubePopUp-Hide").delay(515).queue(function () {
          $(this).remove();
        });
      });

    });

    $document.on('keyup', function (e) {
      if (e.keyCode == 27) {
        $('.YouTubePopUp-Wrap:visible').click();
      }
    });

  };

})(jQuery);

$(function () {
  $(".js-modal-video").YouTubePopUp();
});
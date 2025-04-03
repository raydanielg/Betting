(function ($) {
    $.fn.highlighter22 = function (options) {
      var settings = $.extend({
        color: 'black',
        backgroundColor: '#FFFF00',
        padding: 0,
        targets: []
      }, options);
  
      function markStyle() {
        return `color: ${settings.color}; background-color: ${settings.backgroundColor}; padding: ${settings.padding}px;`;
      }
  
  
      return this.each(function () {
        var $input = $(this);
  
        $input.on('input', function () {
          var inputValue = $input.val().toString().trim();
          var lowerCaseValue;
  
          lowerCaseValue = inputValue.toLowerCase().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  
          if (settings.targets.length > 0) {
            $.each(settings.targets, function (index, targetEl) {
  
              $(targetEl).each(function (_index, target) {
                var highlightedText = $(target).text().replace(new RegExp(lowerCaseValue, 'gi'), function (match) {
                  return `<mark style="${markStyle()}">${match}</mark>`;
                });
  
                $(target).html(highlightedText);
              });
            });
          }
        });
      });
    };
  })(jQuery);
@php
    $banners = getContent('banner.element', false, null, true);
@endphp

<div class="banner-slider hero-slider mb-3">
    @foreach ($banners as $banner)
        <div class="banner_slide">
            <img class="banner_image" src="{{ frontendImage('banner', @$banner->data_values->image, '1610x250') }}">
        </div>
    @endforeach
</div>

@pushOnce('script')
    <script>
        (function($) {
            "use strict";
            $(".banner-slider").stepCycle({
                autoAdvance: true,
                transitionTime: 1,
                displayTime: 5,
                transition: "zoomIn",
                easing: "linear",
                childSelector: false,
                ie8CheckSelector: ".ltie9",
                showNav: false,
                transitionBegin: function() {},
                transitionComplete: function() {},
            });

            function controlSliderHeight() {
                let width = $(".banner-slider")[0].clientWidth;
                let height = (width / 37) * 7;
                $(".banner-slider").css({
                    height: height,
                });

                $(".banner_image").css({
                    height: height,
                });
            }

            controlSliderHeight();

        })(jQuery);
    </script>
@endPushOnce


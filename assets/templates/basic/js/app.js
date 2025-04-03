(function ($) {
  "use strict";
    // Search Popup
    $('.mobile-category').on('click',  function(){

      if($('body').hasClass('open-betslip')) {
        $(".bet-slip-button").click();
      }


      $("#body-overlay").toggleClass('active')
      $(this).children().toggleClass("far fa-times");
      $('.sports-category').toggleClass('active-category-side');
      $('body').toggleClass('hide-scroll')
    });

    $("#body-overlay").on('click', function(){
      $(this).removeClass('active')
      $(".mobile-category").children().removeClass("far fa-times");
      $('.sports-category').removeClass('active-category-side');
      $('body').removeClass('hide-scroll')
    })
    // Search Popup End

    // Search Popup
    $('.sidenav-toggler').on('click',  function(){
      $("#body-overlay").toggleClass('active')
      $(this).children().toggleClass("far fa-times");
      $('.dashboard-sidebar').toggleClass('active-sidebar');
    });

    $("#body-overlay").on('click', function(){
      $(this).removeClass('active')
      $(".sidenav-toggler").children().removeClass("far fa-times");
      $('.dashboard-sidebar').removeClass('active-sidebar');
    })
    // Search Popup End


    // Sub Category Drawer - For Mobile
    let subCategoryToggler = $(".sports-sub-category__toggler");
    let subCategoryClose = $(".sub-category-drawer__head-close");
    if (subCategoryToggler && subCategoryClose) {
      subCategoryToggler.on("click", function () {
        $("body").toggleClass("open-sub-category-drawer");
      });
      subCategoryClose.on("click", function () {
        $("body").removeClass("open-sub-category-drawer");
      });
    }

    // Add Support Ticket
    let addFile = $(".addFile");
    let removeFile = $(".removeFile");
    var fileAdded = 0;

    if (addFile || removeFile) {
      addFile.on("click", function () {
        if (fileAdded >= 4) {
          notify("error", "You've added maximum number of file");
          return false;
        }
        fileAdded++;

        $("#fileUploadsContainer").append(`
          <div class="input-group">
            <input type="file" name="attachments[]" class="form-control form--control" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx" required>
            <button type="button" class="btn text-white removeFile input-group-text bg--danger"><i class="las la-times-circle"></i></button>
          </div>`);
      });
      $(document).on("click", ".removeFile", function () {
        fileAdded--;
        $(this).closest(".input-group").remove();
      });
    }
    // Add Support Ticket End

    // ============== Header Hide Click On Body Js Start ========
    $(".bet-slip-button").on("click", function () {

      if($('.sports-category').hasClass('active-category-side')) {
        $('.mobile-category').click();
      }

      $(".header-overlay").toggleClass("show");
      $("body").toggleClass("hide-scroll")
    });

    $(".header-overlay").on("click", function () {
      $(this).removeClass("show");
      $("body").removeClass("open-betslip");
      $("body").toggleClass("hide-scroll")
    });

    // Password Show Hide Toggle
    let passTypeToggle = $(".pass-toggle");
    if (passTypeToggle) {
      passTypeToggle.each(function () {
        $(this).on("click", function () {
          $(this)
            .children()
            .toggleClass("las la-eye-slash")
            .toggleClass("las la-eye");
          var input = $(this).parent().find("input");
          if (input.attr("type") == "password") {
            input.attr("type", "text");
          } else {
            input.attr("type", "password");
          }
        });
      });
    }
    // Password Show Hide Toggle End

    // Category Slider


    // Category Slider end
    $('.sub-category-drawer__list').slick({
      mobileFirst: true,
      slidesToShow: 9,
      infinite: false,
      variableWidth: true,
      prevArrow:
        '<button type="button" class="category-slider-btn arrow-prev"><i class="las la-angle-left"></i></button>',
      nextArrow:
        '<button type="button" class="category-slider-btn arrow-next"><i class="las la-angle-right"></i></button>',

      responsive: [
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 8,
          },
        },
        {
          breakpoint: 576,
          settings: {
            slidesToShow: 6,
          },
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 4,
          },
        },
        {
          breakpoint: 300,
          settings: {
            slidesToShow: 3,
          },
        },
      ],
    });



    // 13. MagnificPopup video view js
    $(".popup-video").magnificPopup({
      type: "iframe",
    });

    // Tooltip Initalize
    const tooltipTriggerList = document.querySelectorAll(
      '[data-bs-toggle="tooltip"]'
    );
    const tooltipList = [...tooltipTriggerList].map(
      (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
    );
    // Tooltip Initalize End
    // Data Copy
    $(".copy-btn").on("click", function (e) {
      e.preventDefault();
      var copyText = document.getElementById("qr-code-text");
      copyText.select();
      copyText.setSelectionRange(0, 99999);
      document.execCommand("copy");

      $(this)
        .tooltip("show")
        .attr("data-bs-original-title", "Copied")
        .tooltip("show");
    });

    $(".copy-btn").on("mouseout", function (e) {
      $(this).attr("data-bs-original-title", "Copy to clipboard");
    });
    // Data Copy End

    // Open Betslip in Mobile Screen
    $(".open-betslip").on("click", function (e) {
      e.preventDefault();
      $("body").toggleClass("open-betslip");
    });
    // Open Betslip in Mobile Screen End
})(jQuery);

$(window).on("load", function () {
  // Preloader
  var preLoder = $(".preloader");
  preLoder.fadeOut(1000);
});


// Screen Size Counting End

function appendQueryParameter(paramName, paramValue) {
  const searchParams = new URLSearchParams(location.search);
  if (searchParams.has(paramName)) {
    searchParams.set(paramName, paramValue);
  } else {
    searchParams.append(paramName, paramValue);
  }
  const updatedUrl =
    location.protocol +
    "//" +
    location.host +
    location.pathname +
    "?" +
    searchParams.toString();
  return updatedUrl;
}

function initOddsSlider(element = $(".option-odd-list")) {
  element.slick({
    dots: false,
    infinite: false,
    speed: 300,
    arrows: true,
    slidesToShow: 1,
    variableWidth: true,
    slidesToScroll: 1,
    prevArrow:
      '<button type="button" class="sports-category__arrow sports-category__arrow-prev"><i class="fas fa-angle-left"></i></button>',
    nextArrow:
      '<button type="button" class="sports-category__arrow sports-category__arrow-next slick-next"><i class="fas fa-angle-right"></i></button>',
  });


  
  $(element).each(function (index, element) {
   
    if(!$(element).find(".slick-arrow")[0]) {
      return;
    }


    let arrowWidth = $(element).find(".slick-arrow")[0].clientWidth;
    let slickListWidth =
      $(element).find(".slick-list")[0].clientWidth - arrowWidth - 14;

    let width = 0;

    $($(element).find(".option-odd-list__item")).each(function (
      index,
      oddElement
    ) {
      width += oddElement.clientWidth + 6;
    });

    if (slickListWidth - width > 12) {
      $(element).find(".slick-arrow").hide();
    } else {
      $(element).find(".slick-arrow").show();
    }
  });


  // set height in variable
  let headHeight = $('.header-primary')[0]?.clientHeight;
  document.documentElement.style.setProperty('--header-h', `${headHeight}px`);
  

  let navHeight = $('.app-nav__menu')[0]?.clientHeight;
  document.documentElement.style.setProperty('--nav-h', `${navHeight}px`);

}

initOddsSlider();

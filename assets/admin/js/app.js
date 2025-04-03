"use strict";

// responsive sidebar expand js
$(".res-sidebar-open-btn").on("click", function () {
  $(".sidebar").addClass("open");
});

$(".res-sidebar-close-btn").on("click", function () {
  $(".sidebar").removeClass("open");
});

$(".sidebar-dropdown > a").on("click", function () {
  if ($(this).parent().find(".sidebar-submenu").length) {
    if ($(this).parent().find(".sidebar-submenu").first().is(":visible")) {
      $(this).find(".side-menu__sub-icon").removeClass("transform rotate-180");
      $(this).removeClass("side-menu--open");
      $(this)
        .parent()
        .find(".sidebar-submenu")
        .first()
        .slideUp({
          done: function done() {
            $(this).removeClass("sidebar-submenu__open");
          },
        });
    } else {
      $(this).find(".side-menu__sub-icon").addClass("transform rotate-180");
      $(this).addClass("side-menu--open");
      $(this)
        .parent()
        .find(".sidebar-submenu")
        .first()
        .slideDown({
          done: function done() {
            $(this).addClass("sidebar-submenu__open");
          },
        });
    }
  }
});

function proPicURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      var preview = $(input)
        .closest(".image-upload-wrapper")
        .find(".image-upload-preview");
      $(preview).css("background-image", "url(" + e.target.result + ")");
      $(preview).addClass("has-image");
      $(preview).hide();
      $(preview).fadeIn(650);
    };
    reader.readAsDataURL(input.files[0]);
  }
}
$(".image-upload-input").on("change", function () {
  proPicURL(this);
});
$(".remove-image").on("click", function () {
  $(this).parents(".image-upload-preview").css("background-image", "none");
  $(this).parents(".image-upload-preview").removeClass("has-image");
  $(this).parents(".image-upload-wrapper").find("input[type=file]").val("");
});
$("form").on("change", ".file-upload-field", function () {
  $(this)
    .parent(".file-upload-wrapper")
    .attr(
      "data-text",
      $(this)
        .val()
        .replace(/.*(\/|\\)/, "")
    );
});

var inputElements = $("input,select,textarea");

$.each(inputElements, function (index, element) {
  element = $(element);
  if (
    !element.hasClass("profilePicUpload") &&
    !element.attr("id") &&
    element.attr("type") != "hidden"
  ) {
    element
      .closest(".form-group")
      .find("label")
      .attr("for", element.attr("name"));
    element.attr("id", element.attr("name"));
  }
});

var tooltipTriggerList = [].slice.call(
  document.querySelectorAll("[title], [data-title], [data-bs-title]")
);
tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl);
});

$.each($("input, select, textarea"), function (i, element) {
  if (element.hasAttribute("required")) {
    $(element)
      .closest(".form-group")
      .find("label")
      .first()
      .addClass("required");
  }
});

//Custom Data Table
$(".custom-data-table")
  .closest(".card")
  .find(".card-body")
  .attr("style", "padding-top:0px");
var tr_elements = $(".custom-data-table tbody tr");
$(document).on("input", "input[name=search_table]", function () {
  var search = $(this).val().toUpperCase();
  var match = tr_elements
    .filter(function (idx, elem) {
      return $(elem).text().trim().toUpperCase().indexOf(search) >= 0
        ? elem
        : null;
    })
    .sort();
  var table_content = $(".custom-data-table tbody");
  if (match.length == 0) {
    table_content.html(
      '<tr><td colspan="100%" class="text-center">Data Not Found</td></tr>'
    );
  } else {
    table_content.html(match);
  }
});

$(".pagination").closest("nav").addClass("d-flex justify-content-end");

$(".showFilterBtn").on("click", function () {
  $(".responsive-filter-card").slideToggle();
});

$(document).on("click", ".short-codes", function () {
  var text = $(this).text();
  var vInput = document.createElement("input");
  vInput.value = text;
  document.body.appendChild(vInput);
  vInput.select();
  document.execCommand("copy");
  document.body.removeChild(vInput);
  $(this).addClass("copied");
  setTimeout(() => {
    $(this).removeClass("copied");
  }, 1000);
});

Array.from(document.querySelectorAll("table")).forEach((table) => {
  let heading = table.querySelectorAll("thead tr th");
  Array.from(table.querySelectorAll("tbody tr")).forEach((row) => {
    let columArray = Array.from(row.querySelectorAll("td"));
    if (columArray.length <= 1) return;
    columArray.forEach((colum, i) => {
      colum.setAttribute("data-label", heading[i].innerText);
    });
  });
});

(function ($) {
  $.each($(".select2"), function () {
    $(this)
      .wrap(`<div class="position-relative"></div>`)
      .select2({
        dropdownParent: $(this).parent(),
      });
  });

  $.each($(".select2-auto-tokenize"), function () {
    $(this)
      .wrap(`<div class="position-relative"></div>`)
      .select2({
        tags: true,
        tokenSeparators: [","],
        dropdownParent: $(this).parent(),
      });
  });

  let disableSubmission = false;
  $(".disableSubmission").on("submit", function (e) {
    if (disableSubmission) {
      e.preventDefault();
    } else {
      disableSubmission = true;
    }
  });

  $(".table-responsive").on(
    "click",
    '[data-bs-toggle="dropdown"]',
    function (e) {
      const { top, left } = $(this)
        .next(".dropdown-menu")[0]
        .getBoundingClientRect();
      $(this)
        .next(".dropdown-menu")
        .css({
          position: "fixed",
          inset: "unset",
          transform: "unset",
          top: top + "px",
          left: left + "px",
        });
    }
  );

  if ($(".table-responsive").length) {
    $(window).on("scroll", function (e) {
      $(".table-responsive .dropdown-menu").removeClass("show");
      $('.table-responsive [data-bs-toggle="dropdown"]').removeClass("show");
    });
  }

  $(".table-responsive").on(
    "click",
    '[data-bs-toggle="dropdown"]',
    function (e) {
      const { top, left } = $(this)
        .next(".dropdown-menu")[0]
        .getBoundingClientRect();
      $(this)
        .next(".dropdown-menu")
        .css({
          position: "fixed",
          inset: "unset",
          transform: "unset",
          top: top + "px",
          left: left + "px",
        });
    }
  );

  if ($(".table-responsive").length) {
    $(window).on("scroll", function (e) {
      $(".table-responsive .dropdown-menu").removeClass("show");
      $('.table-responsive [data-bs-toggle="dropdown"]').removeClass("show");
    });
  }
})(jQuery);

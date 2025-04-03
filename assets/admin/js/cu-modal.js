// Setup modal values
let cuModal  = $("#cuModal");
let form     = cuModal.find("form");
const action = form[0] ? form[0].action : null;

$(document).on("click", ".cuModalBtn", function () {
    let data     = $(this).data();
    let resource = data.resource ?? null;
    // $(form).find('select').val('').trigger('change');

    if (!resource) {
        $(form).trigger("reset");
        form[0].action = `${action}`;
        $(form).find('textarea').text('');
        cuModal.find(".status").empty();
    }
    cuModal.find(".modal-title").text(`${data.modal_title}`);
    if (resource) {
        form[0].action = `${action}/${resource.id}`;
        // If form has image
        if (resource.image_with_path) {
            cuModal
                .find(".profilePicPreview")
                .css("background-image", `url(${resource.image_with_path})`);
        }

        if (data.has_status) {

            cuModal.find(".status").html(`
				<div class="form-group">
					<label class="fw-bold">Status</label>
					<input type="checkbox" data-width="100%" data-height="50" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-toggle="toggle" data-on="Enabled" data-off="Disabled" name="status">
				</div>
			`);

            cuModal.find("[name=status]").bootstrapToggle();
        }

        let fields = cuModal.find("input, select, textarea");
        let fieldName;

        fields.each(function (index, element) {
            fieldName = element.name;

            if ($(element).hasClass('profilePicUpload')) {
                $(element).removeAttr('required');
            }

            // If input name is an array
            if (fieldName.substring(fieldName.length - 2) == "[]") {
                fieldName = fieldName.substring(0, fieldName.length - 2);
            }

            if (fieldName != "_token" && resource[fieldName]) {

                if (element.tagName == "TEXTAREA") {
                    if ($(element).hasClass("nicEdit")) {
                        $(".nicEdit-main").html(resource[fieldName]);
                    } else {
                        $(`[name='${fieldName}']`).text(resource[fieldName]);
                    }
                } else if ($(element).data("toggle") == "toggle") {

                    if (resource[fieldName] != 0) {
                        $(element).bootstrapToggle("on");
                    } else {
                        $(element).bootstrapToggle("off");
                    }
                } else if (element.type == "file") {

                } else {
                    $(`[name='${element.name}']`).val(
                        $.isNumeric(resource[fieldName])
                            ? resource[fieldName] * 1
                            : resource[fieldName]
                    );
                }
            }
        });
    }
    cuModal.modal("show");
});
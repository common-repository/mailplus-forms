(function ($) {
    $(document).on('ready', function () {
        let mpForms = $('form.mpForm')

        if (mpForms.length) {
            $.each(mpForms, function () {
                refreshFormUniqueProperties($(this));
            })
        }
    });

    function refreshFormUniqueProperties(form) {
        if (!$(form).length) {
            return;
        }

        let uniqId = $(form).data('uniq-id'),
            abInfo = $(form).find('input[name="abInfo"]');

        // Detecting the security question within the form, this is needed to place the new security question
        // within the form.
        let fieldDescriptionLabels = $(form).find('label.descriptionLabel'),
            secretFieldGroupId = null,
            isHTMLForm = form.find('ul > li.mpQuestionTable').length;

        $.each(fieldDescriptionLabels, function () {

            // Can match the following string: 20 / 4 - 1 + 2 =
            if (/^(\d+\s*[+x*/÷-]\s*)*\s*\d+\s*=$/.test($(this).text())) {
                secretFieldGroupId = isHTMLForm ? $(this).closest('li').prop('id') :
                    $(this).closest('table.mpQuestionTable').prop('id');

                // Since we've found our match, break out of the each.
                return false;
            }
        });

        if (!uniqId) {
            return;
        }

        let ajaxData = {
            action: 'spotler_get_form',
            enc_id: $('input[name="_form_enc_id_' + uniqId + '"]').val(),
            form_id: $('input[name="_form_id_' + uniqId + '"]').val(),
            nonce: $('input[name="_form_nonce_' + uniqId + '"]').val()
        }

        $.ajax(
            {
                url: '/wp-admin/admin-ajax.php',
                data: ajaxData,
                type: 'POST',
                success: function (response) {
                    if (typeof response.data == 'undefined' || !response.data.length)
                        return;


                    let newForm = $(response.data),
                        newAbInfo = newForm.find('input[name="abInfo"]');

                    if (newAbInfo.length) {
                        abInfo.val(newAbInfo.val());
                    }

                    if (!secretFieldGroupId)
                        return;


                    let secretFieldGroup = isHTMLForm ? form.find('li#' + secretFieldGroupId) :
                            form.find('table#' + secretFieldGroupId),
                        newSecretFieldGroup = isHTMLForm ? newForm.find('li#' + secretFieldGroupId) :
                            newForm.find('table#' + secretFieldGroupId);

                    if (!secretFieldGroup.length || !newSecretFieldGroup.length)
                        return;


                    let newSecretFieldGroupLabel = newSecretFieldGroup.find('label.descriptionLabel').text()

                    if (newSecretFieldGroupLabel === '')
                        return;


                    let secretFieldInput = isHTMLForm ? secretFieldGroup.find('div.mpFormField input') :
                        secretFieldGroup.find('td.mpFormField input');

                    setTimeout(function () {
                        // Unfortunately I had to use eval here since the value of the new input field is not set
                        // when received from the API. This is probably filled by the Javascript that's loaded initially when
                        // the page loads.
                        if (!secretFieldInput.length || secretFieldInput.val() === '')
                            return;


                        secretFieldGroup.find('label.descriptionLabel').text(newSecretFieldGroupLabel);

                        if (isHTMLForm) {
                            secretFieldGroup.find('div.mpFormField input').val(eval(newSecretFieldGroupLabel.replace('=', '')));
                        } else {
                            secretFieldGroup.find('td.mpFormField input').val(eval(newSecretFieldGroupLabel.replace('=', '')));
                        }
                    }, 100);
                }
            }
        )
    }
})(jQuery);
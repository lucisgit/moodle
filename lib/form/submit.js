M.form_submit = {};

M.form_submit.init = function(Y, options) {
    Y.one('#'+options.submitid).on('click', function(e) {
        if (!containsErrors) {
            /**
             * If there are no errors we proceed with submission, but need to
             * disable submit button to prevent multiple clicks. This will result
             * in not passing name/value for submit button itself, but sometimes
             * this is required to destinguish which submit button has been
             * pressed (if there are many of them in the form), therefore we
             * create hidden element to pass submit button name/value.
             */
            var extrahidden = Y.Node.create('<input type="hidden">');
            extrahidden.setAttribute('name', Y.one('#'+options.submitid).getAttribute('name'));
            extrahidden.setAttribute('value', Y.one('#'+options.submitid).getAttribute('value'));
            Y.one('form#'+options.formid).append(extrahidden);

            // Disable submit button.
            e.target.setAttribute('disabled', 'true');

            /**
             * If M.core_formchangechecker is loaded, we need to flag up
             * submission (it has own submit event handler, but after our form
             * modification above it does not get triggered again for some reason),
             * otherwise it will not pass submission through without confirmation.
            */
            if (M.core_formchangechecker) {
                M.core_formchangechecker.set_form_submitted();
            }
            // Finally, submit the form.
            Y.one('form#'+options.formid).submit();
        }
    });
};
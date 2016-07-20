(function ($) {

    var NRE_Vars = {
        idNumber: false
    };

    var Novelist_Review_Excerpts = {

        /**
         * Initialize functions.
         */
        init: function () {
            var repeatGroup = $('#novelist-review-excerpts');

            repeatGroup
                .on('click', '.novelist-review-excerpts-heading', this.toggleExcerpts)
                .on('click', '.novelist-add-review', this.addReview);

            if (repeatGroup.length) {
                repeatGroup.on('novelist_add_review_excerpt', this.emptyValue);
            }
        },

        /**
         * Toggle excerpts open/closed when clicked on.
         */
        toggleExcerpts: function (e) {
            $(this).parent().toggleClass('novelist-review-excerpts-expanded');
        },

        /**
         * Duplicate review fields to add a new one.
         */
        addReview: function (e) {
            e.preventDefault();

            var self = $(this);
            var wrapper = $(self.data('selector'));
            var oldRow = wrapper.find('.novelist-review-excerpts-section').last();
            var prevNum = parseInt(oldRow.data('iterator'));
            var newNum = prevNum + 1;
            var row = oldRow.clone();

            Novelist_Review_Excerpts.cleanRow(row, prevNum);

            var newRow = $('<div class="novelist-review-excerpts-section" data-iterator="' + newNum + '">' + row.html() + '</div>');
            oldRow.after(newRow);

            wrapper.trigger('novelist_add_review_excerpt', newRow);
        },

        cleanRow: function (row, prevNum) {

            var inputs = row.find('input:not([type="button"]), select, textarea, label');
            var other = row.find('[id]').not('input:not([type="button"]), select, textarea, label');

            // Update all elements with an ID.
            if (other.length) {
                other.each(function () {
                    var $_this = $(this);
                    var oldID = $_this.attr('id');
                    var newID = oldID.replace('_' + prevNum, '_' + NRE_Vars.idNumber);
                    var buttons = $row.find('[data-selector="' + oldID + '"]');
                    $_this.attr('id', newID);

                    // Replace data-selector vars
                    if (buttons.length) {
                        buttons.attr('data-selector', newID).data('selector', newID);
                    }
                });
            }
            
            inputs.filter(':checked').prop('checked', false);
            inputs.filter(':selected').prop('selected', false);

        },

        /**
         * Empty All Values
         * @param e
         * @param row
         */
        emptyValue: function (e, row) {
            $('input:not([type="button"]), textarea', row).val('');
        }

    };

    Novelist_Review_Excerpts.init();

})(jQuery);
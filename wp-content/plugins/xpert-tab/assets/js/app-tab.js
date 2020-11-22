(function ($) {

    // Init function for selectize to work each time we clone a panel
    var init = function ($el) {
        $('.icons', $el).selectize({
            sortField: 'text'
        }).removeClass("icons");

    };

    var counter = 1;

    $(document).ready(function () {
        //___Start for cloning Text Panel___//
        $('.action-new-tab').on('click', function () {
            counter++;
            var $templateTab = $('.clonable-tab').clone();
            var  newId = 'tab-' + counter;
            // Remove clonable class and in class from cloned element
            $templateTab.removeClass('clonable-tab');
            $templateTab.find('.in').removeClass('in');
            // Assign random id and attr for newly added panel
            $templateTab.find('#tab-1').attr('id', newId);
            $templateTab.find('[href=#tab-1]').attr('href', '#' + newId);
            // Now append it to parent div #repeatable
            $('#repeatable-tab').append($templateTab);
            $templateTab.show(); // Show the item
            init($templateTab);
        });
        // Trigger the action-new click event
        $('.action-new-tab').trigger("click");

        //__Start for draggin Panel__//
        $("#repeatable-tab").sortable({
            revert: true
        });

        //___Start Panel Remove___//
        $(document).on('click', '.action-remove-tab', function () {
            var panel = $(this).closest('.panel'),
                totalPanel = $('#repeatable-tab').find('.panel-tab').length;

            if (totalPanel != 2) {
                var result = confirm("Are Your sure");
                if (result == true) {
                    panel.remove();
                }

            } else {
                alert("Hey Dude! You can't delete last item :)");
            }
        });
        //___Start Live change Title___//
        $(document).on('keyup', '.title-tab', function () {
            var newValue = $(this).val();
            $(this).closest('.panel-tab').find('.panel-title > a  > .tx-title').text(newValue);
            if(!(newValue.length) == 0 && ($(this).closest('.panel-tab').hasClass('panel-danger'))){
                $(this).closest('.panel-tab').removeClass('panel-danger').addClass('panel-default');
            }
            // console.log($(this).closest('.panel'));
        });
        //___Start Icon change___//
        $(document).on('change', '.selectized', function () {
            var newIcon = $('i').attr('class');
            var newIconChange = $(this).val();

            if (newIcon !== newIconChange) {
                $(this).closest('.panel-tab').find('#title-icon').removeClass();
                $(this).closest('.panel-tab').find('#title-icon').addClass('fa fa-' + newIconChange);
            }
        });

        //___Start Insert into Editor Panel___//

        $(document).on('click', '.action-insert-tab', function () {
            var $presets = $('.presets-tab').val(),
                $tab = $titleTab = $contentTab = $iconTab = '',
                $allIsWell = true;

            $('.panel-tab').each(function () {
                if (!$(this).hasClass("clonable-tab")) {
                    $titleTab = $(this).find('.title-tab').val(),
                        $contentTab = $(this).find('.content-tab').val(),
                        $iconTab = $(this).find('.selectized').val();
                    //  alert($content.length);
                    if( ($titleTab.length == 0) || ($contentTab.length == 0))
                    {
                        $(this).removeClass('panel-default').addClass('panel-danger');
                        $allIsWell = false;

                    }else {
                        $(this).removeClass('panel-danger').addClass('panel-default ');
                    }
                    $tab += '[xt_item title="' + $titleTab + '" icon="' + $iconTab + '"]' + $contentTab + '[/xt_item]';
                }
            });

            if ( $allIsWell )
            {
                wp.media.editor.insert('[xt_tab style="' + $presets + '" ]' + $tab + '[/xt_tab]');
                $('#xt-modal').modal('hide')
            }

        });

        if(!$('.tab-collapse').hasClass('in')) {
            $('.tab-collapse').addClass('in');

        }


    });

// Preset Style

    $(document).on('change', '.presets-tab', function(){

        var newIcon = $('#repeatable-tab').attr('class');
        var newIconChange = $(this).val();

        if(newIcon!==newIconChange){

            $(this).next('#repeatable-tab').removeClass(newIcon);
            $(this).next('#repeatable-tab').addClass('panel-group ui-sortable ' + newIconChange);
        }

    });
//___Admin Style Change___//


})(jQuery);
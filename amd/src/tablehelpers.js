define(['jquery', 'core/modal_factory'], function ($, ModalFactory) {
    function displayDetail(body) {
        ModalFactory.create({
            title: 'Entry Detail',
            body: body,
            footer: '<button id="closeDialog" type="button" class="btn btn-primary">Close</button>',
            large: true
        })
                .done(function (modal) {
                    modal.show();
                    $('#closeDialog').on('click', function () {
                        modal.hide();
                        modal.destroy();
                    });
                });
    }

    return {
        'init': function () {
            $('i[log_detail]').on('click', function (event) {
                event.preventDefault();
                var modal = $('#' + $(this).attr('log_detail'));
                var body = modal.html();
                displayDetail(body);
            });
        }
    };
});
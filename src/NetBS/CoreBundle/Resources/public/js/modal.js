(function() {

    $('[data-modal]').click(function() {

        var path    = $(this).attr('data-modal-url');
        var modal   = new BSModal(path, {});

        modal.launch();
    });

})();

var BSModal = function(path, params) {

    this.path           = path;
    this.id             = 'dn_modal_' + Math.floor(Math.random() * 99999);

    this.launch         = function() {

        var mdl = this;

        $.post(path, params).done(function(data) {

            var modal       = mdl.generate(data);

            $(document.body).append(modal);
            var $modal      = $('#' + mdl.id);

            $modal.modal();

            $modal.on('hidden.bs.modal', function() {
                $modal.remove();
            });

            mdl.attachButtonEvents();

        }).fail(function() {
            alert("Une erreur est survenue, veuillez réessayer ou contactez le développeur!");
        });
    };

    this.generate       = function (content) {
        return '<div id="' + this.id + '" class="modal fade netbs-modal" data-dynamic tabindex="-1" role="dialog" aria-hidden="true">' + content + '</div>';
    };

    this.attachButtonEvents = function() {

        var mdl         = this;
        var $modal      = $('#' + this.id);
        var $confirm    = $modal.find('[data-modal-validate]').first();
        var $form       = $modal.find('form').first();

        $confirm.on('click', function() {

            $.post(mdl.path, $form.serialize())
                .done(function(data, status, response) {

                    var code    = parseInt(response.status);

                    if(data === "redirected")
                        window.location.href = response.getResponseHeader("Location");
                    if(code === 201)
                        location.reload();
                    else
                        $('#' + mdl.id).modal('hide');
                })
                .fail(function(data) {

                    $modal.html(data.responseText);
                    mdl.attachButtonEvents();
                })
            ;
        });
    };
};
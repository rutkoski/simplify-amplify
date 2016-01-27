(function($, window) {

	var Amplify = {};

	window.Amplify = Amplify;

    $(".lightbox").fancybox();

    /**
     *
     * preloader
     * 
     */

    $(document).ready(function() {

        var count = 0,
            $loader, $window = $(window);

        function createLoader() {
            $loader = $('\
	        	<div class="modal fade">\
				  <div class="modal-dialog">\
				    <div class="modal-content">\
				      <div class="modal-body">\
				      	Aguarde...\
				      </div>\
				    </div>\
				  </div>\
				</div>\
			');

            $loader.modal({
                'backdrop': 'static',
                'keyboard': false,
                'show': false
            });
        }

        function loadBegin() {
            count++;

            if (!$loader) {
                createLoader();
            }

            $loader.modal('show');
        }

        function loadEnd() {
            if (!count) return;

            count--;

            if (!count) {
                $loader.modal('hide');
            }
        }

        Amplify.loadBegin = loadBegin;
        Amplify.loadEnd = loadEnd;

    });

}(jQuery, window));

(function ($) {
    "use strict";

    $(document).ready(function () {
        $('.owl-carousel').owlCarousel({
            loop:true,
            autoplay: true,
            margin:10,
            nav:false,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:3
                },
                1000:{
                    items:5
                }
            }
        })
    });

    $('.quantity button').on('click', function () {
        var button = $(this);
        var oldValue = button.parent().parent().find('input.form-control').val();
        if (button.hasClass('btn-plus')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        button.parent().parent().find('input.form-control').val(newVal);
    });

    var modalCart = document.getElementById('modal-cart');

    var header = document.getElementById('sticky');

    modalCart.addEventListener('show.bs.modal', function () {
        // Удаляем класс 'sticky-top' у заголовка
        header.classList.remove('sticky-top');
    });

    modalCart.addEventListener('hidden.bs.modal', function () {
        // Добавляем класс 'sticky-top' к заголовку
        header.classList.add('sticky-top');
    });

})(jQuery);






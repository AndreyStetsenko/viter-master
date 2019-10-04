function slick_prev(name) {
    if (name) {
        return (
            '<button type="button" class="slick-prev"><svg class="svgicon ' +
            name +
            '"><use xlink:href="/wp-content/themes/main/img/sprite.svg#' +
            name +
            '"></use></svg></button>'
        );
    } else {
        return '<button type="button" class="slick-prev"></button>';
    }
}

function slick_next(name) {
    if (name) {
        return (
            '<button type="button" class="slick-next"><svg class="svgicon ' +
            name +
            '"><use xlink:href="/wp-content/themes/main/img/sprite.svg#' +
            name +
            '"></use></svg></button>'
        );
    } else {
        return '<button type="button" class="slick-next"></button>';
    }
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');

    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }

    return "";
}

window.addEventListener('message', function (e) {
    if (e.data.event !== 'undefined' && e.data.event === 'citrus_token') {
        var localKmToken = getCookie('kontramarka_token');

        if (!localKmToken.length || localKmToken !== e.data.value) {
            var d = new Date();
            d.setTime(parseInt(e.data.expires) * 1000);
            document.cookie = 'kontramarka_token=' + encodeURIComponent(e.data.value) + '; expires=' + d.toGMTString() +
                '; path=/; domain=.citrus.ua';
        }
    }
}, false);

$(document).ready(function () {
    svg4everybody();

    let $body       = $('body');
    let $status     = $('.home-slider-count');
    let $homeSlider = $('.home-slider');
    $body.removeClass('preload');

    $homeSlider.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
        let i = (currentSlide ? currentSlide : 0) + 1;
        $status.text(i + ' / ' + slick.slideCount);
    });

    $homeSlider.slick({
        rows: 0,
        dots: true,
        autoplay: true,
        prevArrow: slick_prev('arrow-left'),
        nextArrow: slick_next('arrow-right'),
        appendDots: '.home-slider-dots'
    });

    $('.dropdown-3__selected').on('click', function () {
        $(this).parent().toggleClass('active');
    });
    $('.dropdown-3__item').on('click', function () {
        let $dropdown = $(this).parents('.dropdown-3');
        $('.dropdown-3').removeClass('active');
        $dropdown.find('.dropdown-3__selected').text($(this).text());
        filter_params.site = $(this).data('id');
        get_events(filter_params);
    });
    $body.on('click', function (e) {
        if ($(e.target).closest('.dropdown-3').length === 0) {
            $('.dropdown-3').removeClass('active');
        }
    });

    $('.button-filter').on('click', function () {
        $('.button-filter').removeClass('active');
        $('#date_pick').datepicker().data('datepicker').clear();
        $(this).addClass('active');
        filter_params.date = $(this).data('type');
        get_events(filter_params);
    });

    // filter datepicker
    $('#date_pick').datepicker({
        minDate: new Date(),
        autoClose: true,
        onShow: function (picker) {
            picker.$el.parent().addClass('active');
        },
        onSelect: function (fd, d, picker) {
            $('.button-filter').removeClass('active');
            picker.$el.parent().removeClass('active');
            filter_params.date = d.toLocaleString();
            get_events(filter_params);
        },
        onHide: function (picker) {
            picker.$el.parent().removeClass('active');
        }
    });


    $('.hamburger').on('click', function () {
        $body.addClass('menu-active');
    });
    $('.mobile-menu-overlay').on('click', function () {
        $body.removeClass('menu-active');
    });
    // mobile submenu
    $('.mobile-menu .menu-item-has-children > a').on('click', function (e) {
        e.preventDefault();
        $(this).next().show();
        $(this).next().animate({
            left: 0
        }, 150, function () {
            $(this).parent().siblings().hide();
        });
    });
    // insert menu back in submenu
    $('.mobile-menu .menu-item-has-children').each(function () {
        let parent_name = $(this).find('a').first().html();
        $(this).find('.sub-menu').prepend('<li><a class="sub-menu-back" href="#">' + parent_name + '</a></li>');
    });
    // handle back click
    $body.on('click', '.sub-menu-back', function (e) {
        e.preventDefault();
        $(this).parents('.menu-item-has-children').siblings().show();
        $(this).parents('.sub-menu').animate({
            left: 300
        }, 150, function () {
            $(this).hide();
        });
    });

    // magnific popup
    $('.open-popup').magnificPopup({
        type: 'inline',
        removalDelay: 300,
        mainClass: 'mfp-zoom-in',
    });

    // city filter
    $('.cities__search').on('keyup', function () {
        let input = $(this);
        $('.cities__item a').each(function (index, item) {
            let input_value = input.val().toLowerCase();
            let result      = $(this).text().toLowerCase().indexOf(input_value);
            if (result !== -1) {
                $(item).css('opacity', 1);
            } else {
                $(item).css('opacity', 0.15)
            }
        });
    });

    let $filter       = $('#filter');
    let filter_params = {
        action: 'filter',
        date: '',
        site: '',
        showType: $filter.data('showtype-id'),
        city: Cookies.get('city_id') || $filter.data('default-city'),
        page: 1
    };

    function get_events(data) {
        if ($('.ajax-tickets').length === 0) {
            return;
        }
        filter_params.page = 1;
        $.ajax({
            url: admin_url,
            data: data,
            success: function (res) {
                let $container = $('.ajax-tickets');
                let $loadmore  = $('.button-loadmore');
                let $items     = $(res).filter('.col-6');
                let empty      = '<div class="col-12 text-center">' + $('#filter').data('empty-text') + '</div>';

                if (res !== '') {
                    $container.html(res);
                    if ($items.length < 10) {
                        $loadmore.hide();
                    } else {
                        $loadmore.show();
                    }
                } else {
                    $loadmore.hide();
                    $container.html(empty);
                }
                $container.css('opacity', 1);
            }
        });
    }

    get_events(filter_params);

    function choose_city(link) {
        let city_name = link.text();
        let city_id   = link.data('id');
        Cookies.set('city_name', city_name);
        Cookies.set('city_id', city_id);
        $('.city-picker span').text(city_name);
        $('.mobile-current-city').text(city_name);
        filter_params.city = city_id;
        get_events(filter_params);
    }

    $body.on('click','.mobile-citylink', function (e) {
        e.preventDefault();
        $(this).parents('.menu-item-has-children').siblings().show();
        $(this).parents('.sub-menu').animate({
            left: 300
        }, 150, function () {
            $(this).hide();
        });
        $body.removeClass('menu-active');
        $('.mobile-menu__nav').scrollTop(0);
        choose_city($(this));
    });

    $('.cities__item > a').on('click', function (e) {
        e.preventDefault();
        $.magnificPopup.close();
        choose_city($(this));
    });
    if (Cookies.get('city_name')) {
        let city_name = Cookies.get('city_name');
        $('.city-picker span').text(city_name);
        $('.mobile-current-city').text(city_name);
    }

    // loadmore button
    $body.on('click', '.button-loadmore', function (e) {
        e.preventDefault();
        filter_params.page++;
        $.ajax({
            url: admin_url,
            data: filter_params,
            success: function (res) {
                $('.ajax-tickets').append(res);
                let next_page_params = Object.assign({}, filter_params);
                next_page_params.page++;
                $.ajax({
                    url: admin_url,
                    data: next_page_params,
                    success: function (res) {
                        if (res === '') {
                            $('.button-loadmore').hide();
                        }
                    }
                });
            }
        });
    });
});

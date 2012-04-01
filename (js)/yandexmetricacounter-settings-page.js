jQuery(document).ready(function($){
    var customize_sel = $('#wordefinery-yandexmetricacounter-customize');
    var show_informer_sel = $('#wordefinery-yandexmetricacounter-show_informer');

    var show_informer_f = function() {
        if ($(this).is(':checked')) {
            customize_sel.fadeTo(0, 1);
            customize_sel.children().first().hide();
        } else {
            customize_sel.fadeTo(0, 0.3);
            customize_sel.children().first().show();
        };
    }
    show_informer_sel.change(show_informer_f);
    show_informer_sel.change();

    var align = $('#wordefinery-yandexmetricacounter-align');
    var align_sel = $('#wordefinery-yandexmetricacounter-preview').find('div.align');
    var preview_sel = $('#wordefinery-yandexmetricacounter-preview').find('div.preview');

    var align_f = function() {
        align_sel.find('a').removeClass('selected');
        $(this).addClass('selected');
        align.val($(this).prop('name'));
        preview_sel.css('text-align', align.val());
    }
    align_sel.find('a').click(align_f);
    if (align.val()!='0' && align.val()!='') align_sel.find('a[name='+align.val()+']').click();

    var preview_f = function() {
        var gr = parseInt(gradient.val());
        var color_top = farbtastic.color.substr(1,6);
        if (!isNaN(gr) && gr>=-100 && gr<=100) {
            var c = color_top;
            var r = parseInt(c.substr(0,2),16),
                g = parseInt(c.substr(2,2),16),
                b = parseInt(c.substr(4,2),16);
            r += gr;
            g += gr;
            b += gr;
            r = r>255?255:r; r = r<0?0:r;
            g = g>255?255:g; g = g<0?0:g;
            b = b>255?255:b; b = b<0?0:b;
            r = r.toString(16);
            g = g.toString(16);
            b = b.toString(16);
            r = r.length==1?"0"+r:r;
            g = g.length==1?"0"+g:g;
            b = b.length==1?"0"+b:b;
            var color_bottom = r+g+b;
        } else {
            var color_bottom = color_top;
        }
        var bg_url = size.filter(':checked').val() +
            '_' + arrow.filter(':checked').val() +
            '_' + color_top + alpha_top.val() +
            '_' + color_bottom + alpha_top.val() +
            '_' + text.filter(':checked').val() +
            '_' + info.filter(':checked').val();
        bg_url = 'http://bs.yandex.ru/informer/2/' + bg_url;
        var img_url = size.filter(':checked').val() +
            '_' + text.filter(':checked').val() +
            '_' + info.filter(':checked').val();
        img_url = image_base + img_url + '.png';
        preview_sel.find('img').attr('src', img_url).css('background-image', 'url("' + bg_url + '")');
    }

    var size_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.size');
    var arrow_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.arrow');
    var text_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.text');
    var info_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.info');
    var gradient_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.gradient');
    var color_top_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.color-top');
    var alpha_top_sel = $('#wordefinery-yandexmetricacounter-settings').find('div.alpha-top');

    var size = size_sel.find('input');
    var arrow = arrow_sel.find('input');
    var text = text_sel.find('input');
    var info = info_sel.find('input');
    var gradient = $('#wordefinery-yandexmetricacounter-gradient');
    var color_top = $('#wordefinery-yandexmetricacounter-color_top');
    var alpha_top = $('#wordefinery-yandexmetricacounter-alpha_top');

    var selector_f = function() {
        $(this).parent().parent().find('label').removeClass('selected');
        $(this).parent().addClass('selected');
        preview_f();
    }
    $('#wordefinery-yandexmetricacounter-settings').find('.selector label input').click(selector_f);
    $('#wordefinery-yandexmetricacounter-settings').find('.selector label input:checked').parent().addClass('selected');

    pick_color_f = function(a) {
  		a = a.replace(/[^a-fA-F0-9]+/, '');
        color_top.val(a);
  		if ( a.length === 6 ) {
        	farbtastic.setColor('#' + a);
		    color_top_sel.find('a.color-top-pick').css('background-color', '#' + a);
            preview_f();
        }
    }
    if (color_top_sel.length) {
	    var farbtastic = $.farbtastic(color_top_sel.find('div.color-top-picker'), pick_color_f);
	  	pick_color_f( color_top.val() );
	
	  	color_top.keyup( function() { pick_color_f(color_top.val()); });
	    color_top_sel.find('.color-top-pick').click( function(e) { color_top_sel.find('div.color-top-picker').show(); e.preventDefault(); });
	    $(document).mousedown( function() { color_top_sel.find('div.color-top-picker').hide(); });
    }

    var size_f = function() {
        if ($(this).val() == 3) {
            info_sel.find('label input').first().click();
            info_sel.find('label input').prop('disabled', true);
            preview_f();
        } else {
            info_sel.find('label input').prop('disabled', false);
        };
    }
    size_sel.find('label input').click(size_f);
    size_sel.find('label input:checked').click();

    alpha_top_sel.find('.slider').slider({
			value: parseInt(alpha_top.val(), 16),
			min: 0,
			max: 255,
			step: 16,
			slide: function( event, ui ) {
				alpha_top.val( ui.value<256?ui.value.toString(16).toUpperCase():'FF' );
                preview_f();
			}
		});
  	alpha_top.keyup( function() {
        var a = parseInt(alpha_top.val(), 16);
        if (isNaN(a) || a<0 || a>255) {
            if (a>255) a = 255;
            if (a<0) a = 0;
            if (isNaN(a)) a = 0;
        }
        alpha_top.val(a.toString(16).toUpperCase());
        preview_f();
        alpha_top_sel.find('.slider').slider('value', a);
    });

    gradient_sel.find('.slider').slider({
			value: gradient.val(),
			min: -100,
			max: 100,
			step: 10,
			slide: function( event, ui ) {
				gradient.val( ui.value );
                preview_f();
			}
		});
  	gradient.keyup( function() {
        if (gradient.val() == '-') return;
        var a = parseInt(gradient.val());
        if (isNaN(a) || a<-100 || a>100) {
            if (a>100) a = 100;
            if (a<-100) a = -100;
            if (isNaN(a)) a = 0;
        }
        gradient.val(a);
        preview_f();
        gradient_sel.find('.slider').slider('value', a);
    });

});

/* jshint asi: true */

//////////////////
//WYSIWYG EDITOR
//////////////////


jQuery(document).ready(function($){
	fca_attach_wysiwyg()
})

function unspportedBrowserCheck() {
	var ua = window.navigator.userAgent
	var msie = ua.indexOf('MSIE ')
	if (msie > 0) {
		// IE 10 or older
		return true
	}
	var trident = ua.indexOf('Trident/')
	if (trident > 0) {
		// IE 11
		return true
	}
	
	if (document.documentMode || /Edge/.test(navigator.userAgent)) {
		//EDGE
		return true
	}
	
	if( navigator.userAgent.toLowerCase().indexOf('firefox') > -1 ) {
		return true
	}

	// other browser
	return false
}

var wysihtmlParserRules = {
	"classes": {
		"wysiwyg-text-align-center": 1,
		"wysiwyg-text-align-left": 1,
		"wysiwyg-text-align-right": 1
	},
	'attributes': {
		'style': 'any',
		'class': 'any',
		'data-*': 'any',
		'target': 'any',
		'rel': 'any'	
	},
	
	"tags": {
		"address": 1,
		"abbr": 1,
		"acronym": 1,
		"area": 1,
		"article": 1,
		"aside": 1,
		"audio": 1,
		"b": 1,
		"bdo": 1,
		"big": 1,
		"blockquote": 1,
		"br": 1,
		"button": 1,
		"caption": 1,
		"cite": 1,
		"code": 1,
		"col": 1,
		"colgroup": 1,
		"del": 1,
		"dd": 1,
		"dfn": 1,
		"details": 1,
		"div": 1,
		"dl": 1,
		"dt": 1,
		"em": 1,
		"fieldset": 1,
		"figure": 1,
		"figcaption": 1,
		"font": 1,
		"footer": 1,
		"h1": 1,
		"h2": 1,
		"h3": 1,
		"h4": 1,
		"h5": 1,
		"h6": 1,
		"header": 1,
		"hgroup": 1,
		"hr": 1,
		"i": 1,
		"ins": 1,
		"kbd": 1,
		"label": 1,
		"legend": 1,
		"li": 1,
		"main": 1,
		"map": 1,
		"mark": 1,
		"menu": 1,
		"nav": 1,
		"object": 1,
		"p": 1,
		"pre": 1,
		"q": 1,
		"rb": 1,
		"rp": 1,
		"rt": 1,
		"rtc": 1,
		"ruby": 1,
		"s": 1,
		"samp": 1,
		"span": 1,
		"section": 1,
		"small": 1,
		"strike": 1,
		"strong": 1,
		"sub": 1,
		"summary": 1,
		"sup": 1,
		"table": 1,
		"tbody": 1,
		"td": 1,
		"textarea": 1,
		"tfoot": 1,
		"th": 1,
		"thead": 1,
		"title": 1,
		"tr": 1,
		"track": 1,
		"tt": 1,
		"u": 1,
		"ul": 1,
		"ol": 1,
		"var": 1,
		"video": 1,
		
		"a": {
			"check_attributes": {
				"href":	"url", // important to avoid XSS
				
			}
		},
		"img": {
			"check_attributes": {
				"alt": "alt",
				"src": "url"
			}
		},
		"iframe": {
            "check_attributes": {
                "src": "url",
				"width": "numbers",
				"height": "numbers",
				"frameborder": "numbers",
				
            }
        }
	}
}

function fca_attach_wysiwyg() {
	var $ = jQuery
	
	if ( unspportedBrowserCheck() === false ) {
		
		$('.fca-wysiwyg-view-html').off( 'click' )
		$('.fca-wysiwyg-view-html').on( 'click', function(){
			$(this).siblings('.fca-wysiwyg-group').toggle()
		})
		
		// DESTROY ANY EXISTING CAUSE IT BREAKS WITH SCROLLABLE
		$('.fca-wysiwyg-html.editorActive').each( function( index, element) {
			var wysihtml5Editor = $(this).data("wysihtml5")
			wysihtml5Editor.destroy()
			$(this).removeClass('editorActive')
		})
		
		$('.fca-wysiwyg-html').not('.editorActive').each(function ( index, element ) {
			var editor = new wysihtml5.Editor( element, { // element
				toolbar:	  $(element).siblings('.fca-wysiwyg-nav')[0], // toolbar element
				parserRules:  wysihtmlParserRules, // defined in parser rules set
				stylesheets: [fcaQcAdminData.stylesheet],
				useLineBreaks:  false
			})
			$(element).data("wysihtml5", editor).addClass('editorActive')
			editor.on("load", function() {
				$(element).siblings('.fca-wysiwyg-nav').find('.fca-wysiwyg-view-html').trigger('click').trigger('click')
			} )
		})
		
	} else {
		//DISABLE FOR IE
		$('.fca-wysiwyg-html').not('.editorActive').each(function (index, element) {
			$(element).addClass('editorActive')
			$(element).siblings('.fca-wysiwyg-group').hide()
		})
		
	}
}

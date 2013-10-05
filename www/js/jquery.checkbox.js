/**
 * jQuery custom checkboxes
 * 
 * Copyright (c) 2008 Khavilo Dmitry (http://widowmaker.kiev.ua/checkbox/)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * @version 1.0.0
 * @author Khavilo Dmitry
 * @mailto wm.morgun@gmail.com
**/

jQuery.fn.checkbox = function(options) {
	/* IE < 7.0 background flicker fix */
	if ( jQuery.browser.msie && (parseFloat(jQuery.browser.version) < 7) )
	{
		document.execCommand('BackgroundImageCache', false, true);	
	}
	/* Default settings */
	var settings = {
		cls: 'jquery-checkbox',  /* checkbox  */
		empty: 'js/empty.png'  /* checkbox  */
	};
	
	/* Processing settings */
	settings = jQuery.extend(settings, options || {});
	
	/* Wrapping all passed elements */
	return this.each(function() 
	{
		/* Creating div for checkbox and assigning "hover" event */
		var div = jQuery('<div class="' + settings.cls + '-box"><div class="' + settings.cls + '"><div class="mark"><img src="' + settings.empty + '" /></div></div></div>').hover(
			function() { jQuery('.' + settings.cls, this).addClass(settings.cls + '-hover'); },
			function() { jQuery('.' + settings.cls, this).removeClass(settings.cls + '-hover'); }
		);
		
		/* If custom style was applied - removing it */
		if ( this._div && (oldDiv = jQuery(this._div)) )
		{
			clearInterval(this._int);
			oldDiv.replaceWith(jQuery(this));
		}		

		/* Wrapping checkbox */
		jQuery(this).after(div).css({display: 'none'}).appendTo(div);
		
		/* "disabled" & "checked" state changer hook */ 
		this._div = div;
		var el = this;
		this._disabled = (this.disabled ? true : false);
		this._checked = (this.checked ? true : false);
		this._int = setInterval(function() {
			if ( el._disabled != el.disabled ) {
				el._disabled = (el.disabled ? true : false);
				if ( el.disabled )
					jQuery('.' + settings.cls, div).addClass(settings.cls + '-disabled');
				else
					jQuery('.' + settings.cls, div).removeClass(settings.cls + '-disabled');			
			}
			if ( el._checked != el.checked ) {
				el._checked = (el.checked ? true : false);
				if ( el.checked )
					div.addClass(settings.cls + '-checked');
				else
					div.removeClass(settings.cls + '-checked');
			}
		}, 10);

		/* Creating "click" event handler for checkbox wrapper*/
		jQuery(div).click(function(){
			jQuery('input', this).click();
		});
		
		/* Disable image drag-n-drop  */
		jQuery('img', div).bind('dragstart', function () {return false;}).bind('mousedown', function () {return false;});
		
		/* Firefox div antiselection hack */
		if ( window.getSelection )
			jQuery(div).css('MozUserSelect', 'none');
		
		/* Applying checkbox state */
		if (this.checked)
			div.addClass(settings.cls + '-checked');
		if (this.disabled)
			jQuery('.' + settings.cls, div).addClass(settings.cls + '-disabled');
	});
};
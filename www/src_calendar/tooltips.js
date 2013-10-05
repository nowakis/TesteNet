/*
*
* Copyright (c) 2004-2005 by Zapatec, Inc.
* http://www.zapatec.com
* 1700 MLK Way, Berkeley, California,
* 94709, U.S.A.
* All rights reserved.
*
* $Id: tooltips.js 448 2005-06-05 00:06:40Z dror $
*
* Tooltips
*/

/** 
 * The Tooltip Object constructor (call it with new).  This function links a
 * tooltip element which can be anywhere in the DOM tree to some target
 * element.  Then, when the end-user hovers the target element, the tooltip
 * will appear near the mouse position.
 * 
 * @param target [HTMLElement or string] reference to or ID of the target element
 * @param tooltip [HTMLElement or string] reference to or ID of the tooltip element
 * 
 * @return a new Tooltip object if called with "new Zapatec.Tooltip()"
 */
Zapatec.Tooltip = function(target, tooltip) {
	var self = this;
	if (typeof target == "string")
		target = document.getElementById(target);
	if (typeof tooltip == "string")
		tooltip = document.getElementById(tooltip);
	this.visible = false;
	this.target = target;
	this.tooltip = tooltip;
	this.inTooltip = false;
	this.timeout = null;
	Zapatec.Utils.addClass(tooltip, "tooltip");
	document.body.appendChild(tooltip);
	if (tooltip.title) {
		var title = Zapatec.Utils.createElement("div");
		tooltip.insertBefore(title, tooltip.firstChild);
		title.className = "title";
		title.innerHTML = unescape(tooltip.title);
		tooltip.title = ""; // ;-)
	}
	Zapatec.Utils.addEvent(target, "mouseover", function(ev) {
		return self._onMouseMove(ev);
	});
	if (Zapatec.Tooltip.prefs.move) {
		Zapatec.Utils.addEvent(target, "mousemove", function(ev) {
			return self._onMouseMove(ev);
		});
	}
	Zapatec.Utils.addEvent(target, "mouseout",  function(ev) {
		return self._onMouseOut(ev);
	});
	Zapatec.Utils.addEvent(tooltip, "mouseover", function(ev) {
		self.inTooltip = true;
	});
	Zapatec.Utils.addEvent(tooltip, "mouseout", function(ev) {
		ev || (ev = window.event);
		if (!Zapatec.Utils.isRelated(self.tooltip, ev)) {
			self.inTooltip = false;
			self.hide();
		}
	});
	self.wch = Zapatec.Utils.createWCH();
};

/** 
 * Automagically create tooltips from DFN tags.  The HTML syntax is simple:
 *
 * \code
 *   <dfn title="Tooltip title">
 *      Tooltip contents
 *   </dfn>
 * \endcode
 *
 * Calling this function once when the document has finished loading
 * (body.onload) will turn all DFN tags into tooltips.  Nice, eh?
 *
 * The optional "class_re" parameter allows one to filter elements by some
 * class name.  It can be a RegExp object or a string; if it's a string, only
 * DFN-s containing that string in the value of the "class" attribute will be
 * considered.  If it's a RegExp, only those DFN-s where the value of the class
 * attribute matches the RegExp will be considered.
 *
 * The DFN tag is a standard HTML element.  It's purpose is to markup a
 * \em definition, which seems fairly close to the purpose of a tooltip.
 *
 * @param class_re [string or RegExp, optional] -- filter the DFN elements that display tooltip
 */
Zapatec.Tooltip.setupFromDFN = function(class_re) {
	// init tooltips
	var dfns = document.getElementsByTagName("dfn");
	if (typeof class_re == "string")
		class_re = new RegExp("(^|\\s)" + class_re + "(\\s|$)", "i");
	for (var i = 0; i < dfns.length; ++i) {
		var dfn = dfns[i];
		if (!class_re || class_re.test(dfn.className)) {
			var div = document.createElement("div");
			if (dfn.title) {
				div.title = dfn.title;
				dfn.title = "";
			}
			while (dfn.firstChild)
				div.appendChild(dfn.firstChild);
			dfn.innerHTML = "?";
			dfn.className = "helpIcon";
			new Zapatec.Tooltip(dfn, div);
		}
	} // nice isn't it? :D
};

/// Global Tooltips preferences:
Zapatec.Tooltip.prefs = {
	move : false		/**< If this is true then tooltips will move with mouse */
};
Zapatec.Tooltip._C = null;	/**< [internal] keeps a reference to the currently displayed tooltip */

/** 
 * Called automatically when "onmouseover" or "onmousemove" occurs on the
 * target element.  This function takes care to display the tooltip, if not
 * already visible, and to clear the timeout if the tooltip was set to hide.
 * 
 * @param ev [Event] the event object
 */
Zapatec.Tooltip.prototype._onMouseMove = function(ev) {
	ev || (ev = window.event);
	if (this.timeout) {
		clearTimeout(this.timeout);
		this.timeout = null;
	}
	if (!this.visible && !Zapatec.Utils.isRelated(this.target, ev)) {
		var
			x = ev.clientX + 2,
			y = ev.clientY + 4;
		this.show(x, y);
	}
};

/** 
 * Called automatically when "onmouseout" occurs on the target element.  This
 * function sets a timeout that will hide the tooltip in 150 milliseconds.
 * This timeout can be cancelled if during this time the mouse returns to the
 * target element or enters the tooltip element.
 * 
 * @param ev [Event] the event object.
 */
Zapatec.Tooltip.prototype._onMouseOut = function(ev) {
	ev || (ev = window.event);
	var self = this;
	if (!Zapatec.Utils.isRelated(this.target, ev)) {
		if (this.timeout) {
			clearTimeout(this.timeout);
			this.timeout = null;
		}
		this.timeout = setTimeout(function() {
			self.hide();
		}, 150);
	}
};

/** 
 * Show the tooltip at a specified position.
 * 
 * @param x [number] the X coordinate
 * @param y [number] the Y coordinate
 */
Zapatec.Tooltip.prototype.show = function(x, y) {
	if (Zapatec.Tooltip._C) {
		if (Zapatec.Tooltip._C.timeout) {
			clearTimeout(Zapatec.Tooltip._C.timeout);
			Zapatec.Tooltip._C.timeout = null;
		}
		Zapatec.Tooltip._C.hide();
	}
	var t = this.tooltip;
	t.style.left = t.style.top = "0px";
	t.style.display = "block";
	var box = { x: x, y: y, width: t.offsetWidth, height: t.offsetHeight };
	Zapatec.Utils.fixBoxPosition(box);
	t.style.left = box.x + "px";
	t.style.top = box.y + "px";
	Zapatec.Utils.setupWCH_el(this.wch, t);
	Zapatec.Utils.addClass(this.target, "tooltip-hover");
	this.visible = true;
	Zapatec.Tooltip._C = this;
};

/** 
 * Hides the tooltip.
 */
Zapatec.Tooltip.prototype.hide = function() {
	if (!this.inTooltip) {
		this.tooltip.style.display = "none";
		Zapatec.Utils.hideWCH(this.wch);
		Zapatec.Utils.removeClass(this.target, "tooltip-hover");
		this.visible = false;
	}
};

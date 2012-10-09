/**
 * @package     hubzero-cms
 * @file        modules/mod_sliding_panes/mod_sliding_panes.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
 * Based off of the SlidingTabs mootools plugin by Jenna “Blueberry” Fox!
 * Documentation: http://creativepony.com/journal/scripts/sliding-tabs/
 * version: 1.8
 */

var ModSlidingPanes = new Class({
	options: {
		startingSlide: false, // sets the slide to start on, either an element or an id 
		activeButtonClass: 'active', // class to add to selected button
		activationEvent: 'click', // you can set this to ‘mouseover’ or whatever you like
		wrap: true, // calls to previous() and next() should wrap around?
		slideEffect: { // options for effect used to animate the sliding, see Fx.Base in mootools docs
			duration: 400 // 0.4 of a second
		},
		animateHeight: true, // animate height of container
		rightOversized: 0, // how much of the next pane to show to the right of the current pane
		animate: 'slide'
	},
	current: null, // zero based current pane number, read only
	buttons: false,
	outerSlidesBox: null,
	innerSlidesBox: null,
	panes: null,
	fx: null, // this one animates the scrolling inside
	heightFx: null, // this one animates the height
	periodical: null, // container for the periodical scrolling
	
	initialize: function(container, rotate, options) {
//		document.write(JSON.stringify({
//			'container': container,
//			'rotate': rotate,
//			'options': options
//		}));
		this.setOptions(options);

		// Create a button container
		this.headings = new Element('div', {}).addClass('panes-headings').injectInside($(container));
		
		// Create a slides button container
		this.btnCtnr = new Element('ul', {}).addClass('panes-buttons').injectInside(this.headings);
		
		// Get the slides
		this.outerSlidesBox = $(container).getFirst();
		this.innerSlidesBox = this.outerSlidesBox.getFirst();
		this.panes = this.innerSlidesBox.getChildren();
		
		// Create a button for each slide and add it to the button container
		for (var i = 0; i < this.panes.length; i++)
		{
			var btnEl = new Element('li', {});
			btnEl.innerHTML = i + 1;
			btnEl.injectInside(this.btnCtnr);
		}
		this.buttons = this.btnCtnr.getChildren();

		// Create a "previous slide" button
		this.prevBtn = new Element('p', {
			//href: '#',
			title: 'Previous Slide'
		}).addClass('pane-prev').injectTop(this.headings);
		this.prevBtn.addEvent('click', this.previous.bind(this));

		// Create a "next slide" button
		this.nextBtn = new Element('p', {
			//href: '#',
			title: 'Next Slide'
		}).addClass('pane-next').injectInside(this.headings);
		this.nextBtn.addEvent('click', this.next.bind(this));
		
		// Initiate the scroll FX
		this.heightFx = this.outerSlidesBox.effect('height', this.options.slideEffect);
		
		// set up button highlight
		this.current = this.options.startingSlide ? this.panes.indexOf($(this.options.startingSlide)) : 0;
		if (this.buttons) { this.buttons[this.current].addClass(this.options.activeButtonClass); }
		
		// add needed stylings
		if (this.options.animate === 'slide')
		{
			this.fx = new Fx.Scroll(this.outerSlidesBox, this.options.slideEffect);
			this.outerSlidesBox.setStyle('overflow', 'hidden');
			this.panes.each(function(pane, index) {
				pane.setStyles({
					'float': 'left',
					'overflow': 'hidden'
				});
			}.bind(this));
		}
		else if (this.options.animate === 'fade')
		{
			this.last = this.panes[0];
			this.panes.each(function(pane, idx) { if (idx > 0) pane.setStyle('opacity', 0); });
		}
		
		// stupidness to make IE work - it boggles the mind why this has any effect
		// maybe it's something to do with giving it layout?
		this.innerSlidesBox.setStyle('float', 'left');
		
		if (this.options.startingSlide) this.fx.toElement(this.options.startingSlide);
		
		// add events to the buttons
		if (this.buttons) this.buttons.each( function(button) {
			button.addEvent(this.options.activationEvent, this.buttonEventHandler.bindWithEvent(this, button));
		}.bind(this));
		
		if (this.options.animateHeight) {
			this.heightFx.set(this.panes[this.current].offsetHeight);
		}
		
		// set up all the right widths inside the panes
		this.recalcWidths();
		
		// Set up the periodical
		if (rotate) {
			// Create a "pause" button
			this.pauseBtn = new Element('p', {
				//href: '#',
				title: 'Pause'
			}).addClass('pane-pause').injectTop($(container));
			this.pauseBtn.addEvent('click', function(){
				$clear(this.periodical);
			}.bind(this));

			// Create a "play" button
			this.pauseBtn = new Element('p', {
				//href: '#',
				title: 'Play'
			}).addClass('pane-play').injectTop($(container));
			this.pauseBtn.addEvent('click', function(){
				this.periodical = this.next.periodical(7500, this);
			}.bind(this));
			
			this.periodical = this.next.periodical(7500, this);
		}
	},
	
	// to change to a specific tab, call this, argument is the pane element you want to switch to.
	changeTo: function(element, animate) {
		if ($type(element) == 'number') element = this.panes[element - 1];
		if (!$defined(animate)) animate = true;
		var event = { cancel: false, target: $(element), animateChange: animate };
		this.fireEvent('change', event);
		if (event.cancel == true) { return; };
		
		if (this.buttons) { this.buttons[this.current].removeClass(this.options.activeButtonClass); };
		this.current = this.panes.indexOf($(event.target));
		if (this.buttons) { this.buttons[this.current].addClass(this.options.activeButtonClass); };
		
		if (this.options.animate === 'slide')
		{
			this.fx.stop();
			if (event.animateChange) {
				this.fx.toElement(event.target);
			} else {
				this.outerSlidesBox.scrollTo(this.current * this.outerSlidesBox.offsetWidth.toInt(), 0);
			}
		}
		else if (this.options.animate === 'fade')
		{
			new Fx.Style(this.last, 'opacity').start(1, 0);
			new Fx.Style(element, 'opacity').start(0, 1); 
			this.last = element;
		}
		
		if (this.options.animateHeight)
			this.heightFx.start(this.panes[this.current].offsetHeight);
	},
	
	// Handles a click
	buttonEventHandler: function(event, button) {
		if (event.target == this.buttons[this.current]) return;
		this.changeTo(this.panes[this.buttons.indexOf($(button))]);
		// Clear the periodical
		$clear(this.periodical);
	},
	
	// call this to go to the next tab
	next: function() {
		var next = this.current + 1;
		if (next == this.panes.length) {
			if (this.options.wrap == true) { next = 0 } else { return }
		}
		
		this.changeTo(this.panes[next]);
	},
	
	// to go to the previous tab
	previous: function() {
		var prev = this.current - 1
		if (prev < 0) {
			if (this.options.wrap == true) { prev = this.panes.length - 1 } else { return }
		}
		
		this.changeTo(this.panes[prev]);
	},
	
	// call this if the width of the sliding tabs container changes to get everything in line again
	recalcWidths: function() {
		this.panes.each(function(pane, index) {
			pane.setStyle('width', this.outerSlidesBox.offsetWidth.toInt() - this.options.rightOversized + 'px');
		}.bind(this));
		
		this.innerSlidesBox.setStyle(
			'width', (this.outerSlidesBox.offsetWidth.toInt() * this.panes.length) + 'px'
		);
		
		// fix positioning
		if (this.current > 0) {
			if (this.fx)
				this.fx.stop();
			this.outerSlidesBox.scrollTo(this.current * this.outerSlidesBox.offsetWidth.toInt(), 0);
		}
	}
});

ModSlidingPanes.implement(new Options, new Events);


/*
 * Facebox (for jQuery)
 * version: 1.2 (05/05/2008)
 * @requires jQuery v1.2 or later
 *
 * Examples at http://famspam.com/facebox/
 *
 * Licensed under the MIT:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2007, 2008 Chris Wanstrath [ chris@ozmm.org ]
 *
 * Usage:
 *
 *  jQuery(document).ready(function() {
 *    jQuery('a[rel*=facebox]').facebox()
 *  })
 *
 *  <a href="#terms" rel="facebox">Terms</a>
 *    Loads the #terms div in the box
 *
 *  <a href="terms.html" rel="facebox">Terms</a>
 *    Loads the terms.html page in the box
 *
 *  <a href="terms.png" rel="facebox">Terms</a>
 *    Loads the terms.png image in the box
 *
 *
 *  You can also use it programmatically:
 *
 *    jQuery.facebox('some html')
 *    jQuery.facebox('some html', 'my-groovy-style')
 *
 *  The above will open a facebox with "some html" as the content.
 *
 *    jQuery.facebox(function($) {
 *      $.get('blah.html', function(data) { $.facebox(data) })
 *    })
 *
 *  The above will show a loading screen before the passed function is called,
 *  allowing for a better ajaxy experience.
 *
 *  The facebox function can also display an ajax page, an image, or the contents of a div:
 *
 *    jQuery.facebox({ ajax: 'remote.html' })
 *    jQuery.facebox({ ajax: 'remote.html' }, 'my-groovy-style')
 *    jQuery.facebox({ image: 'stairs.jpg' })
 *    jQuery.facebox({ image: 'stairs.jpg' }, 'my-groovy-style')
 *    jQuery.facebox({ div: '#box' })
 *    jQuery.facebox({ div: '#box' }, 'my-groovy-style')
 *
 *  Want to close the facebox?  Trigger the 'close.facebox' document event:
 *
 *    jQuery(document).trigger('close.facebox')
 *
 *  Facebox also has a bunch of other hooks:
 *
 *    loading.facebox
 *    beforeReveal.facebox
 *    reveal.facebox (aliased as 'afterReveal.facebox')
 *    init.facebox
 *    afterClose.facebox
 *
 *  Simply bind a function to any of these hooks:
 *
 *   $(document).bind('reveal.facebox', function() { ...stuff to do after the facebox and contents are revealed... })
 *
 */
(function($) {
	$.box = {
		_this: this,
		options: {
			id: null,
			url: null,
			img: null,
			overlay: true,
			opacity: 0.5,
			close: true,
			init: null,
			success: null,
			cancel: null,
			box: '\
			    <div id="facebox" style="display:none;"> \
			      <div class="popup"> \
			        <div class="content"> \
			        </div> \
			      </div> \
			    </div>'
		},
		show: function(options) {
			var self = this;
			$.extend(this.options, options);
			
			if($('#facebox').length == 0) {
			    $('body').append(this.options.box);
			    if(this.options.close) {
			    	$('#facebox .popup').append('<a href="#" class="close"></a>')
			    		.children('.close').click(function(){
			    			self.close();
			    		});
			    }
			    $('#facebox').show();
			}
		    
			this.loading();
			
			if(this.options.id) {
				$('#facebox .content').html($('#' + this.options.id).html()).parent()
					.children().show().end()
					.children('.loading').remove();
				self.resize();
				if(self.options.init) {
					self.options.init();
				}
				if(self.options.success) {
					$('#facebox form').submit(function(){
						self.options.success(this);
						self.close();
						return false;
					});
				}
			} else if(this.options.url) {
				$.get(
					this.options.url,
					function(ret){
						$('#facebox .content').html(ret).show()
							.parent().children('.loading').remove();
						self.resize();
						if(self.options.init) {
							self.options.init();
						}
						if(self.options.success) {
							$('#facebox form').submit(function(){
								self.options.success(this);
								self.close();
								return false;
							});
						}
					}
				);
			} else if(this.options.img) {
				$('#facebox .content').html("<img src=\""+this.options.img+"\" />").show()
				.parent().children('.loading').remove();
				self.resize();
				if(self.options.init) {
					self.options.init();
				}
			}
		},
		resize: function() {
			var w = $('#facebox .content').width();
			var h = $('#facebox .content').height();
			var _w = $(window).width();
			var _h = $(window).height();
			if(w > _w) {
				w = _w - 50 - 30;
				h = w / _w * h;
			}
			if(h > _h) {
				h = _h - 50 - 30;
				w = h / _h * w;
			}
			
			$('#facebox').find('.content').width(w).height(h).end()
				.css({"top":((_h - h) / 2 - 15)+"px","left":((_w - w) / 2 - 15)+"px"});
		},
		loading: function() {
			var self = this;
			if ($('#facebox .loading').length > 0) {
				return true;
			}
			
			this.mark(true);

			$('#facebox .content').empty();
			$('#facebox .popup').children().hide().end().
				append('<div class="loading">正在加载中...</div>');

			$('#facebox').css({
					top: ($(window).height() - $('#facebox').height()) / 2,
					left: ($(window).width() - $('#facebox').width()) / 2
				}).show();

			if(this.options.close) {
				$(document).bind('keydown.facebox', function(e) {
						if (e.keyCode == 27) self.close()
						return true
					});	
			}
		},
		close: function() {
			if(this.options.cancel) {
				this.options.cancel();
			}
			$('#facebox').remove();
			$('#facebox_overlay').remove();
			return false;
		},
		mark: function(flag) {
			var self = this;
			if(flag) {
				if(!this.options.overlay) {
					return true;
				}

			    if($('#facebox_overlay').length > 0) {
			    	return true;
			    }
			    
			    $("body").append('<div id="facebox_overlay" class="facebox_hide"></div>')

			    $('#facebox_overlay').hide().addClass("facebox_overlayBG")
			    	.css('opacity', self.options.opacity)
			    	.fadeIn(200);
			    if(this.options.close) {
			    	$('#facebox_overlay').click(
				    		function() {
				    			self.close();
				    		}
				    	);
			    }
			    
			    return true;
			} else {
				if(!this.options.overlay) {
					return true;
				}

			    if($('#facebox_overlay').length == 0) {
			    	return true;
			    }
				
				$('#facebox_overlay').fadeOut(200, function(){
						$("#facebox_overlay").removeClass("facebox_overlayBG")
						$("#facebox_overlay").addClass("facebox_hide")
						$("#facebox_overlay").remove()
					});
			    
			    return true;
			}
		}
	}

})(jQuery);

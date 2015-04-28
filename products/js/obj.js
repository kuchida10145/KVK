

//DIVのIDを表示する

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_changeProp(objName,x,theProp,theValue) { //v6.0
  var obj = MM_findObj(objName);
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)){
    if (theValue == true || theValue == false)
      eval("obj."+theProp+"="+theValue);
    else eval("obj."+theProp+"='"+theValue+"'");
  }
}

function MM_changeProp2(objName,x,theProp,theValue) { //v6.0
  var obj = MM_findObj(objName);
  
  if(obj.style.display == theValue){
  	theValue = "none";
  }
  if (obj && (theProp.indexOf("style.")==-1 || obj.style)){
    if (theValue == true || theValue == false)
      eval("obj."+theProp+"="+theValue);
    else eval("obj."+theProp+"='"+theValue+"'");
  }
}





//このページの上へ戻る

function backToTop() {
	var x1 = x2 = x3 = 0;
	var y1 = y2 = y3 = 0;
	if (document.documentElement) {
		x1 = document.documentElement.scrollLeft || 0;
		y1 = document.documentElement.scrollTop || 0;
	}
	if (document.body) {
		x2 = document.body.scrollLeft || 0;
		y2 = document.body.scrollTop || 0;
	}
	x3 = window.scrollX || 0;
	y3 = window.scrollY || 0;
	var x = Math.max(x1, Math.max(x2, x3));
	var y = Math.max(y1, Math.max(y2, y3));
	window.scrollTo(Math.floor(x / 1.4), Math.floor(y / 1.4));
	if (x > 0 || y > 0) {
		window.setTimeout("backToTop()", 10);
	}
}




//ウインドオープン


function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}










/*******画像の高さを揃える***************************************/

/*高さを揃えたい要素の直属の親要素に「fixHeight」というクラスを付けます。

 * fixHeight - jQuery Plugin
 * http://www.starryworks.co.jp/blog/tips/javascript/fixheightjs.html
 *
 * Author Koji Kimura @ STARRYWORKS inc.
 * http://www.starryworks.co.jp/
 * 
 * Licensed under the MIT License
 *
 */


(function($){
	
	var isInitialized = false;
	var parents = [];
	var textHeight = 0;
	var $fontSizeDiv;
	
	$.fn.fixHeight = function() {
		this.each(function(){
			var childrenGroups = getChildren( this );
			
			$.each( childrenGroups, function(){
				
				var $children = $(this);
				if ( !$children.filter(":visible").length ) return;
				
				var row = [];
				var top = 0;
				$children.each(function(){
					if ( top != $(this).position().top ) {
						$(row).sameHeight();
						row = [];
						top = $(this).position().top;
					}
					row.push(this);
				});
				if ( row.length ) $(row).sameHeight();
			});
			
			
		});
		init();
		return this;
	}
	
	$.checkFixHeight = function( i_force ) {
		if ( $fontSizeDiv.height() == textHeight && i_force !== true ) return;
		textHeight = $fontSizeDiv.height();
		$(parents).fixHeight();
	}
	
	$.fn.sameHeight = function() {
		var maxHeight = 0;
		this.css("height","auto");
		this.each(function(){
			if ( $(this).height() > maxHeight ) maxHeight = $(this).height();
		});
		return this.height(maxHeight);
	}
	
	function getChildren( i_parent ) {
		var $parent = $( i_parent );
		
		if ( $parent.data("fixHeightChildrenGroups") ) return $parent.data("fixHeightChildrenGroups");
		var childrenGroups = [];
		
		var $children = $parent.find(".fixHeightChild");
		if ( $children.length ) childrenGroups.push( $children );
		
		var $groupedChildren = $parent.find("*[class*='fixHeightChild']:not(.fixHeightChild)");
		if ( $groupedChildren.length ) {
			var classNames = {};
			$groupedChildren.each(function(){
				var a = $(this).attr("class").split(" ");
				var i;
				var l = a.length;
				var c;
				for ( i=0; i<l; i++ ) {
					c = a[i].match(/fixHeightChild[a-z0-9_-]+/i);
					if ( !c ) continue;
					c = c.toString();
					if ( c ) classNames[c] = c;
				}
			});
			for ( var c in classNames ) childrenGroups.push( $parent.find("."+c) );
		}
		
		if ( !childrenGroups.length ) {
			$children = $parent.children();
			if ( $children.length ) childrenGroups.push( $children );
		}
		
		$parent.data("fixHeightChildrenGroups", childrenGroups );
		parents.push( $parent );
		
		return childrenGroups;
	}
	
	
	function init() {
		if ( isInitialized ) return;
		isInitialized = true;
		$fontSizeDiv = $(document).append('<div style="position:absolute;left:-9999px;top:-9999px;">s</div>');
		setInterval($.checkFixHeight,1000);
		$(window).resize($.checkFixHeight);
		$.checkFixHeight();
		$(window).load( function(){ $.checkFixHeight(true); } );
	}
	
})(jQuery);



jQuery(document).ready(function(){
	$(".fixHeight").fixHeight();
});







/*ブロック要素ごとリンクを設定させる　個別に設定が必要、CSSも*/
/*
$(function(){
     $(".item2 dl").click(function(){
         window.location=$(this).find("a").attr("href");
         return false;
    });
});*/


/*ストライプのテーブル*/

/*$(function(){
     $(".table01 tr:odd").addClass("odd");
});
$(function(){
     $(".table02 tr:odd").addClass("odd");
});*/

/*トップページの最新情報をストライプに*/
$(function(){
     $("#topinfo dl:odd").addClass("odd");
});













/*CSS3で傾けるをieでも対応させる*/
/*
with (document) {
	write('<script type="text/javascript" src="/common_pc/js/sylvester.js"></script>');
	write('<script type="text/javascript" src="/common_pc/js/pb.transformie.js"></script>');
}
*/




/*タブ設定--*/

$(function(){
	$("a.btn_act").click(function(){
		var connectCont = $("a.btn_act").index(this);
		var showCont = connectCont+1;
		$('.motion').css({display:'none'});
		$('#motion_area'+(showCont)).fadeIn('normal');

		$('a.btn_act').removeClass('active');
		$(this).addClass('active');
	});
});
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}















/*サイト内検索*/

/*
(function() {
var sb = document.getElementById('srchBox');
if (sb && sb.className == 'watermark') {
  var si = document.getElementById('srchInput');
  var f = function() { si.className = 'nomark'; };
  var b = function() {
    if (si.value == '') {
      si.className = '';
    }
  };
  si.onfocus = f;
  si.onblur = b;
  if (!/[&?]p=[^&]/.test(location.search)) {
    b();
  } else {
    f();
  }
}
})();*/


/*スマートフォン用のボタンを表示する(スマフォで見たときだけ表示)
 if ((navigator.userAgent.indexOf('iPhone') > 0 && navigator.userAgent.indexOf('iPad') == -1) || navigator.userAgent.indexOf('iPod') > 0 || navigator.userAgent.indexOf('Android') > 0 ) {
document.write('<p align="center"><a href="../../js/.">スマホのトップへリンク</a>');
}*/








/*フォームに文字を出力
 * jQuery Form Tips 1.2.6
 * By Manuel Boy (http://www.manuelboy.de)
 * Copyright (c) 2012 Manuel Boy
 * Licensed under the MIT License: http://www.opensource.org/licenses/mit-license.php
*/
(function($){
  
  $.fn.formtips = function(options) {
  
    // handle options
    var settings = $.extend({
      tippedClass: "tipped"
    }, options);
  
    return this.each(function() {
      
      // prepare input elements an textareas
      var e = $(this);
      
      // do not apply form tips to inputs of type file, radio or checkbox
      var type = $(e).attr('type');
      if(type != 'file' && type != 'checkbox' && type != 'radio') {
    
        // handle focus event
        $(e).bind('focus', function() {
          var lv = $(this).attr('title');
          if($(this).val() == lv) {
            $(this).val('').removeClass(settings.tippedClass);
          }
          return true;
        });
    
        // handle blur event
        $(e).bind('blur', function() {
          var lv = $(this).attr('title');
          if($(this).val() == '') {
            $(this).val(lv).addClass(settings.tippedClass);
          }
          return true;
        });
    
        // handle initial text
        var lv = $(e).attr('title');
        if($(e).val() == '' || $(e).val() == $(this).attr('title')) {
          $(e).val(lv).addClass(settings.tippedClass);
        } else {
          $(e).removeClass(settings.tippedClass);
        }
      
        // handle removal of default value
        $(e).parentsUntil('form').parent().submit(function() {
          var lv = $(e).attr('title');
          if($(e).val() == lv) {
            $(e).val('').removeClass(settings.tippedClass);
          }
        });
      
      }
    
    });
  };
  
})(jQuery);





/**画像のサイズをそろえる
/**
 * @author Paul Chan / KF Software House 
 * http://www.kfsoft.info
 *
 * Version 0.5
 * Copyright (c) 2010 KF Software House
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
	
(function($) {

    var _options = new Array();

	jQuery.fn.MyThumbnail = function(options) {
		_options[_options.length] = $.extend({}, $.fn.MyThumbnail.defaults, options);
		

		return this.each(function()
		{
			$(this).removeAttr("width").removeAttr("height");
			var img = this;
			var src = $(this).attr("src");
			var width = $(this).width();
			var height = $(this).height();

			$(this).hide();
			if (width==0 || height==0)
			{
				var optIndex = _options.length-1;
				$("<img/>")
				.attr("src", $(this).attr("src"))
				.load(function() {
				
					width = this.width;  
					height = this.height;
					
					addImage(img, width, height, optIndex);
				});
			}
			else
			{
				var optIndex = _options.length-1;
				addImage(img, width, height, optIndex);
			}
		});
		
		function addImage(img, width, height, optIndex)
		{	
			var src = $(img).attr("src");
			
			var opt = _options[optIndex];

			var imageSizeWidthRatio = opt.thumbWidth/width;
			var imageSizeWidth = null;
			var imageSizeHeight = null;
	
			imageSizeWidth = opt.thumbWidth;
			imageSizeHeight = height * imageSizeWidthRatio;
			
			
			if (imageSizeHeight < opt.thumbHeight)
			{
				var resizeFactor = opt.thumbHeight/imageSizeHeight;
				
				//fix
				imageSizeHeight = opt.thumbHeight;
				imageSizeWidth = resizeFactor*imageSizeWidth;
			}

			var appendHtml = null;
			if (!opt.bShowPointerCursor)
			{
				appendHtml = "<DIV class='myThumbDivAutoAdd "+ opt.imageDivClass +"' style='display:none;float:left;width:"+ opt.thumbWidth +"px;height:" + opt.thumbHeight + "px;overflow:hidden;background:url("+src +") no-repeat "+opt.backgroundColor+";";
				appendHtml += "background-position:center;background-size:"+ imageSizeWidth + "px " + imageSizeHeight  +"px;'></DIV>";
			}
			else
			{
				appendHtml = "<DIV class='myThumbDivAutoAdd "+ opt.imageDivClass +"' style='cursor:pointer;display:none;float:left;width:"+ opt.thumbWidth +"px;height:" + opt.thumbHeight + "px;overflow:hidden;background:url("+src +") no-repeat "+opt.backgroundColor+";";
				appendHtml += "background-position:center;background-size:"+ imageSizeWidth + "px " + imageSizeHeight  +"px;'></DIV>";
			}
				
			$(img).after(appendHtml)
			$(".myThumbDivAutoAdd").fadeIn();
		}
	}

	//default values
	jQuery.fn.MyThumbnail.defaults = {
		thumbWidth:130,
		thumbHeight:100,
		backgroundColor:"#ccc",
		imageDivClass:"myPic",
		bShowPointerCursor:false
	};
})(jQuery);









/*吹き出し*/
;(function($) {
	//-----------------------------------------------------------------------------
	// Private
	//-----------------------------------------------------------------------------
	// Helper for meta programming
	var Meta = {};
	Meta.pos  = $.extend(["top", "bottom", "left", "right"], {camel: ["Top", "Bottom", "Left", "Right"]});
	Meta.size = $.extend(["height", "width"], {camel: ["Height", "Width"]});
	Meta.getRelativeNames = function(position) {
		var idx = {
			pos: {
				o: position,                                          // origin
				f: (position % 2 == 0) ? position + 1 : position - 1, // faced
				p1: (position % 2 == 0) ? position : position - 1,
				p2: (position % 2 == 0) ? position + 1 : position,
				c1: (position < 2) ? 2 : 0,
				c2: (position < 2) ? 3 : 1
			},
			size: {
				p: (position < 2) ? 0 : 1, // parallel
				c: (position < 2) ? 1 : 0  // cross
			}
		};
		var names = {};
		for(var m1 in idx) {
			if(!names[m1]) names[m1] = {};
			for(var m2 in idx[m1]) {
				names[m1][m2] = Meta[m1][idx[m1][m2]];
				if(!names.camel) names.camel = {};
				if(!names.camel[m1]) names.camel[m1] = {};
				names.camel[m1][m2] = Meta[m1].camel[idx[m1][m2]];
			}
		}
		names.isTopLeft = (names.pos.o == names.pos.p1);
		return names;
	};

	// Helper class to handle position and size as numerical pixels.
	function NumericalBoxElement() { this.initialize.apply(this, arguments); }
	(function() {
		// Method factories
		var Methods = {
			setBorder: function(pos, isVertical) {
				return function(value) {
					this.$.css("border-" + pos.toLowerCase() + "-width", value + "px");
					this["border" + pos] = value;
					return (this.isActive) ? digitalize(this, isVertical) : this;
				}
			},
			setPosition: function(pos, isVertical) {
				return function(value) {
					this.$.css(pos.toLowerCase(), value + "px");
					this[pos.toLowerCase()] = value;
					return (this.isActive) ? digitalize(this, isVertical) : this;
				}
			}
		};

		NumericalBoxElement.prototype = {
			initialize: function($element) {
				this.$ = $element;
				$.extend(true, this, this.$.offset(), {center: {}, inner: {center: {}}});
				for(var i = 0; i < Meta.pos.length; i++) {
					this["border" + Meta.pos.camel[i]] = parseInt(this.$.css("border-" + Meta.pos[i] + "-width")) || 0;
				}
				this.active();
			},
			active: function() { this.isActive = true; digitalize(this); return this; },
			inactive: function() { this.isActive = false; return this; }
		};
		for(var i = 0; i < Meta.pos.length; i++) {
			NumericalBoxElement.prototype["setBorder" + Meta.pos.camel[i]] = Methods.setBorder(Meta.pos.camel[i], (i < 2));
			if(i % 2 == 0)
				NumericalBoxElement.prototype["set" + Meta.pos.camel[i]] = Methods.setPosition(Meta.pos.camel[i], (i < 2));
		}

		function digitalize(box, isVertical) {
			if(isVertical == undefined) { digitalize(box, true); return digitalize(box, false); }
			var m = Meta.getRelativeNames((isVertical) ? 0 : 2);
			box[m.size.p] = box.$["outer" + m.camel.size.p]();
			box[m.pos.f] = box[m.pos.o] + box[m.size.p];
			box.center[m.pos.o] = box[m.pos.o] + box[m.size.p] / 2;
			box.inner[m.pos.o] = box[m.pos.o] + box["border" + m.camel.pos.o];
			box.inner[m.size.p] = box.$["inner" + m.camel.size.p]();
			box.inner[m.pos.f] = box.inner[m.pos.o] + box.inner[m.size.p];
			box.inner.center[m.pos.o] = box.inner[m.pos.f] + box.inner[m.size.p] / 2;
			return box;
		}
	})();

	// Adjust position of balloon body
	function makeupBalloon($target, $balloon, options) {
		$balloon.stop(true, true);
		var outerTip, innerTip,
			initTipStyle = {position: "absolute", height: "0", width: "0", border: "solid 0 transparent"},
			target = new NumericalBoxElement($target),
			balloon = new NumericalBoxElement($balloon);
		balloon.setTop(-options.offsetY
			+ ((options.position && options.position.indexOf("top") >= 0) ? target.top - balloon.height
			: ((options.position && options.position.indexOf("bottom") >= 0) ? target.bottom
			: target.center.top - balloon.height / 2)));
		balloon.setLeft(options.offsetX
			+ ((options.position && options.position.indexOf("left") >= 0) ? target.left - balloon.width
			: ((options.position && options.position.indexOf("right") >= 0) ? target.right
			: target.center.left - balloon.width / 2)));
		if(options.tipSize > 0) {
			// Add hidden balloon tips into balloon body.
			if($balloon.data("outerTip")) { $balloon.data("outerTip").remove(); $balloon.removeData("outerTip"); }
			if($balloon.data("innerTip")) { $balloon.data("innerTip").remove(); $balloon.removeData("innerTip"); }
			outerTip = new NumericalBoxElement($("<div>").css(initTipStyle).appendTo($balloon));
			innerTip = new NumericalBoxElement($("<div>").css(initTipStyle).appendTo($balloon));
			// Make tip triangle, adjust position of tips.
			var m;
			for(var i = 0; i < Meta.pos.length; i++) {
				m = Meta.getRelativeNames(i);
				if(balloon.center[m.pos.c1] >= target[m.pos.c1] &&
					balloon.center[m.pos.c1] <= target[m.pos.c2]) {
					if(i % 2 == 0) {
						if(balloon[m.pos.o] >= target[m.pos.o] && balloon[m.pos.f] >= target[m.pos.f]) break;
					} else {
						if(balloon[m.pos.o] <= target[m.pos.o] && balloon[m.pos.f] <= target[m.pos.f]) break;
					}
				}
				m = null;
			}
			if(m) {
				balloon["set" + m.camel.pos.p1]
					(balloon[m.pos.p1] + ((m.isTopLeft) ? 1 : -1) * (options.tipSize - balloon["border" + m.camel.pos.o]));
				makeTip(balloon, outerTip, m, options.tipSize, $balloon.css("border-" + m.pos.o + "-color"));
				makeTip(balloon, innerTip, m, options.tipSize - 2 * balloon["border" + m.camel.pos.o], $balloon.css("background-color"));
				$balloon.data("outerTip", outerTip.$).data("innerTip", innerTip.$);
			} else {
				$.each([outerTip.$, innerTip.$], function() { this.remove(); });
			}
		}
		// Make up balloon tip.
		function makeTip(balloon, tip, m, tipSize, color) {
			var len = Math.round(tipSize / 1.7320508);
			tip.inactive()
				["setBorder" + m.camel.pos.f](tipSize)
				["setBorder" + m.camel.pos.c1](len)
				["setBorder" + m.camel.pos.c2](len)
				["set" + m.camel.pos.p1]((m.isTopLeft) ? -tipSize : balloon.inner[m.size.p])
				["set" + m.camel.pos.c1](balloon.inner[m.size.c] / 2 - len)
				.active()
				.$.css("border-" + m.pos.f + "-color", color);
		}
	}

	// True if the event comes from the target or balloon.
	function isValidTargetEvent($target, e) {
		var b = $target.data("balloon") && $target.data("balloon").get(0);
		return !(b && (b == e.relatedTarget || $.contains(b, e.relatedTarget)));
	}

	//-----------------------------------------------------------------------------
	// Public
	//-----------------------------------------------------------------------------
	$.fn.balloon = function(options) {
		options = $.extend(true, {}, $.balloon.defaults, options);
		return this.one("mouseenter", function(e) {
			var $target = $(this), t = this;
			var $balloon = $target.unbind("mouseenter", arguments.callee)
				.showBalloon(options).mouseenter(function(e) {
					isValidTargetEvent($target, e) && $target.showBalloon();
				}).data("balloon");
			if($balloon) {
				$balloon.mouseleave(function(e) {
					if(t == e.relatedTarget || $.contains(t, e.relatedTarget)) return;
					$target.hideBalloon();
				}).mouseenter(function(e) { $target.showBalloon(); });
			}
		}).mouseleave(function(e) {
			var $target = $(this);
			isValidTargetEvent($target, e) && $target.hideBalloon();
		});
	};

	$.fn.showBalloon = function(options) {
		var $target, $balloon, offTimer;
		if(!$.balloon.defaults.css) $.balloon.defaults.css = {};
		if(options || !this.data("options"))
			this.data("options", $.extend(true, {}, $.balloon.defaults, options));
		options = this.data("options");
		return this.each(function() {
			$target = $(this);
			(offTimer = $target.data("offTimer")) && clearTimeout(offTimer);
			var contents = $.isFunction(options.contents)
				? options.contents()
				: (options.contents || $target.attr("title"));
			var isNew = !($balloon = $target.data("balloon"));
			if(isNew) $balloon = $("<div>").append(contents);
			if(!options.url && (!$balloon || $balloon.html() == "")) return;
			if(!isNew && contents && contents != $balloon.html()) $balloon.empty().append(contents);
			$target.removeAttr("title");
			if(options.url) {
				$balloon.load($.isFunction(options.url) ? options.url(this) : options.url, function(res, sts, xhr) {
					if(options.ajaxComplete) options.ajaxComplete(res, sts, xhr);
					makeupBalloon($target, $balloon, options);
				});
			}
			if(isNew) {
				$balloon
					.addClass(options.classname)
					.css(options.css)
					.css({visibility: "hidden", position: "absolute"})
					.appendTo("body");
				$target.data("balloon", $balloon);
				makeupBalloon($target, $balloon, options);
				$balloon.hide().css("visibility", "visible");
			} else {
				makeupBalloon($target, $balloon, options);
			}
			$target.data("onTimer", setTimeout(function() {
				if(options.showAnimation) {
					options.showAnimation.apply($balloon.stop(true, true), [options.showDuration]);
				} else {
					$balloon.show(options.showDuration, function() {
						if(this.style.removeAttribute) { this.style.removeAttribute("filter"); }
					});
				}
			}, options.delay));
		});
	};

	$.fn.hideBalloon = function() {
		var options = this.data("options"), onTimer, offTimer;
		return this.each(function() {
			var $target = $(this);
			(onTimer = $target.data("onTimer")) && clearTimeout(onTimer);
			(offTimer = $target.data("offTimer")) && clearTimeout(offTimer);
			$target.data("offTimer", setTimeout(function() {
				var $balloon = $target.data("balloon");
				if(options.hideAnimation) {
					$balloon && options.hideAnimation.apply($balloon.stop(true, true), [options.hideDuration]);
				} else {
					$balloon && $balloon.stop(true, true).hide(options.hideDuration);
				}
			},
			options.minLifetime));
		});
	};

	$.balloon = {
		defaults: {
			contents: null, url: null, ajaxComplete: null, classname: null,
			position: "top", offsetX: 0, offsetY: 0, tipSize: 12,
			delay: 0, minLifetime: 200,
			showDuration: 100, showAnimation: null,
			hideDuration:  80, hideAnimation: function(d) { this.fadeOut(d); },
			css: {
				minWidth       : "20px",
				padding        : "5px",
				borderRadius   : "6px",
				border         : "solid 1px #777",
				boxShadow      : "4px 4px 4px #555",
				color          : "#666",
				backgroundColor: "#efefef",
				opacity        : ($.support.opacity) ? "0.85" : null,
				zIndex         : "32767",
				textAlign      : "left"
			}
		}
	};
})(jQuery);

















/*ブロック要素ごとリンクを設定させる　個別に設定が必要、CSSも*/

$(function(){
     $(".brandlist dl").click(function(){
         window.location=$(this).find("a").attr("href");
         return false;
    });
});








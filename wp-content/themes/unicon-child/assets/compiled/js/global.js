!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof module&&module.exports?module.exports=function(t,s){return void 0===s&&(s="undefined"!=typeof window?require("jquery"):require("jquery")(t)),e(s),s}:e(jQuery)}(function(e){"use strict";var t=e(document),s=e(window),i="selectric",l="Input Items Open Disabled TempShow HideSelect Wrapper Focus Hover Responsive Above Scroll Group GroupLabel",n=".sl",a=["a","e","i","o","u","n","c","y"],o=[/[\xE0-\xE5]/g,/[\xE8-\xEB]/g,/[\xEC-\xEF]/g,/[\xF2-\xF6]/g,/[\xF9-\xFC]/g,/[\xF1]/g,/[\xE7]/g,/[\xFD-\xFF]/g],r=function(t,s){var i=this;i.element=t,i.$element=e(t),i.state={multiple:!!i.$element.attr("multiple"),enabled:!1,opened:!1,currValue:-1,selectedIdx:-1,highlightedIdx:-1},i.eventTriggers={open:i.open,close:i.close,destroy:i.destroy,refresh:i.refresh,init:i.init},i.init(s)};r.prototype={utils:{isMobile:function(){return/android|ip(hone|od|ad)/i.test(navigator.userAgent)},escapeRegExp:function(e){return e.replace(/[.*+?^${}()|[\]\\]/g,"\\$&")},replaceDiacritics:function(e){for(var t=o.length;t--;)e=e.toLowerCase().replace(o[t],a[t]);return e},format:function(e){var t=arguments;return(""+e).replace(/\{(?:(\d+)|(\w+))\}/g,function(e,s,i){return i&&t[1]?t[1][i]:t[s]})},nextEnabledItem:function(e,t){for(;e[t=(t+1)%e.length].disabled;);return t},previousEnabledItem:function(e,t){for(;e[t=(t>0?t:e.length)-1].disabled;);return t},toDash:function(e){return e.replace(/([a-z])([A-Z])/g,"$1-$2").toLowerCase()},triggerCallback:function(t,s){var l=s.element,n=s.options["on"+t],a=[l].concat([].slice.call(arguments).slice(1));e.isFunction(n)&&n.apply(l,a),e.fn[i].hooks[t]&&e.each(e.fn[i].hooks[t],function(){this.apply(l,a)}),e(l).trigger(i+"-"+this.toDash(t),a)},arrayToClassname:function(t){var s=e.grep(t,function(e){return!!e});return e.trim(s.join(" "))}},init:function(t){var s=this;if(s.options=e.extend(!0,{},e.fn[i].defaults,s.options,t),s.utils.triggerCallback("BeforeInit",s),s.destroy(!0),s.options.disableOnMobile&&s.utils.isMobile())return void(s.disableOnMobile=!0);s.classes=s.getClassNames();var l=e("<input/>",{class:s.classes.input,readonly:s.utils.isMobile()}),n=e("<div/>",{class:s.classes.items,tabindex:-1}),a=e("<div/>",{class:s.classes.scroll}),o=e("<div/>",{class:s.classes.prefix,html:s.options.arrowButtonMarkup}),r=e("<span/>",{class:"label"}),u=s.$element.wrap("<div/>").parent().append(o.prepend(r),n,l),p=e("<div/>",{class:s.classes.hideselect});s.elements={input:l,items:n,itemsScroll:a,wrapper:o,label:r,outerWrapper:u},s.options.nativeOnMobile&&s.utils.isMobile()&&(s.elements.input=void 0,p.addClass(s.classes.prefix+"-is-native"),s.$element.on("change",function(){s.refresh()})),s.$element.on(s.eventTriggers).wrap(p),s.originalTabindex=s.$element.prop("tabindex"),s.$element.prop("tabindex",!1),s.populate(),s.activate(),s.utils.triggerCallback("Init",s)},activate:function(){var e=this,t=e.elements.items.closest(":visible").children(":hidden").addClass(e.classes.tempshow),s=e.$element.width();t.removeClass(e.classes.tempshow),e.utils.triggerCallback("BeforeActivate",e),e.elements.outerWrapper.prop("class",e.utils.arrayToClassname([e.classes.wrapper,e.$element.prop("class").replace(/\S+/g,e.classes.prefix+"-$&"),e.options.responsive?e.classes.responsive:""])),e.options.inheritOriginalWidth&&s>0&&e.elements.outerWrapper.width(s),e.unbindEvents(),e.$element.prop("disabled")?(e.elements.outerWrapper.addClass(e.classes.disabled),e.elements.input&&e.elements.input.prop("disabled",!0)):(e.state.enabled=!0,e.elements.outerWrapper.removeClass(e.classes.disabled),e.$li=e.elements.items.removeAttr("style").find("li"),e.bindEvents()),e.utils.triggerCallback("Activate",e)},getClassNames:function(){var t=this,s=t.options.customClass,i={};return e.each(l.split(" "),function(e,l){var n=s.prefix+l;i[l.toLowerCase()]=s.camelCase?n:t.utils.toDash(n)}),i.prefix=s.prefix,i},setLabel:function(){var t=this,s=t.options.labelBuilder;if(t.state.multiple){var i=e.isArray(t.state.currValue)?t.state.currValue:[t.state.currValue];i=0===i.length?[0]:i;var l=e.map(i,function(s){return e.grep(t.lookupItems,function(e){return e.index===s})[0]});l=e.grep(l,function(t){return l.length>1||0===l.length?""!==e.trim(t.value)&&t.value!==t.text:t}),l=e.map(l,function(i){return e.isFunction(s)?s(i):t.utils.format(s,i)}),t.options.multiple.maxLabelEntries&&(l.length>=t.options.multiple.maxLabelEntries+1?(l=l.slice(0,t.options.multiple.maxLabelEntries),l.push(e.isFunction(s)?s({text:"..."}):t.utils.format(s,{text:"..."}))):l.slice(l.length-1)),t.elements.label.html(l.join(t.options.multiple.separator))}else{var n=t.lookupItems[t.state.currValue];t.elements.label.html(e.isFunction(s)?s(n):t.utils.format(s,n))}},populate:function(){var t=this,s=t.$element.children(),i=t.$element.find("option"),l=i.filter(":selected"),n=i.index(l),a=0,o=t.state.multiple?[]:0;l.length>1&&t.state.multiple&&(n=[],l.each(function(){n.push(e(this).index())})),t.state.currValue=~n?n:o,t.state.selectedIdx=t.state.currValue,t.state.highlightedIdx=t.state.currValue,t.items=[],t.lookupItems=[],s.length&&(s.each(function(s){var i=e(this);if(i.is("optgroup")){var l={element:i,label:i.prop("label"),groupDisabled:i.prop("disabled"),items:[]};i.children().each(function(s){var i=e(this);l.items[s]=t.getItemData(a,i,l.groupDisabled),t.lookupItems[a]=l.items[s],a++}),t.items[s]=l}else t.items[s]=t.getItemData(a,i,i.prop("disabled")),t.lookupItems[a]=t.items[s],a++}),t.setLabel(),t.elements.items.append(t.elements.itemsScroll.html(t.getItemsMarkup(t.items))))},getItemData:function(t,s,i){var l=this;return{index:t,element:s,value:s.val(),className:s.prop("class"),text:s.html(),slug:e.trim(l.utils.replaceDiacritics(s.html())),selected:s.prop("selected"),disabled:i}},getItemsMarkup:function(t){var s=this,i="<ul>";return e.each(t,function(t,l){void 0!==l.label?(i+=s.utils.format('<ul class="{1}"><li class="{2}">{3}</li>',s.utils.arrayToClassname([s.classes.group,l.groupDisabled?"disabled":"",l.element.prop("class")]),s.classes.grouplabel,l.element.prop("label")),e.each(l.items,function(e,t){i+=s.getItemMarkup(t.index,t)}),i+="</ul>"):i+=s.getItemMarkup(l.index,l)}),i+"</ul>"},getItemMarkup:function(t,s){var i=this,l=i.options.optionsItemBuilder;return i.utils.format('<li data-index="{1}" class="{2}">{3}</li>',t,i.utils.arrayToClassname([s.className,t===i.items.length-1?"last":"",s.disabled?"disabled":"",s.selected?"selected":""]),e.isFunction(l)?l(s,s.element,t):i.utils.format(l,s))},unbindEvents:function(){var e=this;e.elements.wrapper.add(e.$element).add(e.elements.outerWrapper).add(e.elements.input).off(n)},bindEvents:function(){var t=this;t.elements.outerWrapper.on("mouseenter"+n+" mouseleave"+n,function(s){e(this).toggleClass(t.classes.hover,"mouseenter"===s.type),t.options.openOnHover&&(clearTimeout(t.closeTimer),"mouseleave"===s.type?t.closeTimer=setTimeout(e.proxy(t.close,t),t.options.hoverIntentTimeout):t.open())}),t.elements.wrapper.on("click"+n,function(e){t.state.opened?t.close():t.open(e)}),t.options.nativeOnMobile&&t.utils.isMobile()||(t.$element.on("focus"+n,function(){t.elements.input.focus()}),t.elements.input.prop({tabindex:t.originalTabindex,disabled:!1}).on("keydown"+n,e.proxy(t.handleKeys,t)).on("focusin"+n,function(e){t.elements.outerWrapper.addClass(t.classes.focus),t.elements.input.one("blur",function(){t.elements.input.blur()}),t.options.openOnFocus&&!t.state.opened&&t.open(e)}).on("focusout"+n,function(){t.elements.outerWrapper.removeClass(t.classes.focus)}).on("input propertychange",function(){var s=t.elements.input.val();clearTimeout(t.resetStr),t.resetStr=setTimeout(function(){t.elements.input.val("")},t.options.keySearchTimeout),s.length&&e.each(t.items,function(e,i){if(new RegExp("^"+t.utils.escapeRegExp(s),"i").test(i.slug)&&!i.disabled)return t.highlight(e),!1})})),t.$li.on({mousedown:function(e){e.preventDefault(),e.stopPropagation()},click:function(){return t.select(e(this).data("index")),!1}})},handleKeys:function(t){var s=this,i=t.keyCode||t.which,l=s.options.keys,n=e.inArray(i,l.previous)>-1,a=e.inArray(i,l.next)>-1,o=e.inArray(i,l.select)>-1,r=e.inArray(i,l.open)>-1,u=s.state.highlightedIdx,p=n&&0===u||a&&u+1===s.items.length,c=0;if(13!==i&&32!==i||t.preventDefault(),n||a){if(!s.options.allowWrap&&p)return;n&&(c=s.utils.previousEnabledItem(s.items,u)),a&&(c=s.utils.nextEnabledItem(s.items,u)),s.highlight(c)}return o&&s.state.opened?(s.select(u),void(s.state.multiple&&s.options.multiple.keepMenuOpen||s.close())):void(r&&!s.state.opened&&s.open())},refresh:function(){var e=this;e.populate(),e.activate(),e.utils.triggerCallback("Refresh",e)},setOptionsDimensions:function(){var e=this,t=e.elements.items.closest(":visible").children(":hidden").addClass(e.classes.tempshow),s=e.options.maxHeight,i=e.elements.items.outerWidth(),l=e.elements.wrapper.outerWidth()-(i-e.elements.items.width());!e.options.expandToItemText||l>i?e.finalWidth=l:(e.elements.items.css("overflow","scroll"),e.elements.outerWrapper.width(9e4),e.finalWidth=e.elements.items.width(),e.elements.items.css("overflow",""),e.elements.outerWrapper.width("")),e.elements.items.width(e.finalWidth).height()>s&&e.elements.items.height(s),t.removeClass(e.classes.tempshow)},isInViewport:function(){var e=this,t=s.scrollTop(),i=s.height(),l=e.elements.outerWrapper.offset().top,n=e.elements.outerWrapper.outerHeight(),a=l+n+e.itemsHeight<=t+i,o=l-e.itemsHeight>t,r=!a&&o;e.elements.outerWrapper.toggleClass(e.classes.above,r)},detectItemVisibility:function(t){var s=this;s.state.multiple&&(t=e.isArray(t)&&0===t.length?0:t,t=e.isArray(t)?Math.min.apply(Math,t):t);var i=s.$li.eq(t).outerHeight(),l=s.$li[t].offsetTop,n=s.elements.itemsScroll.scrollTop(),a=l+2*i;s.elements.itemsScroll.scrollTop(a>n+s.itemsHeight?a-s.itemsHeight:l-i<n?l-i:n)},open:function(s){var l=this;return(!l.options.nativeOnMobile||!l.utils.isMobile())&&(l.utils.triggerCallback("BeforeOpen",l),s&&(s.preventDefault(),s.stopPropagation()),void(l.state.enabled&&(l.setOptionsDimensions(),e("."+l.classes.hideselect,"."+l.classes.open).children()[i]("close"),l.state.opened=!0,l.itemsHeight=l.elements.items.outerHeight(),l.itemsInnerHeight=l.elements.items.height(),l.elements.outerWrapper.addClass(l.classes.open),l.elements.input.val(""),s&&"focusin"!==s.type&&l.elements.input.focus(),setTimeout(function(){t.on("click"+n,e.proxy(l.close,l)).on("scroll"+n,e.proxy(l.isInViewport,l))},1),l.isInViewport(),l.options.preventWindowScroll&&t.on("mousewheel"+n+" DOMMouseScroll"+n,"."+l.classes.scroll,function(t){var s=t.originalEvent,i=e(this).scrollTop(),n=0;"detail"in s&&(n=s.detail*-1),"wheelDelta"in s&&(n=s.wheelDelta),"wheelDeltaY"in s&&(n=s.wheelDeltaY),"deltaY"in s&&(n=s.deltaY*-1),(i===this.scrollHeight-l.itemsInnerHeight&&n<0||0===i&&n>0)&&t.preventDefault()}),l.detectItemVisibility(l.state.selectedIdx),l.highlight(l.state.multiple?-1:l.state.selectedIdx),l.utils.triggerCallback("Open",l))))},close:function(){var e=this;e.utils.triggerCallback("BeforeClose",e),t.off(n),e.elements.outerWrapper.removeClass(e.classes.open),e.state.opened=!1,e.utils.triggerCallback("Close",e)},change:function(){var t=this;t.utils.triggerCallback("BeforeChange",t),t.state.multiple?(e.each(t.lookupItems,function(e){t.lookupItems[e].selected=!1,t.$element.find("option").prop("selected",!1)}),e.each(t.state.selectedIdx,function(e,s){t.lookupItems[s].selected=!0,t.$element.find("option").eq(s).prop("selected",!0)}),t.state.currValue=t.state.selectedIdx,t.setLabel(),t.utils.triggerCallback("Change",t)):t.state.currValue!==t.state.selectedIdx&&(t.$element.prop("selectedIndex",t.state.currValue=t.state.selectedIdx).data("value",t.lookupItems[t.state.selectedIdx].text),t.setLabel(),t.utils.triggerCallback("Change",t))},highlight:function(e){var t=this,s=t.$li.filter("[data-index]").removeClass("highlighted");t.utils.triggerCallback("BeforeHighlight",t),void 0!==e&&(e===-1||t.lookupItems[e].disabled||(s.eq(t.state.highlightedIdx=e).addClass("highlighted"),t.detectItemVisibility(e)),t.utils.triggerCallback("Highlight",t))},select:function(t){var s=this,i=s.$li.filter("[data-index]").removeClass("selected");if(s.utils.triggerCallback("BeforeSelect",s,t),t!==-1&&s.lookupItems[t].disabled)return!1;if(s.state.multiple){s.state.selectedIdx=e.isArray(s.state.selectedIdx)?s.state.selectedIdx:[s.state.selectedIdx];var l=e.inArray(t,s.state.selectedIdx);l!==-1?s.state.selectedIdx.splice(l,1):s.state.selectedIdx.push(t),i.filter(function(t){return e.inArray(t,s.state.selectedIdx)!==-1}).addClass("selected")}else i.eq(s.state.selectedIdx=t).addClass("selected");s.state.multiple&&s.options.multiple.keepMenuOpen||s.close(),s.change(),s.utils.triggerCallback("Select",s,t)},destroy:function(e){var t=this;t.state&&t.state.enabled&&(t.elements.items.add(t.elements.wrapper).add(t.elements.input).remove(),e||t.$element.removeData(i).removeData("value"),t.$element.prop("tabindex",t.originalTabindex).off(n).off(t.eventTriggers).unwrap().unwrap(),t.state.enabled=!1)}},e.fn[i]=function(t){return this.each(function(){var s=e.data(this,i);s&&!s.disableOnMobile?"string"==typeof t&&s[t]?s[t]():s.init(t):e.data(this,i,new r(this,t))})},e.fn[i].hooks={add:function(e,t,s){return this[e]||(this[e]={}),this[e][t]=s,this},remove:function(e,t){return delete this[e][t],this}},e.fn[i].defaults={onChange:function(t){e(t).change()},maxHeight:300,keySearchTimeout:500,arrowButtonMarkup:'<b class="button">&#x25be;</b>',disableOnMobile:!0,nativeOnMobile:!0,openOnFocus:!0,openOnHover:!1,hoverIntentTimeout:500,expandToItemText:!1,responsive:!1,preventWindowScroll:!0,inheritOriginalWidth:!1,allowWrap:!0,optionsItemBuilder:"{text}",labelBuilder:"{text}",keys:{previous:[37,38],next:[39,40],select:[9,13,27],open:[13,32,37,38,39,40],close:[9,27]},customClass:{prefix:i,camelCase:!1},multiple:{separator:", ",keepMenuOpen:!0,maxLabelEntries:!1}}});
!function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery"],t):"undefined"!=typeof module&&module.exports?module.exports=t(require("jquery")):t(jQuery)}(function(t){var e=-1,o=-1,a=function(t){return parseFloat(t)||0},i=function(e){var o=1,i=t(e),n=null,r=[];return i.each(function(){var e=t(this),i=e.offset().top-a(e.css("margin-top")),s=r.length>0?r[r.length-1]:null;null===s?r.push(e):Math.floor(Math.abs(n-i))<=o?r[r.length-1]=s.add(e):r.push(e),n=i}),r},n=function(e){var o={byRow:!0,property:"height",target:null,remove:!1};return"object"==typeof e?t.extend(o,e):("boolean"==typeof e?o.byRow=e:"remove"===e&&(o.remove=!0),o)},r=t.fn.matchHeight=function(e){var o=n(e);if(o.remove){var a=this;return this.css(o.property,""),t.each(r._groups,function(t,e){e.elements=e.elements.not(a)}),this}return this.length<=1&&!o.target?this:(r._groups.push({elements:this,options:o}),r._apply(this,o),this)};r.version="master",r._groups=[],r._throttle=80,r._maintainScroll=!1,r._beforeUpdate=null,r._afterUpdate=null,r._rows=i,r._parse=a,r._parseOptions=n,r._apply=function(e,o){var s=n(o),h=t(e),l=[h],c=t(window).scrollTop(),p=t("html").outerHeight(!0),d=h.parents().filter(":hidden");return d.each(function(){var e=t(this);e.data("style-cache",e.attr("style"))}),d.css("display","block"),s.byRow&&!s.target&&(h.each(function(){var e=t(this),o=e.css("display");"inline-block"!==o&&"flex"!==o&&"inline-flex"!==o&&(o="block"),e.data("style-cache",e.attr("style")),e.css({display:o,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px",overflow:"hidden"})}),l=i(h),h.each(function(){var e=t(this);e.attr("style",e.data("style-cache")||"")})),t.each(l,function(e,o){var i=t(o),n=0;if(s.target)n=s.target.outerHeight(!1);else{if(s.byRow&&i.length<=1)return void i.css(s.property,"");i.each(function(){var e=t(this),o=e.attr("style"),a=e.css("display");"inline-block"!==a&&"flex"!==a&&"inline-flex"!==a&&(a="block");var i={display:a};i[s.property]="",e.css(i),e.outerHeight(!1)>n&&(n=e.outerHeight(!1)),o?e.attr("style",o):e.css("display","")})}i.each(function(){var e=t(this),o=0;s.target&&e.is(s.target)||("border-box"!==e.css("box-sizing")&&(o+=a(e.css("border-top-width"))+a(e.css("border-bottom-width")),o+=a(e.css("padding-top"))+a(e.css("padding-bottom"))),e.css(s.property,n-o+"px"))})}),d.each(function(){var e=t(this);e.attr("style",e.data("style-cache")||null)}),r._maintainScroll&&t(window).scrollTop(c/p*t("html").outerHeight(!0)),this},r._applyDataApi=function(){var e={};t("[data-match-height], [data-mh]").each(function(){var o=t(this),a=o.attr("data-mh")||o.attr("data-match-height");a in e?e[a]=e[a].add(o):e[a]=o}),t.each(e,function(){this.matchHeight(!0)})};var s=function(e){r._beforeUpdate&&r._beforeUpdate(e,r._groups),t.each(r._groups,function(){r._apply(this.elements,this.options)}),r._afterUpdate&&r._afterUpdate(e,r._groups)};r._update=function(a,i){if(i&&"resize"===i.type){var n=t(window).width();if(n===e)return;e=n}a?o===-1&&(o=setTimeout(function(){s(i),o=-1},r._throttle)):s(i)},t(r._applyDataApi),t(window).bind("load",function(t){r._update(!1,t)}),t(window).bind("resize orientationchange",function(t){r._update(!0,t)})});
jQuery(document).ready(function(e){({$header:e(".fullheader-wrapper"),transparentHeader:function(){var t=this;e(document).scrollTop()>=60?t.$header.find("#header.header-v1").removeClass("header-transparent"):t.$header.find("#header.header-v1.stuck").addClass("header-transparent")},init:function(){var t=this;t.$header.length>0&&(t.$header.waypoint("sticky"),e("body").hasClass("header-is-transparent")&&(e(document).scroll(function(){t.transparentHeader()}),t.transparentHeader()))}}).init(),{$slider:e(".rev_slider_wrapper.fullwidthbanner-container"),slides:null,init:function(){var e=this;e.$slider.length>0&&(e.slides=e.$slider.find("ul>li").length,1==e.slides&&e.$slider.addClass("only-one-slide"))}}.init(),{select_elements:[".tribe-bar-search-category-filter>select",".publications-gswa select"],init:function(){var t=this;t.select_elements.forEach(function(t,n){var r=e(t);r.length>0&&r.selectric()})}}.init(),{elements:[".blog-gswa .post",".yarpp-related .post",".events-row-wrapper .post .entry-image",".events-row-wrapper .post .entry-title",".events-row-wrapper .post .entry-content",".publications-gswa .post",".publications-gswa .post-inner",".our-mission-boxes .box","body.home .boxes-beneath .iconbox h3","body.home .boxes-beneath .iconbox .iconbox-content"],init:function(){var t=this;t.elements.forEach(function(t,n){var r=e(t);r.length>0&&r.matchHeight({byRow:!0})})}}.init()});
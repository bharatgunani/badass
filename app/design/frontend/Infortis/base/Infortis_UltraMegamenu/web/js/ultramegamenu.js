;(function($,window,document,undefined){$.widget("infortis.ultramegamenu",{options:{mobileMenuThreshold:770,mode:0,itemSelector:'li',panelSelector:'.nav-panel',openerSelector:'.opener',vertnavTriggerSelector:undefined,isVerticalLayout:false,mobileClasses:'nav-mobile nav-acco',mobnavTriggerableClasses:'nav-mobile-triggerable',regularClasses:'nav-regular',vertnavClasses:'nav-vert',vertnavTriggerableClasses:'nav-vert-triggerable',initMobileMenuCollapsed:true,initVerticalMenuCollapsed:true,outermostContainer:undefined,fullWidthDdContainer:undefined,ddDelayIn:50,ddDelayOut:200,ddAnimationDurationIn:50,ddAnimationDurationOut:200,isTouchDevice:('ontouchstart'in window)||(navigator.msMaxTouchPoints>0)},isMobile:false,bar:undefined,itemsList:undefined,panels:undefined,mobnavTrigger:undefined,vertnavTrigger:undefined,outermostContainerIsWindow:false,fullWidthDdContainerIsWindow:false,_create:function(){this._initPlugin();},_initPlugin:function()
{var _self=this;this.bar=this.element;this.itemsList=this.bar.children('ul');this.panels=this.bar.find(this.options.panelSelector);this.mobnavTrigger=this.bar.parent().children('.mobnav-trigger');if(this.options.vertnavTriggerSelector!==undefined)
{this.vertnavTrigger=$(this.options.vertnavTriggerSelector);}
else
{this.vertnavTrigger=this.bar.parent().children('.vertnav-trigger');}
if(this.mobnavTrigger.length)
{this.options.mobileClasses+=' '+this.options.mobnavTriggerableClasses;}
if(this.options.isVerticalLayout)
{this.options.regularClasses+=' '+this.options.vertnavClasses;if(this.vertnavTrigger.length)
{this.options.regularClasses+=' '+this.options.vertnavTriggerableClasses;}}
if(this.options.mode===0)
{this._initDualMode();}
else if(this.options.mode===-1)
{this._initMobileMode();}
if(this.options.outermostContainer===undefined)
{this.options.outermostContainer=this.bar;}
else if(this.options.outermostContainer==='window')
{this.print('this.options.outermostContainer === window');this.options.outermostContainer=$(window);this.outermostContainerIsWindow=true;}
if(this.options.fullWidthDdContainer===undefined)
{this.options.fullWidthDdContainer=this.bar;}
else if(this.options.fullWidthDdContainer==='window')
{this.print('this.options.fullWidthDdContainer === window');this.options.fullWidthDdContainer=$(window);this.fullWidthDdContainerIsWindow=true;}
if(this.mobnavTrigger.length)
{this.hookToMobnavTriggerClick();}
if(this.vertnavTrigger.length)
{this.hookToVertnavTriggerClick();}
this._hookToStickyHeader();},_initDualMode:function()
{var _self=this;this.itemsList.uaccordion(this.options.panelSelector,this.options.openerSelector,this.options.itemSelector);if($(window).width()>=this.options.mobileMenuThreshold)
{_self._cleanUpAfterMobileMenu();}
enquire.register('screen and (max-width: '+(this.options.mobileMenuThreshold-1)+'px)',{match:function(){_self._activateMobileMenu();},unmatch:function(){_self._cleanUpAfterMobileMenu();}}).register('screen and (min-width: '+this.options.mobileMenuThreshold+'px)',{deferSetup:true,setup:function(){_self._cleanUpAfterMobileMenu();},match:function(){_self._activateRegularMenu();},unmatch:function(){_self._prepareMobileMenu();}});},_initMobileMode:function()
{this.itemsList.uaccordion(this.options.panelSelector,this.options.openerSelector,this.options.itemSelector);this._activateMobileMenu();},_activateMobileMenu:function()
{this.print('_activateMobileMenu');this.isMobile=true;this.bar.addClass(this.options.mobileClasses).removeClass(this.options.regularClasses);this.print('_activateMobileMenu: fire closeMenuViaVertnavTrigger()');this.vertnavTrigger.hide()
this.closeMenuViaVertnavTrigger();this.closeMenuViaMobnavTrigger();this.mobnavTrigger.show();this.print('trigger: activate-MOBILE-menu');$(document).trigger("activate-mobile-menu");},_activateRegularMenu:function()
{this.print('activate_REGULAR_Menu');this.isMobile=false;this.bar.addClass(this.options.regularClasses).removeClass(this.options.mobileClasses);this.print('activate_REGULAR_Menu: fire closeMenuViaMobnavTrigger()');this.mobnavTrigger.hide();this.closeMenuViaMobnavTrigger();this.closeMenuViaVertnavTrigger();this.vertnavTrigger.show();this.print('trigger: activate-REGULAR-menu');$(document).trigger("activate-regular-menu");},_cleanUpAfterMobileMenu:function()
{this.print('_cleanUpAfterMobileMenu');this.panels.css('display','');},_prepareMobileMenu:function()
{this.print('_prepareMobileMenu');this.panels.hide();this.itemsList.find('.item-active').each(function(){$(this).children('.nav-panel').show();});},openMenuViaMobnavTrigger:function()
{this.print('-> openMenuViaMobnavTrigger()');this.mobnavTrigger.addClass('active');this.bar.addClass('show');},closeMenuViaMobnavTrigger:function()
{this.print('x- closeMenuViaMobnavTrigger()');this.mobnavTrigger.removeClass('active');this.bar.removeClass('show');},openMenuViaVertnavTrigger:function()
{this.print('---> openMenuViaVertnavTrigger()');this.vertnavTrigger.addClass('active');this.bar.addClass('show');},closeMenuViaVertnavTrigger:function()
{this.print('X--- closeMenuViaVertnavTrigger()');this.vertnavTrigger.removeClass('active');this.bar.removeClass('show');},hookToMobnavTriggerClick:function()
{var _self=this;this.mobnavTrigger.on('click',function(e){_self.print('on mobnavTrigger click');if($(this).hasClass('active'))
{_self.closeMenuViaMobnavTrigger();}
else
{_self.openMenuViaMobnavTrigger();}});this.print('hookToMobnavTriggerClick: isMobile = '+this.isMobile+', initCollapsed = '+this.options.initMobileMenuCollapsed);if(this.isMobile&&this.options.initMobileMenuCollapsed==false)
{_self.openMenuViaMobnavTrigger();}},hookToVertnavTriggerClick:function()
{var _self=this;this.vertnavTrigger.on('click',function(e){_self.print('on vertnavTrigger click');if($(this).hasClass('active'))
{_self.closeMenuViaVertnavTrigger();}
else
{_self.openMenuViaVertnavTrigger();}});this.print('hookToVertnavTriggerClick: isMobile = '+this.isMobile+', initCollapsed = '+this.options.initVerticalMenuCollapsed);if(this.isMobile==false&&this.options.initVerticalMenuCollapsed==false)
{_self.openMenuViaVertnavTrigger();}},enableDropdowns:function()
{this.print('enableDropdowns');this._hookToItemHoverDynamically();},_hookToItemHoverDynamically:function()
{this.print('_hookToItemHoverDynamically');var _self=this;this.bar.on('mouseenter','li.parent.level0',function(){if(_self.isMobile==false)
{_self._showDropdown($(this));}}).on('mouseleave','li.parent.level0',function(){if(_self.isMobile==false)
{_self._hideDd($(this));}});},_showDropdown:function(item)
{this.print('_showDdHorizontal: '+item.children('a').children('span').text());var _self=this;var menubar=this.bar;var dd=item.children('.nav-panel');var isVert=menubar.hasClass('nav-vert');var itemPos=item.position();var modX=0;var modY=0;if(isVert)
{modX=item.outerWidth();}
else
{modY=item.height();}
var ddPos={left:itemPos.left+modX,top:itemPos.top+modY};this.print('_showDdHorizontal: itemPos.left='+itemPos.left+', itemPos.top='+itemPos.top+', ddPos.left='+ddPos.left+', ddPos.top='+ddPos.top);var origWidth=dd.data('original-width');if(origWidth!==undefined)
{this.print('_showDdHorizontal: get ORIGINAL-width = '+origWidth);dd.width(origWidth);}
var outermostCon=this.options.outermostContainer;var outermostConWidth=outermostCon.outerWidth();var ddWidth=dd.outerWidth();var fullWidthDdCon=this.options.fullWidthDdContainer;var menubarOffset;if(this.outermostContainerIsWindow===false)
{menubarOffset=menubar.offset().left-outermostCon.offset().left;this.print('_showDdHorizontal: menubarOffset='+menubarOffset+' = menubar.offset().left='+menubar.offset().left+' + outermostCon.offset().left='+outermostCon.offset().left);}
else
{menubarOffset=menubar.offset().left;this.print('_showDdHorizontal: menubarOffset='+menubarOffset+' = menubar.offset().left='+menubar.offset().left);}
var ddOffset=menubarOffset+ddPos.left;if(dd.hasClass('full-width'))
{this.print('_showDdHorizontal: -- is --full-width');if(isVert)
{var freeSpaceWidth=outermostConWidth-(menubarOffset+menubar.outerWidth());dd.width(freeSpaceWidth);this.print('_showDdHorizontal: freeSpaceWidth = outermostConWidth - (menubarOffset + menubar.outerWidth())');this.print('_showDdHorizontal: '+freeSpaceWidth+' = '+outermostConWidth+' - ('+menubarOffset+' + '+menubar.outerWidth()+')');}
else
{var manubarShiftToFullWidthDdCon;if(this.fullWidthDdContainerIsWindow===false)
{manubarShiftToFullWidthDdCon=menubar.offset().left-fullWidthDdCon.offset().left;}
else
{manubarShiftToFullWidthDdCon=menubar.offset().left;}
dd.width(fullWidthDdCon.outerWidth());ddPos.left=(-1)*manubarShiftToFullWidthDdCon;this.print('FULL: manubarShiftToFullWidthDdCon='+manubarShiftToFullWidthDdCon+', fullWidthDdCon.outerWidth()='+fullWidthDdCon.outerWidth());}}
else
{this.print('_showDdHorizontal: -- NON --full-width');var diffRight=(ddOffset+ddWidth)-outermostConWidth;this.print('_showDdHorizontal: diffRight = (ddOffset + ddWidth) - outermostConWidth == '+diffRight);this.print('_showDdHorizontal: ddOffset='+ddOffset+', ddWidth='+ddWidth+', outermostConWidth='+outermostConWidth);if(diffRight>0)
{if(isVert)
{var freeSpaceWidth=outermostConWidth-(menubarOffset+menubar.outerWidth());dd.data('original-width',ddWidth);dd.width(freeSpaceWidth);this.print('_showDdHorizontal: freeSpaceWidth = outermostConWidth - (menubarOffset + menubar.outerWidth())');this.print('_showDdHorizontal: '+freeSpaceWidth+' = '+outermostConWidth+' - ('+menubarOffset+' + '+menubar.outerWidth()+')');}
else
{this.print('_showDdHorizontal: sticks out at right side: diffRight > 0');var ddPosLeft_NEW=ddPos.left-diffRight;var diffLeft=ddOffset-diffRight;this.print('_showDdHorizontal: diffLeft = ddOffset - diffRight == '+diffLeft);if(diffLeft<0)
{this.print('_showDdHorizontal: sticks out at the LEFT side too: diffLeft < 0');dd.data('original-width',ddWidth);dd.width(outermostConWidth);ddPos.left=(-1)*menubarOffset;}
else
{this.print('_showDdHorizontal: ddPosLeft_NEW [ddPos.left - diffRight] = '+ddPosLeft_NEW);ddPos.left=ddPosLeft_NEW;}}}}
dd.css({'left':ddPos.left+'px','top':ddPos.top+'px'}).stop(true,true).delay(_self.options.ddDelayIn).fadeIn(_self.options.ddAnimationDurationIn,"easeOutCubic");},_hideDd:function(item)
{var _self=this;item.children(".nav-panel").stop(true,true).delay(_self.options.ddDelayOut).fadeOut(_self.options.ddAnimationDurationOut,"easeInCubic");},_hookToStickyHeader:function()
{var _self=this;$(document).on('activate-sticky-header',function(e){_self.itemsList.children('.nav-item--parent').each(function(){$(this).children('.nav-panel').hide();_self.print('instant hide');});});},print:function(msg){}});})(jQuery,window,document);
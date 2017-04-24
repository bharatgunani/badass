;(function($,window,document,undefined){$.widget("infortis.smartheader",{options:{mobileHeaderThreshold:770,searchBlockSelector:'#header-search',compareBlockSelector:'#mini-compare',cartBlockSelector:'#minicart',accountLinksBlockSelector:'#header-account',dropdownBlockClass:'dropdown-block'},rootContainer:undefined,mainMenuBlock:undefined,mainMenuMarkerMobile:undefined,mainMenuMarkerRegular:undefined,searchBlock:undefined,compareBlock:undefined,cartBlock:undefined,accountLinksBlock:undefined,_create:function()
{this._initPlugin();},_initPlugin:function()
{var _self=this;this.rootContainer=this.element;this.searchBlock=$(this.options.searchBlockSelector);this.compareBlock=$(this.options.compareBlockSelector);this.cartBlock=$(this.options.cartBlockSelector);this.accountLinksBlock=$(this.options.accountLinksBlockSelector);enquire.register('screen and (max-width: '+(this.options.mobileHeaderThreshold-1)+'px)',{match:function(){_self._activateMobileHeader();}}).register('screen and (min-width: '+this.options.mobileHeaderThreshold+'px)',{match:function(){_self._activateRegularHeader();}});$(document).ready(function(){_self._deferredInit();});},_deferredInit:function()
{var _self=this;this._evalMenu();enquire.register('screen and (max-width: '+(this.options.mobileHeaderThreshold-1)+'px)',{deferSetup:true,setup:function(){if(_self.mainMenuBlock!==undefined)
{_self.mainMenuMarkerMobile.after(_self.mainMenuBlock);}}}).register('screen and (min-width: '+this.options.mobileHeaderThreshold+'px)',{deferSetup:true,setup:function(){if(_self.mainMenuBlock!==undefined)
{_self.mainMenuMarkerRegular.after(_self.mainMenuBlock);}}});},_evalMenu:function()
{var menu2=$('#mainmenu2');var menu=$('#mainmenu');if(menu2.length)
{this.mainMenuBlock=menu2;this.mainMenuMarkerRegular=$('#nav-marker-regular2');}
else if(menu.length)
{this.mainMenuBlock=menu;this.mainMenuMarkerRegular=$('#nav-marker-regular');}
this.mainMenuMarkerMobile=$('#nav-marker-mobile');},_activateMobileHeader:function()
{this.rootContainer.addClass('header-mobile').removeClass('header-regular');$(document).trigger("activate-mobile-header");this._moveElementsToMobilePosition();},_activateRegularHeader:function()
{this.rootContainer.addClass('header-regular').removeClass('header-mobile');$(document).trigger("activate-regular-header");this._moveElementsToRegularPosition();},_moveElementsToMobilePosition:function()
{$('#mini-cart-marker-mobile').after(this.cartBlock);$('#search-marker-mobile').after(this.searchBlock);$('#mini-compare-marker-mobile').after(this.compareBlock);$('#account-links-marker-mobile').after(this.accountLinksBlock);if(this.mainMenuBlock!==undefined)
{this.mainMenuMarkerMobile.after(this.mainMenuBlock);}
$('.skip-active').removeClass('skip-active');this.cartBlock.removeClass(this.options.dropdownBlockClass);this.compareBlock.removeClass(this.options.dropdownBlockClass);$('#header-cart').css('display','');$('#header-compare').css('display','');},_moveElementsToRegularPosition:function()
{$('#mini-cart-marker-regular').after(this.cartBlock);$('#search-marker-regular').after(this.searchBlock);$('#mini-compare-marker-regular').after(this.compareBlock);$('#account-links-marker-regular').after(this.accountLinksBlock);if(this.mainMenuBlock!==undefined)
{this.mainMenuMarkerRegular.after(this.mainMenuBlock);}
$('.skip-active').removeClass('skip-active');this.cartBlock.addClass(this.options.dropdownBlockClass);this.compareBlock.addClass(this.options.dropdownBlockClass);}});})(jQuery,window,document);
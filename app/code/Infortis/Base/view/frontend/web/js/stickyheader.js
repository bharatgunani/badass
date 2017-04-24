;(function($,window,document,undefined){$.widget("infortis.stickyheader",{options:{stickyThreshold:770,cartBlockSelector:'#minicart'},isSticky:false,isSuspended:false,headerContainer:undefined,stickyContainer:undefined,compareBlock:undefined,cartBlock:undefined,navHolderBlock1:undefined,navHolderBlock2:undefined,stickyContainerOffsetTop:55,requiredRecalculation:false,_create:function(){this._initPlugin();},_initPlugin:function(customOptions)
{var _self=this;this.headerContainer=this.element;this.stickyContainer=$('.sticky-container');this.compareBlock=$('#mini-compare');this.cartBlock=$(this.options.cartBlockSelector);this.navHolderBlock1=$('#nav-holder2');this.navHolderBlock2=$('#nav-holder3');this.cartMarkerRegular=$('#mini-cart-marker-regular');this.compareMarkerRegular=$('#mini-compare-marker-regular');this.hookToActivatedDeactivated();this.calculateStickyContainerOffsetTop();this.applySticky();this.hookToScroll();this.hookToResize();if(this.options.stickyThreshold>0)
{enquire.register('(max-width: '+(this.options.stickyThreshold-1)+'px)',{match:function(){_self.suspendSticky();},unmatch:function(){_self.unsuspendSticky();}});}},calculateStickyContainerOffsetTop:function()
{this.stickyContainerOffsetTop=this.stickyContainer.offset().top+this.stickyContainer.outerHeight();this.requiredRecalculation=false;},applySticky:function()
{if(this.isSuspended)return;if(this.requiredRecalculation)
{if(!this.isSticky)
{this.calculateStickyContainerOffsetTop();}}
var viewportOffsetTop=$(window).scrollTop();if(viewportOffsetTop>this.stickyContainerOffsetTop)
{if(!this.isSticky)
{this.activateSticky();}}
else
{if(this.isSticky)
{this.deactivateSticky();}}},activateSticky:function()
{var stickyContainerHeight=this.stickyContainer.outerHeight();var originalHeaderHeight=this.headerContainer.css('height');this.headerContainer.css('height',originalHeaderHeight);$(document).trigger("activate-sticky-header");this.headerContainer.addClass('sticky-header');this.isSticky=true;this.stickyContainer.css('margin-top','-'+stickyContainerHeight+'px').animate({'margin-top':'0'},200,'easeOutCubic');},deactivateSticky:function()
{this.headerContainer.css('height','');this.headerContainer.removeClass('sticky-header');this.isSticky=false;$(document).trigger("deactivate-sticky-header");},suspendSticky:function()
{this.isSuspended=true;if(this.isSticky)
{this.deactivateSticky();}},unsuspendSticky:function()
{this.isSuspended=false;this.applySticky();},hookToScroll:function()
{var _self=this;$(window).on("scroll",function(e){_self.applySticky();});},hookToScrollDeferred:function()
{var _self=this;var windowScrollTimeout;$(window).on("scroll",function(){clearTimeout(windowScrollTimeout);windowScrollTimeout=setTimeout(function(){_self.applySticky();},50);});},hookToResize:function()
{var _self=this;$(window).on('themeResize',function(e){_self.requiredRecalculation=true;_self.headerContainer.css('height','');});},hookToActivatedDeactivated:function()
{var _self=this;$(document).on('activate-sticky-header',function(e){if(_self.cartBlock.parent().hasClass('nav-holder')===false)
{_self.navHolderBlock1.prepend(_self.cartBlock);_self.cartBlock.data('moved',true);}
if(_self.compareBlock.parent().hasClass('nav-holder')===false)
{_self.navHolderBlock2.prepend(_self.compareBlock);_self.compareBlock.data('moved',true);}});$(document).on('deactivate-sticky-header',function(e){if(_self.cartBlock.data('moved')&&_self.cartBlock.parent().hasClass('nav-holder')===true)
{_self.cartMarkerRegular.after(_self.cartBlock);}
if(_self.compareBlock.data('moved')&&_self.compareBlock.parent().hasClass('nav-holder')===true)
{_self.compareMarkerRegular.after(_self.compareBlock);}});}});})(jQuery,window,document);
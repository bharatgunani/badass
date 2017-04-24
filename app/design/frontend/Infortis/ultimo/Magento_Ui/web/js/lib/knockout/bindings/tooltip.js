define(['jquery','ko','underscore','mage/template','text!ui/template/tooltip/tooltip.html','../template/renderer'],function($,ko,_,template,tooltipTmpl,renderer){'use strict';var tooltip,defaults,positions,transformProp,checkedPositions={},iterator=0,previousTooltip,tooltipData,positionData={},tooltipsCollection={},isTouchDevice=(function(){return'ontouchstart'in document.documentElement;})(),CLICK_EVENT=(function(){return isTouchDevice?'touchstart':'click';})();defaults={tooltipWrapper:'[data-tooltip=tooltip-wrapper]',tooltipContentBlock:'data-tooltip-content',closeButtonClass:'action-close',tailClass:'data-tooltip-tail',action:'hover',delay:300,track:false,step:20,position:'top',closeButton:false,showed:false,strict:true,center:false};tooltipData={trigger:false,timeout:0,element:false,event:false,targetElement:{},showed:false,currentID:0};transformProp=(function(){var style=document.createElement('div').style,base='Transform',vendors=['webkit','moz','ms','o'],vi=vendors.length,property;if(typeof style.transform!=='undefined'){return'transform';}
while(vi--){property=vendors[vi]+base;if(typeof style[property]!=='undefined'){return property;}}})();positions={map:{horizontal:{s:'w',p:'left'},vertical:{s:'h',p:'top'}},top:function(s){return positions._topLeftChecker(s,positions.map,'vertical','_bottom','top','right');},left:function(s){return positions._topLeftChecker(s,positions.map,'horizontal','_right','left','top');},bottom:function(s){return positions._bottomRightChecker(s,positions.map,'vertical','_top','bottom','left');},right:function(s){return positions._bottomRightChecker(s,positions.map,'horizontal','_left','right','bottom');},_topLeftChecker:function(s,map,direction,className,side,delegate){var result={position:{}},config=tooltip.getTooltip(tooltipData.currentID),startPosition=!config.strict?s.eventPosition:s.elementPosition,changedDirection;checkedPositions[side]=true;if(startPosition[map[direction].p]-s.tooltipSize[map[direction].s]-config.step>s.scrollPosition[map[direction].p]){result.position[map[direction].p]=startPosition[map[direction].p]-s.tooltipSize[map[direction].s]-config.step;result.className=className;result.side=side;changedDirection=direction==='vertical'?'horizontal':'vertical';result=positions._normalize(s,result,config,delegate,map,changedDirection);}else if(!checkedPositions[delegate]){result=positions[delegate].apply(null,arguments);}else{result=positions.positionCenter(s,result);}
return result;},_bottomRightChecker:function(s,map,direction,className,side,delegate){var result={position:{}},config=tooltip.getTooltip(tooltipData.currentID),startPosition=!config.strict?s.eventPosition:{top:s.elementPosition.top+s.elementSize.h,left:s.elementPosition.left+s.elementSize.w},changedDirection;checkedPositions[side]=true;if(startPosition[map[direction].p]+s.tooltipSize[map[direction].s]+config.step<s.scrollPosition[map[direction].p]+s.windowSize[map[direction].s]){result.position[map[direction].p]=startPosition[map[direction].p]+config.step;result.className=className;result.side=side;changedDirection=direction==='vertical'?'horizontal':'vertical';result=positions._normalize(s,result,config,delegate,map,changedDirection);}else if(!checkedPositions[delegate]){result=positions[delegate].apply(null,arguments);}else{result=positions.positionCenter(s,result);}
return result;},positionCenter:function(s,data){data=positions._positionCenter(s,data,'horizontal',positions.map);data=positions._positionCenter(s,data,'vertical',positions.map);return data;},_positionCenter:function(s,data,direction,map){if(s.tooltipSize[map[direction].s]<s.windowSize[map[direction].s]){data.position[map[direction].p]=(s.windowSize[map[direction].s]-s.tooltipSize[map[direction].s])/2+s.scrollPosition[map[direction].p];}else{data.position[map[direction].p]=s.scrollPosition[map[direction].p];data.tooltipSize={};data.tooltipSize[map[direction].s]=s.windowSize[map[direction].s];}
return data;},_normalize:function(s,data,config,delegate,map,direction){var startPosition=!config.center?s.eventPosition:{left:s.elementPosition.left+s.elementSize.w/2,top:s.elementPosition.top+s.elementSize.h/2},depResult;if(startPosition[map[direction].p]-s.tooltipSize[map[direction].s]/2>s.scrollPosition[map[direction].p]&&startPosition[map[direction].p]+s.tooltipSize[map[direction].s]/2<s.scrollPosition[map[direction].p]+s.windowSize[map[direction].s]){data.position[map[direction].p]=startPosition[map[direction].p]-s.tooltipSize[map[direction].s]/2;}else{if(!checkedPositions[delegate]){depResult=positions[delegate].apply(null,arguments);if(depResult.hasOwnProperty('className')){data=depResult;}else{data=positions._normalizeTail(s,data,config,delegate,map,direction,startPosition);}}else{data=positions._normalizeTail(s,data,config,delegate,map,direction,startPosition);}}
return data;},_normalizeTail:function(s,data,config,delegate,map,direction,startPosition){data.tail={};if(s.tooltipSize[map[direction].s]<s.windowSize[map[direction].s]){if(startPosition[map[direction].p]>s.windowSize[map[direction].s]/2+s.scrollPosition[map[direction].p]){data.position[map[direction].p]=s.windowSize[map[direction].s]+s.scrollPosition[map[direction].p]-s.tooltipSize[map[direction].s];data.tail[map[direction].p]=startPosition[map[direction].p]-s.tooltipSize[map[direction].s]/2-data.position[map[direction].p];}else{data.position[map[direction].p]=s.scrollPosition[map[direction].p];data.tail[map[direction].p]=startPosition[map[direction].p]-s.tooltipSize[map[direction].s]/2-data.position[map[direction].p];}}else{data.position[map[direction].p]=s.scrollPosition[map[direction].p];data.tail[map[direction].p]=s.eventPosition[map[direction].p]-s.windowSize[map[direction].s]/2;data.tooltipSize={};data.tooltipSize[map[direction].s]=s.windowSize[map[direction].s];}
return data;}};tooltip={setTooltip:function(config){var property='id-'+iterator;tooltipsCollection[property]=config;iterator++;return property;},getTooltip:function(id){return tooltipsCollection[id];},setContent:function(tooltipElement,viewModel,id,bindingCtx,event){var html=$(tooltipElement).html(),config=tooltip.getTooltip(id),body=$('body');tooltipData.currentID=id;tooltipData.trigger=$(event.currentTarget);tooltip.setTargetData(event);body.on('mousemove.setTargetData',tooltip.setTargetData);tooltip.clearTimeout(id);tooltipData.timeout=_.delay(function(){body.off('mousemove.setTargetData',tooltip.setTargetData);if(tooltipData.trigger[0]===tooltipData.targetElement){tooltip.destroy(id);event.stopPropagation();tooltipElement=tooltip.createTooltip(id);tooltipElement.find('.'+defaults.tooltipContentBlock).append(html);tooltipElement.applyBindings(bindingCtx);tooltip.setHandlers(id);tooltip.setPosition(tooltipElement,id);previousTooltip=id;}},config.delay);},setPosition:function(tooltipElement,id){var config=tooltip.getTooltip(id);tooltip.sizeData={windowSize:{h:$(window).outerHeight(),w:$(window).outerWidth()},scrollPosition:{top:$(window).scrollTop(),left:$(window).scrollLeft()},tooltipSize:{h:tooltipElement.outerHeight(),w:tooltipElement.outerWidth()},elementSize:{h:tooltipData.trigger.outerHeight(),w:tooltipData.trigger.outerWidth()},elementPosition:tooltipData.trigger.offset(),eventPosition:this.getEventPosition(tooltipData.event)};_.extend(positionData,positions[config.position](tooltip.sizeData));tooltipElement.css(positionData.position);tooltipElement.addClass(positionData.className);tooltip._setTooltipSize(positionData,tooltipElement);tooltip._setTailPosition(positionData,tooltipElement);checkedPositions={};},_setTooltipSize:function(data,tooltipElement){if(data.tooltipSize){data.tooltipSize.w?tooltipElement.css('width',data.tooltipSize.w):tooltipElement.css('height',data.tooltipSize.h);}},_setTailPosition:function(data,tooltipElement){var tail,tailMargin;if(data.tail){tail=tooltipElement.find('.'+defaults.tailClass);if(data.tail.left){tailMargin=parseInt(tail.css('margin-left'),10);tail.css('margin-left',tailMargin+data.tail.left);}else{tailMargin=parseInt(tail.css('margin-top'),10);tail.css('margin-top',tailMargin+data.tail.top);}}},getEventPosition:function(event){var position={left:event.originalEvent&&event.originalEvent.pageX||0,top:event.originalEvent&&event.originalEvent.pageY||0};if(position.left===0&&position.top===0){_.extend(position,event.target.getBoundingClientRect());}
return position;},outerClick:function(id,event){var tooltipElement=$(event.target).parents(defaults.tooltipWrapper)[0],isTrigger=event.target===tooltipData.trigger[0]||$.contains(tooltipData.trigger[0],event.target);if(tooltipData.showed&&tooltipElement!==tooltipData.element[0]&&!isTrigger){tooltip.destroy(id);}},keydownHandler:function(event){if(tooltipData.showed&&event.keyCode===27){tooltip.destroy(tooltipData.currentID);}},track:function(event){var inequality={},map=positions.map,translate={left:'translateX',top:'translateY'},eventPosition={left:event.pageX,top:event.pageY},tooltipSize={w:tooltipData.element.outerWidth(),h:tooltipData.element.outerHeight()},direction=positionData.side==='bottom'||positionData.side==='top'?'horizontal':'vertical';inequality[map[direction].p]=eventPosition[map[direction].p]-(positionData.position[map[direction].p]+tooltipSize[map[direction].s]/2);if(positionData.position[map[direction].p]+inequality[map[direction].p]+tooltip.sizeData.tooltipSize[map[direction].s]>tooltip.sizeData.windowSize[map[direction].s]+tooltip.sizeData.scrollPosition[map[direction].p]||inequality[map[direction].p]+positionData.position[map[direction].p]<tooltip.sizeData.scrollPosition[map[direction].p]){return false;}
tooltipData.element[0].style[transformProp]=translate[map[direction].p]+'('+inequality[map[direction].p]+'px)';},setHandlers:function(id){var config=tooltip.getTooltip(id);if(config.track){tooltipData.trigger.on('mousemove.track',tooltip.track);}
if(config.action==='click'){$(window).on(CLICK_EVENT+'.outerClick',tooltip.outerClick.bind(null,id));}
if(config.closeButton){$('.'+config.closeButtonClass).on('click.closeButton',tooltip.destroy.bind(null,id));}
document.addEventListener('scroll',tooltip.destroy,true);$(window).on('keydown.tooltip',tooltip.keydownHandler);$(window).on('scroll.tooltip',tooltip.outerClick.bind(null,id));$(window).on('resize.outerClick',tooltip.outerClick.bind(null,id));},toggleTooltip:function(tooltipElement,viewModel,id){if(previousTooltip===id&&tooltipData.showed){tooltip.destroy(id);return false;}
tooltip.setContent.apply(null,arguments);},createTooltip:function(id){var body=$('body'),config=tooltip.getTooltip(id);$(template(tooltipTmpl,{data:config})).appendTo(body);tooltipData.showed=true;tooltipData.element=$(config.tooltipWrapper);return tooltipData.element;},clearTimeout:function(id){var config=tooltip.getTooltip(id);if(config.action==='hover'){clearTimeout(tooltipData.timeout);}},checkPreviousTooltip:function(){if(!tooltipData.timeout){tooltip.destroy();}},destroy:function(){if(tooltipData.element){tooltipData.element.remove();tooltipData.showed=false;}
positionData={};tooltipData.timeout=false;tooltip.removeHandlers();},removeHandlers:function(){$('.'+defaults.closeButtonClass).off('click.closeButton');tooltipData.trigger.off('mousemove.track');document.removeEventListener('scroll',tooltip.destroy,true);$(window).off(CLICK_EVENT+'.outerClick');$(window).off('keydown.tooltip');$(window).off('resize.outerClick');},setTargetData:function(event){tooltipData.event=event;if(event.timeStamp-(tooltipData.timestamp||0)<1){return;}
if(event.type==='mousemove'){tooltipData.targetElement=event.target;}else{tooltipData.targetElement=event.currentTarget;tooltipData.timestamp=event.timeStamp;}},processingConfig:function(config){return _.extend({},defaults,config);}};ko.bindingHandlers.tooltip={init:function(elem,valueAccessor,allBindings,viewModel,bindingCtx){var config=tooltip.processingConfig(valueAccessor()),$parentScope=config.parentScope?$(config.parentScope):$(elem).parent(),tooltipId;$(elem).addClass('hidden');if(isTouchDevice){config.action='click';}
tooltipId=tooltip.setTooltip(config);if(config.action==='hover'){$parentScope.on('mouseenter',config.trigger,tooltip.setContent.bind(null,elem,viewModel,tooltipId,bindingCtx));$parentScope.on('mouseleave',config.trigger,tooltip.checkPreviousTooltip.bind(null,tooltipId));}else if(config.action==='click'){$parentScope.on('click',config.trigger,tooltip.toggleTooltip.bind(null,elem,viewModel,tooltipId,bindingCtx));}
return{controlsDescendantBindings:true};}};renderer.addAttribute('tooltip');});
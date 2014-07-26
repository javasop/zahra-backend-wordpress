/*
Easy Admin Color Schemes <http://JamesDimick.com/easy-admin-color-schemes>
Created by James Dimick <http://JamesDimick.com>

  Copyright (C) 2011 James Dimick <mail@jamesdimick.com> - JamesDimick.com

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

var eacs;

jQuery(function($){
  eacs = new function(){
    this.css = new function(){
      this.input = $('#content');
      this.label = this.input.prev();
    };

    this.preview = new function(){
      this.frame = $('<iframe id="schemepreview" src="' + eacsL10n.adminBaseUrl + '"/>').insertAfter('#previewjsnote').prev().remove().prevObject;
      this.colorsCss = this.previewCss = this.timer = undefined;
      this.update = function(){};
    };

    this.colors = new function(){
      this.inputs = new function(){
        this.all = $('#eacs-primarycolors input');
        this.one = this.all.filter('#priclr1');
        this.two = this.all.filter('#priclr2');
        this.three = this.all.filter('#priclr3');
        this.four = this.all.filter('#priclr4');
      };

      this.picker = $('<div id="colorPickerDiv" style="display:none"/>').insertAfter('#eacs-primarycolors p:first-of-type');
      this.shortcodeRegex = /\[primarycolor([1-4])\]/gi;
      this.hexColorRegex = /^#[a-f0-9]{6}$/i;
      this.blank = '#ffffff';
      this.farbtastic = $.farbtastic(this.picker);
      this.origFarbUpdDispFunc = this.farbtastic.updateDisplay;
      this.timer = undefined;
    }
  };

  eacs.preview.update = function(){
    try {
      var l = $(this).contents().get(0).location,
          $head = $('head', $(this).contents());

      eacs.preview.colorsCss = $('#colors-css', $head);
      eacs.preview.previewCss = $('#eacs-previewstyles', $head);

      if('href' in l && l.href.search(new RegExp('^' + eacsL10n.adminBaseUrl, 'i')) != -1) {
        var csstext = $.trim(eacs.css.input.val());

        if(csstext.length > 0) {
          if(csstext.search(eacs.colors.shortcodeRegex) != -1) {
            var clr = function(val){val=$.trim(val);return val.search(eacs.colors.hexColorRegex)!=-1?val:eacs.colors.blank},
                clrs = [
                         clr(eacs.colors.inputs.one.val()),
                         clr(eacs.colors.inputs.two.val()),
                         clr(eacs.colors.inputs.three.val()),
                         clr(eacs.colors.inputs.four.val())
                       ];

            csstext = csstext.replace(eacs.colors.shortcodeRegex, function(s,p){return clrs[p-1]});
          }

          if(eacs.preview.colorsCss.length > 0) eacs.preview.colorsCss.prop('disabled', true);

          if(eacs.preview.previewCss.length > 0) {
            eacs.preview.previewCss.prop('disabled', false);
            if(eacs.preview.previewCss.html() != csstext) eacs.preview.previewCss.html(csstext);
          } else {
            $('head', $(this).contents()).append('<style type="text/css" id="eacs-previewstyles">' + csstext + '</style>');
          }
        } else {
          if(eacs.preview.colorsCss.length > 0) eacs.preview.colorsCss.prop('disabled', false);
          if(eacs.preview.previewCss.length > 0) eacs.preview.previewCss.prop('disabled', true);
        }
      }
    }catch(e){}
  };

  eacs.css.input
    .bind('input keydown', function(){clearTimeout(eacs.preview.timer);eacs.preview.timer=setTimeout(function(){eacs.preview.update.call(eacs.preview.frame)},300)})
    .focus(function(){eacs.css.label.css('visibility','hidden')})
    .blur(function(){if(this.value.length<=0)eacs.css.label.css('visibility','')})
		.droppable({ accept: '#eacs-primarycolors #shortcodes code', drop: function(e,u){$(this).insertAtCaret(u.draggable.text()).trigger('keydown')} });

  eacs.css.label
    .css('visibility', eacs.css.input.val().length<=0?'':'hidden')
    .click(function(){$(this).css('visibility','hidden');eacs.css.input.focus()});

  eacs.preview.frame.load(eacs.preview.update);

  eacs.colors.farbtastic.updateDisplay = function(){
    var thiz = eacs.colors.farbtastic.callback.get(0);

    eacs.colors.origFarbUpdDispFunc.call(thiz);

    if(thiz.value.search(eacs.colors.hexColorRegex) != -1 && eacs.css.input.val().search(eacs.colors.shortcodeRegex) != -1)
      eacs.preview.update.call(eacs.preview.frame);
  };

  eacs.colors.inputs.all
    .each(function(){eacs.colors.farbtastic.linkTo(this)})
    .focus(function(){eacs.colors.farbtastic.linkTo(this);clearTimeout(eacs.colors.timer);eacs.colors.picker.filter(':hidden').animate({opacity:'show',height:'show'},'slow')})
    .blur(function(){eacs.colors.timer=setTimeout(function(){eacs.colors.picker.filter(':visible').animate({opacity:'hide',height:'hide'},'slow')},50)});

  $('#eacs-primarycolors #shortcodes code')
    .css('cursor', 'pointer')
    .attr('title', eacsL10n.insertShortcodeTitle)
		.draggable({ helper: 'clone' })
    .click(function(){eacs.css.input.insertAtCaret($(this).text());eacs.preview.update.call(eacs.preview.frame)});
});

(function($){$.fn.insertAtCaret=function(d){return this.each(function(){if(document.selection){this.focus();sel=document.selection.createRange();sel.text=d;this.focus()}else if(this.selectionStart||this.selectionStart=='0'){var a=this.selectionStart;var b=this.selectionEnd;var c=this.scrollTop;this.value=this.value.substring(0,a)+d+this.value.substring(b,this.value.length);this.focus();this.selectionStart=a+d.length;this.selectionEnd=a+d.length;this.scrollTop=c}else{this.value+=d;this.focus()}})}})(jQuery);
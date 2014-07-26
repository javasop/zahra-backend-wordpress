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
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.	If not, see <http://www.gnu.org/licenses/>.
*/

jQuery(function($){

	var showTB = function(e){
		e.preventDefault();

		var url = $(this).attr('href'),
		    isView = $(this).parent().attr('class') == 'view';

		tb_show(
			( isView ? eacsL10n.previewQuote : eacsL10n.exportQuote ) + $(this).closest('tr').find('a.row-title').text() + eacsL10n.endQuote,
			url + ( /\?/i.test(url) ? '&' : '?' ) + 'TB_iframe=true&height=' + ( isView ? '700' : '270' ) + '&width=' + ( isView ? '1200' : '650' ),
			false
		);

		$('#TB_iframeContent').on('load', { passedData: e }, ( isView ? updatePreview : updateExport ));
	},

	updatePreview = function(e){
		$('head #colors-css', $('#TB_iframeContent').contents())
			.attr('href', eacsL10n.adminBaseUrl + 'post.php?action=eacs-css&post=' + $(e.data.passedData.currentTarget).closest('tr').attr('id').replace('post-', ''));
	},

	updateExport = function(e){
		var $html = $('html', $(this).contents()).removeClass('wp-toolbar');

		$('body', $html).html($('div.wrap', $html).html()).css({ padding: '1.5em', height: 'auto', minWidth: 0, margin: 0 });

		$('#icon-edit, #eacs-author-support-link', $html).remove();

		$('body > h2:first-of-type', $html)
			.css({ fontFamily: 'Georgia,"Times New Roman",Times,serif', fontWeight: 'normal', fontSize: '1.6em', margin: '0 0 1em' })
			.text(eacsL10n.selectExportMethod);

		$('body > p:first-of-type', $html).css({ margin: '0 0 1.5em' });

		$('body .submit', $html).css({ padding: 0, margin: '1.5em 0 0' });
		$('<input/>', {
			type: 'submit',
			name: 'cancel',
			id: 'cancel',
			'class': 'button',
			css: { marginLeft: '0.7em' },
			val: eacsL10n.cancel,
			click: tb_remove
		}).appendTo($('body .submit', $html));

		$(this).height($('body', $html).outerHeight());
	};

	$('<span class="view"><a href="' + eacsL10n.adminBaseUrl + 'index.php" title="' + eacsL10n.previewSchemeTitle + '">' + eacsL10n.previewSchemeText + '</a> | </span>')
		.prependTo('tbody#the-list td.name div.row-actions.can-preview');

	$('tbody#the-list td.name div.row-actions span.view a, tbody#the-list td.name div.row-actions span.export a')
		.on('click', showTB);

});
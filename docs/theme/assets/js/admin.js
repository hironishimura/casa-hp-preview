/* 管理画面：ギャラリー選択（WordPress標準のメディアアップローダーを使用） */
jQuery( function ( $ ) {
	'use strict';

	$( document ).on( 'click', '.dcs-gallery__pick', function ( e ) {
		e.preventDefault();

		var wrap = $( this ).closest( '.dcs-gallery' );
		var input = wrap.find( 'input[type="hidden"]' );
		var preview = wrap.find( '.dcs-gallery__preview' );

		var frame = wp.media( {
			title: '写真を選ぶ（複数選択できます。並び順は選んだ順になります）',
			button: { text: 'この写真を使う' },
			library: { type: 'image' },
			multiple: 'add'
		} );

		frame.on( 'open', function () {
			var selection = frame.state().get( 'selection' );
			String( input.val() || '' ).split( ',' ).forEach( function ( id ) {
				id = parseInt( id, 10 );
				if ( id ) {
					var att = wp.media.attachment( id );
					att.fetch();
					selection.add( att );
				}
			} );
		} );

		frame.on( 'select', function () {
			var ids = [];
			preview.empty();
			frame.state().get( 'selection' ).each( function ( att ) {
				var a = att.toJSON();
				ids.push( a.id );
				var url = ( a.sizes && a.sizes.thumbnail ) ? a.sizes.thumbnail.url : a.url;
				preview.append( '<span class="dcs-gallery__item"><img src="' + url + '" alt=""></span>' );
			} );
			input.val( ids.join( ',' ) );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.dcs-gallery__clear', function ( e ) {
		e.preventDefault();
		var wrap = $( this ).closest( '.dcs-gallery' );
		wrap.find( 'input[type="hidden"]' ).val( '' );
		wrap.find( '.dcs-gallery__preview' ).empty();
	} );
} );

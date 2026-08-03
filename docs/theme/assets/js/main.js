/* design casa 宇都宮 × S HOME — フロントの挙動 */
( function () {
	'use strict';

	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ---------- ヘッダーの地色切り替え ---------- */
	var head = document.querySelector( '.site-head' );
	var hero = document.querySelector( '.hero' );

	function updateHead() {
		if ( ! head ) { return; }
		if ( ! hero ) { head.classList.add( 'is-solid' ); return; }
		head.classList.toggle( 'is-solid', window.scrollY > hero.offsetHeight - 90 );
	}
	updateHead();
	window.addEventListener( 'scroll', updateHead, { passive: true } );
	window.addEventListener( 'resize', updateHead );

	/* ---------- モバイルナビ ---------- */
	var toggle = document.querySelector( '.navtoggle' );
	var nav = document.getElementById( 'gnav' );

	function closeNav() {
		if ( ! toggle || ! nav ) { return; }
		toggle.setAttribute( 'aria-expanded', 'false' );
		nav.classList.remove( 'is-open' );
		document.body.style.overflow = '';
	}

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function () {
			var open = toggle.getAttribute( 'aria-expanded' ) === 'true';
			toggle.setAttribute( 'aria-expanded', String( ! open ) );
			nav.classList.toggle( 'is-open', ! open );
			document.body.style.overflow = open ? '' : 'hidden';
		} );

		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) { closeNav(); }
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) { closeNav(); }
		} );

		window.addEventListener( 'resize', function () {
			if ( window.innerWidth > 860 ) { closeNav(); }
		} );
	}

	/* ---------- スクロールで現れる ---------- */
	var targets = document.querySelectorAll(
		'.sec-head, .about__text, .about__fig, .trio__item, .reason__item,' +
		'.card, .names li, .flow__step, .faq__item, .company__lead,' +
		'.company__dl, .cta__body, .figures__item, .featgrid__item,' +
		'.archcard, .speccard, .gallery__item, .way, .intro__text, .intro__fig'
	);

	if ( ! reduce && 'IntersectionObserver' in window ) {
		var io = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) { return; }
				entry.target.classList.add( 'is-in' );
				io.unobserve( entry.target );
			} );
		}, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 } );

		Array.prototype.forEach.call( targets, function ( el, i ) {
			el.classList.add( 'reveal' );
			el.style.transitionDelay = ( i % 3 ) * 90 + 'ms';
			io.observe( el );
		} );
	}

	/* ---------- FAQ：ひとつ開いたら他を閉じる ---------- */
	var faqItems = document.querySelectorAll( '.faq__item' );
	Array.prototype.forEach.call( faqItems, function ( item ) {
		item.addEventListener( 'toggle', function () {
			if ( ! item.open ) { return; }
			Array.prototype.forEach.call( faqItems, function ( other ) {
				if ( other !== item ) { other.open = false; }
			} );
		} );
	} );

	/* ---------- 固定ヘッダー分のアンカー補正 ---------- */
	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest( 'a[href^="#"]' );
		if ( ! link ) { return; }
		var id = link.getAttribute( 'href' );
		if ( id === '#' || id === '#main' ) { return; }
		var target = document.querySelector( id );
		if ( ! target ) { return; }
		e.preventDefault();
		var top = target.getBoundingClientRect().top + window.scrollY - 70;
		window.scrollTo( { top: top, behavior: reduce ? 'auto' : 'smooth' } );
		history.replaceState( null, '', id );
	} );

	/* ---------- 送信結果メッセージまでスクロール ---------- */
	var msg = document.querySelector( '.formmsg' );
	if ( msg ) {
		window.setTimeout( function () {
			var top = msg.getBoundingClientRect().top + window.scrollY - 110;
			window.scrollTo( { top: top, behavior: reduce ? 'auto' : 'smooth' } );
		}, 120 );
	}
} )();

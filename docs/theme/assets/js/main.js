/* design casa 宇都宮 × S HOME — フロントの挙動 */
( function () {
	'use strict';

	var reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ---------- ヘッダーの地色切り替え ---------- */
	var head = document.querySelector( '.site-head' );
	/* .hero は旧テンプレート、.hero-block はブロック（カバー）のヒーロー */
	var hero = document.querySelector( '.hero, .hero-block' );

	function updateHead() {
		if ( ! head ) { return; }
		if ( ! hero ) { head.classList.add( 'is-solid' ); return; }
		head.classList.toggle( 'is-solid', window.scrollY > hero.offsetHeight - 90 );
	}
	updateHead();
	window.addEventListener( 'scroll', updateHead, { passive: true } );
	window.addEventListener( 'resize', updateHead );

	/* ---------- ハンバーガーメニュー ----------
	 * iPad / iPhone の Safari 対策
	 *  - 背面スクロールの固定は body に position:fixed を使う
	 *    （overflow:hidden だけでは iOS でスクロールが止まらない）
	 *  - :has() 非対応の端末でも動くよう、html に .is-navopen を付ける
	 */
	var toggle = document.querySelector( '.navtoggle' );
	var nav = document.getElementById( 'gnav' );
	var root = document.documentElement;
	var scrollY = 0;

	function lockScroll() {
		scrollY = window.scrollY || window.pageYOffset || 0;
		document.body.style.position = 'fixed';
		document.body.style.top = '-' + scrollY + 'px';
		document.body.style.left = '0';
		document.body.style.right = '0';
		document.body.style.width = '100%';
	}

	function unlockScroll() {
		document.body.style.position = '';
		document.body.style.top = '';
		document.body.style.left = '';
		document.body.style.right = '';
		document.body.style.width = '';
		window.scrollTo( 0, scrollY );
	}

	function setNav( open ) {
		if ( ! toggle || ! nav ) { return; }
		if ( open === ( toggle.getAttribute( 'aria-expanded' ) === 'true' ) ) { return; }

		toggle.setAttribute( 'aria-expanded', String( open ) );
		toggle.setAttribute( 'aria-label', open ? 'メニューを閉じる' : 'メニューを開く' );
		nav.classList.toggle( 'is-open', open );
		root.classList.toggle( 'is-navopen', open );

		if ( open ) { lockScroll(); } else { unlockScroll(); }
	}

	function closeNav() { setNav( false ); }

	if ( toggle && nav ) {
		toggle.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			setNav( toggle.getAttribute( 'aria-expanded' ) !== 'true' );
		} );

		nav.addEventListener( 'click', function ( e ) {
			if ( e.target.closest( 'a' ) ) { closeNav(); }
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) { closeNav(); }
		} );

		/* 画面幅が広がった／回転したときはメニューを閉じる */
		var mq = window.matchMedia( '(max-width:1080px)' );
		var onChange = function () { if ( ! mq.matches ) { closeNav(); } };
		if ( mq.addEventListener ) {
			mq.addEventListener( 'change', onChange );
		} else if ( mq.addListener ) {
			mq.addListener( onChange );
		}
		window.addEventListener( 'orientationchange', closeNav );
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

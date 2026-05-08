/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*/


$(document).ready(function () {

    /* Background video autoplay fallback (handles strict browser autoplay policies) */
    var $bgVideo = $('video.header-bg-video');
    if ($bgVideo.length) {
        $bgVideo.each(function () {
            var vid = this;
            vid.muted = true;
            var p = vid.play();
            if (p !== undefined) {
                p.catch(function () {});
            }
        });
    }

    var $taglineEl = $('.js-header-tagline');
    var _taglineTimer = null;

    function updateTagLineWithAnim(tag) {
        if (!$taglineEl.length) { return; }
        if (_taglineTimer) {
            clearTimeout(_taglineTimer);
            _taglineTimer = null;
        }
        $taglineEl.addClass('wk-tagline-out');
        _taglineTimer = setTimeout(function () {
            _taglineTimer = null;
            $taglineEl.text(tag).removeClass('wk-tagline-out wk-tagline-in');
            if (tag) {
                $taglineEl.show();
                $taglineEl[0].offsetHeight;
                $taglineEl.addClass('wk-tagline-in');
                setTimeout(function () { $taglineEl.removeClass('wk-tagline-in'); }, 400);
            } else {
                $taglineEl.hide();
            }
        }, 400);
    }

    var $imgCarousel = $('.header-img-carousel');
    if ($imgCarousel.length) {
        var autoPlay  = parseInt($imgCarousel.data('auto-play'), 10) === 1;
        var interval  = parseInt($imgCarousel.data('interval'), 10) || 5000;
        var navType   = parseInt($imgCarousel.data('nav-type'), 10) || 1; // 1=dots 2=arrows 3=both
        var animType  = parseInt($imgCarousel.data('anim-type'), 10) || 1; // 1=slide 2=fade 3=zoom 4=blur
        var animNames = {1: 'Slide', 2: 'Fade', 3: 'Zoom', 4: 'Blur'};
        var animLabel = animNames[animType] || 'Slide';
        var animOut   = 'wk' + animLabel + 'Out';
        var animIn    = 'wk' + animLabel + 'In';

        var showDots = (navType === 1 || navType === 3);
        $imgCarousel.owlCarousel({
            loop:               true,
            items:              1,
            dots:               showDots,
            dotsContainer:      showDots ? '#wk-header-owl-dots' : false,
            nav:                false,
            autoplay:           autoPlay,
            autoplayTimeout:    interval,
            autoplaySpeed:      800,
            autoplayHoverPause: true,
            animateOut:         animOut,
            animateIn:          animIn,
            responsiveClass:    true,
            rtl:                (typeof language_is_rtl !== 'undefined' ? language_is_rtl : false)
        });

        $('.js-header-media-prev').on('click', function () {
            $imgCarousel.trigger('prev.owl.carousel');
        });
        $('.js-header-media-next').on('click', function () {
            $imgCarousel.trigger('next.owl.carousel');
        });

        $imgCarousel.on('changed.owl.carousel', function () {
            var $slide = $imgCarousel.find('.owl-item.active:not(.cloned) .header-slide-img');
            if (!$slide.length) { return; } // clone is temporarily active (loop snap) — skip
            var tag = $slide.data('tagline') || '';
            updateTagLineWithAnim(tag);
        });

        if (autoPlay) {
            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    $imgCarousel.trigger('stop.owl.autoplay').trigger('play.owl.autoplay');
                }
            });
        }
    }

});

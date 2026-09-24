/* ------------------------------------------------------------------
 * Pemilih jam (HH:MM, 24 jam) dengan tampilan sama seperti popup datepicker.
 *
 *   <div class="form-control-wrap">
 *       <div class="form-icon form-icon-left"><i class="cil-clock"></i></div>
 *       <input type="text" class="form-control time-picker" name="start_time">
 *   </div>
 *
 * Klik field -> pilih jam (00-23) -> pilih menit (kelipatan 5) -> field terisi "HH:MM".
 * Field tetap bisa diketik; saat keluar field ketikan dirapikan ("930" -> "09:30",
 * "9" -> "09:00"). Field readonly / disabled tidak membuka popup.
 * Mengirim event 'input' + 'change' setelah jam dipilih. Dimuat dari layouts/app.
 * ------------------------------------------------------------------ */
(function ($) {
    'use strict';

    var MINUTE_STEP = 5;
    var $popup = null;      // satu popup untuk semua field
    var current = null;     // field yang sedang dibuka
    var view = 'hour';      // 'hour' | 'minute'
    var pickedHour = null;

    function pad(n) {
        return (n < 10 ? '0' : '') + n;
    }

    /** "HH:MM" -> {h, m}; null kalau tidak valid. */
    function parse(value) {
        var m = /^(\d{1,2}):(\d{1,2})(:\d{2})?$/.exec($.trim(value || ''));
        if (!m || +m[1] > 23 || +m[2] > 59) {
            return null;
        }
        return { h: +m[1], m: +m[2] };
    }

    /** Ketikan bebas -> "HH:MM" ("930", "9.30", "9:5", "9") atau null. */
    function normalize(value) {
        var v = $.trim(value || '');
        if (v === '') {
            return '';
        }
        var p = parse(v.replace('.', ':'));
        if (!p) {
            var digits = v.replace(/\D/g, '');
            if (digits.length >= 1 && digits.length <= 4) {
                var h = digits.length <= 2 ? +digits : +digits.slice(0, digits.length - 2);
                var m = digits.length <= 2 ? 0 : +digits.slice(-2);
                p = (h <= 23 && m <= 59) ? { h: h, m: m } : null;
            }
        }
        return p ? pad(p.h) + ':' + pad(p.m) : null;
    }

    function usable(el) {
        return el && !el.readOnly && !el.disabled;
    }

    function build() {
        $popup = $(
            '<div class="timepicker-dropdown" role="dialog" aria-modal="false">' +
                '<div class="tp-head">' +
                    '<button type="button" class="tp-back" tabindex="-1" aria-label="Back">&laquo;</button>' +
                    '<div class="tp-title"><span class="tp-h">--</span><span class="tp-sep">:</span><span class="tp-m">--</span></div>' +
                    '<span class="tp-back-spacer"></span>' +
                '</div>' +
                '<div class="tp-grid"></div>' +
            '</div>'
        ).appendTo(document.body).hide();

        // mousedown supaya field tidak kehilangan fokus sebelum klik diproses
        $popup.on('mousedown', function (e) {
            e.preventDefault();
        });
        $popup.on('click', '.tp-cell', function () {
            var val = +$(this).data('value');
            if (view === 'hour') {
                pickedHour = val;
                render('minute');
            } else {
                commit(pad(pickedHour) + ':' + pad(val));
            }
        });
        $popup.on('click', '.tp-back', function () {
            render('hour');
        });
        $popup.on('click', '.tp-title .tp-h', function () {
            render('hour');
        });
    }

    function render(which) {
        view = which;
        var value = parse(current.value);
        var now = new Date();
        var html = '';
        var i;

        $popup.toggleClass('tp-view-minute', which === 'minute');
        $popup.find('.tp-h').text(which === 'minute' ? pad(pickedHour) : (value ? pad(value.h) : '--'));
        $popup.find('.tp-m').text(value && which === 'hour' ? pad(value.m) : '--');

        if (which === 'hour') {
            for (i = 0; i < 24; i++) {
                html += '<button type="button" tabindex="-1" class="tp-cell' +
                    (value && value.h === i ? ' active' : '') +
                    (now.getHours() === i ? ' now' : '') +
                    '" data-value="' + i + '">' + pad(i) + '</button>';
            }
        } else {
            for (i = 0; i < 60; i += MINUTE_STEP) {
                var selected = value && value.h === pickedHour && value.m === i;
                html += '<button type="button" tabindex="-1" class="tp-cell' +
                    (selected ? ' active' : '') + '" data-value="' + i + '">' + pad(i) + '</button>';
            }
        }
        $popup.find('.tp-grid').attr('class', 'tp-grid tp-grid-' + which).html(html);
    }

    function place() {
        if (!current || !$popup) {
            return;
        }
        var $el = $(current);
        var off = $el.offset();
        var top = off.top + $el.outerHeight() + 6;
        // tidak muat di bawah -> tampil di atas field
        if (top + $popup.outerHeight() > $(window).scrollTop() + $(window).height() &&
            off.top - $popup.outerHeight() - 6 > $(window).scrollTop()) {
            top = off.top - $popup.outerHeight() - 6;
            $popup.addClass('tp-above');
        } else {
            $popup.removeClass('tp-above');
        }
        $popup.css({ top: top, left: off.left });
    }

    function open(el) {
        if (!usable(el)) {
            return;
        }
        if (!$popup) {
            build();
        }
        current = el;
        var value = parse(el.value);
        pickedHour = value ? value.h : null;
        render('hour');
        $popup.show();
        place();
    }

    function close() {
        if ($popup) {
            $popup.hide();
        }
        current = null;
    }

    function commit(value) {
        var el = current;
        if (!el) {
            return;
        }
        el.value = value;
        $(el).trigger('input').trigger('change');
        close();
    }

    $(document).on('focus click', 'input.time-picker', function () {
        if (current !== this || !$popup || !$popup.is(':visible')) {
            open(this);
        }
    });

    $(document).on('keydown', 'input.time-picker', function (e) {
        if (e.key === 'Escape' || e.key === 'Enter' || e.key === 'Tab') {
            close();
        } else if (e.key === 'ArrowDown' && (!$popup || !$popup.is(':visible'))) {
            open(this);
        }
    });

    // Ketikan dirapikan saat keluar field
    $(document).on('blur', 'input.time-picker', function () {
        var el = this;
        var fixed = normalize(el.value);
        if (fixed !== null && fixed !== el.value) {
            el.value = fixed;
            $(el).trigger('input').trigger('change');
        }
        // klik di popup tidak memindahkan fokus (mousedown dicegah), jadi blur = selesai
        setTimeout(function () {
            if (current === el && document.activeElement !== el) {
                close();
            }
        }, 0);
    });

    $(document).on('mousedown', function (e) {
        if (current && !$(e.target).closest('.timepicker-dropdown').length && e.target !== current) {
            close();
        }
    });
    $(window).on('resize', place);
    // scroll halaman maupun kontainer (mis. modal) -> popup ikut posisi field
    document.addEventListener('scroll', place, true);
})(jQuery);

/* ------------------------------------------------------------------
 * TWP portal - script bersama (dimuat di <head> layout setelah jQuery,
 * CoreUI, DataTables, Select2, SweetAlert2, bootstrap-datepicker).
 * ------------------------------------------------------------------ */
(function ($) {
    'use strict';

    // CSRF untuk semua request AJAX jQuery
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Select2: tema bootstrap-5 & lebar penuh untuk semua pemanggilan .select2()
    if ($.fn.select2) {
        $.fn.select2.defaults.set('theme', 'bootstrap-5');
        $.fn.select2.defaults.set('width', '100%');
    }

    // DataTables: teks bahasa & jarak
    if ($.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: '',
                searchPlaceholder: 'Search...',
                lengthMenu: 'Show _MENU_',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries',
                infoFiltered: '(filtered from _MAX_ total)',
                emptyTable: 'No data available',
                zeroRecords: 'No matching records found',
                processing: 'Loading...'
            }
        });
    }

    // Select2 di dalam modal: dropdown harus menempel ke modal (focus trap CoreUI)
    if ($.fn.select2) {
        var origSelect2 = $.fn.select2;
        $.fn.select2 = function (opts) {
            if (arguments.length === 0 || (opts && typeof opts === 'object')) {
                return this.each(function () {
                    var o = $.extend({}, opts || {});
                    var $modal = $(this).closest('.modal');
                    if ($modal.length && !o.dropdownParent) {
                        o.dropdownParent = $modal;
                    }
                    origSelect2.call($(this), o);
                });
            }
            return origSelect2.apply(this, arguments);
        };
        $.extend($.fn.select2, origSelect2);
    }

    // Datepicker: <input class="date-picker" data-date-format="dd/mm/yyyy">
    // Dipanggil saat halaman siap dan setiap ada elemen baru (isi modal via AJAX).
    function initDatepickers() {
        if (!$.fn.datepicker) {
            return;
        }
        $('.date-picker').each(function () {
            if ($(this).data('datepicker')) {
                return;
            }
            $(this).datepicker({
                format: $(this).data('date-format') || 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                orientation: 'bottom auto'
            });
        });
    }
    window.initDatepickers = initDatepickers;

    $(function () {
        initDatepickers();

        if (window.MutationObserver) {
            var pending = null;
            new MutationObserver(function () {
                clearTimeout(pending);
                pending = setTimeout(initDatepickers, 50);
            }).observe(document.body, { childList: true, subtree: true });
        }

        // Tooltip CoreUI
        $('[data-coreui-toggle="tooltip"]').each(function () {
            new coreui.Tooltip(this);
        });
    });

    // ------------------------------------------------------------------
    // Helper global yang dipakai view lama (dulu ada di header admin)
    // ------------------------------------------------------------------

    /** block(true, '#el') / block(false, '#el'): overlay "Loading..." di dalam elemen. */
    window.block = function (on, div) {
        var $el = $(div);
        if (!$el.length) {
            return;
        }
        if (on) {
            if ($el.css('position') === 'static') {
                $el.css('position', 'relative');
            }
            if (!$el.children('.block-overlay').length) {
                $el.append(
                    '<div class="block-overlay"><div class="block-msg">' +
                    '<span class="spinner-border spinner-border-sm" role="status"></span> Loading ...' +
                    '</div></div>'
                );
            }
        } else {
            $el.children('.block-overlay').remove();
        }
    };

    function pad2(n) {
        return n < 10 ? '0' + n : String(n);
    }

    /** 'yyyy-mm-dd hh:mm:ss' -> 'dd-mm-yyyy' */
    window.FormatDateNew = function (data) {
        if (data == null || data === '') {
            return 'Not Set';
        }
        var d = new Date(String(data).replace(/\s/, 'T'));
        if (isNaN(d.getTime())) {
            return String(data);
        }
        return pad2(d.getDate()) + '-' + pad2(d.getMonth() + 1) + '-' + d.getFullYear();
    };

    /** 'yyyy-mm-dd hh:mm:ss' -> 'dd-mm-yyyy hh:mm' */
    window.FormatDateTimeNew = function (data) {
        if (data == null || data === '') {
            return 'Not Set';
        }
        var d = new Date(String(data).replace(/\s/, 'T'));
        if (isNaN(d.getTime())) {
            return String(data);
        }
        return pad2(d.getDate()) + '-' + pad2(d.getMonth() + 1) + '-' + d.getFullYear() +
            ' ' + pad2(d.getHours()) + ':' + pad2(d.getMinutes());
    };

    /** Escape HTML untuk render DataTables / template string. */
    window.escapeHtml = function (s) {
        return $('<div>').text(s == null ? '' : String(s)).html();
    };

    /**
     * Tombol kecil "lihat gambar" untuk tabel ticket (dipasang di samping badge status);
     * '' kalau ticket tidak punya gambar (url dari App\Support\TicketHd::withPictures).
     * Klik -> gambar tampil di #modallg (layouts/app). Teks dari window.TICKET_PICTURE_LANG.
     */
    window.ticketPictureButton = function (url) {
        if (!url) {
            return '';
        }
        var title = (window.TICKET_PICTURE_LANG || {}).button || 'View picture';
        return ' <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1 ms-1 align-baseline btn-ticket-picture"'
            + ' data-url="' + window.escapeHtml(url) + '" title="' + window.escapeHtml(title) + '" aria-label="' + window.escapeHtml(title) + '">'
            + '<i class="cil-image"></i></button>';
    };

    $(document).on('click', '.btn-ticket-picture', function (e) {
        e.preventDefault();
        e.stopPropagation();   // baris tabel yang bisa dipilih tidak ikut terpilih
        var url = $(this).data('url');
        var L = window.TICKET_PICTURE_LANG || {};
        var $img = $('<img class="img-fluid rounded border d-block mx-auto" alt="">').attr('src', url)
            .on('error', function () {
                $(this).replaceWith($('<p class="text-body-secondary text-center mb-0"></p>').text(L.failed || 'Picture could not be loaded.'));
            });
        $('#modaltitlelg').text(L.title || 'Picture');
        $('#modalbodylg').empty().append($img, $('<div class="text-center mt-2"></div>').append(
            $('<a target="_blank" rel="noopener" class="small"></a>').attr('href', url).text(L.open || 'Open in new tab')
        ));
        $('#modalfooterlg').empty();
        $('#modallg').modal('show');
    });
})(jQuery);

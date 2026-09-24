/* ------------------------------------------------------------------
 * Tampilan seragam untuk semua pilihan file ("dropzone").
 *
 *   <input type="file" class="form-control" ...>          -> kotak besar
 *   <input type="file" class="form-control form-control-sm"> -> versi ringkas
 *
 * Input aslinya tetap ada (transparan, menutupi seluruh kotak), jadi klik, tarik & lepas,
 * atribut accept, name, onChange dan kode yang membaca .files tidak berubah.
 * Nama & ukuran file ikut diperbarui saat input dikosongkan lewat jQuery .val('')
 * atau form di-reset. Input yang disembunyikan (.d-none, mis. foto profil) tidak diubah.
 * Teks dari window.FILE_INPUT_LANG (partials/i18n_plugins). Dimuat dari layouts/app.
 * ------------------------------------------------------------------ */
(function ($) {
    'use strict';

    var L = $.extend({
        choose: 'Choose file',
        drop: 'or drag & drop here',
        none: 'No file chosen',
        many: ':count files chosen'
    }, window.FILE_INPUT_LANG || {});

    function size(bytes) {
        if (bytes < 1024) {
            return bytes + ' B';
        }
        if (bytes < 1024 * 1024) {
            return (bytes / 1024).toFixed(0) + ' KB';
        }
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    /** Perbarui teks nama file di kotak milik input ini. */
    function refresh(input) {
        var $wrap = $(input).closest('.file-drop');
        if (!$wrap.length) {
            return;
        }
        var files = input.files || [];
        var $name = $wrap.find('.file-drop-name');

        if (!files.length) {
            $name.text(L.none);
        } else if (files.length === 1) {
            $name.text(files[0].name + ' (' + size(files[0].size) + ')');
        } else {
            $name.text(L.many.replace(':count', files.length));
        }
        $wrap.toggleClass('has-file', files.length > 0);
        $wrap.toggleClass('is-disabled', !!input.disabled);
    }

    function enhance(input) {
        var $input = $(input);
        if ($input.data('fileDrop') || $input.hasClass('d-none') || $input.closest('.file-drop').length) {
            return;
        }
        $input.data('fileDrop', true);

        var small = $input.hasClass('form-control-sm');
        var $wrap = $(
            '<div class="file-drop' + (small ? ' file-drop-sm' : '') + '">' +
                '<span class="file-drop-icon"><i class="cil-cloud-upload"></i></span>' +
                '<span class="file-drop-body">' +
                    '<span class="file-drop-title"></span>' +
                    '<span class="file-drop-name"></span>' +
                '</span>' +
            '</div>'
        );
        $wrap.find('.file-drop-title').append(
            $('<strong>').text(L.choose),
            small ? '' : $('<span>').text(' ' + L.drop)
        );

        $input.before($wrap);
        $wrap.append($input);
        $input.removeClass('form-control form-control-sm').addClass('file-drop-input');

        $input.on('change', function () {
            refresh(this);
        });
        $input.on('dragenter dragover', function () {
            $wrap.addClass('is-dragover');
        });
        $input.on('dragleave drop', function () {
            $wrap.removeClass('is-dragover');
        });
        $input.closest('form').on('reset', function () {
            setTimeout(function () {
                refresh(input);
            }, 0);
        });

        refresh(input);
    }

    function init(root) {
        $(root || document).find('input[type="file"].form-control, input[type="file"].file-drop-input').each(function () {
            enhance(this);
        });
    }
    window.initFileInputs = init;

    // $('#x').val('') pada input file -> teks nama file ikut dikosongkan
    var origHook = $.valHooks.file;
    $.valHooks.file = {
        set: function (el, value) {
            if (origHook && origHook.set) {
                origHook.set(el, value);
            } else {
                el.value = value;
            }
            refresh(el);
            return true;
        }
    };

    $(function () {
        init(document);

        // isi yang dimuat belakangan (modal via AJAX, dialog SweetAlert)
        if (window.MutationObserver) {
            var pending = null;
            new MutationObserver(function () {
                clearTimeout(pending);
                pending = setTimeout(function () { init(document); }, 50);
            }).observe(document.body, { childList: true, subtree: true });
        }
    });
})(jQuery);

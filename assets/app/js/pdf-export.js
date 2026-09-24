/*
 * Tombol PDF DataTables (extend: 'pdf' / 'pdfHtml5') seragam di semua halaman:
 * - PDF dibuka di tab baru dulu (download dari viewer browser), sama seperti PDF admin.
 * - Tampilan mengikuti DataTables: header gelap #1d2333, baris belang, garis tipis,
 *   status (badge) tetap berwarna.
 * - Orientasi otomatis: landscape kalau tabel lebar (banyak kolom / isi panjang),
 *   selain itu portrait. Halaman tetap bisa memaksa lewat orientation: 'portrait'|'landscape'.
 * - Kolom dengan class "no-export" (mis. Action, checkbox) tidak ikut dicetak.
 *
 * Teks (Dicetak / Halaman) dari window.PDF_EXPORT_LANG (partials/i18n_plugins).
 * Harus dimuat setelah buttons.html5.min.js dan sebelum DataTable dibuat.
 */
(function ($) {
    if (!$ || !$.fn.dataTable || !$.fn.dataTable.ext.buttons.pdfHtml5) {
        return;
    }

    var HEAD_BG = '#1d2333';
    var HEAD_LINE = '#2c3446';
    var BODY_LINE = '#dee2e6';
    var STRIPE = '#f5f6f8';
    var TEXT = '#212631';
    var MUTED = '#6b7280';
    var BADGE = {
        primary: ['#eef0ff', '#3d48b8'],
        success: ['#e3f8ee', '#1b7f4a'],
        info: ['#e3f3fb', '#1a6e97'],
        warning: ['#fff5e0', '#9a6a00'],
        danger: ['#fde9e7', '#b42318'],
        secondary: ['#eceef2', '#4b5563'],
        dark: ['#e2e4ea', '#1f2937']
    };
    // penanda badge di teks sel (dibuang lagi di customize)
    var MARK = '\u0001';

    function lang(key, fallback) {
        var l = window.PDF_EXPORT_LANG || {};
        return l[key] || fallback;
    }

    function stripText(data, opts) {
        var Buttons = $.fn.dataTable.Buttons;
        if (Buttons && typeof Buttons.stripData === 'function') {
            return Buttons.stripData(data, opts);
        }
        return $('<div>').html(data == null ? '' : String(data)).text().replace(/\s+/g, ' ').trim();
    }

    function badgeTone(html) {
        if (typeof html !== 'string' || html.indexOf('badge') === -1) {
            return null;
        }
        var $b = $('<div>').html(html).find('.badge').first();
        if (!$b.length) {
            return null;
        }
        var cls = $b.attr('class') || '';
        for (var tone in BADGE) {
            if (new RegExp('(^|[\\s-])' + tone + '(\\s|$)').test(cls)) {
                return tone;
            }
        }
        return 'secondary';
    }

    function cellText(cell) {
        return cell && cell.text != null ? String(cell.text) : '';
    }

    var NUMERIC = /^[-+(]?\s*(rp\.?\s*|idr\s*)?[\d.,]+\)?$/i;
    var DATE = /^\d{1,4}[\/\-.]\d{1,2}[\/\-.]\d{1,4}( \d{1,2}:\d{2}(:\d{2})?)?$|^\d{1,2} \S+ \d{4}( \d{1,2}:\d{2}(:\d{2})?)?$/;

    // Perataan per kolom: No & tanggal di tengah, angka di kanan, lainnya kiri
    function columnAlign(rows, col, header) {
        var h = header.toLowerCase().replace(/[.\s]/g, '');
        if (h === 'no' || h === '#' || h === 'nomor') {
            return 'center';
        }
        var total = 0, num = 0, date = 0, badge = 0;
        rows.forEach(function (r) {
            var c = r[col];
            var t = cellText(c).trim();
            if (!t || t === '-') {
                return;
            }
            total++;
            if (c._badge) {
                badge++;
            } else if (DATE.test(t)) {
                date++;
            } else if (NUMERIC.test(t)) {
                num++;
            }
        });
        if (!total) {
            return 'left';
        }
        if (badge === total || date === total) {
            return 'center';
        }
        if (num === total) {
            return 'right';
        }
        return 'left';
    }

    // Lebar relatif kolom dari panjang isi (dibatasi supaya kolom deskripsi tidak memakan semuanya)
    function columnWeights(header, rows) {
        return header.map(function (h, i) {
            var max = Math.min(cellText(h).length, 18);
            rows.forEach(function (r) {
                max = Math.max(max, Math.min(cellText(r[i]).length, 45));
            });
            return Math.max(max, 3);
        });
    }

    function styleDoc(doc, config) {
        var table = null;
        doc.content.forEach(function (item) {
            if (!table && item.table) {
                table = item;
            }
        });
        if (!table) {
            return;
        }

        var body = table.table.body;
        var headerRows = table.table.headerRows || 0;
        var footerRows = table.table.footerRows || 0;
        var header = body[headerRows - 1] || body[0] || [];
        var dataRows = body.slice(headerRows, body.length - footerRows);

        // badge -> teks berwarna dengan latar lembut seperti di tabel
        dataRows.forEach(function (row) {
            row.forEach(function (cell) {
                var t = cellText(cell);
                if (t.charAt(0) === MARK) {
                    var end = t.indexOf(MARK, 1);
                    var tone = t.substring(1, end);
                    cell.text = t.substring(end + 1);
                    cell._badge = true;
                    cell.fillColor = BADGE[tone][0];
                    cell.color = BADGE[tone][1];
                    cell.bold = true;
                    cell.fontSize = 8;
                }
            });
        });

        // judul kolom huruf besar seperti header DataTables (app.css)
        body.slice(0, headerRows).forEach(function (row) {
            row.forEach(function (cell) {
                if (cell && typeof cell.text === 'string') {
                    cell.text = cell.text.toUpperCase();
                }
            });
        });

        var colCount = header.length;
        var weights = columnWeights(header, dataRows);
        var totalWeight = weights.reduce(function (a, b) { return a + b; }, 0);

        // Orientasi: paksa dari halaman kalau ada, selain itu otomatis
        var orientation = config._twpOrientation;
        if (orientation !== 'portrait' && orientation !== 'landscape') {
            orientation = (colCount >= 7 || totalWeight > 95) ? 'landscape' : 'portrait';
        }
        doc.pageOrientation = orientation;
        doc.pageSize = config.pageSize || 'A4';
        doc.pageMargins = [28, 30, 28, 36];

        var fontSize = colCount >= 10 ? 7.5 : (colCount >= 7 ? 8.5 : 9.5);
        doc.defaultStyle = { fontSize: fontSize, color: TEXT };
        doc.styles.title = { fontSize: 14, bold: true, color: HEAD_BG, alignment: 'left' };
        doc.styles.tableHeader = {
            bold: true,
            fontSize: fontSize - 0.5,
            color: '#ffffff',
            fillColor: HEAD_BG,
            alignment: 'center'
        };
        doc.styles.tableFooter = { bold: true, fontSize: fontSize, fillColor: '#eef0f4', color: TEXT };
        doc.styles.table = { margin: [0, 4, 0, 0] };
        doc.styles.message = { fontSize: 9, color: MUTED };

        // Judul + waktu cetak (judul bawaan DataTables diganti blok ini)
        var titleIdx = -1;
        doc.content.forEach(function (item, i) {
            if (titleIdx === -1 && item.style === 'title') {
                titleIdx = i;
            }
        });
        var titleText = titleIdx > -1 ? doc.content[titleIdx].text : '';
        var printed = lang('printed', 'Printed: :date').replace(':date', window.moment ? moment().format('DD MMMM YYYY HH:mm') : new Date().toLocaleString());
        var headBlock = {
            margin: [0, 0, 0, 8],
            stack: [
                {
                    columns: [
                        { text: titleText, style: 'title', width: '*' },
                        { text: printed, fontSize: 8, color: MUTED, alignment: 'right', width: 'auto', margin: [0, 4, 0, 0] }
                    ]
                },
                { canvas: [{ type: 'line', x1: 0, y1: 6, x2: orientation === 'landscape' ? 785 : 539, y2: 6, lineWidth: 1.5, lineColor: HEAD_BG }] }
            ]
        };
        if (titleIdx > -1) {
            doc.content.splice(titleIdx, 1, headBlock);
        } else {
            doc.content.unshift(headBlock);
        }

        // Perataan & lebar kolom
        var aligns = header.map(function (h, i) { return columnAlign(dataRows, i, cellText(h)); });
        dataRows.forEach(function (row) {
            row.forEach(function (cell, i) {
                if (!cell.alignment) {
                    cell.alignment = aligns[i];
                }
            });
        });
        table.table.widths = weights.map(function (w, i) {
            var h = cellText(header[i]).toLowerCase().replace(/[.\s]/g, '');
            if (h === 'no' || h === '#') {
                return 'auto';
            }
            return w >= 30 ? '*' : 'auto';
        });
        if (table.table.widths.indexOf('*') === -1 && colCount) {
            // tabel tetap memenuhi lebar halaman
            var widest = weights.indexOf(Math.max.apply(null, weights));
            table.table.widths[widest] = '*';
        }
        table.table.dontBreakRows = true;

        table.layout = {
            hLineWidth: function () { return 0.5; },
            vLineWidth: function () { return 0.5; },
            hLineColor: function (i) { return i <= headerRows ? HEAD_LINE : BODY_LINE; },
            vLineColor: function () { return BODY_LINE; },
            fillColor: function (rowIndex) {
                if (rowIndex < headerRows) {
                    return HEAD_BG;
                }
                return (rowIndex - headerRows) % 2 === 1 ? STRIPE : null;
            },
            paddingLeft: function () { return 5; },
            paddingRight: function () { return 5; },
            paddingTop: function () { return 4; },
            paddingBottom: function () { return 4; }
        };

        // Kosong: satu baris keterangan
        if (!dataRows.length && colCount) {
            var empty = [{ text: lang('empty', 'No data available'), colSpan: colCount, alignment: 'center', color: MUTED, italics: true }];
            for (var e = 1; e < colCount; e++) {
                empty.push({});
            }
            body.splice(headerRows, 0, empty);
        }

        doc.footer = function (page, pages) {
            return {
                margin: [28, 10, 28, 0],
                columns: [
                    { text: titleText, fontSize: 7.5, color: MUTED },
                    { text: lang('page', 'Page :page of :total').replace(':page', page).replace(':total', pages), fontSize: 7.5, color: MUTED, alignment: 'right' }
                ]
            };
        };
    }

    var base = $.fn.dataTable.ext.buttons.pdfHtml5;
    var origAction = base.action;

    base.download = 'open';
    base.pageSize = 'A4';
    base.orientation = 'auto';
    base.footer = false;
    base.exportOptions = { columns: ':visible:not(.no-export)' };

    base.action = function (e, dt, button, config, cb) {
        var cfg = $.extend({}, config);
        var exportOptions = $.extend(true, {}, config.exportOptions);
        var stripOpts = $.extend({ stripHtml: true, stripNewlines: true, decodeEntities: true, trim: true }, exportOptions);

        // badge dikenali dari HTML hasil render, teksnya tetap dibersihkan seperti biasa
        if (!exportOptions.format || !exportOptions.format.body) {
            exportOptions.format = $.extend({}, exportOptions.format, {
                body: function (data) {
                    var text = stripText(data, stripOpts);
                    var tone = badgeTone(data);
                    return tone && text ? MARK + tone + MARK + text : text;
                }
            });
        }
        cfg.exportOptions = exportOptions;

        // pdfmake butuh orientasi valid saat membuat dokumen; nilai akhir ditentukan di styleDoc
        cfg._twpOrientation = config.orientation;
        cfg.orientation = config.orientation === 'landscape' ? 'landscape' : 'portrait';

        var pageCustomize = config.customize;
        cfg.customize = function (doc, c, api) {
            styleDoc(doc, cfg);
            if (typeof pageCustomize === 'function') {
                pageCustomize(doc, c, api);
            }
        };

        return origAction.call(this, e, dt, button, cfg, cb);
    };

    // Posisi & gaya tombol sama dengan admin History (Ticket / Log User): btn-pdf (app.css)
    // di kanan judul halaman (.page-head-row). Tabel di dalam kartu yang punya judul sendiri
    // (.card-title-group, mis. dasbor) -> di kanan judul kartu. Tidak ada keduanya -> tetap di tempat.
    $(document).on('init.dt', function (e, settings) {
        if (e.namespace !== 'dt') {
            return;
        }
        var api = new $.fn.dataTable.Api(settings);
        if (!api.buttons) {
            return;
        }
        var $nodes = $(api.buttons('.buttons-pdf').nodes());
        if (!$nodes.length) {
            return;
        }

        $nodes.each(function () {
            var $b = $(this);
            var label = $.trim($b.text()) || lang('button', 'PDF');
            $b.attr('class', 'btn btn-pdf buttons-pdf buttons-html5')
                .html('<i class="cil-cloud-download"></i><span></span>')
                .find('span').text(label);
        });

        var $container = $(api.buttons().container());
        var $table = $(api.table().node());
        var $group = $table.closest('.card').find('.card-title-group').first();
        var $head = $table.closest('.page-body').find('.page-head-row').first();
        var id = 'pdf-slot-' + $table.attr('id');
        $('#' + id).remove();

        if ($group.length) {
            var $tools = $group.children('.card-tools');
            if (!$tools.length) {
                $tools = $('<div class="card-tools"></div>').append($group.children().not('.title'));
                $group.append($tools);
            }
            $tools.prepend($('<div id="' + id + '"></div>').append($container));
            $nodes.addClass('btn-sm');
        } else if ($head.length) {
            $head.append($('<div class="page-head-content" id="' + id + '"></div>').append($container));
        } else {
            return;
        }
        $container.removeClass('btn-group flex-wrap').addClass('d-flex gap-2');
    });
})(window.jQuery);

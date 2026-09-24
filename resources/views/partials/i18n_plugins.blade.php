{{--
    Teks bawaan plugin JS mengikuti bahasa aktif (lang/{locale}/shared/plugins.php):
    DataTables, Select2, jQuery Validate, serta nama bulan/hari moment & bootstrap-datepicker.
    Dipanggil sekali dari layouts/app setelah semua plugin dimuat.
--}}
@php
    $locale = app()->getLocale();
    $dt = __('shared/plugins.datatables');
@endphp
<script>
    window.APP_LOCALE = @json($locale);

    if ($.fn.dataTable) {
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: @json($dt['search']),
                lengthMenu: @json($dt['lengthMenu']),
                info: @json($dt['info']),
                infoEmpty: @json($dt['infoEmpty']),
                infoFiltered: @json($dt['infoFiltered']),
                loadingRecords: @json($dt['loadingRecords']),
                processing: @json($dt['processing']),
                zeroRecords: @json($dt['zeroRecords']),
                emptyTable: @json($dt['emptyTable']),
                paginate: {
                    first: @json($dt['first']),
                    last: @json($dt['last']),
                    next: @json($dt['next']),
                    previous: @json($dt['previous'])
                }
            }
        });
    }

    if ($.fn.select2) {
        $.fn.select2.defaults.set('language', {
            noResults: function () { return @json(__('shared/plugins.select2.noResults')); },
            searching: function () { return @json(__('shared/plugins.select2.searching')); }
        });
    }

    if ($.validator) {
        $.extend($.validator.messages, {
            required: @json(__('shared/plugins.validate.required')),
            email: @json(__('shared/plugins.validate.email')),
            number: @json(__('shared/plugins.validate.number')),
            date: @json(__('shared/plugins.validate.date')),
            equalTo: @json(__('shared/plugins.validate.equalTo')),
            maxlength: $.validator.format(@json(__('shared/plugins.validate.maxlength'))),
            minlength: $.validator.format(@json(__('shared/plugins.validate.minlength')))
        });
    }

    @if ($locale === 'id')
    // Nama bulan / hari bahasa Indonesia (moment.min.js & bootstrap-datepicker hanya membawa English)
    if (window.moment && !moment.locales().includes('id')) {
        moment.defineLocale('id', {
            months: 'Januari_Februari_Maret_April_Mei_Juni_Juli_Agustus_September_Oktober_November_Desember'.split('_'),
            monthsShort: 'Jan_Feb_Mar_Apr_Mei_Jun_Jul_Agu_Sep_Okt_Nov_Des'.split('_'),
            weekdays: 'Minggu_Senin_Selasa_Rabu_Kamis_Jumat_Sabtu'.split('_'),
            weekdaysShort: 'Min_Sen_Sel_Rab_Kam_Jum_Sab'.split('_'),
            weekdaysMin: 'Mg_Sn_Sl_Rb_Km_Jm_Sb'.split('_')
        });
    }
    if (window.moment) { moment.locale('id'); }

    if ($.fn.datepicker) {
        $.fn.datepicker.dates['id'] = {
            days: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            daysShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
            daysMin: ['Mg', 'Sn', 'Sl', 'Rb', 'Km', 'Jm', 'Sb'],
            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            today: 'Hari Ini',
            clear: 'Kosongkan'
        };
        $.fn.datepicker.defaults.language = 'id';
    }
    @endif
</script>

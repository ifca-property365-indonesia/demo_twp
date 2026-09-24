@extends('admin.template.layout2.base')

@section('title', $jdl)

@push('styles')
<style>
    .ck-editor__editable { min-height: 300px; }
    #picturebox { max-height: 220px; object-fit: contain; }
</style>
@endpush

@push('head-scripts')
    <script src="{{ url('assets/vendor/ckeditor5/ckeditor.js') }}"></script>
@endpush

@section('content')
<div class="page-body">
    <div class="page-head">
        <div class="page-head-row">
            <div class="page-head-content">
                <h3 class="page-title">{{ $jdl }}</h3>
                <div class="page-desc">{{ __('admin/news.desc') }}</div>
            </div>
            <div class="page-head-content">
                <a href="{{ url('admin/news') }}" class="btn btn-outline-secondary"><i class="cil-arrow-left"></i><span>{{ __('admin/news.back_to_list') }}</span></a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="frmEditor" method="post" action="" novalidate autocomplete="off">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('admin/news.content_type') }}</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="content_type" id="news" value="news" checked>
                            <label class="form-check-label" for="news">{{ __('admin/news.news') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="content_type" id="promo" value="promo">
                            <label class="form-check-label" for="promo">{{ __('admin/news.promo') }}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="start_date">{{ __('common.start_date') }}</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                            <input type="text" id="start_date" name="start_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="end_date">{{ __('common.end_date') }}</label>
                        <div class="form-control-wrap">
                            <div class="form-icon form-icon-left"><i class="cil-calendar"></i></div>
                            <input type="text" id="end_date" name="end_date" class="form-control date-picker" data-date-format="dd/mm/yyyy" value="{{ date('d/m/Y') }}">
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="news_title" class="form-label">{{ __('common.title') }}</label>
                        <input type="text" class="form-control" name="news_title" id="news_title" placeholder="{{ __('common.title') }}" maxlength="160">
                        <div class="form-note text-end"><span id="news_length">0</span>/160</div>
                    </div>

                    <div class="col-12">
                        <label for="news_descs" class="form-label">{{ __('admin/news.content') }}</label>
                        <textarea class="form-control" name="news_descs" id="news_descs" placeholder="{{ __('admin/news.content_placeholder') }}" rows="12"></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label d-block">{{ __('admin/news.attachment_type') }}</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="attach_type" id="type-P" value="P" checked>
                            <label class="form-check-label" for="type-P">{{ __('common.picture') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="attach_type" id="type-Y" value="Y">
                            <label class="form-check-label" for="type-Y">{{ __('admin/news.youtube') }}</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="picture">
                            <label for="userfile" class="form-label">{{ __('admin/news.upload_picture') }}</label>
                            <div class="mb-2">
                                <img src="{{ url('images/PlProject/no_image.png') }}" id="picturebox" class="img-fluid rounded border" alt="">
                            </div>
                            <input type="file" id="userfile" name="userfile" class="form-control" accept="image/*">
                            <div class="form-note">{!! __('admin/news.picture_note') !!}</div>
                        </div>
                        <div id="youtube">
                            <label for="youtubelink" class="form-label">{{ __('admin/news.youtube_link') }}</label>
                            <input type="text" id="youtubelink" name="youtubelink" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="picturepath" id="picturepath" value="">
                <input type="hidden" name="picturename" id="picturename">

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary" id="btnBack">{{ __('common.back') }}</button>
                    <button type="button" id="btnSave" class="btn btn-primary"><i class="cil-save"></i><span>{{ __('common.save') }}</span></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    let editorInstance;

    $(function () {
        ClassicEditor
            .create(document.querySelector('#news_descs'), {
                toolbar: [
                    'undo', 'redo', '|', 'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                    'link', 'insertTable', 'uploadImage', 'blockQuote', 'codeBlock', '|',
                    'bulletedList', 'numberedList', 'todoList', '|', 'alignment', '|', 'outdent', 'indent', '|', 'removeFormat'
                ]
            })
            .then(function (editor) {
                editorInstance = editor;
                loaddata();
            })
            .catch(function (error) { console.error(error); });

        $('#news_title').on('input', function () {
            $('#news_length').text(this.value.length);
        });

        $('#btnBack').on('click', function () {
            window.location.href = "{{ url('admin/news') }}";
        });

        function applyAttachType() {
            var type = $('input[name=attach_type]:checked').val();
            $('#youtube').toggle(type === 'Y');
            $('#picture').toggle(type === 'P');
        }
        $('input[name=attach_type]').on('change', applyAttachType);
        applyAttachType();

        $('#frmEditor').validate({
            ignore: [],
            rules: { news_title: { required: true }, start_date: { required: true }, end_date: { required: true } },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function (el) { $(el).addClass('is-invalid'); },
            unhighlight: function (el) { $(el).removeClass('is-invalid'); },
            errorPlacement: function (error, element) { error.insertAfter(element); }
        });

        $('#userfile').on('change', function () {
            var file = this.files[0];
            if (!file) { return; }
            var data = new FormData();
            data.append('userfile', file);

            $.ajax({
                url: "{{ url('admin/news/savepic') }}",
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json'
            }).done(function (res) {
                if (res.status == 'OK') {
                    $('#picturebox').attr('src', res.url);
                    $('#picturepath').val(res.url);
                    $('#picturename').val(res.picname);
                } else {
                    Swal.fire({ title: @json(__('common.error')), text: res.pesan, icon: 'error' });
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                Swal.fire({ title: @json(__('common.error')), text: textStatus + ' : ' + errorThrown, icon: 'error' });
            });
        });

        $('#btnSave').on('click', function () {
            if (!$('#frmEditor').valid()) { return; }

            var datafrm = $('#frmEditor').serializeArray();
            datafrm.push(
                { name: 'action', value: '{{ $form }}' },
                { name: 'id', value: '{{ $id }}' },
                { name: 'news_descs', value: editorInstance ? editorInstance.getData() : $('#news_descs').val() }
            );

            $('#btnSave').prop('disabled', true);

            $.ajax({
                url: "{{ url('/admin/news/save') }}",
                type: 'POST',
                data: datafrm,
                dataType: 'json'
            }).done(function (res) {
                if (res.status == 'OK') {
                    Swal.fire({ title: @json(__('common.information')), icon: 'success', text: res.pesan })
                        .then(function () { window.location.href = "{{ url('/admin/news') }}"; });
                } else {
                    Swal.fire({ title: @json(__('common.information')), icon: 'error', text: res.pesan });
                    $('#btnSave').prop('disabled', false);
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                Swal.fire({ title: @json(__('common.error')), icon: 'error', text: textStatus + ' : ' + errorThrown });
                $('#btnSave').prop('disabled', false);
            });
        });

        function loaddata() {
            var rowID = '{{ $id }}';
            if (!(rowID > 0)) { return; }

            $.getJSON("{{ url('/admin/news/id') }}/" + rowID, function (data) {
                if (!data || !data.length) { return; }
                var d = data[0];

                $('#' + d.content_type).prop('checked', true);
                $('#type-' + d.attach_type).prop('checked', true).trigger('change');
                $('#youtubelink').val(d.youtube_link);
                $('#news_title').val(d.subject).trigger('input');

                if (d.picture) {
                    $('#picturebox').attr('src', d.picture);
                    $('#picturepath').val(d.picture);
                }

                // tanggal dari DB "yyyy-mm-dd hh:mm:ss" -> dd/mm/yyyy
                var toDmy = function (v) { return v ? v.substr(8, 2) + '/' + v.substr(5, 2) + '/' + v.substr(0, 4) : ''; };
                if (d.start_date) { $('#start_date').datepicker('update', toDmy(d.start_date)); }
                if (d.end_date) { $('#end_date').datepicker('update', toDmy(d.end_date)); }

                if (editorInstance) { editorInstance.setData(d.content || ''); }
            });
        }
    });
</script>
@endpush

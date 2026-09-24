@extends('admin.template.layout2.base')

@section('title', $jdl)

@push('styles')
<style>
    .ck-editor__editable { min-height: 300px; }
    /* preview gambar / video, rasio 4:3 seperti resolusi yang disarankan (640 x 480) */
    .news-preview { position: relative; aspect-ratio: 4 / 3; max-height: 260px; border: 1px solid var(--cui-border-color); border-radius: .375rem; background: #f8f9fb; overflow: hidden; }
    .news-preview img, .news-preview iframe { width: 100%; height: 100%; object-fit: contain; border: 0; display: block; }
    .news-preview-empty { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .35rem; color: var(--cui-secondary-color); font-size: .85rem; }
    .news-preview-empty i { font-size: 2rem; opacity: .6; }
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
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <input type="file" id="userfile" name="userfile" class="form-control" accept="image/png,image/jpeg,image/gif">
                                    <div class="form-note">{!! __('admin/news.picture_note') !!}</div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="news-preview">
                                        <div class="news-preview-empty" id="pictureEmpty">
                                            <i class="cil-image"></i><span>{{ __('admin/news.no_picture') }}</span>
                                        </div>
                                        <img src="" id="picturebox" class="d-none" alt="">
                                    </div>
                                    <div class="mt-2 d-flex align-items-center gap-2 d-none" id="pictureBar">
                                        <span class="form-note mt-0 text-truncate" id="pictureInfo"></span>
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="btnRemovePicture"><i class="cil-trash"></i><span>{{ __('common.remove') }}</span></button>
                                    </div>
                                    <div class="form-note text-primary d-none" id="pictureHint">{{ __('admin/news.picture_hint') }}</div>
                                </div>
                            </div>
                        </div>
                        <div id="youtube">
                            <label for="youtubelink" class="form-label">{{ __('admin/news.youtube_link') }}</label>
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <input type="text" id="youtubelink" name="youtubelink" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                                <div class="col-lg-6">
                                    <div class="news-preview">
                                        <div class="news-preview-empty" id="youtubeEmpty">
                                            <i class="cil-video"></i><span>{{ __('admin/news.no_video') }}</span>
                                        </div>
                                        <iframe id="youtubebox" class="d-none" src="" title="YouTube" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="picturepath" id="picturepath" value="">

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

        // ------------------------------------------------------------------
        // Gambar: dipilih -> hanya preview di browser. Unggah ke server baru
        // saat Simpan (seperti form Ticket), jadi batal tidak meninggalkan file.
        // ------------------------------------------------------------------
        var pendingFile = null;

        function showPicture(src, info, isNew) {
            $('#picturebox').attr('src', src || '').toggleClass('d-none', !src);
            $('#pictureEmpty').toggleClass('d-none', !!src);
            $('#pictureBar').toggleClass('d-none', !src);
            $('#pictureInfo').text(info || '');
            $('#pictureHint').toggleClass('d-none', !isNew);
        }

        function clearPicture() {
            pendingFile = null;
            $('#userfile').val('');
            $('#picturepath').val('');
            showPicture('');
        }

        $('#userfile').on('change', function () {
            var file = this.files[0];
            if (!file) { return; }
            if (!/^image\/(png|jpe?g|gif)$/i.test(file.type)) {
                Swal.fire({ title: @json(__('common.information')), text: @json(__('common.upload_only_image')), icon: 'warning' });
                $(this).val('');
                return;
            }
            if (file.size > 5000000) {
                Swal.fire({ title: @json(__('common.information')), text: @json(__('common.upload_max_size', ['size' => '5MB'])), icon: 'warning' });
                $(this).val('');
                return;
            }
            pendingFile = file;
            var reader = new FileReader();
            reader.onload = function (e) {
                showPicture(e.target.result, file.name + ' (' + Math.round(file.size / 1024) + ' KB)', true);
            };
            reader.readAsDataURL(file);
        });

        $('#btnRemovePicture').on('click', clearPicture);

        // Unggah gambar yang dipilih (dipanggil saat Simpan). Mengembalikan promise.
        function uploadPicture() {
            var d = $.Deferred();
            if (!pendingFile || $('input[name=attach_type]:checked').val() !== 'P') {
                return d.resolve().promise();
            }
            var data = new FormData();
            data.append('userfile', pendingFile);

            $.ajax({
                url: "{{ url('admin/news/savepic') }}",
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                dataType: 'json'
            }).done(function (res) {
                if (res.status == 'OK') {
                    pendingFile = null;
                    $('#picturepath').val(res.path);
                    d.resolve();
                } else {
                    d.reject(res.pesan || @json(__('common.upload_error')));
                }
            }).fail(function (xhr, textStatus, errorThrown) {
                d.reject(@json(__('common.upload_error')) + ' (' + textStatus + (errorThrown ? ': ' + errorThrown : '') + ')');
            });
            return d.promise();
        }

        // ------------------------------------------------------------------
        // Preview YouTube dari tautan yang diisi
        // ------------------------------------------------------------------
        function youtubeId(link) {
            var m = String(link || '').match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/))([\w-]{11})/i);
            return m ? m[1] : null;
        }
        function showYoutube() {
            var id = youtubeId($('#youtubelink').val());
            var src = id ? 'https://www.youtube.com/embed/' + id : '';
            if ($('#youtubebox').attr('src') !== src) {
                $('#youtubebox').attr('src', src);
            }
            $('#youtubebox').toggleClass('d-none', !id);
            $('#youtubeEmpty').toggleClass('d-none', !!id);
        }
        $('#youtubelink').on('input change', showYoutube);

        $('#btnSave').on('click', function () {
            if (!$('#frmEditor').valid()) { return; }

            $('#btnSave').prop('disabled', true);

            uploadPicture().then(function () {
                var datafrm = $('#frmEditor').serializeArray();
                datafrm.push(
                    { name: 'action', value: '{{ $form }}' },
                    { name: 'id', value: '{{ $id }}' },
                    { name: 'news_descs', value: editorInstance ? editorInstance.getData() : $('#news_descs').val() }
                );

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
            }, function (msg) {
                Swal.fire({ title: @json(__('common.error')), icon: 'error', text: msg });
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
                $('#youtubelink').val(d.youtube_link).trigger('change');
                $('#news_title').val(d.subject).trigger('input');

                if (d.picture) {
                    $('#picturepath').val(d.picture);
                    showPicture(d.picture_url, @json(__('admin/news.current_picture')), false);
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

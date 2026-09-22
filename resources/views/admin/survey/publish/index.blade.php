@extends('admin.template.layout2.base')
@section('content')
<div class="page-body">
    <div>
        <div class="page-block">
            <div class="page-head">
                <div class="page-head-row">
                    <div class="page-head-content">
                        <h3 class="page-title">Publish Survey</h3>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-underline-border mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-coreui-toggle="tab" href="#t_new"><i class="cil-pencil"></i> &nbsp;New Survey</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-coreui-toggle="tab" href="#t_published"><i class="cil-task"></i> &nbsp; Published Survey</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="t_new">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered w-100" id="tblsurveyy">
                                    <thead>
                                    <tr>
                                        <th>No.</th>          
                                        <th class="sorting_asc">Survey Title</th>
                                        <th>Subject</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="t_published">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered w-100" id="tblpublished">
                                    <thead>
                                    <tr>
                                        <th>No.</th>          
                                        <th class="sorting_asc">Survey Title</th>
                                        <th>Subject</th>
                                        <th>Publish Date</th>
                                        <th>Expired Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div><!-- . -->
        </div> <!-- page-block -->
    </div>
</div>
<script type="text/javascript">
    var tblsurvey;
    var tblpublishedd;
    $(function(){
         tblpublishedd = $('#tblpublished').DataTable( 
        {
             "language": {
                "decimal": ",",
                "thousands": ".",
            },
             "dom": '<"toolbar tblsurvey_published">frtip',
            select: true,
            "serverSide": true,
            "ajax":{
                "url": "{{ url('admin/survey/publish/allpublished') }}",
                "data":{"sSearch": function(d){
                    var search = $('#txt_search').val();
                    var b="";
                    if(search == null || search==""){
                        return b;
                    }{
                        return search;
                    }
                 }},             
                "type":"POST"
            },
            "columns": [
                {data: "row_number",name:"row_number", searchable:false},
                {data: "title",name:"title", searchable:true},
                {data:"subjects",name:"subjects",searchable:true,
                     render: function (data, type, row) {
                    x=data;
                    var cc='<ul style="list-style-type:disc;margin-left: 20px">';
                    xArray = x.split(',');
                    $.each(xArray, function(index, value) { 
                        cc=cc+'<li>'+value+'</li>';
                    });
                    return cc+'</ul>';
                    }
                },
                {data: "publishdate",name:"publishdate",searchable:true,
                render: function (data, type, row) {
                    return FormatDateNew(data);
                }},
                {data: "expireddate",name:"expireddate",searchable:true,
                render: function (data, type, row) {
                    return FormatDateNew(data);
                }},
               { data: null, name: "action", searchable: false, orderable: false, render: function (data, type, row) { return '<button type="button" class="btn btn-danger btn-sm btn-delete-published" ' + 'data-id="' + row.publish_id + '">' + '<i class="cil-trash"></i> Delete' + '</button>'; } }
            ]
          
        });
        tblsurvey = $('#tblsurveyy').DataTable( 
        {
             "language": {
                "decimal": ",",
                "thousands": ".",
            },
             "dom": '<"toolbar tblsurvey">frtip',
            select: true,
            order: [[ 0, 'asc' ]],
            "serverSide": true,
            "ajax":{
                "url":"{{ url('admin/survey/publish/all')}}",
                "data":{"sSearch": function(d){
                    var search = $('#txt_search').val();
                    var b="";
                    if(search == null || search==""){
                        return b;
                    }{
                        return search;
                    }
                 }},             
                "type":"POST"
            },
            "columns": [
                {data: "row_number",name:"row_number", searchable:false},
                {data: "title",name:"title", searchable:true},
                {data:"subjects",name:"subjects",searchable:true,
                     render: function (data, type, row) {
                    x=data;
                    var cc='<ul style="list-style-type:disc;margin-left: 20px">';
                    xArray = x.split(',');
                    $.each(xArray, function(index, value) { 
                        cc=cc+'<li>'+value+'</li>';
                    });
                    return cc+'</ul>';
                    }
                },
                {data: "publish_id",name:"publish_id", visible:false}
               
            ]
          
        });
        $("div.tblsurvey").html(
            '<button id="addparam" class="btn btn-sm btn-primary">Add</button>&nbsp;'+
            '<button id="editparam" class="btn btn-sm btn-info">Edit</button>&nbsp;'+
            '<button id="deleteparam" class="btn btn-sm btn-danger">Delete</button>&nbsp;'+
            '<button id="publishparam" class="btn btn-sm btn-secondary">Publish</button>&nbsp;'
        );
        tblsurvey.on('click', 'tr', function() {
            if ($(this).hasClass('selected')) {
                $(this).removeClass('selected');
            } else {
                tblsurvey.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
    
         $('#fn_search').click(function(){
            block(true,'#tblsurvey');
       
            var state = document.readyState
                if (state == 'complete') {
                    setTimeout(function(){
                        document.getElementById('interactive');
                        tblsurvey.ajax.reload(null,true);
                        block(false,'#tblsurvey');
                    },1000);
                }  
        });
         $("input[type='search']").keyup(function(event){
            var a = $("input[type='search']").val();
                console.log($("input[type='search']"));
                if(a==''){
                    tblsurvey.ajax.reload(null,true);   
                }
                if(event.keyCode == 13){
                tblsurvey.ajax.reload(null,true);   
            }
        });
         $('#addparam').click(function(){
 
                block(true,'#modalbodyxl');
                $('#modalxl').modal({backdrop: 'static', keyboard: false});
                $('#modaltitlexl').addClass('white');
                $('#modaltitlexl').html('Add New Survey');
                $('.modal-footer').html('<button type="button" class="btn btn-sm btn-primary" id="savefrmxl">Save</button><button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Close</button>');
                $('#modalbodyxl').html("");
                $('#modalbodyxl').load("{{ url('/admin/survey/publish/form') }}");
                
                $('#modalxl').data('id', 0);
                $('#modalxl').data('form', 'add');
                $('#modalxl').modal('show');
                
   
        });
        $('#editparam').click(function(){
            var rows = tblsurvey.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire("Information",'Please select a row',"warning");
                return;
            } 
            var data = tblsurvey.rows(rows).data();
            var publish_id = data[0].publish_id;
          
            block(true,'#modalbodyxl');
            $('#modalbodyxl').html("");
            $('#modalxl').modal({backdrop: 'static', keyboard: false})  
         
            $('#modaltitlexl').addClass('white');
            $('#modaltitlexl').html('Edit Survey');
            $('.modal-footer').html("");
            $('.modal-footer').html('<button type="button" class="btn btn-sm btn-primary" id="savefrmxl">Save</button><button type="button" class="btn btn-sm btn-secondary" data-coreui-dismiss="modal">Close</button>');
            $('#modalbodyxl').load("{{ url('/admin/survey/publish/form') }}");
            $('#modalxl').data('id', publish_id);
            $('#modalxl').data('form', 'edit');
            $('#modalxl').modal('show');
            
        });
        $('#publishparam').click(function(){
            var rows = tblsurvey.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire("Information",'Please select a row',"warning");
                return;
            } 
            var data = tblsurvey.rows(rows).data();
            var publish_id = data[0].publish_id;
            var title  = data[0].title;
            Swal.fire({
                    title: 'Do you want to publish this survey?',
                    text: 'Please input expired date to proceed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                })
                .then(function(a){
                    if (a.value==true) {
                    
                        $('#modalbodyxl').html("");
                        $('#modalxl').modal({backdrop: 'static', keyboard: false})  
                        $('.modal-footer').html("");
                        $('.modal-footer').html('<button type="button" class="btn btn-sm btn-danger" id="savefrm_publish">Publish</button><button type="button" class="btn grey btn-secondary" data-coreui-dismiss="modal">Close</button>');
                        $('#modaltitlexl').addClass('white');
                        $('#modaltitlexl').html('Publish Survey');
                        $('#modalbodyxl').load("{{ url('/admin/survey/publish/add') }}");
                        
                        $('#modalxl').data('id', publish_id);
                        $('#modalxl').data('title', title);
                        $('#modalxl').data('form', 'publish');
                        $('#modalxl').modal('show');
            
                    }else{
                        block(false,'.page-body');
                    }
                });

        });
        $('#deleteparam').click(function(){
            block(true,'.page-body');
            var rows = tblsurvey.rows('.selected').indexes();
            if (rows.length < 1) {
                Swal.fire("Information",'Please select a row',"warning");
                block(false,'.page-body');
                return;
                
            } 
            var data = tblsurvey.rows(rows).data();
            var publish_id = data[0].publish_id;
            Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                })
                .then(function(a){
                    if (a.value==true) {
                        Delete(publish_id);
                    }else{
                        block(false,'.page-body');
                    }
                });
        });   
            
    });
    
    
    function Delete(publish_id) {
        $.ajax({
            url : "{{ url('/admin/survey/publish/delete')}}",
            type:"POST",
            data: { publish_id: publish_id},
            dataType:"json",
            success:function(event, data){
                block(false,'.page-body');
                tblsurvey.ajax.reload(null,true); 
                if(event.status =='OK'){
                    Swal.fire("Information",event.pesan,"success");
                    tblsurvey.ajax.reload(null,true); 
                } else {
                    Swal.fire("Information",event.pesan,"error");
                }
            },                    
            error: function(jqXHR, textStatus, errorThrown){        
                Swal.fire("Information",textStatus+' Save : '+errorThrown,"warning");
                block(false,'.page-body');
            }
        });
    }
$('#tblpublished').on('click', '.btn-delete-published', function () {

    var publish_id = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(result) {

        if (result.value == true) {

            block(true, '.page-body');

            $.ajax({
                url: "{{ url('/admin/survey/publish/delete') }}",
                type: "POST",
                data: {
                    publish_id: publish_id
                },
                dataType: "json",
                success: function(event) {

                    block(false, '.page-body');

                    if (event.status == 'OK') {

                        Swal.fire(
                            "Information",
                            event.pesan,
                            "success"
                        );

                        tblpublishedd.ajax.reload(null, false);

                    } else {

                        Swal.fire(
                            "Information",
                            event.pesan,
                            "error"
                        );
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {

                    block(false, '.page-body');

                    Swal.fire(
                        "Information",
                        textStatus + ' Delete : ' + errorThrown,
                        "warning"
                    );
                }
            });

        }
    });
});
</script>
@endsection
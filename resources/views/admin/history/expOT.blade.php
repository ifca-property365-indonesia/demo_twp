<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>IFCA Software</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="{{ url('assets/pdf/bootstrap.min.css');}}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/pdf/skin-yellow.min.css'); }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/pdf/AdminLTE.min.css'); }}" rel="stylesheet" type="text/css" />
  <style type="text/css">.boxx{position: relative;width: 16px;height: 16px;border-radius: 3px;}
    .footer{position: fixed; bottom: 0px; text-align: center;}
    thead:before, thead:after { display: none; }tbody:before, tbody:after { display: none; }</style>
  </head>
  <body>
    <section class="invoice" style="margin:0px!important;">
      <h4>{{ __('admin/history.pdf_overtime_title') }}</h4><hr/>
      <table id="tbLog" class="table table-bordered table-striped" >
        <thead  style="background:#ffa500;">
          <tr class="odd">
            <th class="sorting" style="width: 7px;padding:5px;"> #</th>
            <th class="sorting" style="width: 15px;padding:5px;">{{ __('common.lot_no') }}</th>
            <th class="sorting" style="width: 40px;padding:5px;">{{ __('common.tenant') }}</th>
            <th class="sorting" style="width: 30px;padding:5px;">{{ __('admin/history.start_overtime') }}</th>
            <th class="sorting" style="width: 30px;padding:5px;">{{ __('admin/history.end_overtime') }}</th>
            <th class="sorting" style="width: 10px;padding:5px;">{{ __('common.status') }}</th>
            <th class="sorting" style="width: 50px;padding:5px;">{{ __('common.description') }}</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if(!empty($listD)) {
            echo $listD;
          } else {
            echo e(__('admin/history.pdf_no_data'));
          }
          ?>
        </tbody>
      </table>
      <div class="footer">
        <p style="font-size:8px">{{ __('admin/history.pdf_disclaimer') }}</p>
      </div> 
    </section>

  </body>
  </html>
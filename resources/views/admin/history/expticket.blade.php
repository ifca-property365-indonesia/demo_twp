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
      <h4>{{ __('admin/history.pdf_ticket_title') }}</h4><hr/>
      <table id="tbLog" class="table table-bordered table-striped" style="width:100%!important">
        <thead  style="background:#ffa500;">
          <tr class="odd">
            <th class="sorting_asc" style="width: 6px;padding: 4px"> #</th>
            <th  style="width: 10px;padding: 4px">{{ __('admin/history.ticket_number') }}</th>
            <th style="width: 10px;padding: 4px">{{ __('common.category') }}</th>
            <th  style="width: 70px!important;padding: 4px">{{ __('admin/history.tenant_name') }}</th>
            <th class="sorting" style="width: 20px;padding: 4px">{{ __('common.description') }}</th>
            <th class="sorting" style="width: 50px;padding: 4px">{{ __('admin/history.reported_date') }}</th>
            <th class="sorting" style="width: 15px;padding: 4px">{{ __('admin/history.request_by') }}</th>
            <th class="sorting" style="width: 5px;padding: 4px">{{ __('common.lot_no') }}</th>
            <th class="sorting" style="width: 15px;padding: 4px">{{ __('admin/history.ticket_status') }}</th>
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
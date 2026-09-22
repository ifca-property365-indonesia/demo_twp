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
      <h4>History Overtime</h4><hr/>
      <table id="tbLog" class="table table-bordered table-striped" >
        <thead  style="background:#ffa500;">
          <tr class="odd">
            <th class="sorting" style="width: 7px;padding:5px;"> #</th>
            <th class="sorting" style="width: 15px;padding:5px;">Lot No</th>
            <th class="sorting" style="width: 40px;padding:5px;">Tenant</th>
            <th class="sorting" style="width: 30px;padding:5px;">Start Overtime</th>
            <th class="sorting" style="width: 30px;padding:5px;">End Overtime</th>
            <th class="sorting" style="width: 10px;padding:5px;">Status</th>
            <th class="sorting" style="width: 50px;padding:5px;">Description</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if(!empty($listD)) {
            echo $listD;
          } else {
            echo "Data Not Available";
          }
          ?>
        </tbody>
      </table>
      <div class="footer">
        <p style="font-size:8px">WINDAS Tenant Web Portal may contain information that is created and managed by various sources, both internal and external. At no time shall WINDAS Building Management be responsible or liable, directly or indirectly, for any damage or loss resulting from or alleged to result from the use of or reliance on any such content in WINDAS Tenant Web Portal</p>
      </div> 
    </section>

  </body>
  </html>
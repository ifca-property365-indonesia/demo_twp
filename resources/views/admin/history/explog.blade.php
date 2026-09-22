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
    <section class="invoice">
      <h4>History User Login</h4><hr/>
      <table id="tbLog" class="table table-bordered table-striped" style="width:534px">
        <thead  style="background:#ffa500;">
          <tr class="odd">
          <th class="sorting" style="width:150px;">Login Date</th>
            <th class="sorting" >User Name</th>
            <th class="sorting" style="width:24px;">Login From</th>
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
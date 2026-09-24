<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>IFCA Software</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ url('assets/pdf/bootstrap.min.css');}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <!-- Theme -->
    <link href="{{ url('assets/pdf/skin-yellow.min.css'); }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/pdf/AdminLTE.min.css'); }}" rel="stylesheet" type="text/css" />
    <style type="text/css">.boxx{position: relative;width: 16px;height: 16px;border-radius: 3px;}thead:before, thead:after { display: none; }
tbody:before, tbody:after { display: none; }.footer{position: fixed; bottom: 0px; text-align: center;}</style>
  </head>
  <body>
    <section class="invoice">
      <h4 style="font-family: Tahoma!important;">{{ __('admin/dashboard.pdf_heading') }}</h4><hr/>
      <div class="col-sm-12">
        <img id="chart" style="width: 600px;" src="{{ $im }}" />
        <div id="legendDiv"></div>
      </div>        
      <hr>
      <table class="table table-bordered table-striped dataTable" role="grid">
        <thead style="background:#ffa500;">
          <tr>
            <th>{{ __('common.period') }}</th>
            <th>{{ __('admin/dashboard.pdf_lwbp') }}</th>
            <th>{{ __('admin/dashboard.pdf_wbp') }}</th>
          </tr>            
        </thead>
        <tbody>
            <?php echo $cl;?>
        </tbody>
      </table>
    </section>
    <div class="footer">
        <p style="font-size:8px">{{ __('admin/dashboard.pdf_disclaimer') }}</p>
      </div> 
  </body>
</html>

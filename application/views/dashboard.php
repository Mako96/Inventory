<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" style="background-color:white;">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
      <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <?php if ($is_admin == true) : ?>

      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $total_products ?></h3>

              <p>Total Products</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="<?php echo base_url('products/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_paid_orders ?></h3>

              <p>Total Placed Orders</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="<?php echo base_url('orders/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $total_users; ?></h3>

              <p>Total Users</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-people"></i>
            </div>
            <a href="<?php echo base_url('users/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo $total_locations ?></h3>

              <p>Total locations</p>
            </div>
            <div class="icon">
              <i class="ion ion-android-home"></i>
            </div>
            <a href="<?php echo base_url('locations/') ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
    <?php endif; ?>

    <?php if (count($requested_orders) != 0) : ?>
      <h1 class="text-center mb-3">Requested Orders</h1>
      <div class="row">
        <?php foreach ($requested_orders as $order => $o) : ?>
          <div class="item active" id="<?php echo $o['id'] ?>">
            <div class="col-lg-4 col-s-8 col-md-3">
              <!-- small box -->
              <div class="small-box bg-blue">
                <div class="inner">
                  <h4>Order No. <?php echo $o['order_no'] ?></h4>
                  <h5 style="position: absolute; top: 20px;  right: 10px; z-index: 0;" id="elapsed" class="display_time_elapsed"> <?php echo date('d-m-Y h:i a', $o['date_time']) ?></h5>
                </div>
                <?php if (!$o['accepted']) : ?>

                  <a href="<?php echo base_url('orders/update/' . $o['id']) ?>" class="small-box-footer">View and Accept Order <i class="fa fa-check-circle"></i></a>

                <?php endif; ?>
              </div>
            </div>
          </div>

        <?php endforeach ?>
      </div>
    <?php endif; ?>

    <h1 class="text mb-3">Receive Order</h1>
    <div class="form-group col-lg-3 col-xs-6">
      <label for="order_no">Enter Order Number :</label>
      <input type="text" class="form-control" id="order_no"></br>
      <button type="button" class="btn btn-primary" onclick="fetchReceivingOrderData()">Search</button>
    </div>


    <section id="invoice" style="display: none;" class="invoice col-lg-4 col-xs-6">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <!-- <small class="pull-right">Date: ' . $order_date . '</small> -->
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <h2 class="page-header">
        <center text-align:center>ORDER Details</center>
      </h2>
      <br /> <br />
      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped" id="order_table">
            <thead>
              <tr>
                <th>Product name</th>
                <th>Requested Qty</th>
                <th>Dispatched Qty</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->

    <section id="invoiceFactory" style="display: none;" class="invoice col-lg-4 col-xs-6">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <!-- <small class="pull-right">Date: ' . $order_date . '</small> -->
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <h2 class="page-header">
        <center text-align:center>ORDER Details</center>
      </h2>
      <br /> <br />
      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped" id="order_table_factory">
            <thead>
              <tr>
                <th>Product name</th>
                <th>Qty</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->

  </section>
  <!-- /.content -->
  <br /><br />
  <button id="confirm_order" type="button" style="display: none" class="btn btn-primary" onclick="receiveOrder()">Confirm Order</button>
</div>
<!-- /.content-wrapper -->

<script type="text/javascript">
  var base_url = "<?php echo base_url(); ?>";
  $(document).ready(function() {
    $("#dashboardMainMenu").addClass('active');
  });

  /*
   * It gets the product id and fetch the order data. 
   * The order print logic is done here 
   */

  function fetchReceivingOrderData() {
    var order_number = $('#order_no').val();
    $.ajax({
      url: base_url + 'orders/getReceivingOrderData/' + String(order_number),
      type: 'post',
      dataType: 'json',
      success: function(response) {
        var orderData = response.orderData;
        var orderItems = response.orderItems;
        console.log(response);
        console.log(orderData);
        console.log(orderItems);
        console.log(String(order_number).substring(0, 4));

        if (!String(order_number).includes("_")) {
          $('#invoice').show();
          $('#confirm_order').show();

          orderItems.forEach(element => {
            console.log(element);
            $appendData = '<tr><td>' + element.name + '</td>' +
              '<td>' + element.requested_qty + '</td>' +
              '<td>' + element.qty + '</td> </tr>';

            $('#order_table tbody').append($appendData);
          });
        } else if (String(order_number).includes("_")) {
          $('#invoiceFactory').show();
          $('#confirm_order').show();

          orderItems.forEach(element => {
            console.log(element);
            $appendData = '<tr><td>' + element.name + '</td>' +
              '<td>' + element.qty + '</td> </tr>';

            $('#order_table_factory tbody').append($appendData);
          });
        }
      } // /success
    }); // /ajax function to fetch the product data 
  }

  function receiveOrder() {
    var order_number = $('#order_no').val();
    $.ajax({
      url: base_url + 'orders/receiveOrder/' + order_number,
      type: 'post',
      dataType: 'json',
      success: function(response) {
        console.log('res=' + response);
        if (response) {
          $('#invoice').hide();
          $('#dispatched_invoice').hide();
          $('#confirm_order').hide();
          $('#order_no').val("");
          alert("Order Received! Stock has been updated");
          location.reload();
        } else {
          alert("Error occurred!! Possibly Receiving previously received order");
          location.reload();
        }

      }
    });
  }
</script>
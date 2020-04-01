<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Manage
      <small>Orders</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Orders</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if ($this->session->flashdata('success')) : ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif ($this->session->flashdata('error')) : ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>


        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Place Order</h3>

            <?php base_url('orders/create') ?>
          </div>

          <a id="btn_add_new_product" style="margin-left:10px;display:none;" class="btn btn-warning" target="_blank" href="<?php echo base_url('products/create') ?>">Add new product</a>
          <!-- /.box-header -->
          <form name="createOrderForm" role="form" action="<?php base_url('orders/create') ?>" method="post" class="form-horizontal">
            <input type="hidden" id='order_type' name="order_type" type='text'>
            <div class="box-body">

              <?php echo validation_errors(); ?>

              <div class="form-group">
                <label for="gross_amount" class="col-sm-12 control-label">Date: <?php echo date('Y-m-d') ?></label>
              </div>
              <div class="form-group">
                <label for="gross_amount" class="col-sm-12 control-label">Time: <?php echo date('h:i a') ?></label>
              </div>

              <div id="container_info" style="display:none;" class="col-md-4 col-xs-12 pull pull-left">

                <div class="form-group">
                  <label for="container_number" class="col-sm-5 control-label" style="text-align:left;">Container Number</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="container_number" name="container_number" placeholder="Enter Container Number" autocomplete="off" />
                  </div>
                </div>

                <div class="form-group">
                  <label for="container_description" class="col-sm-5 control-label" style="text-align:left;">Container Description</label>
                  <div class="col-sm-7">
                    <input type="text" class="form-control" id="continer_description" name="container_description" placeholder="Enter Container Description" autocomplete="off">
                  </div>
                </div>

                <div class="form-group">
                  <label for="container_size" class="col-sm-5 control-label" style="text-align:left;">Container Size</label>
                  <div class="col-sm-7">
                    <select class="form-control" name="container_size" id="container_size">
                      <option value=""></option>
                      <option value="20">20</option>
                      <option value="40">40</option>
                    </select>
                  </div>
                </div>
              </div>

              <br />
              <table class="table table-bordered" id="product_info_table">
                <thead>
                  <tr>
                    <th style="width:50%">Product</th>
                    <th style="width:10%">Qty</th>
                    <!-- <th style="width:10%">Rate</th> -->
                    <!-- <th style="width:20%">Amount</th> -->
                    <th style="width:10%"><button type="button" id="add_row" class="btn btn-default"><i class="fa fa-plus"></i></button></th>
                  </tr>
                </thead>

                <tbody>
                  <tr id="row_1">
                    <td>
                      <select class="form-control select_group product" data-row-id="row_1" id="product_1" name="product[]" style="width:100%;" onchange="getProductData(1)" required>
                        <option value=""></option>
                        <?php foreach ($products as $k => $v) : ?>
                          <option value="<?php echo $v['id'] ?>"><?php echo $v['name'] ?></option>
                        <?php endforeach ?>
                      </select>
                    </td>
                    <td><input type="number" name="qty[]" id="qty_1" class="form-control" required onkeyup=""></td>
                    <!-- <td>
                      <input type="text" name="rate[]" id="rate_1" class="form-control" disabled autocomplete="off">
                      <input type="hidden" name="rate_value[]" id="rate_value_1" class="form-control" autocomplete="off">
                    </td>
                    <td>
                      <input type="text" name="amount[]" id="amount_1" class="form-control" disabled autocomplete="off">
                      <input type="hidden" name="amount_value[]" id="amount_value_1" class="form-control" autocomplete="off">
                    </td> -->
                    <td><button type="button" class="btn btn-default" onclick="removeRow('1')"><i class="fa fa-close"></i></button></td>
                  </tr>
                </tbody>
              </table>

              <br /> <br />
            </div>
            <!-- /.box-body -->

            <div class="box-footer">

              <button type="submit" class="btn btn-primary">Create Order</button>
              <a href="<?php echo base_url('orders/') ?>" class="btn btn-warning">Back</a>
            </div>
          </form>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->

    <div id='loadingmessage' style='display:none;  width: 100%; height: 100%; top: 100px; left: 0px; position: fixed; z-index: 10000; text-align: center;'>
      <img src='/assets/images/ajax-loader.gif' width="45" height="45" alt="Loading..." style="position: fixed; top: 50%; left: 50%;" />
    </div>

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<div class="modal" id='secondModal' tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Select Type of Order : </h3>
        </button>
      </div>
      <form role="form" method="post" id="deliveryPaymentForm">

        <div class="modal-body">

          <!-- small box -->
          <div class="small-box bg-aqua" onclick='orderType(1)'>
            <div class="inner">
              <h3>In-Shop Order</h3>
            </div>
            <a class="small-box-footer"> <i class="fa fa-arrow-circle-right"></i></a>
          </div>

          <!-- ./col -->

          <!-- small box -->
          <div class="small-box bg-orange" onclick='orderType(2)'>
            <div class="inner">
              <h3>Warehouse Order</h3>
            </div>
            <a class="small-box-footer"> <i class="fa fa-arrow-circle-right"></i></a>
          </div>

          <!-- small box -->
          <div class="small-box bg-green" onclick='orderType(3)'>
            <div class="inner">
              <h3>New Factory Order</h3>
            </div>
            <a class="small-box-footer"> <i class="fa fa-arrow-circle-right"></i></a>
          </div>

          <!-- small box -->
          <div class="small-box bg-red" onclick='orderType(4)'>
            <div class="inner">
              <h3>Container Order</h3>
            </div>
            <a class="small-box-footer"> <i class="fa fa-arrow-circle-right"></i></a>
          </div>

          <!-- ./col -->
        </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
  var order_type = -1;
  var base_url = "<?php echo base_url(); ?>";

  $(document).ready(function() {
    $(".select_group").select2();
    // $("#description").wysihtml5();

    $("#mainOrdersNav").addClass('active');
    $("#addOrderNav").addClass('active');

    $("#secondModal").modal({
      backdrop: 'static',
      keyboard: false
    });
    $('#product_1').val(null).trigger('change');
    $('#qty_1').val('');
    $('#secondModal').modal('toggle');
    $('#secondModal').modal('show');

    var btnCust = '<button type="button" class="btn btn-secondary" title="Add picture tags" ' +
      'onclick="alert(\'Call your custom code here.\')">' +
      '<i class="glyphicon glyphicon-tag"></i>' +
      '</button>';

    // Add new row in the table 
    $("#add_row").unbind('click').bind('click', function() {
      var table = $("#product_info_table");
      var count_table_tbody_tr = $("#product_info_table tbody tr").length;
      var row_id = count_table_tbody_tr + 1;

      $.ajax({
        url: base_url + '/orders/getTableProductRow/',
        type: 'post',
        dataType: 'json',
        success: function(response) {

          // console.log(reponse.x);
          var html = '<tr id="row_' + row_id + '">' +
            '<td>' +
            '<select class="form-control select_group product" data-row-id="' + row_id + '" id="product_' + row_id + '" name="product[]" style="width:100%;" onchange="getProductData(' + row_id + ')">' +
            '<option value=""></option>';
          $.each(response, function(index, value) {
            html += '<option value="' + value.id + '">' + value.name + '</option>';
          });

          html += '</select>' +
            '</td>' +
            '<td><input type="number" name="qty[]" id="qty_' + row_id + '" class="form-control" onkeyup=""></td>' +
            // '<td><input type="text" name="rate[]" id="rate_' + row_id + '" class="form-control" disabled><input type="hidden" name="rate_value[]" id="rate_value_' + row_id + '" class="form-control"></td>' +
            // '<td><input type="text" name="amount[]" id="amount_' + row_id + '" class="form-control" disabled><input type="hidden" name="amount_value[]" id="amount_value_' + row_id + '" class="form-control"></td>' +
            '<td><button type="button" class="btn btn-default" onclick="removeRow(\'' + row_id + '\')"><i class="fa fa-close"></i></button></td>' +
            '</tr>';

          if (count_table_tbody_tr >= 1) {
            $("#product_info_table tbody tr:last").after(html);
          } else {
            $("#product_info_table tbody").html(html);
          }

          $(".product").select2();

        }
      });

      return false;
    });

  }); // /document

  function orderType(type) {
    //$(document).getElementById('createOrderForm').reset();
    $('#secondModal').modal('hide');
    var value = $('#order_type').val(type);
    if (type == '3') {
      $('#btn_add_new_product').show();
      $('#btn_refresh_product').show();
    } else if (type == '4') {
      $('#btn_add_new_product').show();
      $('#btn_refresh_product').show();
      $('#container_info').show();
    } else {
      $('#btn_add_new_product').hide();
      $('#btn_refresh_product').hide();
    }

  }



  // get the product information from the server
  function getProductData(row_id) {
    var product_id = $("#product_" + row_id).val();
    if (product_id == "") {
      // $("#rate_" + row_id).val("");
      // $("#rate_value_" + row_id).val("");

      $("#qty_" + row_id).val("");

      // $("#amount_" + row_id).val("");
      // $("#amount_value_" + row_id).val("");

    } else {
      $.ajax({
        url: base_url + 'orders/getProductValueById',
        type: 'post',
        data: {
          product_id: product_id
        },
        dataType: 'json',
        success: function(response) {
          // setting the rate value into the rate input field

          // $("#rate_" + row_id).val(response.price);
          // $("#rate_value_" + row_id).val(response.price);

          $("#qty_" + row_id).val(1);
          $("#qty_value_" + row_id).val(1);

          // var total = Number(response.price) * 1;
          // total = total.toFixed(2);
          // $("#amount_" + row_id).val(total);
          // $("#amount_value_" + row_id).val(total);

          // subAmount();
        } // /success
      }); // /ajax function to fetch the product data 
    }
  }


  // calculate the total amount of the order
  function subAmount() {
    var service_charge = <?php echo ($company_data['service_charge_value'] > 0) ? $company_data['service_charge_value'] : 0; ?>;
    var vat_charge = <?php echo ($company_data['vat_charge_value'] > 0) ? $company_data['vat_charge_value'] : 0; ?>;

    var tableProductLength = $("#product_info_table tbody tr").length;
    var totalSubAmount = 0;
    for (x = 0; x < tableProductLength; x++) {
      var tr = $("#product_info_table tbody tr")[x];
      var count = $(tr).attr('id');
      count = count.substring(4);

      totalSubAmount = Number(totalSubAmount) + Number($("#amount_" + count).val());
    } // /for

    totalSubAmount = totalSubAmount.toFixed(2);

    // sub total
    $("#gross_amount").val(totalSubAmount);
    $("#gross_amount_value").val(totalSubAmount);

    // vat
    var vat = (Number($("#gross_amount").val()) / 100) * vat_charge;
    vat = vat.toFixed(2);
    $("#vat_charge").val(vat);
    $("#vat_charge_value").val(vat);

    // service
    var service = (Number($("#gross_amount").val()) / 100) * service_charge;
    service = service.toFixed(2);
    $("#service_charge").val(service);
    $("#service_charge_value").val(service);

    // total amount
    var totalAmount = (Number(totalSubAmount) + Number(vat) + Number(service));
    totalAmount = totalAmount.toFixed(2);
    // $("#net_amount").val(totalAmount);
    // $("#totalAmountValue").val(totalAmount);

    var discount = $("#discount").val();
    if (discount) {
      var grandTotal = Number(totalAmount) - Number(discount);
      grandTotal = grandTotal.toFixed(2);
      $("#net_amount").val(grandTotal);
      $("#net_amount_value").val(grandTotal);
    } else {
      $("#net_amount").val(totalAmount);
      $("#net_amount_value").val(totalAmount);

    } // /else discount 

  } // /sub total amount

  function removeRow(tr_id) {
    $("#product_info_table tbody tr#row_" + tr_id).remove();
    subAmount();
  }
</script>
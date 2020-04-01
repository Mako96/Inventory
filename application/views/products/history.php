<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Product
      <small>History</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Products</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">
        <div class="box">
          <div class="box-header">
            <h1 class="box-title">Product Name : <?php echo $product_name ?>
              <input hidden id="product-id" value="<?php echo $product_id ?>">
            </h1>

          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="ui celled table">
              <thead>
                <tr>
                  <th>Date/Time</th>
                  <th>Order Type</th>
                  <th>Order Number</th>
                  <th>Qty</th>
                  <th>Order Placed by</th>
                </tr>
              </thead>

            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->


  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->


<script type="text/javascript">
  var manageTable;
  var base_url = "<?php echo base_url(); ?>";

  var productId = $("#product-id").val();
  console.log(productId);

  var url = base_url + 'products/getproducthistory/' + productId;
  console.log(url);

  $(document).ready(function() {

    $("#mainProductNav").addClass('active');

    // initialize the datatable 
    manageTable = $('#manageTable').DataTable({
      'ajax': url,
      'order': []
    });

  });
</script>
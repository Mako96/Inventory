<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->not_logged_in();

		$this->data['page_title'] = 'Orders';

		$this->load->model('model_orders');
		$this->load->model('model_products');
		$this->load->model('model_company');
	}

	/* 
	* It only redirects to the manage order page
	*/
	public function index()
	{
		if (!in_array('viewOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Manage Orders';
		$this->render_template('orders/index', $this->data);
	}

	/*
	* Fetches the orders data from the orders table 
	* this function is called from the datatable ajax function
	*/
	public function fetchOrdersData()
	{
		$result = array('data' => array());

		$data = $this->model_orders->getOrdersData();

		foreach ($data as $key => $value) {

			$count_total_item = $this->model_orders->countOrderItem($value['id']);
			$date = date('d-m-Y', $value['date_time']);
			$time = date('h:i a', $value['date_time']);

			$date_time = $date . ' ' . $time;

			// button
			$buttons = '';

			if (in_array('viewOrder', $this->permission)) {
				$buttons .= '<a target="__blank" href="' . base_url('orders/printDiv/' . $value['id']) . '" class="btn btn-default"><i class="fa fa-print"></i></a>';
			}

			// if (in_array('updateOrder', $this->permission)) {
			// 	// $buttons .= ' <a href="' . base_url('orders/update/' . $value['id']) . '" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			// 	$buttons .= ' <a href="" class="btn btn-default"><i class="fa fa-pencil"></i></a>';
			// }

			// if (in_array('deleteOrder', $this->permission)) {
			// 	$buttons .= ' <button type="button" class="btn btn-default" onclick="removeFunc(' . $value['id'] . ')" data-toggle="modal" data-target="#removeModal"><i class="fa fa-trash"></i></button>';
			// }

			// if ($value['paid_status'] == 1) {
			// 	$paid_status = '<span class="label label-success">Paid</span>';
			// } else {
			// 	$paid_status = '<span class="label label-warning">Not Paid</span>';
			// }

			if ($value['order_type'] == 1) {
				$order_type = '<span class="label bg-aqua">Shop</span>';
			} else if ($value['order_type'] == 2) {
				$order_type = '<span class="label label-warning">Warehouse</span>';
			} else if ($value['order_type'] == 3) {
				$order_type = '<span class="label bg-green">Factory</span>';
			} else if ($value['order_type'] == 4) {
				$order_type = '<span class="label bg-red">Container</span>';
			}

			$result['data'][$key] = array(
				$value['order_no'],
				$order_type,
				// $value['customer_name'],
				// $value['customer_phone'],
				$date_time,
				$count_total_item,
				// $value['net_amount'],
				//$paid_status,
				$buttons
			);
		} // /foreach

		echo json_encode($result);
	}

	/**
	 * Fetching the details of the order that is being received
	 */
	public function getReceivingOrderData($id)
	{
		$order_data = $this->model_orders->getReceivingOrdersData($id);
		$orders_items = $this->model_orders->getReceivingOrdersItemData($order_data['id']);
		echo json_encode(['orderData' => $order_data, 'orderItems' => $orders_items]);
	}

	/**
	 * Receiving order and changing status to received
	 */
	public function receiveOrder($order_number)
	{
		$response = $this->model_orders->receiveOrder($order_number);
		echo json_encode($response);
		// echo "<script>console.log('Debug Objects: " . $response . "' );</script>";
		// if ($response == false) {
		// 	echo "<script>alert('Order already  recieved');</script>";
		// 	$this->session->set_flashdata('errors', 'Error occurred!! Possibly accepting already received order');
		// 	redirect('dashboard/', 'refresh');
		// } else
		// 	redirect('dashboard/', 'refresh');
	}

	/*
	* If the validation is not valid, then it redirects to the create page.
	* If the validation for each input field is valid then it inserts the data into the database 
	* and it locations the operation message into the session flashdata and display on the manage group page
	*/
	public function create()
	{

		if (!in_array('createOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Add Order';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');


		if ($this->form_validation->run() == TRUE) {
			echo "<script>alert('Order taking time');</script>";

			$order_id = $this->model_orders->create();

			if ($order_id) {
				$this->session->set_flashdata('success', 'Successfully created');
				//redirect('dashboard/', 'refresh');
			} else {
				$this->session->set_flashdata('errors', 'Error occurred!! Possibly ordering more than available quantity');
				redirect('orders/create/', 'refresh');
			}
		} else {
			// false case
			$company = $this->model_company->getCompanyData(1);
			$this->data['company_data'] = $company;
			$this->data['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
			$this->data['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

			$this->data['products'] = $this->model_products->getActiveProductData();

			$this->render_template('orders/create', $this->data);
		}
	}

	/*
	* It gets the product id passed from the ajax method.
	* It checks retrieves the particular product data from the product id 
	* and return the data into the json format.
	*/
	public function getProductValueById()
	{
		$product_id = $this->input->post('product_id');
		if ($product_id) {
			$product_data = $this->model_products->getProductData($product_id);
			echo json_encode($product_data);
		}
	}

	public function debug_to_console($data)
	{
		$output = $data;
		if (is_array($output))
			$output = implode(',', $output);

		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}

	/*
	* It gets the all the active product inforamtion from the product table 
	* This function is used in the order page, for the product selection in the table
	* The response is return on the json format.
	*/
	public function getTableProductRow()
	{
		$products = $this->model_products->getActiveProductData();
		echo json_encode($products);
	}

	/*
	* If the validation is not valid, then it redirects to the edit orders page 
	* If the validation is successfully then it updates the data into the database 
	* and it locations the operation message into the session flashdata and display on the manage group page
	*/
	public function update($id)
	{
		if (!in_array('updateOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if (!$id) {
			redirect('dashboard', 'refresh');
		}

		$this->data['page_title'] = 'Update Order';

		$this->form_validation->set_rules('product[]', 'Product name', 'trim|required');


		if ($this->form_validation->run() == TRUE) {

			$update = $this->model_orders->update($id);

			if ($update == true) {
				$this->session->set_flashdata('success', 'Successfully Accepted');
				redirect('orders/' . $id, 'refresh');
			} else {
				$this->session->set_flashdata('errors', 'Error occurred!! Possibly ordering more than available quantity');
				redirect('orders/update/' . $id, 'refresh');
			}
		} else {
			// false case
			$company = $this->model_company->getCompanyData(1);
			$this->data['company_data'] = $company;
			$this->data['is_vat_enabled'] = ($company['vat_charge_value'] > 0) ? true : false;
			$this->data['is_service_enabled'] = ($company['service_charge_value'] > 0) ? true : false;

			$result = array();
			$orders_data = $this->model_orders->getOrdersData($id);

			$result['order'] = $orders_data;
			$orders_item = $this->model_orders->getOrdersItemData($orders_data['id']);
			//var_dump($orders_item);

			foreach ($orders_item as $k => $v) {
				$result['order_item'][] = $v;
			}

			$this->data['order_data'] = $result;

			$this->data['products'] = $this->model_products->getActiveProductData();
			$this->data['orders_data'] = $orders_data;
			$this->render_template('orders/edit', $this->data);
		}
	}

	/*
	* It removes the data from the database
	* and it returns the response into the json format
	*/
	public function remove()
	{
		if (!in_array('deleteOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		$order_id = $this->input->post('order_id');

		$response = array();
		if ($order_id) {
			$delete = $this->model_orders->discardOrder($order_id);
			if ($delete == true) {
				$response['success'] = true;
				$response['messages'] = "Successfully removed";
			} else {
				$response['success'] = false;
				$response['messages'] = "Error in the database while removing the product information";
			}
		} else {
			$response['success'] = false;
			$response['messages'] = "Refresh the page again!!";
		}

		echo json_encode($response);
	}

	/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/
	public function printDiv($id)
	{
		if (!in_array('viewOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if ($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y h:i a', $order_data['date_time']);

			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="' . base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') . '">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="' . base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') . '">
			  <link rel="stylesheet" href="' . base_url('assets/dist/css/AdminLTE.min.css') . '">
			</head>
			<body onload="window.print();">
			
			<div class="wrapper">
			  <section class="invoice">
			    <!-- title row -->
			    <div class="row">
			      <div class="col-xs-12">
			        <h2 class="page-header">
			          ' . $company_info['company_name'] . '
					  <small class="pull-right">' . $order_date . '</small>
					  <br>
					  <h5>P.O.BOX:5404</h3>
					  <h5>DAR ES SALAAM, TANZANIA</h3>
					  <h5>TIN 102-873-246</h3>
					  <h5>VRN 10017395U</h3>';
			if ($order_data['driver_name'] != "") {
				$html .= '<b>Driver Name : </b>' . $order_data['driver_name'] . '</br>';
				$html .= '<b>Vehicle Number : </b>' . $order_data['vehicle_number'] . '</br>';
			} else if ($order_data['container_number'] != "" &&  $order_data['container_size'] != null) {
				$html .= '<b>Container Number : </b>' . $order_data['container_number'] . '</br>';
				$html .= '<b>Container Size : </b>' . $order_data['container_size'] . '</br>';
				$html .= '<b>Container Description : </b>' . $order_data['container_description'] . '</br>';
			}
			$html .= '
			        </h2>
			      </div>
			      <!-- /.col -->
				</div>
				<h2 class="page-header">
			           <center text-align:center>DELIVERY ORDER - STOCK TRANSFER</center>
			        </h2>
			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-sm-4 invoice-col">
			        
			        <h3>Order ID: ' . $order_data['order_no'] . '</h3><br>
			      </div>
			      <!-- /.col -->
			    </div>
				<!-- /.row -->
				
				
			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
			            <th>Product name</th>
			            <th>Quantity</th>
			          </tr>
			          </thead>
			          <tbody>';

			foreach ($orders_items as $k => $v) {

				$product_data = $this->model_products->getProductData($v['product_id']);

				$html .= '<tr>
				            <td>' . $product_data['name'] . '</td>
				            <td>' . $v['qty'] . '</td>
			          	</tr>';
			}

			$html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			echo $html;
		}
	}
	/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/
	public function printDivStore($id)
	{
		if (!in_array('viewOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if ($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y h:i a', $order_data['date_time']);

			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="' . base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') . '">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="' . base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') . '">
			  <link rel="stylesheet" href="' . base_url('assets/dist/css/AdminLTE.min.css') . '">
			</head>
			
			<body onload="window.print();">
			
			<div class="wrapper">
			  <section class="invoice">
			    <!-- title row -->
			    <div class="row">
			      <div class="col-xs-12">
			        <h2 class="page-header">
			          ' . $company_info['company_name'] . '
					  <small class="pull-right">' . $order_date . '</small>
			        </h2>
			      </div>
			      <!-- /.col -->
				</div>
				<h2 class="page-header">
			           <center text-align:center>ORDER SHEET</center>
			        </h2>
			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-sm-4 invoice-col">
			        
			        <h3>Order ID: ' . $order_data['order_no'] . '</h3><br>
			      </div>
			      <!-- /.col -->
			    </div>
				<!-- /.row -->
				
				
			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
			            <th>Product name</th>
			            <th>Quantity</th>
			          </tr>
			          </thead>
			          <tbody>';

			foreach ($orders_items as $k => $v) {

				$product_data = $this->model_products->getProductData($v['product_id']);

				$html .= '<tr>
				            <td>' . $product_data['name'] . '</td>
				            <td>' . $v['qty'] . '</td>
			          	</tr>';
			}

			$html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			echo $html;
		}
	}

	/*
	* It gets the product id and fetch the order data. 
	* The order print logic is done here 
	*/
	public function printDivContainer($id)
	{
		if (!in_array('viewOrder', $this->permission)) {
			redirect('dashboard', 'refresh');
		}

		if ($id) {
			$order_data = $this->model_orders->getOrdersData($id);
			$orders_items = $this->model_orders->getOrdersItemData($id);
			$company_info = $this->model_company->getCompanyData(1);

			$order_date = date('d/m/Y h:i a', $order_data['date_time']);

			$html = '<!-- Main content -->
			<!DOCTYPE html>
			<html>
			<head>
			  <meta charset="utf-8">
			  <meta http-equiv="X-UA-Compatible" content="IE=edge">
			  <title>Invoice</title>
			  <!-- Tell the browser to be responsive to screen width -->
			  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
			  <!-- Bootstrap 3.3.7 -->
			  <link rel="stylesheet" href="' . base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css') . '">
			  <!-- Font Awesome -->
			  <link rel="stylesheet" href="' . base_url('assets/bower_components/font-awesome/css/font-awesome.min.css') . '">
			  <link rel="stylesheet" href="' . base_url('assets/dist/css/AdminLTE.min.css') . '">
			</head>
			
			<body onload="window.print();">
			
			<div class="wrapper">
			  <section class="invoice">
			    <!-- title row -->
			    <div class="row">
			      <div class="col-xs-12">
			        <h2 class="page-header">
			          ' . $company_info['company_name'] . '
					  <small class="pull-right">' . $order_date . '</small>
					</h2>
					</br> 
					<h5>Container Number : ' . $order_data['container_number'] . '</h5>
					<h5>Container Size : ' . $order_data['container_size'] . '</h5>
					<h5>Container Description : ' . $order_data['container_description'] . '</h5>
			      </div>
			      <!-- /.col -->
				</div>
				<h2 class="page-header">
			           <center text-align:center>ORDER SHEET</center>
			        </h2>
			    <!-- info row -->
			    <div class="row invoice-info">
			      
			      <div class="col-sm-4 invoice-col">
			        
			        <h3>Order ID: ' . $order_data['order_no'] . '</h3><br>
			      </div>
			      <!-- /.col -->
			    </div>
				<!-- /.row -->
				
				
			    <!-- Table row -->
			    <div class="row">
			      <div class="col-xs-12 table-responsive">
			        <table class="table table-striped">
			          <thead>
			          <tr>
			            <th>Product name</th>
			            <th>Quantity</th>
			          </tr>
			          </thead>
			          <tbody>';

			foreach ($orders_items as $k => $v) {

				$product_data = $this->model_products->getProductData($v['product_id']);

				$html .= '<tr>
				            <td>' . $product_data['name'] . '</td>
				            <td>' . $v['qty'] . '</td>
			          	</tr>';
			}

			$html .= '</tbody>
			        </table>
			      </div>
			      <!-- /.col -->
			    </div>
			    <!-- /.row -->
			  </section>
			  <!-- /.content -->
			</div>
		</body>
	</html>';

			echo $html;
		}
	}
}

<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//include("application\utils\Log.php");


class Model_orders extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the orders data */
	public function getOrdersData($id = null)
	{
		if ($id) {
			$sql = "SELECT * FROM orders WHERE id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM orders ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// get the orders item data
	public function getOrdersItemData($order_id = null)
	{
		if (!$order_id) {
			return false;
		}

		$sql = "SELECT * FROM orders_item WHERE order_id = ?";
		$query = $this->db->query($sql, array($order_id));
		return $query->result_array();
	}

	/* get the orders data */
	public function getReceivingOrdersData($id = null)
	{
		if ($id) {
			$sql = "SELECT * FROM orders WHERE order_no = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM orders ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	// get the orders item data
	public function getReceivingOrdersItemData($order_id = null)
	{
		if (!$order_id) {
			return false;
		}
		$sql = "SELECT orders_item.requested_qty,orders_item.qty,products.name FROM orders_item,products WHERE orders_item.order_id = ? and (orders_item.product_id = products.id)";
		$query = $this->db->query($sql, array($order_id));
		return $query->result_array();
	}

	/* get the requested orders data */
	public function getRequestedOrdersData()
	{
		$sql = "SELECT orders.id,orders.order_no,orders.date_time,users.firstname,users.lastname,orders.accepted FROM orders,users WHERE orders.order_type = 2 and orders.received <> 1 and orders.user_id = users.id";
		$query = $this->db->query($sql);
		$requestedorders =  $query->result_array();
		return $requestedorders;
	}

	public function create()
	{
		$user_id = $this->session->userdata('id');
		if ($this->input->post('order_type') == 1 || $this->input->post('order_type') == 2) {
			$order_no = 'AKB-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
		} else if ($this->input->post('order_type') == 3) {
			$order_no = 'AKB_FA-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
		} else if ($this->input->post('order_type') == 4) {
			$order_no = 'AKB_CO-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 10));
		}

		$data = array(
			'order_no' => $order_no,
			// 'customer_name' => $this->input->post('customer_name'),
			// 'customer_address' => $this->input->post('customer_address'),
			// 'customer_phone' => $this->input->post('customer_phone'),
			'date_time' => strtotime(date('Y-m-d h:i:s a')),
			// 'gross_amount' => $this->input->post('gross_amount_value'),
			// 'service_charge_rate' => $this->input->post('service_charge_rate'),
			// 'service_charge' => ($this->input->post('service_charge_value') > 0) ?$this->input->post('service_charge_value'):0,
			// 'vat_charge_rate' => $this->input->post('vat_charge_rate'),
			// 'vat_charge' => ($this->input->post('vat_charge_value') > 0) ? $this->input->post('vat_charge_value') : 0,
			// 'net_amount' => $this->input->post('net_amount_value'),
			// 'discount' => $this->input->post('discount'),
			// 'paid_status' => 2,
			'user_id' => $user_id,
			'order_type' => $this->input->post('order_type'),
			'requested' => 1,
			'accepted' => 0,
			'received' => 0,
			'container_number' => $this->input->post('container_number'),
			'container_description' => $this->input->post('container_description'),
			'container_size' => $this->input->post('container_size')
		);

		$insert = $this->db->insert('orders', $data);
		$order_id = $this->db->insert_id();

		// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode('order type=' .$this->input->post('order_type') . ',order id=' . $order_id) . PHP_EOL, FILE_APPEND);

		$this->load->model('model_products');

		$count_product = count($this->input->post('product'));


		$errorMessage = [];
		if ($this->input->post('order_type') == 1) {
			for ($i = 0; $i < $count_product; $i++) {
				//get product info
				$productId = $this->input->post('product')[$i];
				$productQty = $this->input->post('qty')[$i];
				//get the product stock
				$product_data = $this->model_products->getProductData($this->input->post('product')[$i]);
				$productStock = (int) $product_data['qty_store'];
				$productName = $product_data['name'];
				if (($productStock - $productQty) < 0) {
					$message = ("Product:" . $productName . ', ' . "              Available quantity:" . $productStock
						. "             Requested quantity:" . $productQty . ', ' . " ERROR: Insufficient quantity");
					array_push($errorMessage, $message);
				}
			}
		}
		if (count($errorMessage) != 0) {
			$displayMsg = '';
			foreach ($errorMessage as $message) {
				$displayMsg .= $message . '<br>';
			}

			die($displayMsg);
			return;
		}
		// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode($this->input->post('product')) . PHP_EOL, FILE_APPEND);
		// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode('count_product=' . $count_product) . PHP_EOL, FILE_APPEND);
		for ($x = 0; $x < $count_product; $x++) {
			// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode('x=' . $x) . PHP_EOL, FILE_APPEND);
			$product_data = $this->model_products->getProductData($this->input->post('product')[$x]);
			if ($this->input->post('order_type') == 1) {
				$balance_whouse = $product_data['qty_warehouse'];
				$balance_str = (int) $product_data['qty_store'] - (int) $this->input->post('qty')[$x];
			} else if ($this->input->post('order_type') == 2) {
				$balance_whouse = $product_data['qty_warehouse'];
				$balance_str = (int) $product_data['qty_store'];
			} else if ($this->input->post('order_type') == 3) {
				$balance_whouse = $product_data['qty_warehouse'];
				$balance_str = (int) $product_data['qty_store'];
			} else if ($this->input->post('order_type') == 4) {
				$balance_whouse = (int) $product_data['qty_warehouse'] + (int) $this->input->post('qty')[$x];
				$balance_str = $product_data['qty_store'];
			}
			$items = array(
				'order_id' => $order_id,
				'product_id' => $this->input->post('product')[$x],
				'qty' => $this->input->post('qty')[$x],
				'requested_qty' => $this->input->post('qty')[$x],
				'balance_whouse' => $balance_whouse,
				'balance_str' => $balance_str,
				// 'rate' => $this->input->post('rate_value')[$x],
				// 'amount' => $this->input->post('amount_value')[$x],
			);

			$this->db->insert('orders_item', $items);

			// now decrease the stock from the product


			// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("product Data below x=" . $x . ', p id=' . $this->input->post('product')[$x]) . PHP_EOL, FILE_APPEND);
			// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode($product_data) . PHP_EOL, FILE_APPEND);

			if ($this->input->post('order_type') == 1) {
				$qty = (int) $product_data['qty_store'] - (int) $this->input->post('qty')[$x];
				$update_product = array('qty_store' => $qty);
				$this->model_products->update($update_product, $this->input->post('product')[$x]);
			} else if ($this->input->post('order_type') == 2) {
			} else if ($this->input->post('order_type') == 3) {
			} else if ($this->input->post('order_type') == 4) {
				// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode($product_data) . PHP_EOL, FILE_APPEND);
				$newQuantity = (int) $product_data['qty_warehouse'] + (int) $this->input->post('qty')[$x];
				$update_product = array('qty_warehouse' => $newQuantity);
				$this->model_products->update($update_product, $this->input->post('product')[$x]);
			}
		}

		if ($this->input->post('order_type') == 1) {
			redirect('orders/printDivStore/' . $order_id);
		} else if ($this->input->post('order_type') == 2) {
			redirect('dashboard/');
		} else if ($this->input->post('order_type') == 3) {
			redirect('orders/printDiv/' . $order_id);
		} else if ($this->input->post('order_type') == 4) {
			redirect('orders/printDivContainer/' . $order_id);
		}

		return ($order_id) ? $order_id : false;
	}

	public function countOrderItem($order_id)
	{
		if ($order_id) {
			$sql = "SELECT * FROM orders_item WHERE order_id = ?";
			$query = $this->db->query($sql, array($order_id));
			return $query->num_rows();
		}
	}

	public function update($id)
	{
		if ($id) {
			//	file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("id=" . $id), FILE_APPEND);

			$user_id = $this->session->userdata('id');



			// now the order item 
			$this->load->model('model_products');

			$order_type = $this->getOrderType($id);


			if ($order_type == 2) {
				$get_order_item = $this->getOrdersItemData($id);
				//first for loop is validating that the quantity does not fall below 0
				$index = 0;
				foreach ($get_order_item as $k => $v) {
					$product_id = $v['product_id'];

					$dispatched_qty = $this->input->post('qty')[$index];
					//file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("disp qty=" . $dispatched_qty) . PHP_EOL, FILE_APPEND);
					$product_data = $this->model_products->getProductData($product_id);

					$productName = $product_data['name'];
					$productQuantity = $product_data['qty_warehouse'];


					if ($productQuantity - $dispatched_qty < 0) {
						die("Product:" . $productName . PHP_EOL . "      Available quantity:" . $productQuantity . PHP_EOL . "    Requested quantity:" . $dispatched_qty . PHP_EOL . "INVALID OPERATION");
						return false;
					}
					$index++;
				}

				//2nd for loop updates that order table and the order_item table
				$index = 0;
				foreach ($get_order_item as $k => $v) {
					$product_id = $v['product_id'];
					$dispatched_qty = $this->input->post('qty')[$index];
					//file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("disp qty zz=" . $dispatched_qty) . PHP_EOL, FILE_APPEND);
					$product_data = $this->model_products->getProductData($product_id);
					$productName = $product_data['name'];
					$productQuantity = $product_data['qty_warehouse'];
					$remainingQuantity = $productQuantity - $dispatched_qty;
					$product_data = $this->model_products->getProductData($product_id); //qty,
					$update_product_data = array('qty_warehouse' => $remainingQuantity); //balance

					//----------------------------
					// update the product qty
					$this->model_products->update($update_product_data, $product_id);


					$productId = $this->input->post('product')[$index];
					$balance_whouse = (int) $product_data['qty_warehouse'] - (int) $dispatched_qty;
					$balance_str = $product_data['qty_store'];
					$items = array(
						'order_id' => $id,
						'product_id' => $this->input->post('product')[$index],
						'qty' => $dispatched_qty,
						'balance_whouse' => $balance_whouse,
						'balance_str' => $balance_str,
						// 'rate' => $this->input->post('rate_value')[$x],
						// 'amount' => $this->input->post('amount_value')[$x],
					);

					$query = "UPDATE orders_item SET qty=? , balance_whouse = ? , balance_str = ? WHERE product_id=? AND order_id = ?";
					$query = $this->db->query($query, array($dispatched_qty, $balance_whouse, $balance_str, $productId, $id));

					$index++;
				}
				$data = array(
					'driver_name' => $this->input->post('driver_name'),
					'vehicle_number' => $this->input->post('vehicle_number'),
					//'driver_phone' => $this->input->post('driver_phone'),
					'accepted' => 1,
					'user_id' => $user_id
				);

				$this->db->where('id', $id);
				$update = $this->db->update('orders', $data);
			}
			return;
		}
	}

	public function receiveOrder($number)
	{
		if ($number) {
			$sql2 = "SELECT id,received,order_type from orders where order_no = ?";
			$query2  = $this->db->query($sql2, array($number));
			$result2 = $query2->result_array();
			//file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("receive order called for order id : " . $result2[0]['id']) . "" . PHP_EOL, FILE_APPEND);
			$get_order_item = $this->getOrdersItemData($result2[0]['id']);
			// file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("Accepted = " . $result2[0]['order_type']) . "" . PHP_EOL, FILE_APPEND);
			if ($result2[0]['received'] != 1) {
				$index = 0;
				foreach ($get_order_item as $k => $v) {
					$product_id = $v['product_id'];
					$dispatched_qty = $v['qty'];
					$product_data = $this->model_products->getProductData($product_id);
					if ($result2[0]['order_type'] == 2) {
						$productQuantity = $product_data['qty_store'];
						$newQuantity = $productQuantity + $dispatched_qty;
						$product_data = $this->model_products->getProductData($product_id); //qty,
						$update_product_data = array('qty_store' => $newQuantity);

						$balance_str = $newQuantity;
						$sql = "UPDATE orders_item set balance_str = ? where id = ?";
						$this->db->query($sql, array($balance_str, $v['id']));
					} else if ($result2[0]['order_type'] == 3) {
						$productQuantity = $product_data['qty_warehouse'];
						$newQuantity = $productQuantity + $dispatched_qty;
						$product_data = $this->model_products->getProductData($product_id); //qty,
						$update_product_data = array('qty_warehouse' => $newQuantity);

						$balance_whouse = $newQuantity;
						$sql = "UPDATE orders_item set balance_whouse = ? , balance_str = ?  where id = ?";
						$this->db->query($sql, array($balance_whouse, $product_data['qty_store'], $v['id']));
					}

					// update the product qty and balance quantity in order_items
					//----------------------------
					$this->model_products->update($update_product_data, $product_id);
					$index++;
				}
				$sql = "UPDATE orders set received = 1 where order_no = ?";
				$query = $this->db->query($sql, array($number));
				return true;
			} else if ($result2[0]['received'] == 1) {
				//file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("Received for order id : " . $result2[0]['id']) . "" . PHP_EOL, FILE_APPEND);
				//$this->session->set_flashdata('errors', 'Error occurred!! Possibly Receiving previously received order');
				//redirect('dashboard/');
				// die("The order " . $number . " has already been received!! 		INVALID OPERATION");
				return false;
			}
		}
	}

	public function discardOrder($id)
	{
		if ($id) {
			$this->db->where('order_id', $id);
			$delete_item = $this->db->delete('orders_item');


			$this->db->where('id', $id);
			$delete = $this->db->delete('orders');
			return ($delete && $delete_item);
		}
	}

	public function getOrderType($id)
	{
		if ($id) {
			$sql = "select order_type from orders where id = ?";
			$query = $this->db->query($sql, array($id));
			$result = $query->row_array();
			return $result['order_type'];
		}
	}

	public function countTotalPaidOrders()
	{
		$sql = "SELECT * FROM orders ";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}

	public function getProductQuantity($productId)
	{
		//file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode("inside ger prod method" . $productId) . "" . PHP_EOL, FILE_APPEND);

		$sql = "SELECT name,qty_warehouse FROM products where id = ?";
		$query = $this->db->query($sql, array($productId));
		$result = $query->result_array();
		//file_put_contents('/Users/murtazababrawala/Desktop/Projects/log_file.txt', json_encode('val=' . $result[0]['qty_warehouse']) . "" . PHP_EOL, FILE_APPEND);
		return $result[0]['qty_warehouse'];
	}
}

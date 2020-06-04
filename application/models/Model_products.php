<?php

class Model_products extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* get the brand data */
	public function getProductData($id = null)
	{
		if ($id) {
			$sql = "SELECT * FROM products where id = ?";
			$query = $this->db->query($sql, array($id));
			return $query->row_array();
		}

		$sql = "SELECT * FROM products ORDER BY id DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getActiveProductData()
	{
		$sql = "SELECT * FROM products WHERE availability = ? ORDER BY id DESC";
		$query = $this->db->query($sql, array(1));
		return $query->result_array();
	}

	public function getProductHistory($id = null)
	{
		$sql = "SELECT orders.date_time,orders.order_type, products.name as product_name, orders.order_no, products.qty_warehouse as qty_whouse,products.qty_store as qty_str, orders_item.qty, orders_item.balance_whouse, orders_item.balance_str, users.firstname FROM orders_item inner join orders on orders_item.order_id = orders.id inner join products on orders_item.product_id = products.id inner join users on orders.user_id = users.id where products.id = ? ORDER BY orders.id DESC";
		$query = $this->db->query($sql, array($id));
		return $query->result_array();
	}

	public function create($data)
	{
		if ($data) {
			$insert = $this->db->insert('products', $data);
			return ($insert == true) ? true : false;
		}
	}

	public function update($data, $id)
	{
		if ($data && $id) {
			$this->db->where('id', $id);
			$update = $this->db->update('products', $data);
			return ($update == true) ? true : false;
		}
	}

	public function remove($id)
	{
		if ($id) {
			$this->db->where('id', $id);
			$delete = $this->db->delete('products');
			return ($delete == true) ? true : false;
		}
	}

	public function countTotalProducts()
	{
		$sql = "SELECT * FROM products";
		$query = $this->db->query($sql);
		return $query->num_rows();
	}
}

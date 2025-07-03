<?php


class Cart
{
	
	protected $cartId;

	
	protected $cartMaxItem = 0;

	
	protected $itemMaxQuantity = 0;

	
	protected $useCookie = false;

	
	private $items = [];

	
	public function __construct($options = [])
	{
		if (!session_id()) {
			session_start();
		}

		if (isset($options['cartMaxItem']) && preg_match('/^\d+$/', $options['cartMaxItem'])) {
			$this->cartMaxItem = $options['cartMaxItem'];
		}

		if (isset($options['itemMaxQuantity']) && preg_match('/^\d+$/', $options['itemMaxQuantity'])) {
			$this->itemMaxQuantity = $options['itemMaxQuantity'];
		}

		if (isset($options['useCookie']) && $options['useCookie']) {
			$this->useCookie = true;
		}

		$this->cartId = md5((isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : 'SimpleCart') . '_cart';

		$this->read();
	}

	
	public function getItems()
	{
		return $this->items;
	}

	
	public function isEmpty()
	{
		return empty(array_filter($this->items));
	}

	
	public function getTotalItem()
	{
		$total = 0;

		foreach ($this->items as $items) {
			foreach ($items as $item) {
				++$total;
			}
		}

		return $total;
	}

	
	public function getTotalQuantity()
	{
		$quantity = 0;

		foreach ($this->items as $items) {
			foreach ($items as $item) {
				$quantity += $item['quantity'];
			}
		}

		return $quantity;
	}


	public function getAttributeTotal($attribute = 'price')
	{
		$total = 0;

		foreach ($this->items as $items) {
			foreach ($items as $item) {
				if (isset($item['attributes'][$attribute])) {
					$total += $item['attributes'][$attribute] * $item['quantity'];
				}
			}
		}

		return $total;
	}

	
	public function clear()
	{
		$this->items = [];
		$this->write();
	}

	
	public function isItemExists($id, $attributes = [])
	{
		$attributes = (is_array($attributes)) ? array_filter($attributes) : [$attributes];

		if (isset($this->items[$id])) {
			$hash = md5(json_encode($attributes));
			foreach ($this->items[$id] as $item) {
				if ($item['hash'] == $hash) {
					return true;
				}
			}
		}

		return false;
	}

	
	public function getItem($id, $hash = null)
	{
		if($hash){
			$key = array_search($hash, array_column($this->items[$id], 'hash'));
			if($key !== false)
				return $this->items[$id][$key];
			return false;
		}
		else
			return reset($this->items[$id]);
	}

	public function add($id, $quantity = 1, $attributes = [])
	{
		$quantity = (preg_match('/^\d+$/', $quantity)) ? $quantity : 1;
		$attributes = (is_array($attributes)) ? array_filter($attributes) : [$attributes];
		$hash = md5(json_encode($attributes));

		if (count($this->items) >= $this->cartMaxItem && $this->cartMaxItem != 0) {
			return false;
		}

		if (isset($this->items[$id])) {
			foreach ($this->items[$id] as $index => $item) {
				if ($item['hash'] == $hash) {
					$this->items[$id][$index]['quantity'] += $quantity;
					$this->items[$id][$index]['quantity'] = ($this->itemMaxQuantity < $this->items[$id][$index]['quantity'] && $this->itemMaxQuantity != 0) ? $this->itemMaxQuantity : $this->items[$id][$index]['quantity'];

					$this->write();

					return true;
				}
			}
		}

		$this->items[$id][] = [
			'id'         => $id,
			'quantity'   => ($quantity > $this->itemMaxQuantity && $this->itemMaxQuantity != 0) ? $this->itemMaxQuantity : $quantity,
			'hash'       => $hash,
			'attributes' => $attributes,
		];

		$this->write();

		return true;
	}


	public function update($id, $quantity = 1, $attributes = [])
	{
		$quantity = (preg_match('/^\d+$/', $quantity)) ? $quantity : 1;

		if ($quantity == 0) {
			$this->remove($id, $attributes);

			return true;
		}

		if (isset($this->items[$id])) {
			$hash = md5(json_encode(array_filter($attributes)));

			foreach ($this->items[$id] as $index => $item) {
				if ($item['hash'] == $hash) {
					$this->items[$id][$index]['quantity'] = $quantity;
					$this->items[$id][$index]['quantity'] = ($this->itemMaxQuantity < $this->items[$id][$index]['quantity'] && $this->itemMaxQuantity != 0) ? $this->itemMaxQuantity : $this->items[$id][$index]['quantity'];

					$this->write();

					return true;
				}
			}
		}

		return false;
	}

	
	public function remove($id, $attributes = [])
	{
		if (!isset($this->items[$id])) {
			return false;
		}

		if (empty($attributes)) {
			unset($this->items[$id]);

			$this->write();

			return true;
		}
		$hash = md5(json_encode(array_filter($attributes)));

		foreach ($this->items[$id] as $index => $item) {
			if ($item['hash'] == $hash) {
				unset($this->items[$id][$index]);
				$this->items[$id] = array_values($this->items[$id]);

				$this->write();

				return true;
			}
		}

		return false;
	}

	public function destroy()
	{
		$this->items = [];

		if ($this->useCookie) {
			setcookie($this->cartId, '', -1);
		} else {
			unset($_SESSION[$this->cartId]);
		}
	}


	private function read()
	{
		$this->items = ($this->useCookie) ? json_decode((isset($_COOKIE[$this->cartId])) ? $_COOKIE[$this->cartId] : '[]', true) : json_decode((isset($_SESSION[$this->cartId])) ? $_SESSION[$this->cartId] : '[]', true);
	}

	
	private function write()
	{
		if ($this->useCookie) {
			setcookie($this->cartId, json_encode(array_filter($this->items)), time() + 604800, "/");
		} else {
			$_SESSION[$this->cartId] = json_encode(array_filter($this->items));
		}
	}
}
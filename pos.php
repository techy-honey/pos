<?php

class POS{

private $products = array();
private $price_file_path = 'productprices.json';
/** scaning products */
public function scan($item)
{
	if(isset($this->products[$item]))
		$this->products[$item]++;
	else
		$this->products[$item] = 1;
}

/** evaluating total price */
public function total()
{
	$total_price = 0;
	$prices = $this->getPricing();
	foreach($this->products as $product=>$count)
	{
		$product_price_total = 0;
		if(isset($prices[$product]))
		{
			$volumes = array_keys($prices[$product]);
			$volume = max($volumes);
			$volume_price = $prices[$product][$volume];
			$unit_price = $prices[$product][1];
			$product_volume_price_total = floor($count/$volume) * $volume_price;
			$product_unit_price_total = ($count % $volume) * $unit_price;
			$product_price_total = $product_volume_price_total + $product_unit_price_total;
			$total_price += $product_price_total; 
		}
	}
	return $total_price;
}
/** setting the price of the products */
public function setPricing($item, $price, $volume = 1)
{
	$prices_json = file_get_contents($this->price_file_path);
	$prices_array = json_decode($prices_json,true);
	if(!is_array($prices_array))
		$prices_array = array();
	$prices_array[$item][$volume] = $price;
	$updated_prices_json = json_encode($prices_array);
	file_put_contents($this->price_file_path, $updated_prices_json);
}

/** getting the price of the product*/
public function getPricing()
{
	$prices_json = file_get_contents($this->price_file_path);
	$prices_array = json_decode($prices_json,true);
	return $prices_array;
}

}

$terminal = new POS();
$terminal->setPricing('A',2,1);
$terminal->setPricing('A',7,4);
$terminal->setPricing('B',12,1);
$terminal->setPricing('C',1.25,1);
$terminal->setPricing('C',6,6);
$terminal->setPricing('D',0.15,1);

$scanned_products = $_POST['product_txt'];
$scanned_products_array = str_split($scanned_products);
foreach($scanned_products_array as $product)
{
	$terminal->scan($product);
}

$result = $terminal->total();
echo "$".number_format($result,2);


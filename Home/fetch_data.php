<?php

class BestBuyAPI {
    private $api_key;
    private $base_url;

    public function __construct($api_key) {
        $this->api_key = $api_key;
        $this->base_url = "https://api.bestbuy.com/v1/products";
    }

    private function fetchProducts($category_id) {
        $url = $this->base_url . "(categoryPath.id={$category_id})?format=json&show=sku,name,image&sort=bestSellingRank&apiKey={$this->api_key}&pageSize=10";
        $response = file_get_contents($url);
        return json_decode($response, true);
    }

    public function getPhones() {
        $phones = $this->fetchProducts('pcmcat209400050001');
        return isset($phones['products']) ? $phones['products'] : [];
    }

    public function getLaptops() {
        $laptops = $this->fetchProducts('abcat0502000');
        return isset($laptops['products']) ? $laptops['products'] : [];
    }

    public function getProducts() {
        return json_encode([
            'phones' => $this->getPhones(),
            'laptops' => $this->getLaptops()
        ]);
    }
}

$api_key = "q4QsfQfONWJSYrz1933Higf3";
$bestBuyAPI = new BestBuyAPI($api_key);
echo $bestBuyAPI->getProducts();

?>

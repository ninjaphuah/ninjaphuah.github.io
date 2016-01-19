<?php

require 'vendor/autoload.php';
require 'model/productModel.php';

use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParseClient;

final class parseController {

    function parseXML() {
        $brand = "";
        $rakutens = simplexml_load_file('sample_rakuten.xml');
        foreach ($rakutens->product as $product) {

            $prod = new productModel();
            $prod->name = $product['name'];
            $prod->sku = $product['sku_number'];

            //Category
            $category = $product->category;
            $prod->primary = $category->primary;
            $prod->secondary = $category->secondary;

            //URL
            $url = $product->url;
            $prod->productUrl = $url->product;
            $prod->productImage = $url->productImage;
            $prod->buyLink = $url->buyLink;

            //description
            $description = $product->description;
            $prod->short_description = $description->short;
            $prod->long_description = $description->long;

            //price
            $price = $product->price;
            $prod->price_currency = $price['currency'];
            $prod->retail = $price->retail;
            $prod->final_price = $prod->retail;
            $prod->discount_amount = 0;

            //discount
            $discount = $product->discount;
            $prod->discount_currency = $discount['currency'];

            $discount_type = $discount->type;
            if ($discount_type == "amount") {
                $prod->discount_amount = $discount->amount;
                if ($prod->discount_amount > 0) {
                    $prod->final_price = $prod->retail - $prod->discount_amount;
                }
            } else {
                
            }

            //brand
            $brandItem = $product->brand;
            //todo replaced this with brand
            //get brands
            $query = new ParseQuery("Brand");

            // Get a specific object:
            //$object = $query->get("anObjectId");
            $query->limit(100); // default 100, max 1000
            //$query->equalTo("name", "");
            // All results:
            $results = $query->find();
            echo "<br />\n";
            echo "Successfully retrieved " . count($results) . " objects.";
            for ($i = 0; $i < count($results); $i++) {
                $object = $results[$i];
                echo $object->getObjectId() . ' - ' . $object->get('name');
                echo "<br />\n";
            }

            //Category
            //
                
            //************************  
            //Query SKU
            //************************
            echo "<br />\n Querying product " . $prod->sku;
            echo "<br />\n";

            $query = new ParseQuery("Rakuten_Product");
            $query->equalTo("sku", (string) $prod->sku);
            $object = $query->first();
            if (count($object)>0) {
                
                $this->updateProduct($prod,$object);
            } else {
                $this->createProduct($prod);
            }
            
        }
    }

//end function

    function createProduct($prod) {
        echo "<br />\n";
        echo 'Start creating product';
        $rakuten_product = new ParseObject("Rakuten_Product");
        // Set values
        $rakuten_product->set("sku", (string) $prod->sku);
        $rakuten_product->set("title", (string) $prod->name);
        $rakuten_product->set("imageUrl", (string) $prod->productImage);
        $rakuten_product->set("info", (string) $prod->long_description);
        $rakuten_product->set("affiliateUrl", (string) $prod->productUrl);
        $rakuten_product->set("price", floatval($prod->final_price));
        $rakuten_product->set("MSRP", floatval($prod->retail));
        try {
            echo "<br />\n";
            echo "Start Saving product. <br />\n";
            $rakuten_product->save();
            echo "End saving.";
            echo 'New object created with objectId: ' . $rakuten_product->getObjectId();
        } catch (Exception $ex) {
            echo "<br />\n";
            echo "Error saving";
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
        // Save:
        $newId = $rakuten_product->getObjectId();
        //save to product images
    }

//end create function

    function updateProduct($prod,$rakuten_product) {
        echo "<br />\n";
        echo $prod->name . "exists, updating...";
                
        // Set values
        $rakuten_product->set("sku", (string) $prod->sku);
        $rakuten_product->set("title", (string) $prod->name);
        $rakuten_product->set("imageUrl", (string) $prod->productImage);
        $rakuten_product->set("info", (string) $prod->long_description);
        $rakuten_product->set("affiliateUrl", (string) $prod->productUrl);
        $rakuten_product->set("price", floatval($prod->final_price));
        $rakuten_product->set("MSRP", floatval($prod->retail));
        try {
            echo "<br />\n";
            echo "Start updating product. <br />\n";
            $rakuten_product->save();
            echo "End saving.";
        } catch (Exception $ex) {
            echo "<br />\n";
            echo "Error saving";
            echo 'Failed to create new object, with error message: ' . $ex->getMessage();
        }
    }

//end update function
}

?>
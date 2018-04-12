# magento2-product-rules-price
<h2>Features:</h2>
This module reveals which catalog rules are applied to a product.<br/>
You can see it from admin area in the product sheet's "Product Rules Price" section.
<br/>
<h2>Composer Installation Instructions</h2>
Since this package is in a development stage, you will need to change the minimum-stability as well to the composer.json file:
<pre>
"minimum-stability": "dev",
</pre>

After that, need to install this module as follows:
<pre>
  composer require magento/magento-composer-installer
  composer require simone-baldini/product-rules-price
</pre>

<h2> Manual installation instructions</h2>
go to magento 2 project root dir and create following directory structure:<br/>
<strong>/app/code/SimoneBaldini/ProductRulesPrice</strong>
<br/>
<h2> Enable SimoneBaldini/ProductRulesPrice module</h2>
to enable this module you need to follow these steps:

<ul>
<li>
<strong>Enable the module</strong>
<pre>bin/magento module:enable SimoneBaldini_ProductRulesPrice</pre></li>
<li>
<strong>Run setup upgrade</strong>
<pre>bin/magento setup:upgrade</pre></li>
<li>
<strong>Re-compile (in-case you have compilation enabled)</strong>
	<pre>bin/magento setup:di:compile</pre>
</li>
</ul>

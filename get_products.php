<?php
	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	session_start();
	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;
	require __DIR__.'/conf.php';
	require __DIR__.'/func.php';
	
	//$shopify = shopify\client($_SESSION['shop'], SHOPIFY_APP_API_KEY, $_SESSION['oauth_token']);
	$shopify = shopify\client('logicats-demo.myshopify.com', SHOPIFY_APP_API_KEY, '84d6afd04f4486aca0f9d44dc884aed1');
	try
	{
		# Making an API request can throw an exception
		$products = $shopify('GET /admin/products.json', array('published_status'=>'published'));
		pr($products);
	}
	catch (shopify\ApiException $e)
	{
		# HTTP status code was >= 400 or response contained the key 'errors'
		echo $e;
		print_r($e->getRequest());
		print_r($e->getResponse());
	}
	catch (shopify\CurlException $e)
	{
		# cURL error
		echo $e;
		print_r($e->getRequest());
		print_r($e->getResponse());
	}
?>
	 <div class="product-template" itemscope itemtype="http://schema.org/Product" id="ProductSection-{{ section.id }}" data-section-id="{{ section.id }}" data-section-type="product" data-enable-history-state="true">

{% assign header_title = product.title %}
{% include 'page_header' with header_title %}

<div class="wrapper wrapper--margins">

  <div class="product grid">

    {% assign current_variant = product.selected_or_first_available_variant %}
    {% assign featured_image = current_variant.featured_image | default: product.featured_image %}

    <meta itemprop="url" content="{{ shop.url }}{{ product.url }}">
    <meta itemprop="image" content="{{ product.featured_image.src | img_url: 'grande' }}">

    <div class="product__image grid__item one-half medium-down--one-whole">
      <div id="slider-{{ section.id }}" class="regular-slider flexslider">
        <ul class="slides">
          {% if product.images.size > 0 %}
            {%- assign img_url = featured_image | img_url: '1x1' | replace: '_1x1.', '_{width}x.' -%}
                <li class="slide" data-index="0" data-variant-img="{{ featured_image.id }}">
                  <a class="image-popup">
                  <div class="lazyload__image-wrapper supports-no-js" style="padding-top:{{ 1 | divided_by: featured_image.aspect_ratio | times: 100}}%;">
                    <img class="no-js lazyload"
                    src="{{ featured_image | img_url: '300x300' }}"
                    data-src="{{ img_url }}"
                    data-widths="[180, 360, 540, 720, 900, 1080, 1296, 1512, 1728, 2048]"
                    data-aspectratio="{{ featured_image.aspect_ratio }}"
                    data-sizes="auto"
                    data-parent-fit="width"
                    alt="{{ featured_image.alt | escape }}">
                    </div>
                    <noscript>
                      <img src="{{ featured_image | img_url: '1024x1024' }}"
                        srcset="{{ featured_image | img_url: '1024x1024' }} 1x, {{ featured_image | img_url: '1024x1024', scale: 2 }} 2x"
                        alt="{{ featured_image.alt }}" style="opacity:1;">
                        <style>
                          .flexslider .slides li {
                            display: block;
                          }
                        </style>
                    </noscript>
                  </a>
                </li>
                {% if product.images.size > 1 %}
                  {% assign index = 0 %}
                  {% for image in product.images %}
                  {%- assign img_url = image | img_url: '1x1' | replace: '_1x1.', '_{width}x.' -%}
                    {% unless image contains featured_image %}
                      {% assign index = index | plus: 1 %}
                      <li class="slide" data-index="{{ index }}" data-variant-img="{{ image.id }}">
                        <a class="image-popup">
                        <div class="lazyload__image-wrapper supports-no-js" style="padding-top:{{ 1 | divided_by: image.aspect_ratio | times: 100}}%;">
                          <img class="no-js lazyload"
                          data-src="{{ img_url }}"
                          data-widths="[320, 360, 375, 414, 568, 684, 720, 732, 736, 768, 1024, 1200, 1296, 1512, 1728, 1944, 2048, 4472]"
                          data-aspectratio="{{ image.aspect_ratio }}"
                          data-sizes="auto"
                          data-parent-fit="width"
                          alt="{{ image.alt | escape }}">
                          </div>
                        </a>
                      </li>
                    {% endunless %}
                  {% endfor %}
                {% endif %}
          {% else %}
            <li class="slide">
              {{ 'product-1' | placeholder_svg_tag: 'placeholder-svg' }}
            </li>
          {% endif %}
        </ul>
      </div>
    </div>

    <div class="product__content grid__item one-half medium-down--one-whole">

      <h2 class="secondary-title hidden">{{ product.title }}</h2>

      <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

        <meta itemprop="priceCurrency" content="{{ shop.currency }}">

        <link itemprop="availability" href="http://schema.org/{% if product.available %}InStock{% else %}OutOfStock{% endif %}">

        <form action="/cart/add" method="post" enctype="multipart/form-data" id="AddToCartForm">

          {% if product.compare_at_price_max > product.price %}
            <span id="ComparePriceA11y" class="visually-hidden">{{ 'products.product.sale_price' | t }}</span>
          {% else %}
            <span class="visually-hidden">{{ 'products.product.regular_price' | t }}</span>
          {% endif %}
          <span id="ProductPrice-{{ section.id }}" class="h1 price" itemprop="price" content="{{ current_variant.price | divided_by: 100.00 }}">
            {{ current_variant.price | money }}
          </span>

          {% if product.compare_at_price_max > product.price %}
            <span class="visually-hidden">{{ 'products.product.regular_price' | t }}</span>
            <p id="ComparePrice-{{ section.id }}" class="h3 price compare-price">
              {{ current_variant.compare_at_price | money }}
            </p>
          {% endif %}

          <div class="form__row">

      	      <select name="id" id="productSelect-{{ section.id }}" class="product-single__variants">
      	        {% for variant in product.variants %}
      	          {% if variant.available %}

      	            <option {% if variant == current_variant %} selected="selected" {% endif %} data-sku="{{ variant.sku }}" value="{{ variant.id }}">{{ variant.title }} - {{ variant.price | money_with_currency }}</option>

      	          {% else %}
      	            <option disabled="disabled">
      	              {{ variant.title }} - {{ 'products.product.sold_out' | t }}
      	            </option>
      	          {% endif %}
      	        {% endfor %}
      	      </select>

      		    <div id="quantity-selector-{{ section.id }}" class="form__column quantity-selector">
      			    <label for="Quantity" class="quantity-selector">{{ 'products.product.quantity' | t }}</label>
      			    <input type="number" id="Quantity" name="quantity" value="1" min="1" class="qty-remove-defaults quantity-selector">
      		    </div>

              <div class="form__column">
              <label>&nbsp;</label>
                <button type="submit" name="add" id="AddToCart-{{ section.id }}" class="btn btn--fill btn--regular btn--color">
                  <span id="AddToCartText-{{ section.id }}">{{ 'products.product.add_to_cart' | t }}</span>
                </button>
              </div>

          </div>

        </form>

      </div>

      {% if section.settings.social_sharing_products %}
        {% include 'page_share' %}
      {% endif %}

      <div class="product-description rte" itemprop="description">
        {{ product.description }}
      </div>

    </div>

  </div>

 </div>
</div>

{% unless product == empty %}
  <script type="application/json" id="ProductJson-{{ section.id }}">
    {{ product | json }}
  </script>
{% endunless %}

{% schema %}
  {
    "name": "Product pages",
    "settings": [
      {
        "type": "checkbox",
        "id": "social_sharing_products",
        "label": "Enable product sharing",
        "default": true
      }
    ]
  }
{% endschema %}


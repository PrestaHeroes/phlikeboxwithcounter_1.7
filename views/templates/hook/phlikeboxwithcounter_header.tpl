{**
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement.
*
* You must not modify, adapt or create derivative works of this source code
*
*  @author    PrestaHeroes <info@prestaheroes.com>
*  @copyright 2016 Heroic Business Solutions LLC
*  @license   LICENSE.txt
*}

<meta property="og:type" content="product" />
{*<meta property="og:url" content="{$ph_product_link}" />*}
<meta property="og:url" content="{$urls.current_url}" />
<meta property="og:title" content="{$page.meta.title}" />
<meta property="og:description" content="{$page.meta.description}" />
{if $ph_cover_img && $ph_cover_img != null}
     <meta property="og:image" content="{$ph_cover_img}" />
{else}
     <meta property="og:image" content="{$product.cover.large.url}" />
{/if}
<div id="fb-root"></div>

<script type="text/javascript">
     ph_language_iso_code = "{$ph_language_iso_code|escape:'html':'UTF-8'}";
</script>

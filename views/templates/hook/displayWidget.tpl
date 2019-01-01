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

<div class="fb-like"
     data-href="{$urls.current_url}"
     data-layout="{if $type_button}{$type_button|lower|escape:'htmlall':'UTF-8'}{else}box_count{/if}"
     data-action="{if $type_action}{$type_action|lower|escape:'htmlall':'UTF-8'}{else}like{/if}"
     data-size="{if $box_size}{$box_size|lower|escape:'htmlall':'UTF-8'}{else}small{/if}"
     data-show-faces="{if $show_faces}{$show_faces|lower|escape:'htmlall':'UTF-8'}{else}false{/if}"
     data-share="{if $share_button}{$share_button|lower|escape:'htmlall':'UTF-8'}{else}false{/if}">
</div>
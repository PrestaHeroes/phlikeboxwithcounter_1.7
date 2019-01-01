/**
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
*/

$(document).ready(function() {
    initfbLikeBox(document, 'script', 'facebook-jssdk');
});

function initfbLikeBox(d, s, id)
{
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.async = true;
    js.src = 'https://connect.facebook.net/'+ph_language_iso_code+'/sdk.js#xfbml=1&version=v3.2&autoLogAppEvents=1';
    fjs.parentNode.insertBefore(js, fjs);
}
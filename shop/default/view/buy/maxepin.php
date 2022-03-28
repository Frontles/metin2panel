<link rel="stylesheet" href="https://api2.maxepin.com/modal/jquery.remodal.css?v=1">
<script type="text/javascript" src="https://api2.maxepin.com/modal/jquery.remodal.js"></script>


<div class="content clearfix" id="wt_refpoint">
    <div id="category">

        <h2>
            <ul class="breadcrumb">
                <li>
                    <a href="#" title="En gözdeler">Maxepin Ödeme Yöntemi</a>
                </li>
            </ul>
        </h2>

        <div class="tabbable tabs-left">
            <ul id="subnavi" class="nav nav-tabs">
				<?php if (\StaticDatabase\StaticDatabase::settings('paywant_status')):?>
                    <li class="has-subnavi2">
                        <a class="btn-catitem-active" href="<?=URI::get_path('buy/paywant')?>">
                            <img style="width: 82px;" src="<?=URI::public_path('images/paywant.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
				<?php if (\StaticDatabase\StaticDatabase::settings('sanalpay_status')):?>
                    <li class="has-subnavi2">
                        <a class="btn-catitem-active" href="<?=URI::get_path('buy/sanalpay')?>">
                            <img style="width: 82px;" src="<?=URI::public_path('images/sanalpay.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
				<?php if (\StaticDatabase\StaticDatabase::settings('teckcard_status')):?>
                    <li class="has-subnavi2">
                        <a class="btn-catitem-active" href="<?=URI::get_path('buy/teckcard')?>">
                            <img style="width: 75px;" src="<?=URI::public_path('images/teckcard.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
				<?php if (\StaticDatabase\StaticDatabase::settings('max_epin_status')):?>
                    <li class="has-subnavi2">
                        <a class="btn-catitem-active" href="<?=URI::get_path('buy/maxepin')?>">
                            <img style="width: 70px;" src="<?=URI::public_path('images/maxepin.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
				<?php if (\StaticDatabase\StaticDatabase::settings('itemci_status')):?>
                    <li class="has-subnavi2" id="itemci">
                        <a class="btn-catitem-active" href="<?=\StaticDatabase\StaticDatabase::settings('itemci_link')?>" target="_blank">
                            <img style="width: 78px;" src="<?=URI::public_path('images/itemci.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
				<?php if (\StaticDatabase\StaticDatabase::settings('oyunalisverisi_status')):?>
                    <li class="has-subnavi2" id="oyunalisveris">
                        <a class="btn-catitem-active" href="<?=\StaticDatabase\StaticDatabase::settings('oyunalisverisi_link')?>" target="_blank">
                            <img style="width: 78px;" src="<?=URI::public_path('images/oyunalisveris_logo.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
				<?php if (\StaticDatabase\StaticDatabase::settings('itemsultan_status')):?>
                    <li class="has-subnavi2" id="itemsultan">
                        <a class="btn-catitem-active" href="<?=\StaticDatabase\StaticDatabase::settings('itemsultan_link')?>" target="_blank">
                            <img style="width: 78px;" src="<?=URI::public_path('images/itemsultan.png')?>" class="icon"></a>
                    </li>
				<?php endif;?>
                <script>
                    $(document).ready(function () {
                        var genislik = $(window).width();
                        if (genislik < 801) {
                            document.getElementById('itemci').style.display = 'none';
                        }
                    });
                </script>
            </ul>
            <div class="tab-content">

                <div class="scrollable_container row-fluid">
                    <!--CONTENT BURAYA-->
                    <img src="https://developers.maxepin.com/b-2.png" alt="" style="width: 75%;margin-left: auto;margin-right: auto;display: block;">

                    <div class="paywant animated infinite pulse" align="center" style="margin-top: 50px;"><a href='maxepin#maxepinModal' ><img style="margin-left:-7px"src="http://developers.maxepin.com/b-1.png " border="0"/></a></div>

                    <div class="remodal" data-remodal-id="maxepinModal">
                        <div class="login-body">
							<?php
							if($_SESSION["aId"] != "")
							{
								$loginBul = $_SESSION["aId"];
								$sorgu = \StaticGame\StaticGame::sql("SELECT id,login FROM account WHERE id = ?",[$loginBul]);
								if(count($sorgu) > 0 ){
								    $getAccountInfo = $sorgu[0];
									$merchant_id  = \StaticDatabase\StaticDatabase::settings('max_epin_id');
									$merchant_key = \StaticDatabase\StaticDatabase::settings('max_epin_key');
									$return_id	= base64_encode($getAccountInfo['id'].'|'.$getAccountInfo['login'].'|'.$merchant_id.'|'.$merchant_key);

									date_default_timezone_set('Europe/Istanbul');


									function paywantAntiInjection($sql){
										$sql 			= preg_replace(@sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
										$sql 			= trim($sql);
										$sql 			= strip_tags($sql);
										$sql 			= addslashes($sql);
										return $sql;
									}


									$postData = array(
										'merchant_id' => paywantAntiInjection($merchant_id),
										'merchant_key' => paywantAntiInjection($merchant_key),
										'return_id'=> paywantAntiInjection($return_id)
									);
									$postData = http_build_query($postData);



									$curl = curl_init();

									curl_setopt_array($curl, array(
										CURLOPT_URL => "http://api2.maxepin.com/",
										CURLOPT_RETURNTRANSFER => true,
										CURLOPT_ENCODING => "",
										CURLOPT_MAXREDIRS => 10,
										CURLOPT_TIMEOUT => 30,
										CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										CURLOPT_CUSTOMREQUEST => "POST",
										CURLOPT_POSTFIELDS => $postData,
									));

									$response = curl_exec($curl);
									$err = curl_error($curl);

									curl_close($curl);

									if ($err) {
										echo "cURL Error #:" . $err;
									} else {
										$jsonDecode = json_decode($response);
										$jsonStatus = isset($jsonDecode->status) ? $jsonDecode->status : null;
										if($jsonStatus == 100)
										{
											?>

                                            <iframe seamless="seamless" id="maxEpinFrame" style="display:block; width:800px; height:489px;" frameborder="0" scrolling='yes' src="<?php echo $jsonDecode->link?>" id='odemeFrame'></iframe>
                                            <script type="text/javascript">
                                                (function (wd, doc) {
                                                    var w = wd.innerWidth || doc.documentElement.clientWidth;
                                                    var h = wd.innerHeight || doc.documentElement.clientHeight;
                                                    var screenSize = {w: w, h: h};
                                                    if (screenSize.w > 0 && screenSize.w < 801) {
                                                        document.getElementById('maxEpinFrame').style.width = '650px';
                                                    }
                                                })(window, document);
                                            </script>
											<?php

										}else{
											echo $response;
										}
									}
								}else{
									echo "Bu alanı sadece giriş yapmış kullanıcılarımız görebilir.";
								}

							}else{
								echo "Bu alanı sadece giriş yapmış kullanıcılarımız görebilir.";
							}
							?>
                        </div>
                    </div>
                    <br class="clearfloat">
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        // click on currency dropdown
        $('a[data-selected-currency]').click(function (ev) {
            ev.preventDefault();

            var currency = $(this).data('selectedCurrency');
            if ('' !== currency) {
                // user has clicked on "currency anzeigen und merken"

                // hide or show "In der gemerkten Währung gibt es keine Artikel" text
                $('#category h2.js_currency').hide();
                $('#category h2[data-currency=' + currency + ']').show();

                // hide all articles
                $('#category li.js_currency').hide().removeClass('shown');
                // show articles of selected currency
                $('#category li[data-currency*="' + currency + '"]').show().addClass('shown');

                // hide all quickbuy buttons
                $('#category li.js_currency > div > div > div.price_desc').hide().removeClass('currency-show');
                // show quickbuy button of selected currency, remove "Artikel gibt es nicht in der gewünschten Währung" title
                $('#category li.js_currency > div > div > div.price_desc[data-currency=' + currency + ']').show().addClass('currency-show').find('a').removeAttr('title');

                // hide all banderoles
                $('#category li.js_currency > p.item-status').hide().removeClass('currency-show');
                // show banderole of selected currency
                $('#category li.js_currency > p.item-status[data-currency=' + currency + ']').show().addClass('currency-show');

                // change image and text of "Sie haben folgende Währung gewählt: "
                /*
                 $('p.selected-currency img').attr('src', zs.data.currencies[currency]['image']);
                 $('p.selected-currency img').attr('alt', zs.data.currencies[currency]['loca']);
                 $('p.selected-currency img').attr('title', zs.data.currencies[currency]['loca']);
                 $('p.selected-currency span').text(zs.data.currencies[currency]['loca']);
                 */

                /*
                 // update currecy icons on header
                 $('#header .currency_status li.selected-currency')
                 .removeClass('selected-currency')
                 .attr('data-toggle', 'popover');
                 $('#header .currency_status li[data-currency=' + currency + ']')
                 .addClass('selected-currency')
                 .attr('data-toggle', '');
                 */

            } else {
                // user has clicked on "alle Währungen anzeigen"

                // hide "In der gemerkten Währung gibt es keine Artikel" text
                $('h2.js_currency').hide();

                // show all articles
                $('li.js_currency').show().addClass('shown');

                // remove all "Artikel gibt es nicht in der gewünschten Währung" titles
                $('li.js_currency > div > div > div.price_desc > a').removeAttr('title');
                // hide all quickbuy buttons
                $('li.js_currency > div > div > div.price_desc').hide();

                // hide all banderoles
                $('li.js_currency > p.item-status').hide();

                // show the last selected currency banderole and quickbuy button if it exists,
                // the default currency banderole and quickbuy button otherwise
                $('li.js_currency').each(function (i, li) {
                    if ($(li).find('div.price_desc.currency-show').size() > 0) {
                        $(li).find('.currency-show').show();
                    }
                    else {
                        $(li).find('.js_currency_default').show();
                        $(li).find('div.price_desc.js_currency_default > a').attr('title', "Bu eşya seçilen birimde mevcut değil.");
                    }
                });
            }

            // recalculate article card margins
            cardMargin();

            // replace dropdown text with selected text
            $('#currencydropdown span:first').html($(this).html());

            // recalculate amount of shown vs. total article count
            // for breadcrumb
            var breadcrumbtext = zs.data.categoryArticleCount.total;
            var shownarticles = $('#category .card li.span4.shown').size();
            if (shownarticles != breadcrumbtext) {
                breadcrumbtext = '(' + shownarticles + '/' + breadcrumbtext + ')';
            } else {
                breadcrumbtext = '(' + breadcrumbtext + ')';
            }
            $('ul.breadcrumb li:last .item_count').text(breadcrumbtext);

            // and for every subcategory
            var subcategorytext = 0;
            $(zs.data.subcategoryIds).each(function (i, id) {
                subcategorytext = zs.data.categoryArticleCount[id];
                shownarticles = $('#ul_sub_' + id + ' li.span4.shown').size();
                if (shownarticles != subcategorytext) {
                    subcategorytext = '(' + shownarticles + '/' + subcategorytext + ')';
                }
                else {
                    subcategorytext = '(' + subcategorytext + ')';
                }
                $('#h3_sub_' + id + ' .item_count').text(subcategorytext);
            });
        });


        $('.article-limit-counter').each(function () {
            var elem = $(this),
                seconds = elem.data('seconds');

            elem.countdown({
                until: seconds,
                format: 'dHMS',
                compact: true,
                onExpiry: function () {
                    window.location.href = window.location.href;
                }
            });
        });

        // load the article images
        window.onload = function () {
            var images = document.querySelectorAll('img.lazy-loading[lazy-src]');

            if (images && images.length > 0) {
                for (var i = 0, len = images.length; i < len; i = i + 1) {
                    var img = images[i];
                    img.setAttribute('src', img.getAttribute('lazy-src'));

                    // debug lazy loaded images
                    //img.style.border = '2px solid #FF0A5B';
                }
            }
        };

    </script>
</div>
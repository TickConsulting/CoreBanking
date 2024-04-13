<div class="hero-wrap inner_page short">
    <div class="row m-0">
    <div class="container">
        <div class="">
            <div class="overlay"></div>
            <div class="circle-bg"></div>
            <div class="circle-bg-2"></div>
            <div class="circle-bg-3"></div>
            <div class="container-fluid pt-2">
                <div class="row no-gutters d-flex slider-text align-items-center justify-content-center" data-scrollax-parent="true">
                    <div class="col-md-12 ftco-animate text-left" data-scrollax=" properties: { translateY: '70%' }">
                        <!-- <p class="breadcrumbs" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><span class="mr-2"><a href="<?php echo site_url(''); ?>"><?php echo translate('Home'); ?></a></span> <span><?php echo translate('Features'); ?></span></p> -->
                        <h1 class="mb-3 bread magic_hdr" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"><?php echo $this->application_settings->application_name; ?> <?php echo translate('Pricing'); ?></h1>
                        <p data-scrollax="properties: { translateY: '30%', opacity: 1.6 }" style="margin-top:-20px;"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<section class="ftco-section inner_page_cont px-4 pt-5 pb-5" style="z-index:1;">
    <div class="container px-4 ftco-animate fadeInUp ftco-animated" style="margin-top:-120px;">
        <div class="row d-md-flex bg-white- p-" style="border-radius:4px;box-shadow:0px 0px 20px 0px rgba(0, 0, 0, 0);">
            <div class="col-md-4">
                <div class="block-7">
                    <div class="text-center">
                        <h2 class="heading"><?php echo translate('On-cloud Solution'); ?></h2>
                        <span class="price"><sup><?php echo $this->default_country->currency_code; ?></sup> <span class="number">100</span></span>
                        <span class="excerpt d-block"><?php echo translate('Up to 250 members'); ?></span>
                        <a href="<?php echo site_url('signup'); ?>" class="btn btn-primary d-block px-3 py-3 mb-4"><?php echo translate('Get Started'); ?></a>
                        
                        <!-- <h3 class="heading-2 mb-3">Enjoy All The Features</h3> -->
                        
                        <ul class="pricing-text">
                            <!-- <li><strong><?php echo translate('Every member above 250'); ?>, </strong> <?php echo $this->default_country->currency_code; ?> 50</li> -->
                            <li><strong><?php echo $this->default_country->currency_code; ?></strong> 50,000 <strong>optional setup fee</strong></li>
                            <li>Android<strong> app for members</strong></li>
                            <li>USSD<strong> platform available</strong></li>
                            <li>Dedicated<strong> support</strong></li>
                        </ul>
                        <a href="<?php echo site_url('solutions/on-cloud-sacco-solution'); ?>" class="btn btn-primary btn-sm d-block- px-3 py- mt-4"><?php echo translate('Learn more'); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="block-7">
                    <div class="text-center">
                        <h2 class="heading"><?php echo translate('On-cloud Solution'); ?> <strong>*Custom</strong></h2>
                        <span class="price"><sup><?php echo $this->default_country->currency_code; ?></sup> <span class="number">*</span></span>
                        <span class="excerpt d-block"><?php echo translate('Above 250 members'); ?></span>
                        <a href="<?php echo site_url('demo'); ?>" class="btn btn-primary d-block px-3 py-3 mb-4"><?php echo translate('Get Started'); ?></a>
                        
                        <!-- <h3 class="heading-2 mb-3">Enjoy All The Features</h3> -->
                        
                        <ul class="pricing-text">
                            <!-- <li><strong><?php echo translate('Every member above 250'); ?>, </strong> <?php echo $this->default_country->currency_code; ?> 50</li> -->
                            <li><strong><?php echo $this->default_country->currency_code; ?></strong> 50,000 <strong>optional setup fee</strong></li>
                            <li>Android<strong> app for members</strong></li>
                            <li>USSD<strong> platform available</strong></li>
                            <li>Dedicated<strong> support</strong></li>
                        </ul>
                        <a href="<?php echo site_url('solutions/on-cloud-sacco-solution'); ?>" class="btn btn-primary btn-sm d-block- px-3 py- mt-4"><?php echo translate('Learn more'); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="block-7">
                    <div class="text-center">
                        <h2 class="heading"><?php echo translate('On-premise Solution'); ?></h2>
                        <span class="price"><sup><?php echo $this->default_country->currency_code; ?></sup> <span class="number">1,000,000</span></span>
                        <span class="excerpt d-block"><?php echo translate('One off fee'); ?></span>
                        <a href="<?php echo site_url('demo'); ?>" class="btn btn-primary d-block px-3 py-3 mb-4"><?php echo translate('Get Started'); ?></a>
                        
                        <!-- <h3 class="heading-2 mb-3">Enjoy All The Features</h3> -->
                        
                        <ul class="pricing-text">
                            <!-- <li><strong><?php echo translate('Every member above 250'); ?>, </strong> <?php echo $this->default_country->currency_code; ?> 50</li> -->
                            <li><strong><?php echo $this->default_country->currency_code; ?></strong> 50,000<strong> installation fee</strong></li>
                            <li><strong><?php echo $this->default_country->currency_code; ?></strong> 100,000<strong> annual support fee</strong></li>
                            <li><small><strong><?php echo $this->default_country->currency_code; ?></strong> 50,000<strong> PayBill integration ~ Optional</strong></small></li>
                            <li>&nbsp;</li>
                        </ul>
                        <a href="<?php echo site_url('solutions/on-premise-sacco-solution'); ?>" class="btn btn-primary btn-sm d-block- px-3 py- mt-4"><?php echo translate('Learn more'); ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mb-5 pb-5 mt-5 d-none">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <h2 class="mb-0"><?php echo translate('Price Calculator'); ?></h2>
                <span class="subheading"><?php echo translate("Use the calculator below to find out your investment group's subscrption price"); ?>.</span>
            </div>
            <div class="col-md-12 calc_price">
                <!-- <a href="<?php echo site_url('signup'); ?>" class="btn btn-primary d-block px-3 py-3 mb-4">Calculate</a> -->
                <form class="form-inline row calc_form" id="price_calculator" method="post" onsubmit="return false;" autocomplete="off">
                    <!-- <label class="sr-only" for="nmembers">Members</label> -->
                    <div class="col-sm-3">
                        <input type="text" class="form-control mb-2 mr-sm-2" id="nmembers" placeholder="Number of members">
                    </div>
                    <div class="col-sm-6" style="text-align:center;">
                        <div class="form-check mb-2 mr-sm-2">
                            <input class="form-check-input" type="radio" id="subscr_monthly" name="plan_type" value="1" checked="checked">
                            <label class="form-check-label" for="subscr_monthly"><?php echo translate('Monthly'); ?></label>
                            <div class="check"><div class="inside"></div></div>
                        </div>
                        <div class="form-check mb-2 mr-sm-2">
                            <input class="form-check-input" type="radio" id="subscr_quarterly" name="plan_type" value="2">
                            <label class="form-check-label" for="subscr_quarterly"><?php echo translate('Quarterly'); ?></label>
                            <div class="check"><div class="inside"></div></div>
                        </div>
                        <div class="form-check mb-2 mr-sm-2">
                            <input class="form-check-input" type="radio" id="subscr_annually" name="plan_type" value="3">
                            <label class="form-check-label" for="subscr_annually"><?php echo translate('Annually'); ?></label>
                            <div class="check"><div class="inside"></div></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-primary mb-2 btn-block" id="calculate_price"><?php echo translate('Calculate Price'); ?></button>
                    </div>
                </form>
                <div class="calc_results">
                    <div class="row">
                        <div class="col-sm-3 calc_results_amount_shell">
                            <div class="calc_results_tag"><?php echo translate('Your subscription pricing'); ?></div>
                            <div class="calc_results_amount">KES 24,800</div>
                        </div>
                        <div class="col-sm-9 calc_results_info_shell">
                            <div class="calc_results_info">* <span><?php echo translate('Payment Plan'); ?></span> <?php echo translate('Quartely'); ?></div>
                            <div class="calc_results_info">* <span><?php echo translate('Members'); ?></span> 128</div>
                            <div class="calc_results_info">* <?php echo translate('Total amount'); ?> <span><?php echo translate('VAT inclusive'); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

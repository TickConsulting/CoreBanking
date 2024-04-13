<!-- END nav -->
<div class="hero-wrap <?php if(preg_match('/websacco/i', $this->application_settings->application_name)){ echo '_websacco'; } ?>">
    <div class="row m-0">
    <div class="container">
        <div class="">
        <div class="overlay"></div>
        <div class="circle-bg"></div>
        <div class="circle-bg-2"></div>
        <div class="circle-bg-3"></div>
        <div class="container-fluid pt-2">
            <div class="slider-text d-md-flex align-items-center mt-lg-5" data-scrollax-parent="true">

            <?php if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
            <div class="one-forth pr-md-4 ftco-animate align-self-md-center" data-scrollax=" properties: { translateY: '70%' }">
                <h1 class="mb-4"><span style="font-weight:200!important;font-size:50px;"><?php echo translate('Do not wait');?></span><br><?php echo translate('Digitize your <br>Group now')?>.</h1>
                <p class="mb-md-5 mb-sm-3"><?php echo translate('WebSacco is what your Group needs to move to the next level');?>. <?php echo translate('Focus on Investments as we handle the Financial Administration');?>!</p>
                <p><a href="<?php echo site_url('signup');?>" class="btn btn-primary btn-sm px-4 py-3 mt-1"><?php echo translate('Sign up now'); ?> <i class="la la-angle-right"></i></a>&nbsp;&nbsp;<a href="<?php echo site_url('login');?>" class="btn btn-outline-primary btn-sm px-4 py-3 mt-1"><?php echo translate('I have an account'); ?></a></p>
            </div>
            <?php } else{ ?>
            <div class="one-forth pr-md-4 ftco-animate align-self-md-center" data-scrollax=" properties: { translateY: '70%' }">
                <h1 class="mb-4"><span style="font-weight:200!important;font-size:50px;"><?php echo translate('Do not wait');?></span><br><?php echo translate('Digitize your <br>Savings Group now')?>.</h1>
                <p class="mb-md-5 mb-sm-3"> <?php echo $this->application_settings->application_name;?> <?php echo translate('is what your Savings Group needs to move to the next level');?>. <?php echo translate('Focus on Investments as we handle the Financial Administration');?>!</p>
                <p><a href="<?php echo site_url('signup');?>" class="btn btn-primary btn-sm px-4 py-3 mt-1"><?php echo translate('Sign up now'); ?> <i class="la la-angle-right"></i></a>&nbsp;&nbsp;<a href="<?php echo site_url('login');?>" class="btn btn-outline-primary btn-sm px-4 py-3 mt-1"><?php echo translate('I have an account'); ?></a></p>
            </div>
            <?php } ?>

            <div class="one-half align-self-md-end ftco-animate align-self-sm-center mt-5">
                <div class="slider-carousel owl-carousel">
                    <?php if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                    <div class="item">
                        <img src="{group:url:base}{group:theme:path}images/dashboard_full_2_websacco.png" class="img-fluid img"alt="">
                    </div>
                    <div class="item">
                        <img src="{group:url:base}{group:theme:path}images/dashboard_full_3_websacco.png" class="img-fluid img"alt="">
                    </div>
                    <?php } else{ ?>
                    <div class="item">
                        <img src="{group:url:base}{group:theme:path}images/dashboard_full_2_.png" class="img-fluid img"alt="">
                    </div>
                    <div class="item">
                        <img src="{group:url:base}{group:theme:path}images/dashboard_full_3_.png" class="img-fluid img"alt="">
                    </div>
                    <?php } ?>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</div>

<section class="ftco-section services-section pb-0 bg-light">
    <div class="container">
        <!-- <div class="row justify-content-center mb-5 pb-5">
            <div class="col-md-7 text-center heading-section ftco-animate">
            <span class="subheading">Learn more about Chamasoft</span>
            <h2 class="mb-0">Why Chamasoft</h2>
            </div>
        </div> -->
        <div class="row">
            <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services d-block text-center">
                <div class="d-flex justify-content-center"><div class="icon"><span class="la la-cog"></span></div></div>
                <div class="media-body p-2 mt-3">
                <h3 class="heading"><?php echo translate('EASY SET UP'); ?></h3>
                <p><?php echo translate('Only five steps is required to set up the platform and start using'); ?>!</p>
                </div>
            </div>      
            </div>
            <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services d-block text-center">
                <div class="d-flex justify-content-center"><div class="icon"><span class="la la-lock"></span></div></div>
                <div class="media-body p-2 mt-3">
                <h3 class="heading"><?php echo translate('SAFE & SECURE'); ?></h3>
                <p><?php echo translate('Our platform is in a secure environment and all your data is safe.'); ?></p>
                </div>
            </div>    
            </div>
            <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services d-block text-center">
                <div class="d-flex justify-content-center"><div class="icon"><span class="la la-headphones"></span></div></div>
                <div class="media-body p-2 mt-3">
                <h3 class="heading"><?php echo translate('DEDICATED SUPPORT'); ?></h3>
                <p><?php echo translate('All-time available support team ready to help you with any question.'); ?></p>
                </div>
            </div>      
            </div>
        </div>
    </div>
</section>

<section class="ftco-section pb-5 pt-5">
    <div class="container">
        <div class="row justify-content-center mb-0 pb-0">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <span class="subheading"><?php echo translate('Services'); ?></span>
                <h2 class="mb-5"><?php echo translate('Meet').' '.$this->application_settings->application_name; ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 nav-link-wrap mb- pb-md-5 pb-sm-1 ftco-animate">
                <div class="nav ftco-animate nav-pills justify-content-center text-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link active" id="v-pills-nextgen-tab" data-toggle="pill" href="#v-pills-nextgen" role="tab" aria-controls="v-pills-nextgen" aria-selected="true"><?php echo $this->application_settings->application_name;?>  - <?php echo translate('What is it').'?'; ?></a>
                   <!--  <a class="nav-link" id="v-pills-performance-tab" data-toggle="pill" href="#v-pills-performance" role="tab" aria-controls="v-pills-performance" aria-selected="false"><?php echo translate('Who we are'); ?></a> -->
                    <a class="nav-link" id="v-pills-effect-tab" data-toggle="pill" href="#v-pills-effect" role="tab" aria-controls="v-pills-effect" aria-selected="false"><?php echo translate('Why use').' '.$this->application_settings->application_name.'?'; ?></a>
                </div>
            </div>
            <div class="col-md-12 align-items-center ftco-animate">
                <div class="tab-content ftco-animate" id="v-pills-tabContent">

                    <div class="tab-pane fade show active" id="v-pills-nextgen" role="tabpanel" aria-labelledby="v-pills-nextgen-tab">
                        <div class="d-md-flex">
                            <div class="one-forth align-self-center">
                                <?php if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                <img src="{group:url:base}{group:theme:path}images/dashboard_full_1_websacco.png" class="img-fluid border" alt="">
                                <?php } else{ ?>
                                <img src="{group:url:base}{group:theme:path}images/dashboard_full_1.png" class="img-fluid border" alt="">
                                <?php } ?>
                            </div>
                            <div class="one-half ml-md-5 align-self-center">
                                <h2 class="mb-4"><?php echo $this->application_settings->application_name;?>  - <?php echo translate('What is it').'?'; ?></h2>
                                <p><?php echo $this->application_settings->application_name.' '.translate('is an easy to use solution that is designed and enriched to manage all your Group activities to fostser accountability, transparency and enhance efficiency');?>.</p>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="v-pills-effect" role="tabpanel" aria-labelledby="v-pills-effect-tab">
                        <div class="d-md-flex">
                            <div class="one-forth order-last align-self-center">
                                <?php if(preg_match('/websacco/i', $this->application_settings->application_name)){ ?>
                                <img src="{group:url:base}{group:theme:path}images/dashboard_full_5_websacco.png" class="img-fluid border" alt="">
                                <?php } else{ ?>
                                <img src="{group:url:base}{group:theme:path}images/dashboard_full_1.png" class="img-fluid border" alt="">
                                <?php } ?>
                            </div>
                            <div class="one-half ml-md-5 align-self-center">
                                <h2 class="mb-4"><?php echo translate('Why use').' '.$this->application_settings->application_name.'?'; ?></h2>
                                <p>
                                <?php 
                                    echo translate('When it comes to managing your Group finances, one needs a tool that eases the workload, is accurate, secure and accessible.')?> 
                                </p>
                                <p>
                                <?php 
                                    echo translate('Financial information stored on '.$this->application_settings->application_name.' is done securely ensuring your information is only visible to authorized users.');?>
                                </p> 
                                <p>
                                    <?php 
                                    echo translate('We are available anywhere 24/7 as long as you have a reliable Internet connection');?>.
                                </p>
                                <p><?php echo $this->application_settings->application_name ?> 
                                    <?php 
                                    echo translate('provides an enterprise-class security architecture which enables your Group account to be integrated with core banking, also designed and engineered for large scale Groups, which possesses high sophistication and great performance');?>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section ftco-counter img pb-5 pt-5 bg-light d-none" id="section-counter" style="background-image: url({group:url:base}{group:theme:path}images/bg_1.jpg);">
    <div class="container">
        <div class="row justify-content-center mb- pb-1">
        <div class="col-md-7 text-center heading-section heading-section-white ftco-animate">
        <h2 class="mb-0"><?php echo $this->application_settings->application_name.' '.translate('Facts'); ?></h2>
        <span class="subheading"><?php echo translate('More than 800,000 groups using'); ?></span>
        </div>
    </div>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 text-center">
                    <div class="text">
                    <strong class="number" data-number="2000">0</strong>
                    <span><?php echo translate('Active Groups'); ?></span>
                    </div>
                </div>
                </div>
                <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 text-center">
                    <div class="text">
                    <strong class="number" data-number="100">0</strong>
                    <span><?php echo translate('Awards Won'); ?></span>
                    </div>
                </div>
                </div>
                <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 text-center">
                    <div class="text">
                    <strong class="number" data-number="32000">0</strong>
                    <span><?php echo translate('Active Users'); ?></span>
                    </div>
                </div>
                </div>
                <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                <div class="block-18 text-center">
                    <div class="text">
                    <strong class="number" data-number="31998">0</strong>
                    <span><?php echo translate('Transactions Made'); ?></span>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<section class="ftco-section testimony-section pb-5 pt-5 d-none">
    <div class="container">
    <div class="row justify-content-center mb-5 pb-5">
        <div class="col-md-7 text-center heading-section ftco-animate">
        <!-- <span class="subheading">Chamasoft Customer Says</span> -->
        <h2 class="mb-4"><?php echo translate('Our satisfied customer says'); ?></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Separated they live in</p>
        </div>
    </div>
    <div class="row ftco-animate">
        <div class="col-md-12">
        <div class="carousel-testimony owl-carousel ftco-owl">
            <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url({group:url:base}{group:theme:path}images/person_1.jpg)">
                <span class="quote d-flex align-items-center justify-content-center">
                    <i class="icon-quote-left"></i>
                </span>
                </div>
                <div class="text">
                <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <p class="name">Dennis Green</p>
                <span class="position">Marketing Manager</span>
                </div>
            </div>
            </div>
            <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url({group:url:base}{group:theme:path}images/person_2.jpg)">
                <span class="quote d-flex align-items-center justify-content-center">
                    <i class="icon-quote-left"></i>
                </span>
                </div>
                <div class="text">
                <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <p class="name">Dennis Green</p>
                <span class="position">Interface Designer</span>
                </div>
            </div>
            </div>
            <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url({group:url:base}{group:theme:path}images/person_3.jpg)">
                <span class="quote d-flex align-items-center justify-content-center">
                    <i class="icon-quote-left"></i>
                </span>
                </div>
                <div class="text">
                <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <p class="name">Dennis Green</p>
                <span class="position">UI Designer</span>
                </div>
            </div>
            </div>
            <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url({group:url:base}{group:theme:path}images/person_1.jpg)">
                <span class="quote d-flex align-items-center justify-content-center">
                    <i class="icon-quote-left"></i>
                </span>
                </div>
                <div class="text">
                <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <p class="name">Dennis Green</p>
                <span class="position">Web Developer</span>
                </div>
            </div>
            </div>
            <div class="item">
            <div class="testimony-wrap p-4 pb-5">
                <div class="user-img mb-5" style="background-image: url({group:url:base}{group:theme:path}images/person_1.jpg)">
                <span class="quote d-flex align-items-center justify-content-center">
                    <i class="icon-quote-left"></i>
                </span>
                </div>
                <div class="text">
                <p class="mb-5">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <p class="name">Dennis Green</p>
                <span class="position">System Analytics</span>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
</section>

<!-- <section class="ftco-section pb-5 pt-5 bg-light">
    <div class="container">
    <div class="row justify-content-center mb-5 pb-5">
        <div class="col-md-7 text-center heading-section ftco-animate">
        <h2><?php echo translate('Blog'); ?></h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Separated they live in</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 ftco-animate">
        <div class="blog-entry">
            <a href="blog-single.html" class="block-20" style="background-image: url('{group:url:base}{group:theme:path}images/image_1.jpg');">
            </a>
            <div class="text p-4 d-block">
            <div class="meta mb-3">
                <div><a href="#">August 12, 2018</a></div>
                <div><a href="#">Admin</a></div>
                <div><a href="#" class="meta-chat"><span class="icon-chat"></span> 3</a></div>
            </div>
            <h3 class="heading"><a href="#">Even the all-powerful Pointing has no control about the blind texts</a></h3>
            </div>
        </div>
        </div>
        <div class="col-md-4 ftco-animate">
        <div class="blog-entry" data-aos-delay="100">
            <a href="blog-single.html" class="block-20" style="background-image: url('{group:url:base}{group:theme:path}images/image_2.jpg');">
            </a>
            <div class="text p-4">
            <div class="meta mb-3">
                <div><a href="#">August 12, 2018</a></div>
                <div><a href="#">Admin</a></div>
                <div><a href="#" class="meta-chat"><span class="icon-chat"></span> 3</a></div>
            </div>
            <h3 class="heading"><a href="#">Even the all-powerful Pointing has no control about the blind texts</a></h3>
            </div>
        </div>
        </div>
        <div class="col-md-4 ftco-animate">
        <div class="blog-entry" data-aos-delay="200">
            <a href="blog-single.html" class="block-20" style="background-image: url('{group:url:base}{group:theme:path}images/image_3.jpg');">
            </a>
            <div class="text p-4">
            <div class="meta mb-3">
                <div><a href="#">August 12, 2018</a></div>
                <div><a href="#">Admin</a></div>
                <div><a href="#" class="meta-chat"><span class="icon-chat"></span> 3</a></div>
            </div>
            <h3 class="heading"><a href="#">Even the all-powerful Pointing has no control about the blind texts</a></h3>
            </div>
        </div>
        </div>
    </div>
    </div>
</section> -->
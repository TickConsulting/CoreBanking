<?php if($this->group->enable_member_information_privacy){
    //do nothing
}else{ ?>
    <div class="row d-none">
        <div class="col-lg-6">
            <div class="btn-group">
                <a href="<?php echo site_url($this->uri->segment(1).'/members/view/'.$next_group_member_id); ?>" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon">
                    <span>
                        <i class="fa fa-user"></i>
                        <span>
                            <?php echo translate('CURRENT MEMBER').': '.$this->group_member_options[$post->id]; ?>
                        </span>
                    </span>
                </a>
            </div>
        </div>
        <div class="col-lg-6 m--align-right">
            <div class="btn-group">
                <a href="<?php echo site_url($this->uri->segment(1).'/members/view/'.$next_group_member_id); ?>" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon">
                    <span>
                        <i class="fa fa-user"></i>
                        <span>
                            <?php echo translate('NEXT MEMBER').': '.$this->group_member_options[$next_group_member_id]; ?>
                        </span>
                    </span>
                </a>
                
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="sr-only"><?php echo translate('Members'); ?></span>
                </button>
                <div class="dropdown-menu">
                    <?php 
                        foreach($this->active_group_member_options as $member_id => $member_name):
                    ?>
                        <a class="dropdown-item <?php if($member_id==$post->id){ echo 'active'; } ?>" href="<?php echo site_url($this->uri->segment(1).'/members/view/'.$member_id); ?>">
                            <i class="la la-user"></i>
                            <?php echo $member_name; ?>
                        </a>
                    <?php
                        endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xl-3 col-lg-4">
        <div class="m-card-profile">
            <div class="m-card-profile__title m--hide">
                <?php echo translate('Your Profile'); ?>
            </div>
            <div class="m-card-profile__pic">
                <div class="m-card-profile__pic-wrapper">
                    <img src="https://ui-avatars.com/api/?name=<?php echo $post->first_name; ?>+<?php echo $post->last_name; ?>&background=00abf2&color=fff&size=56&" class="m--img-rounded m--marginless m--img-centered" alt=""/>
                </div>
            </div>

            <div class="m-card-profile__details">
                <span class="m-card-profile__name"><?php echo $post->first_name.' '.$post->last_name; ?></span>
                <span class="m-card-profile__email">
                    <?php echo translate(isset($group_role_options[$post->group_role_id])?$group_role_options[$post->group_role_id]:'Loan Applicant'); ?>

                    <?php if($post->membership_number):
                        echo ' : '.$post->membership_number;
                    endif; ?>
                </span>
                <br>
                <a class="m-card-profile__email"><?php echo $post->phone; ?></a>
                <?php if($post->email){
                    echo '<br><a href="" class="m-card-profile__email m-link">'.$post->email.'</a>';
                } ?>
            </div>
        </div>  
        <ul class="m-nav m-nav--hover-bg m-portlet-fit--sides">
            <li class="m-nav__separator m-nav__separator--fit"></li>
            <li class="m-nav__section m--hide">
                <span class="m-nav__section-text">
                    <?php echo translate('Section'); ?>
                </span>
            </li>
           
          
        </ul>
    </div>
    <div class="col-xl-9 col-lg-8">
        <ul class="nav nav-tabs  m-tabs-line m-tabs-line--success" role="tablist">
           
            <li class="nav-item dropdown m-tabs__item">
                <a class="nav-link m-tabs__link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="flaticon-folder-2 "></i><?php echo translate('Loans'); ?></a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" data-toggle="tab" href="#m_tabs_7_3"><i class="la la-hourglass-half"></i><?php echo translate('Ongoing'); ?></a>
                    <a class="dropdown-item" data-toggle="tab" href="#m_tabs_7_4"><i class="la la-hourglass-o"></i><?php echo translate('Fully Paid'); ?></a>
                </div>
            </li>
        </ul>                        
        <div class="tab-content">
            <div class="tab-pane active" id="m_tabs_7_1" role="tabpanel">
                <div class="table-responsive">
                <table class="table m-table m-table--head-no-border table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo translate('LOAN DURATION'); ?></th>
                                <th class='text-right'><?php echo translate('PAYABLE'); ?></th>
                                <th class='text-right'><?php echo translate('TOTAL PAID'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count=1; $total_loan_payable = 0; $total_loan_paid = 0;
                                foreach($ongoing_member_loans as $ongoing_member_loan): 
                                    $total_loan_payable+=$ongoing_loan_amounts_payable[$ongoing_member_loan->id];
                                    $total_loan_paid+=$ongoing_loan_amounts_paid[$ongoing_member_loan->id];
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $count++; ?></th>
                                    <td>
                                        <?php echo timestamp_to_date($ongoing_member_loan->disbursement_date).' to '.timestamp_to_date($ongoing_member_loan->loan_end_date);?>
                                    </td>
                                    <td class='text-right'><?php echo $this->group_currency.' '.number_to_currency($ongoing_loan_amounts_payable[$ongoing_member_loan->id]); ?></td>
                                    <td class='text-right'><?php echo $this->group_currency.' '.number_to_currency($ongoing_loan_amounts_paid[$ongoing_member_loan->id]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class='text-right' colspan="2"><?php echo translate('Totals'); ?></td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.number_to_currency($total_loan_payable); ?>
                                    </span>
                                </td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.number_to_currency($total_loan_paid); ?>
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="m_tabs_7_2" role="tabpanel">
                <div class="table-responsive"> 
                    <table class="table m-table m-table--head-no-border table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo translate('Fine Name'); ?></th>
                                <th class='text-right'><?php echo translate('Total Paid'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count=1; foreach($fine_category_options as $fine_category_id => $fine_category_name): ?>
                            <tr>
                                <td> <?php echo $count++; ?></td>
                                <td> <?php echo $fine_category_name; ?> </td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.isset($total_fines_paid_per_member_array[$post->id][$fine_category_id]) ? number_to_currency($total_fines_paid_per_member_array[$post->id][$fine_category_id]) : number_to_currency(0); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php foreach($contribution_options as $contribution_id => $contribution_name): ?>
                                <tr>
                                    <td> <?php echo $count++; ?> </td>
                                    <td> <?php echo $contribution_name.translate('late payment fine'); ?> </td>
                                    <td class='text-right'>
                                        <span class="bold theme-font">
                                            <?php echo $this->group_currency.' '.number_to_currency($total_contribution_fines_paid_per_member_array[$post->id][$contribution_id]); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfooter>
                            <tr>
                                <td></td>
                                <td class="text-right">
                                    <strong>
                                        <?php echo translate('Total Fines Paid'); ?>
                                    </strong>
                                </td>
                                <td class="text-right">
                                    <span class="bold theme-font">
                                        <strong>
                                            <?php echo $this->group_currency.' '.number_to_currency($total_member_fines); ?>
                                        </strong>
                                    </span>
                                </td>
                            </tr>
                        </tfooter>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="m_tabs_7_3" role="tabpanel">
                <div class="table-responsive">
                    <table class="table m-table m-table--head-no-border table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo translate('LOAN DURATION'); ?></th>
                                <th class='text-right'><?php echo translate('PAYABLE'); ?></th>
                                <th class='text-right'><?php echo translate('TOTAL PAID'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count=1; $total_loan_payable = 0; $total_loan_paid = 0;
                                foreach($ongoing_member_loans as $ongoing_member_loan): 
                                    $total_loan_payable+=$ongoing_loan_amounts_payable[$ongoing_member_loan->id];
                                    $total_loan_paid+=$ongoing_loan_amounts_paid[$ongoing_member_loan->id];
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $count++; ?></th>
                                    <td>
                                        <?php echo timestamp_to_date($ongoing_member_loan->disbursement_date).' to '.timestamp_to_date($ongoing_member_loan->loan_end_date);?>
                                    </td>
                                    <td class='text-right'><?php echo $this->group_currency.' '.number_to_currency($ongoing_loan_amounts_payable[$ongoing_member_loan->id]); ?></td>
                                    <td class='text-right'><?php echo $this->group_currency.' '.number_to_currency($ongoing_loan_amounts_paid[$ongoing_member_loan->id]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class='text-right' colspan="2"><?php echo translate('Totals'); ?></td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.number_to_currency($total_loan_payable); ?>
                                    </span>
                                </td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.number_to_currency($total_loan_paid); ?>
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="m_tabs_7_4" role="tabpanel">
                <div class="table-responsive">
                    <table class="table m-table m-table--head-no-border table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo translate('LOAN DURATION'); ?></th>
                                <th class='text-right'><?php echo translate('PAYABLE'); ?></th>
                                <th class='text-right'><?php echo translate('TOTAL PAID'); ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php 
                                $count=1; $total_loan_payable = 0; $total_loan_paid = 0; foreach($fully_paid_member_loans as $fully_paid_member_loan): 
                                $total_loan_payable+=$fully_paid_loan_amounts_payable[$fully_paid_member_loan->id];
                                $total_loan_paid+=$fully_paid_loan_amounts_paid[$fully_paid_member_loan->id];
                            ?>
                                <tr>
                                    <th scope="row"><?php echo $count++; ?></th>
                                    <td>
                                        <?php echo timestamp_to_date($fully_paid_member_loan->disbursement_date).' to '.timestamp_to_date($fully_paid_member_loan->loan_end_date);?>
                                    </td>
                                    <td class='text-right'>
                                        <span class="bold theme-font"><?php echo $this->group_currency.' '.number_to_currency($fully_paid_loan_amounts_payable[$fully_paid_member_loan->id]); ?></span>
                                    </td>
                                    <td class='text-right'>
                                        <span class="bold theme-font"><?php echo $this->group_currency.' '.number_to_currency($fully_paid_loan_amounts_paid[$fully_paid_member_loan->id]); ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class='text-right' colspan="2"><?php echo translate('Totals'); ?></td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.number_to_currency($total_loan_payable); ?>
                                    </span>
                                </td>
                                <td class='text-right'>
                                    <span class="bold theme-font">
                                        <?php echo $this->group_currency.' '.number_to_currency($total_loan_paid); ?>
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>  
    </div>
</div>

<div class="modal fade" id="next_of_kin_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo $post->first_name.' '.$post->last_name; ?> <?php echo translate('next of kin'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="m-widget5">
                    <?php 
                       foreach($next_of_kin_entries as $next_of_kin_entry): 
                    ?>
                        <div class="m-widget5__item">
                            <div class="m-widget5__content">
                                <div class="m-widget5__section pl-0">
                                    <h4 class="m-widget5__title">
                                        <?php echo $next_of_kin_entry->full_name; ?>
                                    </h4>
                                    <div class="m-widget5__info mt-0">
                                        <span class="m-widget5__author">
                                            <?php echo translate('Phone'); ?>:
                                        </span>
                                        <span class="m-widget5__info-author m--font-info">
                                            <?php echo $next_of_kin_entry->phone; ?>
                                        </span>
                                    </div>
                                    <?php if($next_of_kin_entry->email){ ?>
                                        <div class="m-widget5__info mt-0">
                                            <span class="m-widget5__info-label">
                                                <?php echo translate('Email'); ?>:
                                            </span>
                                            <span class="m-widget5__info-date m--font-info">
                                                <?php echo $next_of_kin_entry->email; ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                    <div class="m-widget5__info mt-0">
                                        <span class="m-widget5__author">
                                            <?php echo translate('Relationship'); ?>:
                                        </span>
                                        <span class="m-widget5__info-author m--font-info">
                                            <?php echo $next_of_kin_entry->relationship; ?>
                                        </span>
                                        <span class="m-widget5__info-label">
                                            <?php echo translate('ID Number'); ?>:
                                        </span>
                                        <span class="m-widget5__info-date m--font-info">
                                            <?php echo $next_of_kin_entry->id_number; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="m-widget5__content">        
                                <div class="m-widget5__stats1">
                                    <span class="m-widget5__number"><?php echo $next_of_kin_entry->allocation.'%'; ?></span><br>
                                    <span class="m-widget5__sales"><?php echo translate('Allocation'); ?></span> 
                                </div>
                            </div>  
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
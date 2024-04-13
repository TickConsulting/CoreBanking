<div class="search-page search-content-1">
    <?php echo form_open('member/members/directory', ' id="form"  class="form-horizontal" method="get" '); ?>
        <div class="search-bar bordered">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group">
                        <input value="<?php echo $this->input->get('name'); ?>" type="text" name="name" class="form-control" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn blue uppercase bold" type="submit">Search</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php echo form_close(); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="search-container bordered">
                <ul>
                    <?php foreach($posts as $post): ?>
                        <li class="search-item clearfix">
                            <?php if($this->group->enable_member_information_privacy){ ?>
                                <a href="#">
                                    <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$post->avatar)?site_url('uploads/groups/'.$post->avatar):site_url('templates/admin_themes/groups/img/default_avatar.png'); ?>" />
                                </a>
                            <?php }else{ ?>
                                <a href="<?php echo site_url('member/members/view/'.$post->id); ?>">
                                    <img src="<?php echo is_file(FCPATH.'uploads/groups/'.$post->avatar)?site_url('uploads/groups/'.$post->avatar):site_url('templates/admin_themes/groups/img/default_avatar.png'); ?>" />
                                </a>
                            <?php } ?>
                            <div class="search-content">
                                <h2 class="search-title">
                                    <?php if($post->id == $this->member->id){ ?>
                                        <?php echo $post->first_name.' '.$post->last_name; ?>
                                    <?php }else{ ?>
                                        <?php if($this->group->enable_member_information_privacy){ ?>
                                            <span class="font-black"><?php echo $post->first_name.' '.$post->last_name; ?></span> 
                                        <?php }else{ ?>
                                            <?php echo $post->first_name.' '.$post->last_name; ?>
                                        <?php } ?>
                                    <?php } ?>
                                    <small class=" font-grey-cascade" > <?php echo isset($group_role_options[$post->group_role_id])?$group_role_options[$post->group_role_id]:'Member'; ?> </small>
                                    <div class="directory-actions">
                                        <?php if($this->group->enable_member_information_privacy){ ?>

                                        <?php }else{ ?>
                                            <div class="btn-group">
                                                <button class="btn blue btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown" data-close-others="true"> 
                                                    <?php
                                                        $default_message='Actions';
                                                        $this->languages_m->translate('actions',$default_message);
                                                    ?>
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li>
                                                        <a href="<?php echo site_url('member/members/view/'.$post->id); ?>"><i class="fa fa-eye"></i> View Profile</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                    </div>

                                </h2>
                                <p class="search-desc">
                                    <span class=''><i class="fa fa-phone"></i></span>: <?php echo $post->phone; ?>
                                    <?php if($post->email): ?>
                                        <br/><span class=''><i class="fa fa-envelope"></i></span>: <?php echo $post->email; ?>
                                    <?php endif; ?>

                                    <?php if($this->group->enable_member_information_privacy){ ?>

                                    <?php }else{ ?>
                                        <br/><span class='bold'>
                                            <?php
                                                $default_message='Total Contributions';
                                                $this->languages_m->translate('total_contributions',$default_message);
                                            ?>
                                        </span> : <?php echo $this->group_currency.' '.number_to_currency($group_member_contribution_totals[$post->id]-$group_member_contribution_refund_totals[$post->id]+$member_total_contribution_transfers_from_loans_array[$post->id]-$member_total_contribution_transfers_to_loan_array[$post->id]); ?>
                                        <br/><span class='bold'>Total Fines</span> : <?php echo $this->group_currency.' '.number_to_currency($group_member_fine_totals[$post->id]); ?>
                                        <?php 
                                            if($group_member_contribution_balance_totals[$post->id]-$group_member_cumulative_contribution_balance_totals[$post->id]>0): 
                                        ?>
                                        <br/><span class='bold'>
                                            <?php
                                                $default_message='Total Contributions Arrears';
                                                $this->languages_m->translate('total_contribution_arrears',$default_message);
                                            ?>

                                        </span> : <?php echo $this->group_currency.' '.number_to_currency($group_member_contribution_balance_totals[$post->id]-$group_member_cumulative_contribution_balance_totals[$post->id]); ?>
                                        <?php 
                                            endif; 
                                            if($group_member_fine_balance_totals[$post->id]>0): 
                                        ?>
                                        <br/><span class='bold'>
                                            <?php
                                                $default_message='Total Fines Arrears';
                                                $this->languages_m->translate('total_fines_arrears',$default_message);
                                            ?>

                                        </span> : <?php echo $this->group_currency.' '.number_to_currency($group_member_fine_balance_totals[$post->id]); ?>
                                        <?php
                                            endif;
                                        ?>
                                    <?php } ?>
                                </p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="search-pagination">
                    <?php 
                        if ( ! empty($pagination['links'])):
                            echo '<p class="paging">Showing from <span class="greyishBtn">'.$pagination['from'].'</span> to <span class="greyishBtn">'.$pagination['to'].'</span> of <span class="greyishBtn">'.$pagination['total'].'</span> Members</p>';
                            echo '<div class ="pagination">';
                            echo $pagination['links']; 
                            echo '</div></div>';
                        endif; 
                    ?> 
                </div>
            </div>
        </div>
    </div>
</div>
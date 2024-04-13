
<div id="member_statements_listing" style="position: relative;">  
    <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">        
        <tbody>
            <tr>
                <td>1</td>
                <td>Guarantor Approvals (<?php echo count($guarantor_requests) ?>)</td>
                <td>
                    <?php
                        if(count($guarantor_requests) > 1){ ?>
                            <a href="<?php echo site_url('member/members/view_loan_requests_listing')?>" class="btn blue btn-xs default">
                                <i class="fa fa-eye"></i>View &nbsp;&nbsp; 
                            </a><?php
                        }else{
                            ?>
                            <button href="<?php echo site_url('member/members/view_loan_requests_listing')?>" class="btn blue btn-xs default">
                                <i class="fa fa-eye"></i>View &nbsp;&nbsp; 
                            </button><?php
                        }                         
                    ?>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Suppervisor Reccomendations (<?php echo count($supervisor_recommendations) ?>)</td>
                <td>
                    <?php
                        if(count($supervisor_recommendations) > 1){ ?>
                            <a href="<?php echo site_url('member/members/view_loan_requests_listing')?>" class="btn blue btn-xs default">
                                <i class="fa fa-eye"></i>View &nbsp;&nbsp; 
                            </a><?php
                        }else{
                            ?>
                            <button href="<?php echo site_url('member/loans/view_supervisor_recommendatios')?>" class="btn blue btn-xs default">
                                <i class="fa fa-eye"></i>View &nbsp;&nbsp; 
                            </a><?php
                        }                         
                    ?>
                </td>
            </tr>
            <tr>
        </tbody>
    </table>
<div class="clearfix"></div>
<div class="row col-md-12">
</div>
<div class="clearfix"></div></div>
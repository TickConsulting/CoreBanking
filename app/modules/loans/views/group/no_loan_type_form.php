<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="m-alert m-alert--outline alert alert-info fade show">
                Hello <?php echo $this->user->first_name;?>, no need to worry. We have updated the protal and you need to have loan products before creating a loan.
                <br/> <br/>
                Use this link to add a loan product. <a href="<?php echo site_url('group/loan_types/create');?>">Create loan Type</a>
            </div>
        </div>
    </div>
</div>
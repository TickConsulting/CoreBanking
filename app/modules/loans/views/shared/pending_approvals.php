<div class="row">
    <div class="col-md-12">
        <div class="btn-group margin-bottom-20 search-button">
            <button class="btn green dropdown-toggle btn-sm" type="button" data-toggle=""> Search
                <i class="fa fa-angle-down"></i>
            </button>
            <div class="dropdown-menu dropdown-content input-large hold-on-click" role="menu">
                <?php echo form_open(current_url(),'method="GET" class="filter"');?>
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label ">
                                <?php
                                    $default_message='Member';
                                    $this->languages_m->translate('member',$default_message);
                                ?>
                            </label>
                            <div class="">
                            <?php
                                echo form_dropdown('member_id[]',array()+$this->group_member_options,$this->input->get('member_id'),'class="form-control select2" multiple="multiple"');
                            ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button name="filter" value="filter" type="submit"  class="btn blue submit_form_button btn-sm"><i class="fa fa-filter"></i> 
                            <?php
                                $default_message='Filter';
                                $this->languages_m->translate('filter',$default_message);
                            ?>
                        </button>
                        <button  type="button"  readonly="readonly" class="btn blue processing btn-sm processing"><i class="fa fa-spinner fa-spin"></i> </button>
                        <button class="btn btn-xs btn-danger close-filter" type="button"><i class="fa fa-close"></i></button>
                    </div>

                <?php echo form_close();?>
            </div>
        </div>
    </div>
</div>
<div id="member_statements_listing" style="position: relative;">  
    <table class="table table-condensed table-striped table-hover table-header-fixed table-searchable">        
        <tbody>
            <tr>
                <?php print_r($guarantor_requests) ?>
                <td>1</td>
                <td>Aggrey Koros</td>
                <td>
                    <a href="http://flairs.chamasoft.local/group/statements/view/13817" class="btn blue btn-xs default">
                        <i class="fa fa-eye"></i>Contribution Statement &nbsp;&nbsp; 
                    </a>
                    <a href="http://flairs.chamasoft.local/group/statements/fine_statement/13817" class="btn red btn-xs default">
                        <i class="fa fa-eye"></i>Fine Statement &nbsp;&nbsp; 
                    </a>  

                    <a href="http://flairs.chamasoft.local/group/statements/miscellaneous_statement/13817" class="btn btn-xs default">
                        <i class="fa fa-eye"></i>Miscellaneous Statement &nbsp;&nbsp; 
                    </a>  
                </td>
            </tr>
            <tr>
        </tbody>
    </table>
<div class="clearfix"></div>
<div class="row col-md-12">
</div>
<div class="clearfix"></div></div>
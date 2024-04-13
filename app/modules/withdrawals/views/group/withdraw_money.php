<div class="row">
	<div class="col-md-12">
		<div id='append-new-line' style="display:none;">
			<div class="form-group m-form__group row withdrawal_row">
        		<div class="col-lg-3 withdrawal_for_holder">
					<label>
                        <?php echo translate('Withdrawal for');?>:
					</label>
					<span class="m-input--air">
                        <?php echo form_dropdown('withdrawal_for',array(''=>translate('Select Withdrawal for'))+translate($withdrawal_fors),'',' class="m-select2-append form-control m-input withdrawal_for" ');?>
                    </span>
					<span class="m-form__help">
                        <?php echo translate("Select what you're withdrawing for");?>
					</span>
				</div>
				<div class="col-8 particulars_place_holder"></div>
				<div class="col-lg-1">
					<label class="">&nbsp;</label>
					<div class="m-input">
						<a href="javascript:;" class="btn btn-danger m-btn m-btn--icon btn-sm m-btn--icon-only remove-line" ata-container="body" data-toggle="m-tooltip" data-placement="top" title="" data-original-title="Remove withdrawal line">
							<i class="la la-trash"></i>
						</a>
					</div>
				</div>
			</div>
        </div>

        <div class="loan_withdrawal_fields" style="display:none;">
        	<div class="col-lg-6 payment_fields">
		        <div class="row">
			        <div class="col-6">
			        	<label class="">
                        	<?php echo translate("Member");?>:
			        	</label>
						<span class="m-input--air">
		                    <?php echo form_dropdown('member_id',array(''=>translate('Select Member'))+$this->group_member_options,'',' class="m-select2-append form-control m-input--air member" ');?>
		                </span>
						<span class="m-form__help">
                        	<?php echo translate("Select the loanee");?>
						</span>
			        </div>
			         <div class="col-6">
			        	<label class="">
                        	<?php echo translate("Loan type");?>:
			        	</label>
						<div class="m-input-icon m-input-icon--right">
							<span class="m-input--air">
		                        <?php echo form_dropdown('loan_type_id',array(''=>translate('Select Loan Type'))+translate($loan_type_options),'',' class="m-select2-append form-control m-input--air loan_type" ');?>
		                    </span>
						</div>
						<span class="m-form__help">
                        	<?php echo translate("Select the loan type to disburse");?>
						</span>
			        </div>
		        </div>
        	</div>
        	<div class="col-lg-3 payment_fields">
				<label class="">
                    <?php echo translate("Amount");?>:
				</label>
				<div class="m-input-icon m-input-icon--right">
					<?php echo form_input('amount',"",'  class="form-control currency m-input amount text-right currency" placeholder="'.translate('Enter Amount').'" id="amount"');?>
				</div>
				<span class="m-form__help">
                    <?php echo translate("Enter loan amount");?>
				</span>
			</div>
		</div>

		<div class="expense_withdrawal_fields" style="display:none;">
			<div class="col-lg-5 payment_fields">
				<label class="">
                    <?php echo translate("Expense Category");?>:
				</label>
				<span class="m-input--air">
                    <?php echo form_dropdown('expense_category_id',array(''=>translate('Select Expense Category'))+$expense_category_options,'',' class="m-select2-append form-control m-input expense_category" ');?>
                </span>
				<span class="m-form__help">
                    <?php echo translate("Please select the withdrawal expense category");?>
				</span>
			</div>
			<div class="col-lg-4 payment_fields">
				<label class="">
                    <?php echo translate("Amount");?>:
				</label>
				<div class="m-input-icon m-input-icon--right">
					<?php echo form_input('amount',"",'  class="form-control currency m-input amount text-right currency" placeholder="'.translate('Enter Amount').'" id="amount"');?>
				</div>
				<span class="m-form__help">
                    <?php echo translate("Enter the expense Amount");?>:
				</span>
			</div>
		</div>

		<div class="dividend_withdrawal_fields" style="display:none;">
			<div class="col-lg-6 payment_fields">
		        <div class="row">
			        <div class="col-6">
			        	<label class="">
                        	<?php echo translate("Member");?>:
			        	</label>
						<span class="m-input--air">
		                    <?php echo form_dropdown('member_id',array(''=>translate('Select Member'))+$this->group_member_options,'',' class="m-select2-append form-control m-input--air member" ');?>
		                </span>
						<span class="m-form__help">
                        	<?php echo translate("Select group member");?>
						</span>
			        </div>
			         <div class="col-6">
			        	<label class="">
                   			<?php echo translate("Description");?>:
						</label>
						<span class="m-input--air">
		                    <?php 
		                        $textarea = array(
		                            'name'=>'description',
		                            'id'=>'',
		                            'value'=> '',
		                            'cols'=>25,
		                            'rows'=>5,
		                            'maxlength'=>'',
		                            'class'=>'form-control description',
		                            'style'=>'',
		                            'placeholder'=>''
		                        ); 
		                        echo form_textarea($textarea);
		                    ?>
		                </span>
			        </div>
		        </div>
        	</div>

	
			<div class="col-lg-3 payment_fields">
				<label class="">
                    <?php echo translate("Amount");?>:
				</label>
				<div class="m-input-icon m-input-icon--right">
					<?php echo form_input('amount',"",'  class="form-control currency m-input amount text-right" placeholder="'.translate('Enter Amount').'" id="amount"');?>
				</div>
				<span class="m-form__help">
                    <?php echo translate("Enter the divindend amount");?>:
				</span>
			</div>
		</div>

		<div class="shares_refund_withdrawal_fields" style="display:none;">
        	<div class="col-lg-6 payment_fields">
				<div class="row">
			        <div class="col-6">
			        	<label class="">
                        	<?php echo translate("Member");?>:
			        	</label>
						<span class="m-input--air">
		                    <?php echo form_dropdown('member_id',array(''=>translate('Select Member'))+$this->group_member_options,'',' class="m-select2-append form-control m-input--air member" ');?>
		                </span>
						<span class="m-form__help">
                        	<?php echo translate("Select Member to be refunded");?>:
						</span>
			        </div>
			        <div class="col-6">
			        	<label class="">
                        	<?php echo translate("Shares");?>:
			        	</label>
						<div class="m-input-icon m-input-icon--right">
							<span class="m-input--air">
		                        <?php echo form_dropdown('contribution_id',array(''=>translate('Select Shares'))+$contribution_options,'',' class="m-select2-append form-control m-input--air contribution" ');?>
		                    </span>
						</div>
						<span class="m-form__help">
                        	<?php echo translate("Select the shares to refund");?>
						</span>
			        </div>
		        </div>
		    </div>
			<div class="col-3 payment_fields">
				<label class="">
                    <?php echo translate("Amount");?>:
				</label>
				<div class="m-input-icon m-input-icon--right">
					<?php echo form_input('amount',"",'  class="form-control currency m-input amount text-right" placeholder="'.translate('Enter Amount').'" id="amount"');?>
				</div>
				<span class="m-form__help">
                    <?php echo translate("Enter the refund amount");?>
				</span>
			</div>
		</div>

		<div class="welfare_withdrawal_fields" style="display:none;">
			<div class="col-lg-6 payment_fields">
		        <div class="row">
			        <div class="col-6">
			        	<label class="">
	                        <?php echo translate("Member");?>:
						</label>
						<span class="m-input--air">
		                    <?php echo form_dropdown('member_id',array(''=>translate('Select Member'))+$this->group_member_options,'',' class="m-select2-append form-control m-input member" ');?>
		                </span>
						<span class="m-form__help">
	                        <?php echo translate("Select the welfare recipient");?>
						</span>
			        </div>
			         <div class="col-6">
			        	<label class="">
                   			<?php echo translate("Description");?>:
						</label>
						<span class="m-input--air">
		                    <?php 
		                        $textarea = array(
		                            'name'=>'description',
		                            'id'=>'',
		                            'value'=> '',
		                            'cols'=>25,
		                            'rows'=>5,
		                            'maxlength'=>'',
		                            'class'=>'form-control description',
		                            'style'=>'',
		                            'placeholder'=>''
		                        ); 
		                        echo form_textarea($textarea);
		                    ?>
		                </span>
			        </div>
		        </div>
        	</div>

			<div class="col-lg-3 payment_fields">
				<label class="">
                    <?php echo translate("Amount");?>:
				</label>
				<div class="m-input-icon m-input-icon--right">
					<?php echo form_input('amount',"",'  class="form-control currency m-input amount text-right" placeholder="'.translate('Enter Amount').'" id="amount"');?>
				</div>
				<span class="m-form__help">
                    <?php echo translate("Enter the disbursement amount");?>
				</span>
			</div>
		</div>

		<div class="account_withdrawal_fields" style="display:none;">
			<div class="col-lg-6 payment_fields">
		        <div class="row">
			        <div class="col-6">
			        	<label class="">
	                        <?php echo translate("Account to");?>:
						</label>
						<span class="m-input--air">
		                    <?php echo form_dropdown('account_to_id',array(''=>translate('Select Account'))+translate($active_accounts),'',' class="m-select2-append form-control m-input account_to_id" ');?>
		                </span>
						<span class="m-form__help">
	                        <?php echo translate("Select the account to transfer to");?>
						</span>
			        </div>
			         <div class="col-6">
			        	<label class="">
                   			<?php echo translate("Description");?>:
						</label>
						<span class="m-input--air">
		                    <?php 
		                        $textarea = array(
		                            'name'=>'description',
		                            'id'=>'',
		                            'value'=> '',
		                            'cols'=>25,
		                            'rows'=>5,
		                            'maxlength'=>'',
		                            'class'=>'form-control description',
		                            'style'=>'',
		                            'placeholder'=>''
		                        ); 
		                        echo form_textarea($textarea);
		                    ?>
		                </span>
			        </div>
		        </div>
        	</div>

			<div class="col-lg-3 payment_fields">
				<label class="">
                    <?php echo translate("Amount");?>:
				</label>
				<div class="m-input-icon m-input-icon--right">
					<?php echo form_input('amount',"",'  class="form-control currency m-input amount text-right" placeholder="'.translate('Enter Amount').'" id="amount"');?>
				</div>
				<span class="m-form__help">
                    <?php echo translate("Enter the disbursement amount");?>
				</span>
			</div>
		</div>

		<?php echo form_open($this->uri->uri_string(),"class='m-form m-form--fit m-form--label-align-right m-form--group-seperator-dashed_ m-form--state make_withdrawals_form'"); ?>
			<?php if($this->group_unlinked_bank_accounts){?>
					<div class="m-alert m-alert--outline alert alert-danger fade show" role="alert">
              <strong>The group has <?php echo count($this->group_unlinked_bank_accounts)?> account(s) yet to be linked to start online transactions.</strong>
              <br/>
              <br/>
              Kindly proceed to verify and link the accounts.
              <ul>
                <?php foreach ($this->group_unlinked_bank_accounts as $key => $group_unverified_bank_account): ?>
                	<li><a href="<?php echo site_url('group/bank_accounts/connect/'.$group_unverified_bank_account->id);?>"><?php echo $group_unverified_bank_account->account_name.'('.$group_unverified_bank_account->account_number.')'?></a></li>
                <?php endforeach ?>
              </ul>
          </div>
			<?php }?>
			<?php if(!$bank_account_options ){
				if(!$this->group_unlinked_bank_accounts){
			?>
						<div class="m-alert m-alert--outline alert alert-danger fade show" role="alert">
                <strong>Sorry!</strong> The group does not have a verified bank account to transact.
                <br/>
                <br/>
                Kindly connect your  bank account to start transacting.
            </div>
			<?php }}else{?>
				<div class="">
				<!-- <div class="m-portlet__body"> -->
					<span class="error">
					</span>

					<div class="form-group m-form__group row bank_account_row">
						<div class="col-lg-12">
							<label>
	            				<?php echo translate("Group Bank Account");?>:
							</label>
							<span class="m-input--air">
	                            <?php echo form_dropdown('bank_account_id',translate(array(''=>translate('Select bank'))+$bank_account_options),'',' class="m-select2 form-control m-input bank_account_id" ');?>
	                        </span>
							<span class="m-form__help">
	            				<?php echo translate("Select account to withdraw from");?>
							</span>
						</div>
					</div>

					<div id="append-place-holder">
						<div class="form-group m-form__group row withdrawal_row">
							<div class="col-lg-3 withdrawal_for_holder">
								<label>
	                				<?php echo translate("Withdrawal for");?>:
								</label>
								<span class="m-input--air">
	                                <?php echo form_dropdown('withdrawal_for',translate(array(''=>translate('Select Withdrawal for'))+$withdrawal_fors),'',' class="m-select2 form-control m-input withdrawal_for" ');?>
	                            </span>
								<span class="m-form__help">
	                				<?php echo translate("Select what you're withdrawing for");?>
								</span>
							</div>
							<div class="col-8 particulars_place_holder"></div>
						</div>
					</div>

					<div class="form-group m-form__group row  d-none">
						<div class="col-lg-3">
							<button type="button" class="btn btn-sm btn-secondary" id="add-new-line">
								<i class="la la-plus"></i>
								<span>
	                				<?php echo translate("Add new line");?>
								</span>
							</button>
						</div>
					</div>
	            </div>
	        	<div class="m-form__actions ">
					<button type="submit" class="btn btn-primary float-right">
	            		<?php echo translate("Continue");?>
						<i class="la la-arrow-right"></i>
					</button>
				</div>
			<?php }?>
		<?php echo form_close(); ?>
		<!--end::Form-->
		<a href="javascript:;" class="submit_withdrawal_popup d-none" data-toggle="modal" data-target="#submit_withdrawal_popup"></a>
	</div>
</div>


<div class="modal fade" id="submit_withdrawal_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-body">
      	<div id="options_holder">
      		<h5>
                <?php echo translate("How are you disbursing this withdrawal");?>?
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">×</span>
			    </button>
			</h5>
			<div class="row page_menus pt-4" style="min-height: 150px;">
				<div class="col-md-6">
	                <a href="javascript:;" class="mobile_money_disbursement">
	                    <div class="withdrawal_item">
	                        <div class="menu_img">
	                            <i class="img mdi mdi-cash-multiple"></i>
	                        </div>
	                        <div class="menu_cont">
	                            <div class="menu_cont_hdr">
	                                <div class="overflow_text">
                						<?php echo translate("Mobile Money Wallet Account");?>
	                                </div>
	                            </div>
	                            <div class="menu_cont_descr">
	                                <span>
                						<?php echo translate("Transfer money to a mobile wallet subject to approval by group account signatories");?>.
	                                </span>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	            </div>
				<div class="col-md-6">
	                <a href="javascript:;" class="bank_disbursement">
	                    <div class="withdrawal_item">
	                        <div class="menu_img">
	                            <i class="img mdi mdi-cash-multiple"></i>
	                        </div>
	                        <div class="menu_cont">
	                            <div class="menu_cont_hdr">
	                                <div class="overflow_text">
                						<?php echo translate("Bank Account");?>
	                                </div>
	                            </div>
	                            <div class="menu_cont_descr">
	                                <span>
                						<?php echo translate("Transfer money to a bank account subject to approval by group account signatories");?>.
	                                </span>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	            </div>
	      		<div class="col-md-6">
	                <a href="javascript:;" class="cash_payment d-none">
	                    <div class="withdrawal_item">
	                        <div class="menu_img">
	                            <i class="img mdi mdi-cash"></i>
	                        </div>
	                        <div class="menu_cont">
	                            <div class="menu_cont_hdr">
	                                <div class="overflow_text">
                						<?php echo translate("Cash Payment");?>
	                                </div>
	                            </div>
	                            <div class="menu_cont_descr">
	                                <span>
                						<?php echo translate("Record a cash payment from a transaction done outside the system or from another group account");?>.
	                                </span>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	            </div>
            </div>
      	</div>

      	<div id="mobile_money_disbursement" style="display: none;">
      		<h5>
                <?php echo translate("Mobile Money Account Disbursement");?>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">×</span>
			    </button>
			</h5>
			<button type="button" class="btn m-btn--square  btn-primary btn-sm mt-2 mb-4 back_to_disbursement_options">
				<i class="la la-hand-o-left"></i>
                <?php echo translate("Back to disbursement options");?>
            </button>
            <span class="error"></span>

			<!--begin::Form-->
			<?php echo form_open($this->uri->uri_string(),"class='m-form m-form--label-align-right m-form--group-seperator-dashed_ m-form--state mobile_money_disbursement_form'"); ?>
				<div class="form-group m-form__group row">
					<div class="col-lg-6 d-none">
						<label>
            				<?php echo translate("Disburse to");?>:
						</label>
						<span class="m-input--air">
	                        <?php echo form_dropdown('transfer_to',array(''=>translate('Select Option'))+translate($transfer_to_options),'1',' class="m-select2-append form-control m-input transfer_to" ');?>
	                    </span>
						<span class="m-form__help">
            				<?php echo translate("Select the disbursement channel");?>
						</span>
					</div>

					<div class="col-lg-12 recipient">
						<label class="">
            				<?php echo translate("Select Recipient");?>:
						</label>
						<span class="m-input--air disbursement_recipient">
	                        <?php echo form_dropdown('recipient',array(''=>translate('--Select Recipient--'),0 =>translate('Create New Recipient'))+$mobile_money_account_recipients,'','class="form-control m-select2-append recipient" id="recipient"');?>
	                    </span>
						<span class="m-form__help">
            				<?php echo translate("Select the recipient of the disbursement");?>
						</span>
					</div>

				</div>

				<div class="m-form__actions m-form__actions---solid  p-0">
					<div class="row">
						<div class="col-lg-12 m--align-right">
							<button class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
            					<?php echo translate("Cancel");?>
							</button>
							<button type="submit" class="btn btn-primary" id="submit_mobile_money_disbursement">
            					<?php echo translate("Save");?>
							</button>
						</div>
					</div>
				</div>
				<?php echo form_hidden('disbursement_channel',1,'');?>
           	<?php echo form_close(); ?>
			<!--end::Form-->

      	</div>
      	<div id="bank_disbursement" style="display: none;">
      		<h5>
                <?php echo translate("Bank Disbursement");?>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">×</span>
			    </button>
			</h5>
			<button type="button" class="btn m-btn--square  btn-primary btn-sm mt-2 mb-4 back_to_disbursement_options">
				<i class="la la-hand-o-left"></i>
                <?php echo translate("Back to disbursement options");?>
            </button>
            <span class="error"></span>

			<!--begin::Form-->
			<?php echo form_open($this->uri->uri_string(),"class='m-form m-form--label-align-right m-form--group-seperator-dashed_ m-form--state bank_disbursement_form'"); ?>
				<div class="form-group m-form__group row">
					<div class="col-lg-6 d-none">
						<label>
            				<?php echo translate("Disburse to");?>:
						</label>
						<span class="m-input--air">
	                        <?php echo form_dropdown('transfer_to',array(''=>translate('Select Option'))+translate($transfer_to_options),'3',' class="m-select2-append form-control m-input transfer_to" ');?>
	                    </span>
						<span class="m-form__help">
            				<?php echo translate("Select the disbursement channel");?>
						</span>
					</div>

					<div class="col-lg-12 recipient">
						<label class="">
            				<?php echo translate("Select Recipient");?>:
						</label>
						<span class="m-input--air disbursement_recipient">
	                        <?php echo form_dropdown('recipient',array(''=>translate('--Select Recipient--'),0 =>translate('Create New Recipient'))+$bank_account_recipients,'','class="form-control m-select2-append recipient" id="recipient"');?>
	                    </span>
						<span class="m-form__help">
            				<?php echo translate("Select the recipient of the disbursement");?>
						</span>
					</div>
				</div>

				<div class="m-form__actions m-form__actions---solid p-0">
					<div class="row">
						<div class="col-lg-12 m--align-right">
							<button class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
            					<?php echo translate("Cancel");?>
							</button>
							<button type="submit" class="btn btn-primary" id="submit_bank_disbursement">
            					<?php echo translate("Save");?>
							</button>
						</div>
					</div>
				</div>
				<?php echo form_hidden('disbursement_channel',2,'');?>
           	<?php echo form_close(); ?>
			<!--end::Form-->
      	</div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="add_mobile_money_recipient_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">New Mobile Money Account Recipient</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
			<?php echo form_open(current_url(),'class="m-form m-form--state m-form--label-align-right" id="new_mobile_money_recipient_form"');?>
				<span class="error"></span>
				<div class="form-group m-form__group row">
					<div class="col-lg-6">
						<label>Recipient Name:</label>
                        <?php echo form_hidden('type',"1",'');?>
                        <?php echo form_input('name',"",'  class="form-control  m-input" placeholder="Recipient Name" id="name"');?>
						<span class="m-form__help">Enter recipient's name</span>
					</div>
					<div class="col-lg-6">
						<label>Phone Number:</label>
						<div class="m-input">
                   			<?php echo form_input('phone_number',"",'  class="form-control m-input" placeholder="Phone Number" id="phone_number"');?>
						</div>
						<span class="m-form__help">Enter the recipients phone number</span>
					</div>
				</div>

				<div class="mobile_money_account">
					<div class="form-group m-form__group row">
						<div class="col-lg-12">
							<label class="">Description:</label>
							<div class="m-input">
								<?php 
                                    $textarea = array(
                                        'name' => 'description',
                                        'id' => '',
                                        'value' => '',
                                        'cols' => 25,
                                        'rows' => 5,
                                        'maxlength '=> '',
                                        'class' => 'form-control  m-input',
                                        'placeholder' => ''
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
							</div>
							<span class="m-form__help">Write an optional description</span>
						</div>
					</div>	 
				</div>

	            <div class="m-form__actions pl-0 pr-0">
					<div class="row">
						<div class="col-lg-6">
						</div>
						<div class="col-lg-6 m--align-right">
           					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary submit_new_recipient">Save</button>
						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
			</form>
          </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_bank_recipient_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?php echo translate('New Bank Account Recipient');?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
			<?php echo form_open(current_url(),'class="m-form m-form--state m-form--label-align-right" id="new_bank_recipient_form"');?>
				<span class="error"></span>
				<div class="form-group m-form__group row account_name_form_field d-none">
					<div class="col-lg-6">
						<label>Account Name:</label>
                        <?php echo form_hidden('type',"3",'');?>
                        <?php echo form_input('account_name',"",'  class="form-control  m-input" placeholder="Account Name" id="name"');?>
					</div>
					<div class="col-lg-6">
						<label>Account Currency:</label>
                        <?php echo form_input('account_currency',"",'  class="form-control  m-input" placeholder="Account Currency" id="name"');?>
					</div>
				</div>

				<div class="bank_account">
					<div class="form-group m-form__group row">
						<div class="col-lg-6">
							<label>Recipient Country:</label>
							<div class="m-input">
                       			<?php echo form_dropdown('recipient_bank_id',array(''=>'--Select--',54=>'Kenya',55=>"Tanzania"),55,'  class="form-control m-select2 m-input" placeholder="Account Name" id="account_name"');?>
							</div>
							<span class="m-form__help">Select recipient country</span>
						</div>
						<div class="col-lg-6">
							<label class="">Account Number:</label>
							<div class="m-input">
                       			<?php echo form_input('account_number',"",'  class="form-control  m-input" placeholder="Account Number" id="account_number"');?>
							</div>
							<span class="m-form__help">Enter the bank account number</span>
						</div>
					</div>
					<!-- <div class="form-group m-form__group row">
						<div class="col-lg-12">
							<label>Description:</label>
							<div class="m-input">
								<?php 
                                    $textarea = array(
                                        'name' => 'description',
                                        'id' => '',
                                        'value' => '',
                                        'cols' => 25,
                                        'rows' => 5,
                                        'maxlength '=> '',
                                        'class' => 'form-control  m-input',
                                        'placeholder' => ''
                                    ); 
                                    echo form_textarea($textarea);
                                ?>
							</div>
							<span class="m-form__help">Write an optional description</span>
						</div>
					</div> -->
					<button class="account_lookup btn btn-primary" type="button" >Look Up Account Details</button>	 
				</div>

	            <div class="m-form__actions pl-0 pr-0 d-none">
					<div class="row">
						<div class="col-lg-6">
						</div>
						<div class="col-lg-6 m--align-right">
           					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary submit_new_recipient">Save</button>
						</div>
					</div>
				</div>
				<?php echo form_close(); ?>
			</form>
          </div>
          <!-- <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary">Save</button>
          </div> -->
        </div>
    </div>
</div>

<a class="inline d-none add_bank_recipient_popup" data-toggle="modal" data-backdrop="false" data-keyboard="false" data-target="#add_bank_recipient_popup" data-title="Add BankRecipient" data-id="add_bank_recipient_popup"><?php echo translate('Add bank Recipient');?></a>

<a class="inline d-none add_mobile_money_recipient_popup" data-backdrop="false" data-keyboard="false" data-toggle="modal" data-target="#add_mobile_money_recipient_popup" data-title="Add Mobile Money Recipient" data-id="add_mobile_money_recipient_popup"><?php echo translate('Add mobile money Recipient');?></a>

<script>
	$(document).ready(function(){
		$(".m-select2").select2({
            placeholder:{
                id: '-1',
                text: "--Select option--",
            }, 
        });

   		$(document).on('change','.withdrawal_for',function(){
			$(this).parent().parent().parent().find('.payment_fields').each(function(){
	            $(this).remove();
	        });
        	var element = $(this).parent().parent().parent().find('.withdrawal_for_holder');
			if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
            	$(element).after('<div class="col-8 particulars_place_holder"></div>');
			}else{
				$(this).parent().parent().removeClass('has-danger');
				if($(this).val() == 1){
					var html = $('.loan_withdrawal_fields').html();
           			$(element).after(html);
           			$(this).parent().parent().parent().find('.particulars_place_holder').remove();
				}else if($(this).val() == 2){
					var html = $('.expense_withdrawal_fields').html();
           			$(element).after(html);
           			$(this).parent().parent().parent().find('.particulars_place_holder').remove();
				}else if($(this).val() == 3){
					var html = $('.dividend_withdrawal_fields').html();
           			$(element).after(html);
           			$(this).parent().parent().parent().find('.particulars_place_holder').remove();
				}else if($(this).val() == 4){
					var html = $('.welfare_withdrawal_fields').html();
           			$(element).after(html);
           			$(this).parent().parent().parent().find('.particulars_place_holder').remove();
				}else if($(this).val() == 5){
					var html = $('.shares_refund_withdrawal_fields').html();
           			$(element).after(html);
           			$(this).parent().parent().parent().find('.particulars_place_holder').remove();
				}else if($(this).val() == 6){
					var html = $('.account_withdrawal_fields').html();
           			$(element).after(html);
           			$(this).parent().parent().parent().find('.particulars_place_holder').remove();
				}
				//update_field_names($('.make_withdrawals_form'));
		        $('.make_withdrawals_form .m-select2-append').select2({
		            width:'100%',
		            placeholder:{
		                id: '-1',
		                text: "--Select option--",
		            }, 
		        });
			}
			FormInputMask.init();
		});

		$('.make_withdrawals_form #add-new-line').on('click',function(){
	        var html = $('#append-new-line').html();
	        $('.make_withdrawals_form #append-place-holder').append(html);
	        update_field_names($('.make_withdrawals_form'));
	        $('.make_withdrawals_form .m-select2-append').select2({
	            width:'100%',
	            placeholder:{
	                id: '-1',
	                text: "--Select option--",
	            }, 
	        });
	    });
	    //add bank recipient modal open eventt
        $('#add_bank_recipient_popup').on('shown.bs.modal', function () {
        	$('#new_bank_recipient_form .account_name_form_field').addClass('d-none');
        	$('#new_bank_recipient_form #account_number').val('').removeAttr('readonly');
        	$('#new_bank_recipient_form .m-form__actions').addClass('d-none');
        	$('#new_bank_recipient_form .account_lookup').slideDown('');
            $('#add_bank_recipient_popup .m-select2-append').select2({
	            width:'100%',
	            placeholder:{
	                id: '-1',
	                text: "--Select option--",
	            }, 
	        });
        });


        $('#submit_withdrawal_popup').on('shown.bs.modal', function () {
            $('#submit_withdrawal_popup .m-select2-append').select2({
	            width:'100%',
	            placeholder:{
	                id: '-1',
	                text: "--Select option--",
	            }, 
	        });
        });
         
        $(document).on('submit','#new_mobile_money_recipient_form',function(e){
			e.preventDefault();
			mApp.block('#new_mobile_money_recipient_form', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#new_mobile_money_recipient_form #submit_new_recipient').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var form = $("#new_mobile_money_recipient_form");
            $.ajax({
                url:'<?php echo site_url('ajax/recipients/create')?>',
                type:'POST',
                data:form.serialize(),
                success:function(response){
                    if(isJson(response)){
        				var data = $.parseJSON(response);
        				if(data.status == 1){
        					var recipient = data.recipient;
	                        var optiongroups = [];
	                        var optiongroup = "Mobile Money Recipients";
	                        $('.mobile_money_disbursement_form #recipient optgroup').each(function(){
		                        var val = $(this).attr("label");
	                        	optiongroups.push(val);
	                            if(val == optiongroup){
	                                $(this).append(
	                                	'<option value="' + recipient.id + '">' + recipient.name+ '('+recipient.phone_number+') </option>'
	                                ).parent().trigger('change');
	                                $('.mobile_money_disbursement_form #recipient').val(recipient.id).trigger('change');
	                            }
	                        });

	                        if($.inArray(optiongroup,optiongroups) == -1){
	                            $('.mobile_money_disbursement_form #recipient').append(
	                                '<optgroup label="'+optiongroup+'"><option value="' + recipient.id + '">' + recipient.name+ '('+recipient.phone_number+') </option></optgroup>').trigger('change');
	                            $('.mobile_money_disbursement_form #recipient').val(recipient.id).trigger('change');
	                        }
	                		$('#add_mobile_money_recipient_popup .close').trigger('click');
	                	}else{
	                		if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#add_mobile_money_recipient_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#add_mobile_money_recipient_popup select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                                $('.m-form__help').hide();
                            }
	                		$('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
	                	}
	                	mApp.unblock('#new_mobile_money_recipient_form');
                    }else{
                    	$('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please refresh and try again</div>');
                    	mApp.unblock('#new_mobile_money_recipient_form');
        				$('#add_mobile_money_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    }
                },
                always:function(){
                	$('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please try again</div>');
                	mApp.unblock('#new_mobile_money_recipient_form');
    				$('#add_mobile_money_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                },
                error:function(){
                	$('#add_mobile_money_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please try again</div>');
                	mApp.unblock('#new_mobile_money_recipient_form');
    				$('#add_mobile_money_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                }
            });
		});

		$(document).on('submit','#new_bank_recipient_form',function(e){
			e.preventDefault();
			mApp.block('#new_bank_recipient_form', {
                overlayColor: 'grey',
                animate: true,
                type: 'loader',
                state: 'primary',
                message: 'Processing...'
            });
            $('#new_bank_recipient_form #submit_new_recipient').addClass('m-loader m-loader--light m-loader--left').attr('disabled',true);
            var form = $("#new_bank_recipient_form");
            $.ajax({
                url:'<?php echo site_url('ajax/recipients/create')?>',
                type:'POST',
                data:form.serialize(),
                success:function(response){
                    if(isJson(response)){
        				var data = $.parseJSON(response);
        				if(data.status == 1){
        					var recipient = data.recipient;
	                        var optiongroups = [];
                        	var optiongroup = "Bank Recipients";
	                        $('.bank_disbursement_form #recipient optgroup').each(function(){
		                        var val = $(this).attr("label");
	                        	optiongroups.push(val);
	                            if(val == optiongroup){
	                                $(this).append(
	                                	'<option value="' + recipient.id + '">' + recipient.name + ' ('+recipient.account_name + ' - '+recipient.account_number + ' ) </option>').parent().trigger('change');
	                                $('.bank_disbursement_form #recipient').val(recipient.id).trigger('change');
	                            }
	                        });

	                        if($.inArray(optiongroup,optiongroups) == -1){
	                            $('.bank_disbursement_form #recipient').append(
	                                '<optgroup label="'+optiongroup+'"><option value="' + recipient.id + '">' + recipient.name + ' ('+recipient.account_name + ' - '+recipient.account_number + ' ) </option></optgroup>').trigger('change');
	                            $('.bank_disbursement_form #recipient').val(recipient.id).trigger('change');
	                        }
	                		$('#add_bank_recipient_popup .close').trigger('click');
	                	}else{
	                		if(data.validation_errors){
                                $.each(data.validation_errors, function( key, value ) {
                                    var error_message ='<div class="form-control-feedback">'+value+'</div>';
                                    $('#add_bank_recipient_popup input[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                    $('#add_bank_recipient_popup select[name="'+key+'"]').parent().addClass('has-danger').append(error_message);
                                });
                                $('.m-form__help').hide();
                            }
	                		$('#add_bank_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
	                	}
	                	mApp.unblock('#new_bank_recipient_form');
                    }else{
                    	$('#add_bank_recipient_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>An error was encountered processing your request. Please refresh and try again</div>');
                    	mApp.unblock('#new_bank_recipient_form');
        				$('#add_bank_recipient_popup #submit_new_recipient').removeClass('m-loader m-loader--light m-loader--left').attr('disabled',false);
                    }
                },
            });
		});

		$(document).on('change','#submit_withdrawal_popup .bank_disbursement_form #recipient',function(){
			if($(this).val() == ''){
                $(this).parent().parent().addClass('has-danger');
			}else{
                $(this).parent().parent().removeClass('has-danger');
				if($(this).val() == 0){
					$('.add_bank_recipient_popup').trigger('click');
	                $(this).val("").trigger('change');
	                $(this).trigger("select2:close");
				}
			} 
		});

		$(document).on('change','#submit_withdrawal_popup .mobile_money_disbursement_form #recipient',function(){
			if($(this).val() == ''){
                $(this).parent().parent().addClass('has-danger');
			}else{
                $(this).parent().parent().removeClass('has-danger');
				if($(this).val() == 0){
					$('.add_mobile_money_recipient_popup').trigger('click');
	                $(this).val("").trigger('change');
	                $(this).trigger("select2:close");
				}
			} 
		});
		
	    $(document).on('click','.make_withdrawals_form a.remove-line',function(event){
	        if($('.make_withdrawals_form .withdrawal_for_holder').length == 1){
	        	$('.make_withdrawals_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> You cannot remove the last line.</div>');
	        }else{
	            $(this).parent().parent().parent().remove();
	        }
	        update_field_names($('.make_withdrawals_form'));
	    });
	    
	    $(document).on('blur keyup','.make_withdrawals_form input.amount',function(){
	        if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
    			regex = /^[0-9.,\b]+$/;;
		        if(regex.test($(this).val())){
					$(this).parent().parent().removeClass('has-danger');
		        }else{
					$(this).parent().parent().addClass('has-danger');
		        }
    		}
	    });

	    $('.make_withdrawals_form').on('submit',function(e){
        	e.preventDefault();
            var entries_are_valid = true;

            var bank_account_id = $('.make_withdrawals_form select.bank_account_id').val();
            if(bank_account_id == ''){
            	entries_are_valid = false;
            	$('.make_withdrawals_form select.bank_account_id').parent().parent().addClass('has-danger');
            }else{
            	$('.make_withdrawals_form select.bank_account_id').parent().parent().removeClass('has-danger');
            }

        	$('.make_withdrawals_form select.withdrawal_for').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});
        	$('.make_withdrawals_form select.member').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

        	$('.make_withdrawals_form select.loan_type').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

        	$('.make_withdrawals_form .amount').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
        			regex = /^[0-9.,\b]+$/;;
			        if(regex.test($(this).val())){
						$(this).parent().parent().removeClass('has-danger');
			        }else{
						$(this).parent().parent().addClass('has-danger');
		            	entries_are_valid = false;
			        }
        		}
        	});

        	$('.make_withdrawals_form select.expense_category').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

        	$('.make_withdrawals_form select.contribution').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

        	$('.make_withdrawals_form .description').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

        	$('.make_withdrawals_form select.recipient').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

			$('.make_withdrawals_form select.account_to_id').each(function(){
        		if($(this).val() == ''){
            		entries_are_valid = false;
					$(this).parent().parent().addClass('has-danger');
        		}else{
					$(this).parent().parent().removeClass('has-danger');
        		}
        	});

        	if(entries_are_valid){
        		$('.submit_withdrawal_popup').click();
        	}else{
        		$('.make_withdrawals_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error on the form. Correct the higlighted fields and try submitting again.</div>');
        	}

        });

	    $(document).on('click','.cash_payment',function(){
	    	// $('#options_holder').slideUp('slow');
	    	// $('#cash_payment').slideDown('fast');
	    	alert('Under Construction');
	    });

	    $(document).on('click','.bank_disbursement',function(){
	    	$('#bank_disbursement').slideDown();
	    	$('#options_holder').hide();
	    	$('#bank_disbursement .m-select2-append').select2({
	            width:'100%',
	            placeholder:{
	                id: '-1',
	                text: "--Select option--",
	            }, 
	        });
	    });

	    $(document).on('click','.mobile_money_disbursement',function(){
	    	$('#mobile_money_disbursement').slideDown();
	    	$('#options_holder').hide();
	    	$('#mobile_money_disbursement .m-select2-append').select2({
	            width:'100%',
	            placeholder:{
	                id: '-1',
	                text: "--Select option--",
	            }, 
	        });
	    });

	    $(document).on('click','.back_to_disbursement_options',function(){
	    	$('#options_holder').slideDown();
	    	$('#bank_disbursement').hide();
	    	$('#mobile_money_disbursement').hide();
	    	$('#cash_payment').hide();
	    });

	    $(document).on('change','#bank_disbursement .transfer_to',function(){
	    	mApp.block('#bank_disbursement .recipient', {
	            overlayColor: 'grey',
	            animate: true,
	            type: 'loader',
	            state: 'primary',
	        });
	    	if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
				$('#bank_disbursement .bank').slideUp('fast');
		    	$('#bank_disbursement .paybill').slideUp('fast');
		    	$('#bank_disbursement .bank_account_details').slideUp('fast');
		    	// $('#bank_disbursement .description').slideUp('fast');
		    	$('#bank_disbursement .recipient').slideUp('fast');
                mApp.unblock('.recipient');
	    	}else{
				$(this).parent().parent().removeClass('has-danger');
				if($(this).val() == 1){
					$('#bank_disbursement .bank').slideUp('fast');
			    	$('#bank_disbursement .paybill').slideUp('fast');
			    	$('#bank_disbursement .bank_account_details').slideUp('fast');
			    	$('#bank_disbursement .recipient').slideDown('slow');
			    	// $('#bank_disbursement .description').slideDown('slow');
			    	$.ajax({
			            type: "GET",
			            url: '<?php echo base_url("ajax/recipients/get_group_mobile_money_account_recipients"); ?>',
			            dataType : "html",
			                success: function(response) {
				                $('.disbursement_recipient').html(response);
		                    	$('#bank_disbursement .m-select2-append').select2({
						            width:'100%',
						            placeholder:{
						                id: '-1',
						                text: "--Select option--",
						            }, 
						        });
                				mApp.unblock('.recipient');
			                }
				    });
			    	$('#bank_disbursement .recipient').slideDown('slow');
				}else if($(this).val() == 2){
					$('#bank_disbursement .bank').slideUp('fast');
			    	$('#bank_disbursement .bank_account_details').slideUp('fast');
			    	$('#bank_disbursement .recipient').slideUp('fast');
	    			$('#bank_disbursement .paybill').slideDown('slow');
			    	// $('#bank_disbursement .description').slideDown('slow');
			    	$.ajax({
			            type: "GET",
			            url: '<?php echo base_url("ajax/recipients/get_group_paybill_account_recipients"); ?>',
			            dataType : "html",
			                success: function(response) {
				                $('.disbursement_recipient').html(response);
		                    	$('#bank_disbursement .m-select2-append').select2({
						            width:'100%',
						            placeholder:{
						                id: '-1',
						                text: "--Select option--",
						            }, 
						        });
                				mApp.unblock('.recipient');
			    				$('#bank_disbursement .recipient').slideDown('slow');
			                }
				    });
				}else if($(this).val() == 3){
					$('#bank_disbursement .paybill').slideUp('fast');
			    	// $('#bank_disbursement .description').slideUp('fast');
			    	// $('#bank_disbursement .recipient').slideUp('fast');
					$.ajax({
			            type: "GET",
			            url: '<?php echo base_url("ajax/recipients/get_group_bank_account_recipients"); ?>',
			            dataType : "html",
			                success: function(response) {
				                $('.disbursement_recipient').html(response);
		                    	$('#bank_disbursement .m-select2-append').select2({
						            width:'100%',
						            placeholder:{
						                id: '-1',
						                text: "--Select option--",
						            }, 
						        });
                				mApp.unblock('.recipient');
			    				$('#bank_disbursement .recipient').slideDown('slow');
			                }
				    });
	    			// $('#bank_disbursement .bank').slideDown('slow');
	    			// $('#bank_disbursement .bank_account_details').slideDown('slow');
				}
	    	}
	    	// 

	    });

	    $(document).on('change','.make_withdrawals_form select.withdrawal_for',function(){
    		if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
				$(this).parent().parent().removeClass('has-danger');
    		}
    	});

    	$(document).on('change','.make_withdrawals_form select.member',function(){
    		if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
				$(this).parent().parent().removeClass('has-danger');
    		}
    	});

    	$(document).on('change','.make_withdrawals_form select.loan_type',function(){
    		if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
				$(this).parent().parent().removeClass('has-danger');
    		}
    	});

    	$(document).on('change','.make_withdrawals_form select.expense_category',function(){
    		if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
				$(this).parent().parent().removeClass('has-danger');
    		}
    	});

    	$(document).on('change','.make_withdrawals_form .description',function(){
    		if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
				$(this).parent().parent().removeClass('has-danger');
    		}
    	});

    	$(document).on('change','.make_withdrawals_form select.recipient',function(){
    		if($(this).val() == ''){
				$(this).parent().parent().addClass('has-danger');
    		}else{
				$(this).parent().parent().removeClass('has-danger');
    		}
    	});
    	
	    // 1 => 'Mobile Wallet',
       //  2 => 'Paybill',
       //  3 => 'Bank Account',

		$(document).on('submit','.bank_disbursement_form',function(e){
	    	e.preventDefault();
            var entries_are_valid = true;
            if($('#submit_withdrawal_popup select.recipient :visible').val() == ''){
            	entries_are_valid = false;
				$('#submit_withdrawal_popup select.recipient :visible').parent().parent().addClass('has-danger');
			}
	  
    		if(entries_are_valid){
	            swal({
	                title:"<?php echo translate('Are you sure');?>?",
	                text:"<?php echo translate('All group account signatories will receive a withdrawal request to approve or decline this request');?>",
	                type:"info",
	                showCancelButton:1,
	                reverseButtons:1,
	                confirmButtonText:"Yes, request!"
	            }).then(function(e){
	            	if(e.value){
	            		mApp.block('#submit_withdrawal_popup', {
			                overlayColor: 'grey',
			                animate: true,
			                type: 'loader',
			                state: 'primary',
			                message: 'Processing...'
			            });
		    			$.ajax({
			                type: "POST",
			                url: '<?php echo base_url("ajax/withdrawals/withdraw_money"); ?>',
			    			data: $('.bank_disbursement_form, .make_withdrawals_form').serialize(), //we are going to submit the two forms as one
			                success: function(response) {
			                    if(isJson(response)){
			                        var data = $.parseJSON(response);
			                        if(data.hasOwnProperty("status")){
				                        if(data.status == 1){
		                           	 		e.value&&swal("Success!",data.message,"success").then(function(){
				                    			$('#submit_withdrawal_popup .close').click();
				                            	window.location.href = data.refer;
		                           	 		});
				                        }else if(data.status == '202'){
	                                        Toastr.show("Session Expired",data.message,'error');
	                                        window.location.href = data.refer;
                                    	}else{
				                            $('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
				                            if(data.hasOwnProperty('errors')){
					                        	if(data.errors.length){
					                        		$('.bank_disbursement_form .m-form__help').hide();
					                        		$.each(data.errors,function(key,val){
				                                		$("[name='"+val.field+"']").parent().parent().addClass('has-danger').append('<div class="form-control-feedback">'+val.message+'</div>');
				                                		$("[select='"+val.field+"']").parent().parent().addClass('has-danger').append('<div class="form-control-feedback">'+val.message+'</div>');
						                        	});
					                        	}else{
					                        		$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing the withdrawal request. Please refresh the page and try submitting again.</div>');
					                        	}
					                        }else if(data.hasOwnProperty('validation_errors')){
					                        	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are some errors on the form. Ensure all the fields are correct.</div>');
					                        }
				                        }
				                    }else{
				                    	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> Error encounted. Try again later.</div>');
				                    }
	                        		mApp.unblock('#submit_withdrawal_popup');
			                    }else{
			                    	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error processing the withdrawal request. Please refresh the page and try submitting again.</div>');
	                        		mApp.unblock('#submit_withdrawal_popup');
			                    }
			                },
			                always:function(){
			                	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error processing the withdrawal request. Please refresh the page and try submitting again.</div>');
	                        	mApp.unblock('#submit_withdrawal_popup');
			                },
			                error:function(){
			                	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error processing the withdrawal request. Please refresh the page and try submitting again.</div>');
	                        	mApp.unblock('#submit_withdrawal_popup');
			                }
			            });
	            	}
	    		});
    		}else{
    			$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error on the form. Correct the higlighted fields and try submitting again.</div>');
    		}
	    });

		$(document).on('submit','.mobile_money_disbursement_form',function(e){
	    	e.preventDefault();
            var entries_are_valid = true;
            if($('#submit_withdrawal_popup select.recipient :visible').val() == ''){
            	entries_are_valid = false;
				$('#submit_withdrawal_popup select.recipient :visible').parent().parent().addClass('has-danger');
			}
    		if(entries_are_valid){
	            swal({
	                title:"<?php echo translate('Are you sure');?>?",
	                text:"<?php echo translate('All group signatories will receive a withdrawal request');?>",
	                type:"info",
	                showCancelButton:1,
	                reverseButtons:1,
	                confirmButtonText:"Yes, request!"
	            }).then(function(e){
	            	if(e.value){
	            		mApp.block('#submit_withdrawal_popup', {
			                overlayColor: 'grey',
			                animate: true,
			                type: 'loader',
			                state: 'primary',
			                message: 'Processing...'
			            });
		    			$.ajax({
			                type: "POST",
			                url: '<?php echo base_url("ajax/withdrawals/withdraw_money"); ?>',
			    			data: $('.mobile_money_disbursement_form, .make_withdrawals_form').serialize(), //we are going to submit the two forms as one
			                success: function(response) {
			                    if(isJson(response)){
			                        var data = $.parseJSON(response);
			                        if(data.hasOwnProperty("status")){
				                        if(data.status == 1){
		                           	 		e.value&&swal("Success!",data.message,"success").then(function(){
				                    			$('#submit_withdrawal_popup .close').click();
				                            	window.location.href = data.refer;
		                           	 		});
				                        }else{
				                        	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+data.message+'</div>');
				                        	if(data.hasOwnProperty('errors')){
					                        	if(data.errors.length){
					                        		$('.mobile_money_disbursement_form .m-form__help').hide();
					                        		$.each(data.errors,function(key,val){
				                                		$("[name='"+val.field+"']").parent().parent().addClass('has-danger').append('<div class="form-control-feedback">'+val.message+'</div>');
				                                		$("[select='"+val.field+"']").parent().parent().addClass('has-danger').append('<div class="form-control-feedback">'+val.message+'</div>');
						                        	});
					                        	}else{
					                        		$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There were errors processing the withdrawal request. Please refresh the page and try submitting again.</div>');
					                        	}
					                        }else if(data.hasOwnProperty('validation_errors')){
					                        	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are some errors on the form. Ensure all the fields are correct.</div>');
					                        }
				                        }
				                    }else{
				                    	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> Error encounted. Try again later.</div>');
				                    }
	                        		mApp.unblock('#submit_withdrawal_popup');
			                    }else{
			                    	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error processing the withdrawal request. Please refresh the page and try submitting again.</div>');
	                        		mApp.unblock('#submit_withdrawal_popup');
			                    }
			                },
			                always:function(){
			                	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error processing the withdrawal request. Please refresh the page and try submitting again.</div>');
	                        	mApp.unblock('#submit_withdrawal_popup');
			                },
			                error:function(){
			                	$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error processing the withdrawal request. Please refresh the page and try submitting again.</div>');
	                        	mApp.unblock('#submit_withdrawal_popup');
			                }
			            });
	            	}
	    		});
    		}else{
    			$('#submit_withdrawal_popup .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button><strong>Sorry!</strong> There are error on the form. Correct the higlighted fields and try submitting again.</div>');
    		}
	    });


		$(document).on('click','.account_lookup',function(){
			var account_number = $('#new_bank_recipient_form input[name="account_number"]').val();
			var recipient_bank_id = $('#new_bank_recipient_form select[name="recipient_bank_id"]').val();
			if(recipient_bank_id){
				$('#new_bank_recipient_form .error').html('');
				$('#new_bank_recipient_form input[name="recipient_bank_id"]').parent().parent().removeClass('has-danger');
				if(account_number){
					$('#new_bank_recipient_form .error').html('');
					$('#new_bank_recipient_form input[name="account_number"]').parent().parent().removeClass('has-danger');
					var content = $('#new_bank_recipient_form');
					mApp.block(content, {
		                overlayColor: 'grey',
		                animate: true,
		                type: 'loader',
		                state: 'primary',
		                message: 'Looking up account details...'
		            });
					$.ajax({
			            type: "POST",
			            url: '<?php echo base_url("ajax/recipients/lookup_account_details"); ?>',
			            data: {"account_number":account_number,"recipient_bank_id":recipient_bank_id,"ignore_bank_search":1},
			            success:function(response){
			            	if(isJson(response)){
			            		var res = $.parseJSON(response);
			            		if(res){
			            			if(res.status == 1){
			            				console.log('in herer');
			            				var account_name = res.account_name;
			            				var account_currency = res.account_currency;
			            				$('#new_bank_recipient_form input[name="account_name"]').val(account_name);
			            				$('#new_bank_recipient_form input[name="account_currency"]').val(account_currency);
			            				$('.account_lookup').hide();
			            				$('.account_name_form_field,#new_bank_recipient_form .m-form__actions').removeClass('d-none');
			            				$('#new_bank_recipient_form input[name="account_name"], #new_bank_recipient_form input[name="account_currency"], #new_bank_recipient_form input[name="account_number"]').attr('readonly','readonly');
			            			}else{
			            				$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>'+res.message+'</div>');
			            			}
			            		}else{
			            			$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Error encounted on account lookup. Try again later</div>');
			            		}
			            	}else{
			            		$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Error encounted on account lookup. Try again later</div>');
			            	}
			            	mApp.unblock(content);
			            },
			            error:function(){
			            	$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We are experiencing an error at the moment. Kindly refresh and try again</div>');
			            	mApp.unblock(content);
			            },
			            always: function(){
			            	$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>We are experiencing an error at the moment. Kindly refresh and try again</div>');
			            	mApp.unblock(content);
			            },
			        });
				}else{
					$('#new_bank_recipient_form input[name="account_number"]').parent().parent().addClass('has-danger');
					$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Account number field is required</div>');
				}
			}else{
				$('#new_bank_recipient_form select[name="recipient_bank_id"]').parent().parent().addClass('has-danger');
				$('#new_bank_recipient_form .error').html('<div class="alert alert-danger alert-dismissible fade show" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>Recipient Country field is required</div>');
				
			}
		});
	});

	function update_field_names(form){
        if (typeof form == 'undefined') {
            //do nothing
        }else{
            var number = 1;
            form.find('.withdrawal_row').each(function(){
                $(this).find('.withdrawal_for').attr('name','withdrawal_fors['+(number-1)+']');
                $(this).find('.member').attr('name','members['+(number-1)+']');
                $(this).find('.loan_type').attr('name','loan_types['+(number-1)+']');
                $(this).find('.expense_category').attr('name','expense_categories['+(number-1)+']');
                $(this).find('.description').attr('name','descriptions['+(number-1)+']');
                $(this).find('.member').attr('name','members['+(number-1)+']');
                $(this).find('.recipient').attr('name','recipients['+(number-1)+']');
                $(this).find('.contribution').attr('name','contributions['+(number-1)+']');
                $(this).find('.amount').attr('name','amounts['+(number-1)+']');
                number++;
            }); 
            FormInputMask.init();
        }
    }

</script>
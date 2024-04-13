
<div class="col-md-12">
	<div class="row">
		<div class="col-md-12 p-0">
			<select id="settings_search" class="form-control settings-search">
                <option value="" selected="selected">Find a setting</option>
			</select>
		</div>
	</div>
	<div class='row page_menus mt-5'>
		<?php 
		$count = 1; $settings_menus_count = count($settings_menus); 
		foreach($settings_menus as $settings_menu): ?>
			<div class="settings-menu-icon col-md-4 p-0">
				<a href="<?php echo site_url($settings_menu->url); ?>" class="searchable_item">
					<div class="menu_item">
						<div class="menu_img">
							<i class="img settings-icon <?php echo $settings_menu->icon; ?>"></i>
						</div>
						<div class="menu_cont">
							<div class="menu_cont_hdr">
								<div class="overflow_text">
									<?php echo translate($settings_menu->name); ?> <span class="menu_cont_notif_count m-badge m-badge--success d-none deposits_count "></span>
								</div>
							</div>
							<div class="menu_cont_descr">
								<span><?php echo translate($settings_menu->description); ?></span>
							</div>
						</div>
					</div>
				</a>
			</div>
		<?php endforeach;?>	
	</div>
		<!-- <?php if($count==1): ?>
				<?php endif; ?>
				
				<?php 
				if(is_int($count/4)&&$count>0){
				?>
			</div>
			<div class='row'>
				<?php
				}else if($count==$settings_menus_count){
				?>
			</div>
		<?php } ?>
	<?php $count++;  ?> -->
</div>

<script>
	$(document).ready(function(){
		var max_height = 0;
		var height = 0;
		$('.settings-menu-icon').each(function(){
			if($(this).height()>height){
				max_height = $(this).height();
			}
			height = $(this).height();
		});
		$('.settings-menu-icon').height(max_height+"px");

		$('#settings_search').change(function(){
			var url = $(this).val();
			window.location = "../"+url;
		}).select2();
	});
</script>
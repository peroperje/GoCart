<?php include('header.php'); ?>

<div id="social_sharing">
	<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
	<a class="addthis_button_preferred_1"></a>
	<a class="addthis_button_preferred_2"></a>
	<a class="addthis_button_preferred_3"></a>
	<a class="addthis_button_preferred_4"></a>
	<a class="addthis_button_compact"></a>
	<a class="addthis_counter addthis_bubble_style"></a>
	</div>
	<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4ed7263599fdd0"></script>
	<!-- AddThis Button END -->
</div>

<div id="product_left">
	<div id="product_image">
		<?php
		//get the primary photo for the product
		$photo	= '<img src="'.$this->config->item('template_path').'/images/nopicture.png" alt="no picture available"/>';

		if(count($product->images) > 0 )
		{	
			$primary	= $product->images[0];
			foreach($product->images as $image)
			{
				if(isset($image->primary))
				{
					$primary	= $image;
				}
			}

			$photo	= '<a href="/uploads/images/medium/'.$primary->filename.'" rel="gallery" title="'.$primary->caption.'"><img src="/uploads/images/small/'.$primary->filename.'" alt="'.$product->slug.'"/></a>';
		}
		echo $photo;
	
	
		if(!empty($primary->caption)):?>
		<div id="product_caption">
			<?php echo $primary->caption;?>
		</div>
		<?php endif;?>
	</div>

	<?php

	$img_counter	= 1;
	if(count($product->images) > 0):?>
	<div id="product_thumbnails">
		<?php foreach($product->images as $image): ?>
			<div class="product_thumbnail" <?php if($img_counter == 3){echo'style="margin-right:0px;"'; $img_counter=1;}else{$img_counter++;}?>>
				<a rel="gallery" href="/uploads/images/medium/<?php echo $image->filename;?>" title="<?php echo $image->caption;?>"><img src="/uploads/images/thumbnails/<?php echo $image->filename;?>"/></a>
			</div>
		<?php endforeach;?>

	</div>
	<?php endif;?>
</div>


<?php echo form_open('cart/add_to_cart');?>
	
<input type="hidden" name="cartkey" value="<?php echo $this->session->flashdata('cartkey');?>" />
<input type="hidden" name="id" value="<?php echo $product->id?>"/>

<div id="product_right">	
	<div class="product_section">
		<div class="product_sku">SKU: <?php echo $product->sku; ?></div> 
		<?php if($product->saleprice > 0):?>
			<span class="price_slash">price: <?php echo format_currency($product->price); ?></span>
			<span class="price_sale">SALE: <?php echo format_currency($product->saleprice); ?></span>
		<?php else: ?>
			<span class="price_reg">Price: <?php echo format_currency($product->price); ?></span>
		<?php endif;?>
	</div>
	
	
	<?php if(count($options) > 0): ?>
		<div class="product_section">
		<h2>Available Options</h2>
		<?php	
		foreach($options as $option):
			$required	= '';
			if($option->required)
			{
				$required = ' <span class="red">*</span>';
			}
			?>
			<div class="option_container">
				<div class="option_name"><?php echo $option->name.$required;?></div>
				<?php
				/*
				this is where we generate the options and either use default values, or previously posted variables
				that we either returned for errors, or in some other releases of Go Cart the user may be editing
				and entry in their cart.
				*/
						
				//if we're dealing with a textfield or text area, grab the option value and store it in value
				if($option->type == 'checklist')
				{
					$value	= array();
					if($posted_options && isset($posted_options[$option->id]))
					{
						$value	= $posted_options[$option->id];
					}
				}
				else
				{
					$value	= $option->values[0]->value;
					if($posted_options && isset($posted_options[$option->id]))
					{
						$value	= $posted_options[$option->id];
					}
				}
						
				if($option->type == 'textfield'):?>
				
					<input type="textfield" id="input_<?php echo $option->id;?>" name="option[<?php echo $option->id;?>]" value="<?php echo $value;?>" />
				
				<?php elseif($option->type == 'textarea'):?>
					
					<textarea id="input_<?php echo $option->id;?>" name="option[<?php echo $option->id;?>]"><?php echo $value;?></textarea>
				
				<?php elseif($option->type == 'droplist'):?>
					<select name="option[<?php echo $option->id;?>]">
						<option value="">Choose an Option</option>
				
					<?php foreach ($option->values as $values):
						$selected	= '';
						if($value == $values->id)
						{
							$selected	= ' selected="selected"';
						}?>
						
						<option<?php echo $selected;?> value="<?php echo $values->id;?>">
							<?php echo($values->price != 0)?'('.format_currency($values->price).') ':''; echo $values->name;?>
						</option>
						
					<?php endforeach;?>
					</select>
				<?php elseif($option->type == 'radiolist'):
						foreach ($option->values as $values):

							$checked = '';
							if($value == $values->id)
							{
								$checked = ' checked="checked"';
							}?>
							
							<div>
							<input<?php echo $checked;?> type="radio" name="option[<?php echo $option->id;?>]" value="<?php echo $values->id;?>"/>
							<?php echo($values->price != 0)?'('.format_currency($values->price).') ':''; echo $values->name;?>
							</div>
						<?php endforeach;?>
				
				<?php elseif($option->type == 'checklist'):
					foreach ($option->values as $values):

						$checked = '';
						if(in_array($values->id, $value))
						{
							$checked = ' checked="checked"';
						}?>
						<div class="gc_option_list">
						<input<?php echo $checked;?> type="checkbox" name="option[<?php echo $option->id;?>][]" value="<?php echo $values->id;?>"/>
						<?php echo($values->price != 0)?'('.format_currency($values->price).') ':''; echo $values->name;?>
						</div>
					<?php endforeach ?>
				<?php endif;?>
				</div>
		<?php endforeach;?>
	</div>
	<?php endif; ?>
	<div class="product_section">	
		<div style="text-align:center; overflow:hidden;">
			<?php  if($this->config->item('allow_os_purchase') || $product->in_stock == 1) : ?>
			<?php if($product->in_stock == 0):?>
				<div class="red"><small>Out of Stock</small></div>
			<?endif;?>
			QTY <input class="product_quantity" type="text" name="quantity" value=""/>
			<input class="add_to_cart_btn" type="submit" value="Add To Cart" />
			<?php else: ?>
			<h2 class="red">Out of Stock</h2>
			<?php endif;?>
		</div>
	</div>
		
	</form>
	<div class="tabs">
		<ul>
			<li><a href="#description_tab">Description</a></li>
			<?php if(!empty($related)):?><li><a href="#related_tab">Related Products</a></li><?php endif;?>
		</ul>
		<div id="description_tab">
			<?php echo $product->description; ?>
		</div>
	
		<?php if(!empty($related)):?>
		<div id="related_tab">
			<?php
			$cat_counter=1;
			foreach($related as $product):
				if($cat_counter == 1):?>

				<div class="category_container">

				<?php endif;?>

				<div class="category_box">
					<div class="thumbnail">
						<?php
						$product->images	= array_values($product->images);
						if(count($product->images) > 0)
						{
							$primary	= $product->images[0];
							foreach($product->images as $photo)
							{
								if(isset($photo->primary))
								{
									$primary	= $photo;
								}
							}

							$photo	= '<img src="/uploads/images/thumbnails/'.$primary->filename.'" alt="'.$product->seo_title.'"/>';
						}
						?>
						<a href="/<?php echo $product->slug; ?>">
							<?php echo $photo; ?>
						</a>
					</div>
					<div class="gc_product_name">
						<a href="/<?php echo $product->slug; ?>"><?php echo $product->name;?></a>
					</div>
					<?php if($product->excerpt != ''): ?>
					<div class="excerpt"><?php echo $product->excerpt; ?></div>
					<?php endif; ?>
					<div>
						<?php if($product->saleprice > 0):?>
							<span class="gc_price_slash">price: <?php echo $product->price; ?></span>
							<span class="gc_price_sale">SALE: <?php echo $product->saleprice; ?></span>
						<?php else: ?>
							<span class="gc_price_reg">Price: <?php echo $product->price; ?></span>
						<?php endif; ?>
	                    <?php if($product->in_stock==0) { ?>
							<div class="gc_stock_msg">Out of Stock</div>
						<?php } ?>
					</div>
				</div>
			
				<?php 
				$cat_counter++;
				if($cat_counter == 5):?>
			
				
				</div>

				<?php 
				$cat_counter = 1;
				endif;
			endforeach;
		
			if($cat_counter != 1):?>
					<br class="clear"/>
				</div>
			<?php endif;?>
		</div>
		<?php endif;?>
	</div>

</div>

<script type="text/javascript"><!--
$(function(){ 
	$('.tabs').tabs();
	//$('#images_tab>a').colorbox();
	
	$('a[rel="gallery"]').colorbox({ width:'80%', height:'80%', scalePhotos:true });
	
	$('#related_tab').width($('#description_tab').width());

	var w	= parseInt(($('#related_tab').width()/4)-33);

	$('.category_box').width();
	$('.category_container').each(function(){
		$(this).children().equalHeights();
	});	
});



//--></script>

<?php include('footer.php'); ?>
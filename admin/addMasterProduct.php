<?php
include_once '../header/adminHeader.php';
$assessmentObj = new assessmentController(); //Existing Controler Obj for Get Details of Cource
$productObj = new productController();

$clientUserId=$assessmentObj->getSuperClientId($user_group_id );

$currencylist = $productObj->getcurrencylist();


$readonly = (isset($_GET['pid']) && !empty($_GET['pid']))? 'readonly':'';
if (isset($_GET['pid']) && !empty($_GET['pid'])) {
    $id = trim(base64_decode($_GET['pid']));
	
    $productdetailsbyid = $productObj->getAllMaterProductListById($id);
    $product = $productdetailsbyid[0];
    $prod_id = $product['id'];
    $prod_client_id = $product['client_id'];
    $prod_name = $product['product_name'];
    $prod_price = $product['price'];
    $prod_desc = $product['product_desc'];
    $prod_thumbnail = $product['thumbnail'];
    $prod_code = $product['code'];
    $package_code = $product['package_code'];
    $currency_code = $product['currency_code'];
    $prod_discount = $product['discount'];
    $discount_type = $product['discount_type'];
    $expire_on = $product['expire_on'];
    $productthumb = $product['thumbnail'];
    $status = $product['status'];
    $master_products_ids = $product['master_products_ids'];
   }
?><style>

  .panel-heading a:after {    width: 2%;
    font-family:'FontAwesome';
    content:"\f106";
    float: right;
    color: grey;
	font-size:14px;font-weight:700;position:absolute;    right: 0px;
}
 
.panel-heading a.collapsed:after {
    content:"\f107";
}

</style>
<ul class="breadcrumb no-border no-radius b-b b-light">
    <li> <a href="productList.php"><i class="fa fa-arrow-left"></i> Master Product List </a></li>
</ul>
<div class="clear"></div>
<section class="padder">
    <div class="row-centered">
        <div class="col-sm-10 col-xs-12 col-centered">
            <section class="marginBottom40">
			
	
			<?php  if (isset($_GET['pid']) && !empty($_GET['pid'])){ ?>
                <form action="ajax/masterProduct.php" id="manageproduct" class="manageproduct" name="manageproduct"
                    enctype="multipart/form-data" method="POST"  data-validate="parsley"  autocomplete="off" >
                    <div class="row">
                        <input type="hidden" name="product_id" value="<?php echo (isset($prod_id)) ? $prod_id : ''; ?>">
                        <div class="panel panel-default bdrNone">
                            <div class="panel-body padd20">
                                <h3 class="panel-header">Master Product Details</h3>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Product Name <span
                                                class="required">*</span></label>
                                        <input type="text" name="name" id="name" placeholder="Product Name"
                                            class="form-control input-lg " data-required="true"
                                            value="<?php echo (isset($prod_name)) ? $prod_name : ''; ?>" maxlength="250"
                                            autocomplete="off" onchange ="uniqprodname()" <?php echo $readonly; ?> />
                                        <div class="required error" id="name_error"></div>
                                        <input type="hidden" id="unique_name" value="">
                                    <input type="hidden" name="client_id"  value="<?php echo $client_id; ?>" />
							       <input type="hidden" name="master_product_id"  value="<?php echo $id; ?>" />
							
									</div>
									<div class="form-group col-sm-6">
                                        <label class="control-label">Product Description <span
                                                class="required"></span></label>
                                        <textarea type="text" name="description" id="description" placeholder="Product Description"
                                            class="form-control input-lg "maxlength="500"
                                            autocomplete="off"><?php echo $prod_desc; ?></textarea>
                                        <div class="required error" id="desc_error"></div>
                                       
									</div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">UniqueCode <span class="required">*</span></label>
                                        <input type="text" name="code" id="code" placeholder="Code"
                                            class="form-control input-lg "
                                            value="<?php echo (isset($prod_code)) ? $prod_code : ''; ?>" maxlength="30"
                                            autocomplete="off" onchange ="uniqprodcode()" data-required="true" <?php echo (isset($prod_code)) ? $readonly : ''; ?> />
                                        <div class="required error" id="code_error"></div>
                                        <input type="hidden" id="unique_code" value="">
                                    </div>

                                   
									 <div class="form-group col-sm-6"> 
                                        <label class="control-label">Status <span class="required">*</span></label>
                                            <select id="status" name="status" class="form-control"  data-required="true">
                                                <option value="">Select Status</option>
                                                <option  value="1" <?php echo $selectedStatus =($status=='1')?'selected' : '';?>>Active</option>	
                                                <option  value="0" <?php echo $selectedStatus =($status=='0')?'selected' : '';?>>Inactive</option>
                                            </select>
                                    </div>
                                    <div class="form-group col-sm-6">
									
                                        <div class="col-sm-6">
                                        <label class="control-label">Thumbnail</label>
                                        <input type="file" name="prod_thumbnail" id="prod_thumbnail"
                                            autocomplete="off" />
                                        </div>
                                        <div class="col-sm-6"><?php echo ($productthumb!='')? '<img src="../'.$productthumb.'" alt="Product Thumb" style="max-width: 60px;">':''; ?></div>
                                        <div class="required error" id="prod_thumbnail_error"></div>
                                    </div>
                                   
                                   
                                    <div class="clear"></div>
									
									
									<div class="form-group col-sm-6">
                                        <label class="control-label">Course Code <span >Multiple course with (,)</span></label>
                                        <input type="text" name="cCode" id="cCode" placeholder="Course Code"
                                            class="form-control input-lg "
                                            value="" 
                                            autocomplete="off"  />
                                        <div class="required error" id="coursecode_error"></div>
                                        <input type="hidden" id="course_code" value="">
                                    </div>
									
                                </div>
                                
                                <div class="clear"></div>
                            <div class="text-right">
							
                                <a href='' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
                                <button type="submit" class="btn btn-s-md btn-primary" name="saveproduct"
                                    id="saveproduct">Submit</button>
                            </div>
                        </div>
                       </div>
					</div>
                </form>
            
			<?php }else{?>
						<form action="ajax/masterProduct.php" id="masterProduct" class="masterProduct" name="masterProduct"
                    method="POST"  data-validate="parsley"  autocomplete="off" >
                    <div class="row">
                        <input type="hidden" name="master_product_id" value="<?php echo (isset($master_product_id)) ? $master_product_id : ''; ?>">
                        <div class="panel panel-default bdrNone">
                            <div class="panel-body padd20">
                                <h3 class="panel-header">Master Product Details</h3>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Master Product Name <span
                                                class="required">*</span></label>
                                        <input type="text" name="mname" id="mname" placeholder="Master Product Name"
                                            class="form-control input-lg " data-required="true"
                                            value="<?php //echo (isset($prod_name)) ? $prod_name : ''; ?>" maxlength="250"
                                            autocomplete="off"   />
                                        <div class="required error" id="mname_error"></div>
                                       
                                  
									</div>
									
									 <div class="text-right">
							<input type="hidden" name="client_id"  value="<?php echo $client_id; ?>" />
							
                                <a href='' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
                                <button type="submit" class="btn btn-s-md btn-primary" name="savemasterproduct"
                                    id="savemasterproduct" value="savemasterproduct">Submit</button>
                            </div>
                        </div>
				            </div>
						</div>
					</div>	
				</div>
			</form>	
			<?php }				?>
			</section>
        </div>
    </div>
</section>
<script>
$(document).ready(function() {

});

function checkAll(cid, lid, parent_node) {
    let total_child_uncheckd = 0;
    let total_children = parseInt($('#' + parent_node).attr('total-child'));

    if ($("#" + cid).is(':checked')) {
        $("#" + cid).prop('checked', true);
        $('#' + lid + ' input').prop('checked', true);
    } else {

        $("#" + cid).prop('checked', false);
        $('#' + lid + ' input').prop('checked', false);
    }
    $('.' + parent_node).each(function(index, value) {
        console.log(!$(value).prop('checked'));
        if (!$(value).prop('checked')) {
            total_child_uncheckd++;
        }
    });


    if (total_child_uncheckd == total_children) {
        $("#" + parent_node).prop('checked', false);
    } else {
        $("#" + parent_node).prop('checked', true);
    }

}

function uniqprodname() {
    var prodname = $('#name').val();
    if (prodname != "") {
        $.ajax({
            url: "ajax/masterProduct.php",
            type: "POST",
            data: {
                check_prod_name: prodname,
            },
            success: function(data) {
                var status = $.trim(data)
                $('#unique_name').val(status);
            }
        });
    }
}

function uniqprodcode() {
    var prodcode = $('#code').val();
    if (prodcode != "") {
        $.ajax({
            url: "ajax/masterProduct.php",
            type: "POST",
            data: {
                check_prod_code: prodcode,
            },
            success: function(data) {
                var status = $.trim(data)
                $('#unique_code').val(status);
            }
        });
    }
}
</script>
<?php include_once '../footer/adminFooter.php'; ?>
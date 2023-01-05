<?php
include_once '../header/adminHeader.php';
$assessmentObj = new assessmentController(); //Existing Controler Obj for Get Details of Cource
$productObj = new productController();

$clientUserId=$assessmentObj->getSuperClientId($user_group_id );
$productArr1=$productObj->getMasterProductList();
$productArr=array();
$courseArrILT=array();
$courseArrWBT=array();
foreach($productArr1 as $key=>$val){
 $productArr[]=$val['id'];
}
//echo "<pre>";print_r($productArr);exit;
$product_standard_id = implode(",", $productArr);
$courseType='';

$courseArr = $adminObj->getCourseListByLevel($courseType,$clientUserId,$product_standard_id);
//echo "<pre>";print_r($courseArr);exit;
$enableRange = count($courseArr);
$col  = 'product_id';
$sort = array();
foreach ($courseArr as $i => $obj) {
	  $sort[$i] = $obj->{$col};
	}
array_multisort($sort, SORT_ASC, $courseArr);
  foreach ($courseArr as $value) {
	  $course_type = $value->course_type;
	  //$courseArrILT[]=$value;  
	  if($course_type==1){
		$courseArrILT[]=$value;  
	  }else{
		$courseArrWBT[]=$value;
	  }
	}

$productExistArr = array();
foreach ($courseArr as $key1 => $val1) {
    $productExistArr[$val1->product_id] = $val1->product_id;   
}
$productMasterArr = array();


foreach ($productExistArr as $pkey => $pval) {
	
	$productMasterArr[$pkey] = $productObj->getProductMasterNameById($pkey);
	$courseRangeArrILT1 = array();
	$productmasterarrayILT1 = array();
	$courseNameArrILT1 = array();
	$courseRangeArrWBT1 = array();
	$productmasterarrayWBT1 = array();
	$courseNameArrWBT1 = array();
    foreach ($courseArrILT as $key => $val) {
	 if($pkey==$val->product_id){
		    $courseNameArrILT1[] = [$val];
			
			$courseRangeArrILT1[] = [$val->course_id,$val->product_id];
			if(!in_array ( $val->product_id, $productmasterarrayILT ,true )){
				array_push($productmasterarrayILT1,$val->product_id);
			}
		}
	}
  foreach ($courseArrWBT as $key => $val) {
	 if($pkey==$val->product_id){
		    $courseNameArrWBT1[] = [$val];
			
			$courseRangeArrWBT1[] = [$val->course_id,$val->product_id];
			if(!in_array ( $val->product_id, $productmasterarrayWBT ,true )){
				array_push($productmasterarrayWBT1,$val->product_id);
			}
		}
	}
  $courseNameArrILT[$pkey]= $courseNameArrILT1;
  $courseRangeArrILT[$pkey]= $courseRangeArrILT1;
  $productmasterarrayILT[$pkey] = $productmasterarrayILT1;
  $courseNameArrWBT[$pkey]= $courseNameArrWBT1;
  $courseRangeArrWBT[$pkey]= $courseRangeArrWBT1;
  $productmasterarrayWBT[$pkey] = $productmasterarrayWBT1;
 
  //echo "<pre>";print_r($courseRangeArrILT[$pkey]);
}
//echo "<pre>";print_r($courseRangeArrILT);
//echo "<pre>";print_r($courseRangeArr);exit;
$currencylist = $productObj->getcurrencylist();


$readonly = (isset($_GET['pid']) && !empty($_GET['pid']))? 'readonly':'';
if (isset($_GET['pid']) && !empty($_GET['pid'])) {
    $id = trim(base64_decode($_GET['pid']));
	
    $productdetailsbyid = $productObj->GetProductById($id);
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

    $batchDataDetails = $productObj->GetProductConfigById($id);

    $batchdatadetailsarr = array();
    foreach ($batchDataDetails as $key => $value) {
        $batchdatadetailsarr[$key] = $value['is_enabled'];
    }
    $checkedlevel = $batchdatadetailsarr[0];
    $checkedtopic = $batchdatadetailsarr[1];
    $checkedchapter = $batchdatadetailsarr[2];
    $checkedlevel1 = $checkedlevel != 0 ? explode(',', $checkedlevel) : $checkedlevel;
    $checkedtopic1 = $checkedtopic != 0 ? explode(',', $checkedtopic) : $checkedtopic;
    $checkedchapter1 = $checkedchapter != 0 ? explode(',', $checkedchapter) : $checkedchapter;
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
    <li> <a href="customProductList.php"><i class="fa fa-arrow-left"></i> Product List </a></li>
</ul>
<div class="clear"></div>
<section class="padder">
    <div class="row-centered">
        <div class="col-sm-10 col-xs-12 col-centered">
            <section class="marginBottom40">
                <form action="ajax/customProductSubmit.php" id="customProductSubmit" class="customProductSubmit" name="customProductSubmit"
                    enctype="multipart/form-data" method="POST"  data-validate="parsley"  autocomplete="off" >
                    <div class="row">
                        <input type="hidden" name="product_id" value="<?php echo (isset($prod_id)) ? $prod_id : ''; ?>">
                        <div class="panel panel-default bdrNone">
                            <div class="panel-body padd20">
                                <h3 class="panel-header">Product Details</h3>

                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Product Name <span
                                                class="required">*</span></label>
                                        <input type="text" name="name" id="name" placeholder="Product Name"
                                            class="form-control input-lg " data-required="true"
                                            value="<?php echo (isset($prod_name)) ? $prod_name : ''; ?>" maxlength="30"
                                            autocomplete="off" onchange ="uniqprodname()" <?php echo $readonly; ?> />
                                        <div class="required error" id="name_error"></div>
                                        <input type="hidden" id="unique_name" value="">
                                    <input type="hidden" name="client_id"  value="<?php echo $client_id; ?>" />
							
									</div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">UniqueCode <span class="required"></span></label>
                                        <input type="text" name="code" id="code" placeholder="Code"
                                            class="form-control input-lg "
                                            value="<?php echo (isset($prod_code)) ? $prod_code : ''; ?>" maxlength="30"
                                            autocomplete="off" onchange ="uniqprodcode()" <?php echo (isset($prod_code)) ? $readonly : ''; ?> />
                                        <div class="required error" id="code_error"></div>
                                        <input type="hidden" id="unique_code" value="">
                                    </div>
									
                                    <div class="form-group col-sm-6" style="display:none">
                                    <div class="row">
                                        <div class="col-sm-8" style="display:none">
                                            <label class="control-label">Price <span class="required"></span></label>
                                            <input name="price" id="price" class="form-control input-lg "
                                                value="<?php echo (isset($prod_price)) ? $prod_price : ''; ?>"
                                                />
                                        </div>
                                        <div class="col-sm-4" style="display:none">
                                            <label class="control-label">Currency<span class="required"> </span></label>
                                            <select name="currency_code" id="currency_code" class="form-control">
                                                <?php
                                                foreach ($currencylist as $currency) {
                                                    $selected = (isset($_GET['pid']) && !empty($_GET['pid']) && $currency['id'] == $currency_code) ? 'selected' : '';
                                                    echo '<option value="' . $currency['id'] . '"' . $selected . '>(' . $currency['symbol'] . ') ' . $currency['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row"><div class="required error" id="price_error" style="margin-left: 4% !important;"></div></div>
                                    </div>
                                    <div class="form-group col-sm-6"  style="display:none">
                                     <div class="row">
                                        <div class="col-sm-4" style="display:none">
                                            <label class="control-label">Discount <span
                                                    class="required"> </span></label>
                                            <input name="discount" id="discount" class="form-control input-lg "
                                                value="<?php echo (isset($prod_discount)) ? $prod_discount : ''; ?>"
                                               />
                                        </div>
                                        <div class="col-sm-8" style="display:none">
                                            <label class="control-label">Discount Type<span
                                                    class="required"> </span></label>
                                            <select name="discount_type" id="discount_type" class="form-control" >
                                                <option value="p"
                                                    <?php echo ($discount_type == 'p') ? 'selected' : ''; ?>>(%) Percent
                                                </option>
                                                <option value="fp"
                                                    <?php echo ($discount_type == 'fp') ? 'selected' : ''; ?>>Fixed
                                                    Price</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="required error" id="discount_error" style="margin-left: 5% !important;"></div>
                                    </div>
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
									<div class="form-group col-sm-12" >
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label class="control-label">Description <span class="required"></span></label>
                                            <textarea name="product_desc" id="product_desc" class="form-control teaxtarea100"><?php echo (isset($prod_desc)) ? $prod_desc : ''; ?></textarea>
                                        </div>
                                        
                                    </div>
                                    <div class="row"><div class="required error" id="desc_erropr" style="margin-left: 4% !important;"></div></div>
                                    </div>
                                    <div class="form-group col-sm-6" style="display:none">
                                        <label class="control-label">Product Expire <span
                                                class="required">*</span></label>
                                        <input type="text" name="expire_on" id="expire_on" placeholder="Number of days"
                                            class="form-control input-lg " 
                                            value="<?php echo (isset($expire_on)) ? $expire_on.' Days' : ''; ?>" maxlength="30"
                                            autocomplete="off" />
                                        <div class="required error" id="expire_on_error"></div>
                                    </div>
                                   
                                    <div class="clear"></div>
                                </div>
                                
                                <h3 class="panel-header">Product Configuration</h3>
                               <!--Product List Satart-->
							  <div class="col-sm-6">
							    <h4>ILT</h4>
							    <div class="panel-group" id="accordion11">
                                    <?php 
									//echo "<pre>";print_r($productExistArr);
									foreach ($productExistArr as $pkey1 => $pval1) {
										
									//echo $pval1;

										  //echo "<pre>";print_r($courseRangeArrILT[$pkey1]);
										$courseRangeArrILT2=$courseRangeArrILT[$pkey1];
									 
										if (count($courseRangeArrILT2) > 0 && !empty($courseRangeArrILT2)) { ?>
										
										
										<input type="hidden" name="levc" value="<?php echo count($courseRangeArrILT2); ?>" /> 
										<?php
										  echo $productMasterArr[$pkey1][0]['name'];
										
											$codeArr = array();
											$masterproductlist = array();
											$modc = $chapc = $i = 0;
											
												
											foreach ($courseRangeArrILT2 as $key => $val1) {
												
												//$code='CRS-'.$val;
												$code = $val1[0];
												$product_id_master = $val1[1];
												$codeArr[] = $code;
												if (isset($_GET['pid']) && !empty($_GET['pid'])) {
													$selected = (is_array($checkedlevel1) && in_array($code, $checkedlevel1)) ? "checked" : ($checkedlevel1 == 0 ? "checked" : "");

												} else {
													$selected = "";

												}
																	?>
										<div class="panel panel-default parent">
											<div class="panel-heading"
												style="padding: 0px;height: 22px;position: relative;">
												<div class="col-md-1 col-sm-1 displayInline">
													<?php $topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($code, $customTopic = null);
															$modc += count($topic_arr); ?>
													<input type="checkbox" total-child="<?php echo count($topic_arr) ?>"
														name="level[]" <?php echo $selected; ?> value="<?php echo $code; ?>"
														id="chklvl<?php echo $code; ?>"
														onchange="checkAll('chklvl<?php echo $code; ?>','level<?php echo $code; ?>');" />
												</div> <a data-toggle="collapse" data-parent="#accordion<?php echo $code;?>" href="#level<?php echo $code;?>" open="true" onclick=""  class="collapsed">
													<div class="col-md-11 col-sm-11 displayInline">
														<?php echo $courseNameArrILT[$pkey1][$key][0]->name; ?> </div>
												</a>
											</div>
											<div id="level<?php echo $code; ?>" class="col-sm-12 panel-collapse collapse">
												<div class="panel-body">
													<div class="col-sm-12">
														<div class="panel-group" id="accordion<?php echo $code; ?>">
															<div class="panel panel-default">
																<?php

																		if (count($topic_arr) > 0) {

																			foreach ($topic_arr as $key => $value) {

																				$tree_node_id = $value->tree_node_id;

																				$name = $value->name;
																				$edge_id = $value->edge_id;
																				$assessment_type = $value->assessment_type;
																				$is_survey = $value->is_survey;
																				$topic_type = $value->topic_type;
																				if (isset($_GET['pid']) && !empty($_GET['pid'])) {

																					$optionSelected = (is_array($checkedtopic1) && in_array($edge_id, $checkedtopic1)) ? "checked" : ($checkedtopic == 0 ? "checked" : "");
																				} else {
																					$optionSelected = "";
																				}

																		?>

																<div class="panel-heading"
																	style="padding: 0px;height: 22px;margin-bottom:5px;position: relative;">
																	<div class="col-md-1 col-sm-1 displayInline">
																	<input
																			type="checkbox"
																			total-child="<?php echo count($chapter_arr) ?>"
																			name="module[]" <?php echo $optionSelected; ?>
																			value="<?php echo $edge_id; ?>"
																			tree_node_id="<?php echo $tree_node_id; ?>"
																			id="chktpc<?php echo $edge_id; ?>"
																			onchange="checkAll('chktpc<?php echo $edge_id; ?>','topic<?php echo $edge_id; ?>','chklvl<?php echo $code; ?>');"
																			class="chklvl<?php echo $code; ?>" /></div> 
																			<a data-toggle="collapse" data-parent="#accordion<?php echo $edge_id; ?>" href="#topic<?php echo $edge_id; ?>" open="true" onclick="" class="collapsed">
																			<div class="col-md-11 col-sm-11 displayInline">
																				<?php echo $name; ?></div>
																		</a>
																</div>
																<div id="topic<?php echo $edge_id; ?>"
																	class="panel-collapse collapse">
																	<div class="panel-body"  id="accordion<?php echo $edge_id; ?>">
																		<?php $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($edge_id, $customChapter = null);
																						$chapc += count($chapter_arr);
																						//echo "<pre>";print_r($chapter_arr);exit;
																						if (count($chapter_arr) > 0) {

																							foreach ($chapter_arr as $key => $value1) {

																								$ch_tree_node_id = $value1->tree_node_id;

																								$chname = $value1->name;
																								$chdescription = $value1->description;
																								$chthumnailImg = $value1->thumnailImg;
																								$chskill = $value1->chapterSkill;
																								$ch_edge_id = $value1->edge_id;

																								if (isset($_GET['pid']) && !empty($_GET['pid'])) {
																									$optionSelected1 = (is_array($checkedchapter1) && in_array($ch_edge_id, $checkedchapter1)) ? "checked" : ($checkedchapter1 == 0 ? "checked" : "");
																								} else {
																									$optionSelected1 = "";
																								}

																						?>
																		<div class="col-sm-6">
																			<div class="chBox skill<?php echo $chskill; ?>"
																				tree_node_id="<?php echo $ch_tree_node_id; ?>"
																				skill="<?php echo $chskill; ?>">
																				<div
																					class="col-md-1 col-sm-1 displayInline">
																					<input
																						onchange="checkAll('chkchp<?php echo $ch_edge_id; ?>', 'chapter<?php echo $ch_edge_id; ?>', 'chktpc<?php echo $edge_id; ?>')"
																						type="checkbox" name="chapter[]"
																						class="chktpc<?php echo $edge_id; ?>"
																						<?php echo $optionSelected1; ?>
																						value="<?php echo $ch_edge_id; ?>"
																						id="chkchp<?php echo $ch_edge_id; ?>" />
																				</div>
																				<div
																					class="col-md-10 col-sm-10 displayInline">
																					<div class="chBoxDiv">
																						<div class="title">
																							<?php echo $chname; ?></div>
																						<div class="description">
																							<?php echo $chdescription;
																													?>
																						</div>
																					</div>
																					<div
																						class="chthumbnail pull-right skill<?php echo $chskill; ?>">
																						<img
																							src="<?php echo $thumnail_Img_url . $chthumnailImg;
																															?>" />
																					</div>
																				</div>
																			</div>
																		</div>

																		<?php }
																						} else { ?>
																		<div class="col-sm-12">Not Available</div>
																		<?php } ?>

																	</div>
																</div>
																<?php }
																		} else {
																			echo 'Not Available';
																		}
																		?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div><?php $masterId = $product_id_master; } ?>
										<input type="hidden" name="modc" value="<?php echo $modc; ?>" />
										<input type="hidden" name="chapc" value="<?php echo $chapc; ?>" />
										<?php
										} 
									}?> </div>
                                <!--Product List End-->
							  </div>
							  <div class="col-sm-6">
							    <h4>WBT</h4>
							    <div class="panel-group" id="accordion22">
                                    <?php 
									//echo "<pre>";print_r($productExistArr);
									foreach ($productExistArr as $pkey1 => $pval1) {
									  //echo $pval1;
									  //echo "<pre>";print_r($courseRangeArrILT[$pkey1]);
										$courseRangeArrWBT2=$courseRangeArrWBT[$pkey1];
									 
										if (count($courseRangeArrWBT2) > 0 && !empty($courseRangeArrWBT2)) { ?>
										
										
										<input type="hidden" name="levc" value="<?php echo count($courseRangeArrWBT2); ?>" /> 
										<?php
										  echo $productMasterArr[$pkey1][0]['name'];
										
											$codeArr = array();
											$masterproductlist = array();
											$modc = $chapc = $i = 0;
											
												
											foreach ($courseRangeArrWBT2 as $key => $val1) {
												
												//$code='CRS-'.$val;
												$code = $val1[0];
												$product_id_master = $val1[1];
												$codeArr[] = $code;
												if (isset($_GET['pid']) && !empty($_GET['pid'])) {
													$selected = (is_array($checkedlevel1) && in_array($code, $checkedlevel1)) ? "checked" : ($checkedlevel1 == 0 ? "checked" : "");

												} else {
													$selected = "";

												}
																	?>
										<div class="panel panel-default parent">
											<div class="panel-heading"
												style="padding: 0px;height: 22px;position: relative;">
												<div class="col-md-1 col-sm-1 displayInline">
													<?php $topic_arr = $assessmentObj->getTopicOrAssessmentByCourseId($code, $customTopic = null);
															$modc += count($topic_arr); ?>
													<input type="checkbox" total-child="<?php echo count($topic_arr) ?>"
														name="level[]" <?php echo $selected; ?> value="<?php echo $code; ?>"
														id="chklvl<?php echo $code; ?>"
														onchange="checkAll('chklvl<?php echo $code; ?>','level<?php echo $code; ?>');" />
												</div> <a  data-toggle="collapse" data-parent="#accordion<?php echo $code;?>" href="#level<?php echo $code;?>" open="true" onclick=""  class="collapsed">
													<div class="col-md-11 col-sm-11 displayInline">
														<?php echo $courseNameArrWBT[$pkey1][$key][0]->name; ?> </div>
												</a>
											</div>
											<div id="level<?php echo $code; ?>" class="col-sm-12 panel-collapse collapse">
												<div class="panel-body">
													<div class="col-sm-12">
														<div class="panel-group" id="accordion<?php echo $code; ?>">
															<div class="panel panel-default">
																<?php

																		if (count($topic_arr) > 0) {

																			foreach ($topic_arr as $key => $value) {

																				$tree_node_id = $value->tree_node_id;

																				$name = $value->name;
																				$edge_id = $value->edge_id;
																				$assessment_type = $value->assessment_type;
																				$is_survey = $value->is_survey;
																				$topic_type = $value->topic_type;
																				if (isset($_GET['pid']) && !empty($_GET['pid'])) {

																					$optionSelected = (is_array($checkedtopic1) && in_array($edge_id, $checkedtopic1)) ? "checked" : ($checkedtopic == 0 ? "checked" : "");
																				} else {
																					$optionSelected = "";
																				}

																		?>

																<div class="panel-heading"
																	style="padding: 0px;height: 22px;margin-bottom:5px;position: relative;">
																	<div class="col-md-1 col-sm-1 displayInline">
																	<input
																			type="checkbox"
																			total-child="<?php echo count($chapter_arr) ?>"
																			name="module[]" <?php echo $optionSelected; ?>
																			value="<?php echo $edge_id; ?>"
																			tree_node_id="<?php echo $tree_node_id; ?>"
																			id="chktpc<?php echo $edge_id; ?>"
																			onchange="checkAll('chktpc<?php echo $edge_id; ?>','topic<?php echo $edge_id; ?>','chklvl<?php echo $code; ?>');"
																			class="chklvl<?php echo $code; ?>" /></div> 
																			<a data-toggle="collapse" data-parent="#accordion<?php echo $edge_id; ?>" href="#topic<?php echo $edge_id; ?>" onclick="" class="collapsed">
																		<div class="col-md-11 col-sm-11 displayInline">
																			<?php echo $name; ?></div>
																	</a>
																</div>
																<div id="topic<?php echo $edge_id; ?>"
																	class="panel-collapse collapse">
																	<div class="panel-body" id="accordion<?php echo $edge_id; ?>">
																		<?php $chapter_arr = $assessmentObj->getChapterByTopicEdgeId($edge_id, $customChapter = null);
																						$chapc += count($chapter_arr);
																						//echo "<pre>";print_r($chapter_arr);exit;
																						if (count($chapter_arr) > 0) {

																							foreach ($chapter_arr as $key => $value1) {

																								$ch_tree_node_id = $value1->tree_node_id;

																								$chname = $value1->name;
																								$chdescription = $value1->description;
																								$chthumnailImg = $value1->thumnailImg;
																								$chskill = $value1->chapterSkill;
																								$ch_edge_id = $value1->edge_id;

																								if (isset($_GET['pid']) && !empty($_GET['pid'])) {
																									$optionSelected1 = (is_array($checkedchapter1) && in_array($ch_edge_id, $checkedchapter1)) ? "checked" : ($checkedchapter1 == 0 ? "checked" : "");
																								} else {
																									$optionSelected1 = "";
																								}

																						?>
																		<div class="col-sm-6">
																			<div class="chBox skill<?php echo $chskill; ?>"
																				tree_node_id="<?php echo $ch_tree_node_id; ?>"
																				skill="<?php echo $chskill; ?>">
																				<div
																					class="col-md-1 col-sm-1 displayInline">
																					<input
																						onchange="checkAll('chkchp<?php echo $ch_edge_id; ?>', 'chapter<?php echo $ch_edge_id; ?>', 'chktpc<?php echo $edge_id; ?>')"
																						type="checkbox" name="chapter[]"
																						class="chktpc<?php echo $edge_id; ?>"
																						<?php echo $optionSelected1; ?>
																						value="<?php echo $ch_edge_id; ?>"
																						id="chkchp<?php echo $ch_edge_id; ?>" />
																				</div>
																				<div
																					class="col-md-10 col-sm-10 displayInline">
																					<div class="chBoxDiv">
																						<div class="title">
																							<?php echo $chname; ?></div>
																						<div class="description">
																							<?php echo $chdescription;
																													?>
																						</div>
																					</div>
																					<div
																						class="chthumbnail pull-right skill<?php echo $chskill; ?>">
																						<img
																							src="<?php echo $thumnail_Img_url . $chthumnailImg;
																															?>" />
																					</div>
																				</div>
																			</div>
																		</div>

																		<?php }
																						} else { ?>
																		<div class="col-sm-12">Not Available</div>
																		<?php } ?>

																	</div>
																</div>
																<?php }
																		} else {
																			echo 'Not Available';
																		}
																		?>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div><?php $masterId = $product_id_master; } ?>
										<input type="hidden" name="modc" value="<?php echo $modc; ?>" />
										<input type="hidden" name="chapc" value="<?php echo $chapc; ?>" />
										<?php
										} 
									}?> </div>
                                <!--Product List End-->
							  </div>
                            <div class="clear"></div>
                            <div class="text-right">
							
                                <a href='customProductList.php' class="btn btn-primary ">Cancel</a>&nbsp;&nbsp;
                                <button type="submit" class="btn btn-s-md btn-primary" name="saveproduct"
                                    id="saveproduct">Submit</button>
                            </div>
                        </div>

                </form>
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
            url: "ajax/manageproduct.php",
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
            url: "ajax/manageproduct.php",
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
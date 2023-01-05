<?php
error_reporting(1);
include_once('../header/adminHeader.php');
$productObj = new productController();

$_page = empty($_GET['page']) || !is_numeric($_GET['page']) ? 1 : $_GET['page'];

$_limit = PAGINATION_LIMIT;
$objPage = new Pagination($_page, 20);
$response_result = $productObj->getAllMaterProductList();
//echo "<pre>";print_r($response_result);//exit;
//$objPage->_total = $response_result['total'];
//$batchInfoArr = $response_result['result'];
?>
<div class="breadcrumbBgNone breadcrumbPadder">
    <div class="col-md-6 col-sm-6 text-left">Manage Master Product</div>
    <div class="col-md-6 col-sm-6 text-right"><a href='addMasterProduct.php' class="btn btn-primary ">Add Master Product</a></div>
</div>
<div class="clear"></div>
<section class="padder">
    <div class="row-centered">
        <div class="col-sm-12 col-xs-12">
            <section class="panel panel-default">
                <?php if ($err != '') { ?>
                    <div class="alert alert-danger col-sm-12">
                        <button type="button" class="close" data-dismiss="alert">x</button>
                        <i class="fa fa-ban-circle"></i><?php echo $msg; ?> </div>
                <?php } ?>

                <?php if ($succ != '') { ?>
                    <div class="alert alert-success col-sm-12">
                        <button type="button" class="close" data-dismiss="alert">x</button>
                        <i class="fa fa-ban-circle"></i><?php echo $msg; ?></div>
                <?php } ?>
                <div class="panel-body">

                    <?php if ($objPage->_total > 0) {
                        $no = ($_page - 1) * $_limit + 1; ?>
                        <div class="table-responsive">
                            <table class="table table-border dataTable table-fixed">
                                <thead class="fixedHeader">
                                    <tr class="col-sm-12 padd0">
									    <th class="col-sm-2 text-left">Master</th>
                                      
                                        <th class="col-sm-3 text-left">Product Name</th>
                                        <th class="col-sm-3 text-left">Product Code</th>
                                        <th class="col-sm-2 text-center">Status</th>
                                        <th class="col-sm-2 text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php $i = 1;
                                    foreach ($response_result as $key => $value) {
                                        if($value['status']==1){
                                            $status="Active";
                                            $activeClass="style='color:Green'";
                                          }else{
                                              $status="Inactive";
                                              $activeClass="style='color:Red'";
                                           }
										  $productData= $productObj->getProductMasterNameById($value['master_products_ids']);
										   $productDataName=array();
										   foreach ($productData as $key1 => $value1) {
											  $productDataName[]= $value1['name'];
										   }
										   if(count($productDataName)>1){
											  $productName = implode(", ", $productDataName);
											  
										  }else{
											  $productName=$productDataName[0]; 
										  }
										  
                                    ?>
                                        <tr class="col-sm-12 padd0">
										   <td class="col-sm-2 text-left"><?php echo $productName; ?></td>
               
                                            <td class="col-sm-3 text-left"><?php echo $value['product_name']; ?></td>
                                            <td class="col-sm-3 text-left"><?php echo $value['code']; ?></td>
                                            
                                             <td class="col-sm-2 text-center" <?php echo $activeClass; ?>><?php echo $status; ?></td>
                                            <td class="col-sm-2 text-center">
                                                <a class="edit" href="<?php echo "addMasterProduct.php?pid=" . base64_encode($value['master_products_ids']); ?>"> <i class="fa fa-edit"></i> Edit</a></td>
                                        </tr>
                                    <?php $i++;
                                    } ?>
                                    <tr>
                                        <td colspan="12" class="text-center col-sm-12"><?php echo $objPage->createLinks($page_param, 5, 'pagination'); ?></td>
                                    </tr>
                                <?php } else {   ?>

                                    <div class="col-xs-12 noRecord text-center">Records is not available. <br>Click <span class="capitalize">"Add <?php echo $batch; ?>"</span> to add <?php echo $batch; ?>.</div>
                                </tbody>
                            <?php     } ?>

                            </table>
                        </div>

            </section>
        </div>
    </div>
</section>
<?php include_once('../footer/adminFooter.php'); ?>
<style>
    .th-sortable .th-sort {
        float: none;
        position: relative;
        margin-left: 2px;
    }
</style>
<script>
    //On region chnage
    $('#region').change(function() {
        var region = $('#region option:selected').val();
        $('#center_id').html('<option value="">Select Organization</option>');
        $('#batch_id').html('<option value="">Select class</option>');
        if (region == '') {
            $('#country').find('option').remove().end().append('<option value="">Select </option>');
        } else {
            $.post('ajax/getCountryByRegion.php', {
                region_id: region
            }, function(data) {
                if (data != '') {
                    $('#country').html(data);
                } else {
                    $('#country').html('<option value="">Not Available</option>');
                }
            });
        }
    });


    //On country chnage
    $('#country').change(function() {
        var region_id = $('#region').val();
        var country = $('#country option:selected').val();
        $('#batch_id').html('<option value="">Select class</option>');
        if (country == '') {
            $('#center_id').find('option').remove().end().append('<option value="">Select Organization </option>');
        } else {
            $.post('ajax/getCenterByCountry.php', {
                country: country,
                region_id: region_id
            }, function(data) {
                if (data != '') {
                    $('#center_id').html(data);
                } else {
                    $('#center_id').html('<option value="">Not Available</option>');
                }
            });
        }
    });


    //On center chnage
    $('#center_id').change(function() {
        var region_id = $('#region').val();
        var country = $('#country').val();
        var center_id = $('#center_id option:selected').val();
        if (center_id == '') {
            $('#batch_id').find('option').remove().end().append('<option value="">Select Class </option>');
        } else {
            $.post('ajax/getBatchByCenter.php', {
                region_id: region_id,
                country: country,
                center_id: center_id
            }, function(data) {
                if (data != '') {
                    $('#batch_id').html(data);
                } else {
                    $('#batch_id').html('<option value="">Not Available</option>');
                }
            });
        }
    });
</script>
<?php 

if($is_color=='No'){
?>
<style>
/*top banner bg color*/
header.bg-dark.dk, header.bg-dark .dk, header.header,header.bg-dark .nav > li > a:hover ,header.bg-dark .nav .open > a,header.bg-dark a{
    background-color: <?php echo $tbColor; ?>;
	color:<?php echo $tbFontColor; ?>;
}
/*top font color*/
header.bg-dark .nav > li > a,header.bg-dark a {
    color:<?php echo $tbFontColor; ?>;
}
/*left banner bg color*/
aside.bg-dark.lter,aside.bg-dark .lter, aside.bg-dark .nav > li > a{
    background-color: <?php echo $lbColor; ?>;
	 color: <?php echo $lbFontColor; ?>;
}

/*left font color*/
nav > .nav .bg-dark .nav > li > a {
    color: <?php echo $lbFontColor; ?>;
}

header.bg-dark .dropdown-menu > li > a{ background-color: #f1f1f1!important;
	color:#111!important;}
</style>
<?php }?>

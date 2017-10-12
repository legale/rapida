/*--------------------------------------------------------------------
*
* Created by Dexter (http://cssclub.ru)
*
--------------------------------------------------------------------*/
function equalHeights(element) {
var class_name = '.equal-height';
var maxHeight = 0;
var maxItem = 0;
var maxItem_adjust = 0;
element.find(class_name).each(function(i){
$(this).wrapInner('<div></div>');
});
function heightChecker(){
element.find(class_name).each(function(i){
if($(this).outerHeight() > maxHeight) {
maxHeight = $(this).outerHeight();
maxItem = $(this);
setHeight(maxHeight);
}
});
maxItem_adjust = maxItem.outerHeight()-maxItem.height();
if(maxHeight > (maxItem.children().height() + maxItem_adjust)){
maxHeight = maxItem.children().height() + maxItem_adjust;
setHeight(maxHeight);
}
setTimeout(function(){heightChecker()}, 1000)
}
function setHeight(target){
element.find(class_name).each(function(i){
var adjust = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust });
}
else{
$(this).css({'min-height': target - adjust });
}
})
}
heightChecker();
}
function equalHeights_new_products_name(element) {
var class_name_new_products_name = '.equal-height_new_products_name';
var maxHeight_new_products_name = 0;
var maxItem_new_products_name = 0;
var maxItem_new_products_name_adjust = 0;
element.find(class_name_new_products_name).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_new_products_name(){
element.find(class_name_new_products_name).each(function(j){
if($(this).outerHeight() > maxHeight_new_products_name) {
maxHeight_new_products_name = $(this).outerHeight();
maxItem_new_products_name = $(this);
setHeight(maxHeight_new_products_name);
}
});
maxItem_new_products_name_adjust = maxItem_new_products_name.outerHeight()-maxItem_new_products_name.height();
if(maxHeight_new_products_name > (maxItem_new_products_name.children().height() + maxItem_new_products_name_adjust)){
maxHeight_new_products_name = maxItem_new_products_name.children().height() + maxItem_new_products_name_adjust;
setHeight(maxHeight_new_products_name);
}
setTimeout(function(){heightChecker_new_products_name()}, 1000)
}
function setHeight(target){
element.find(class_name_new_products_name).each(function(j){
var adjust_new_products_name = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_new_products_name });
}
else{
$(this).css({'min-height': target - adjust_new_products_name });
}
})
}
heightChecker_new_products_name();
}
function equalHeights_new_products_block(element) {
var class_name_new_products_block = '.equal-height_new_products_block';
var maxHeight_new_products_block = 0;
var maxItem_new_products_block = 0;
var maxItem_new_products_block_adjust = 0;
element.find(class_name_new_products_block).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_new_products_block(){
element.find(class_name_new_products_block).each(function(j){
if($(this).outerHeight() > maxHeight_new_products_block) {
maxHeight_new_products_block = $(this).outerHeight();
maxItem_new_products_block = $(this);
setHeight(maxHeight_new_products_block);
}
});
maxItem_new_products_block_adjust = maxItem_new_products_block.outerHeight()-maxItem_new_products_block.height();
if(maxHeight_new_products_block > (maxItem_new_products_block.children().height() + maxItem_new_products_block_adjust)){
maxHeight_new_products_block = maxItem_new_products_block.children().height() + maxItem_new_products_block_adjust;
setHeight(maxHeight_new_products_block);
}
setTimeout(function(){heightChecker_new_products_block()}, 1000)
}
function setHeight(target){
element.find(class_name_new_products_block).each(function(j){
var adjust_new_products_block = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_new_products_block });
}
else{
$(this).css({'min-height': target - adjust_new_products_block });
}
})
}
heightChecker_new_products_block();
}
function equalHeights_sub_categories_name(element) {
var class_name_sub_categories_name = '.equal-height_sub_categories_name';
var maxHeight_sub_categories_name = 0;
var maxItem_sub_categories_name = 0;
var maxItem_sub_categories_name_adjust = 0;
element.find(class_name_sub_categories_name).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_sub_categories_name(){
element.find(class_name_sub_categories_name).each(function(j){
if($(this).outerHeight() > maxHeight_sub_categories_name) {
maxHeight_sub_categories_name = $(this).outerHeight();
maxItem_sub_categories_name = $(this);
setHeight(maxHeight_sub_categories_name);
}
});
maxItem_sub_categories_name_adjust = maxItem_sub_categories_name.outerHeight()-maxItem_sub_categories_name.height();
if(maxHeight_sub_categories_name > (maxItem_sub_categories_name.children().height() + maxItem_sub_categories_name_adjust)){
maxHeight_sub_categories_name = maxItem_sub_categories_name.children().height() + maxItem_sub_categories_name_adjust;
setHeight(maxHeight_sub_categories_name);
}
setTimeout(function(){heightChecker_sub_categories_name()}, 1000)
}
function setHeight(target){
element.find(class_name_sub_categories_name).each(function(j){
var adjust_sub_categories_name = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_sub_categories_name });
}
else{
$(this).css({'min-height': target - adjust_sub_categories_name });
}
})
}
heightChecker_sub_categories_name();
}
function equalHeights3(element) {
var class_name3 = '.equal-height3';
var maxHeight3 = 0;
var maxItem3 = 0;
var maxItem3_adjust = 0;
element.find(class_name3).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker3(){
element.find(class_name3).each(function(j){
if($(this).outerHeight() > maxHeight3) {
maxHeight3 = $(this).outerHeight();
maxItem3 = $(this);
setHeight(maxHeight3);
}
});
maxItem3_adjust = maxItem3.outerHeight()-maxItem3.height();
if(maxHeight3 > (maxItem3.children().height() + maxItem3_adjust)){
maxHeight3 = maxItem3.children().height() + maxItem3_adjust;
setHeight(maxHeight3);
}
setTimeout(function(){heightChecker3()}, 1000)
}
function setHeight(target){
element.find(class_name3).each(function(j){
var adjust3 = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust3 });
}
else{
$(this).css({'min-height': target - adjust3 });
}
})
}
heightChecker3();
}
function equalHeights_box(element) {
var class_name_box = '.equal-height-box';
var maxHeight_box = 0;
var maxItem_box = 0;
var maxItem_box_adjust = 0;
element.find(class_name_box).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_box(){
element.find(class_name_box).each(function(j){
if($(this).outerHeight() > maxHeight_box) {
maxHeight_box = $(this).outerHeight();
maxItem_box = $(this);
setHeight(maxHeight_box);
}
});
maxItem_box_adjust = maxItem_box.outerHeight()-maxItem_box.height();
if(maxHeight_box > (maxItem_box.children().height() + maxItem_box_adjust)){
maxHeight_box = maxItem_box.children().height() + maxItem_box_adjust;
setHeight(maxHeight_box);
}
setTimeout(function(){heightChecker_box()}, 1000)
}
function setHeight(target){
element.find(class_name_box).each(function(j){
var adjust_box = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_box });
}
else{
$(this).css({'min-height': target - adjust_box });
}
})
}
heightChecker_box();
}
function equalHeights4(element) {
var class_name4 = '.equal-height-price';
var maxHeight4 = 0;
var maxItem4 = 0;
var maxItem4_adjust = 0;
element.find(class_name4).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker4(){
element.find(class_name4).each(function(j){
if($(this).outerHeight() > maxHeight4) {
maxHeight4 = $(this).outerHeight();
maxItem4 = $(this);
setHeight(maxHeight4);
}
});
maxItem4_adjust = maxItem4.outerHeight()-maxItem4.height();
if(maxHeight4 > (maxItem4.children().height() + maxItem4_adjust)){
maxHeight4 = maxItem4.children().height() + maxItem4_adjust;
setHeight(maxHeight4);
}
setTimeout(function(){heightChecker4()}, 1000)
}
function setHeight(target){
element.find(class_name4).each(function(j){
var adjust4 = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust4 });
}
else{
$(this).css({'min-height': target - adjust4 });
}
})
}
heightChecker4();
}
function equalHeights_featured_block(element) {
var class_name_featured_block = '.equal-height_featured_block';
var maxHeight_featured_block = 0;
var maxItem_featured_block = 0;
var maxItem_featured_block_adjust = 0;
element.find(class_name_featured_block).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_featured_block(){
element.find(class_name_featured_block).each(function(j){
if($(this).outerHeight() > maxHeight_featured_block) {
maxHeight_featured_block = $(this).outerHeight();
maxItem_featured_block = $(this);
setHeight(maxHeight_featured_block);
}
});
maxItem_featured_block_adjust = maxItem_featured_block.outerHeight()-maxItem_featured_block.height();
if(maxHeight_featured_block > (maxItem_featured_block.children().height() + maxItem_featured_block_adjust)){
maxHeight_featured_block = maxItem_featured_block.children().height() + maxItem_featured_block_adjust;
setHeight(maxHeight_featured_block);
}
setTimeout(function(){heightChecker_featured_block()}, 1000)
}
function setHeight(target){
element.find(class_name_featured_block).each(function(j){
var adjust_featured_block = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_featured_block });
}
else{
$(this).css({'min-height': target - adjust_featured_block });
}
})
}
heightChecker_featured_block();
}
function equalHeights_featured_name(element) {
var class_name_featured_name = '.equal-height_featured_name';
var maxHeight_featured_name = 0;
var maxItem_featured_name = 0;
var maxItem_featured_name_adjust = 0;
element.find(class_name_featured_name).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_featured_name(){
element.find(class_name_featured_name).each(function(j){
if($(this).outerHeight() > maxHeight_featured_name) {
maxHeight_featured_name = $(this).outerHeight();
maxItem_featured_name = $(this);
setHeight(maxHeight_featured_name);
}
});
maxItem_featured_name_adjust = maxItem_featured_name.outerHeight()-maxItem_featured_name.height();
if(maxHeight_featured_name > (maxItem_featured_name.children().height() + maxItem_featured_name_adjust)){
maxHeight_featured_name = maxItem_featured_name.children().height() + maxItem_featured_name_adjust;
setHeight(maxHeight_featured_name);
}
setTimeout(function(){heightChecker_featured_name()}, 1000)
}
function setHeight(target){
element.find(class_name_featured_name).each(function(j){
var adjust_featured_name = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_featured_name });
}
else{
$(this).css({'min-height': target - adjust_featured_name });
}
})
}
heightChecker_featured_name();
}
function equalHeights_box_specials(element) {
var class_name_box_specials = '.equal-height-box_specials';
var maxHeight_box_specials = 0;
var maxItem_box_specials = 0;
var maxItem_box_specials_adjust = 0;
element.find(class_name_box_specials).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_box_specials(){
element.find(class_name_box_specials).each(function(j){
if($(this).outerHeight() > maxHeight_box_specials) {
maxHeight_box_specials = $(this).outerHeight();
maxItem_box_specials = $(this);
setHeight(maxHeight_box_specials);
}
});
maxItem_box_specials_adjust = maxItem_box_specials.outerHeight()-maxItem_box_specials.height();
if(maxHeight_box_specials > (maxItem_box_specials.children().height() + maxItem_box_specials_adjust)){
maxHeight_box_specials = maxItem_box_specials.children().height() + maxItem_box_specials_adjust;
setHeight(maxHeight_box_specials);
}
setTimeout(function(){heightChecker_box_specials()}, 1000)
}
function setHeight(target){
element.find(class_name_box_specials).each(function(j){
var adjust_box_specials = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_box_specials });
}
else{
$(this).css({'min-height': target - adjust_box_specials });
}
})
}
heightChecker_box_specials();
}
function equalHeights_box_best_sellers(element) {
var class_name_box_best_sellers = '.equal-height-box_best_sellers';
var maxHeight_box_best_sellers = 0;
var maxItem_box_best_sellers = 0;
var maxItem_box_best_sellers_adjust = 0;
element.find(class_name_box_best_sellers).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_box_best_sellers(){
element.find(class_name_box_best_sellers).each(function(j){
if($(this).outerHeight() > maxHeight_box_best_sellers) {
maxHeight_box_best_sellers = $(this).outerHeight();
maxItem_box_best_sellers = $(this);
setHeight(maxHeight_box_best_sellers);
}
});
maxItem_box_best_sellers_adjust = maxItem_box_best_sellers.outerHeight()-maxItem_box_best_sellers.height();
if(maxHeight_box_best_sellers > (maxItem_box_best_sellers.children().height() + maxItem_box_best_sellers_adjust)){
maxHeight_box_best_sellers = maxItem_box_best_sellers.children().height() + maxItem_box_best_sellers_adjust;
setHeight(maxHeight_box_best_sellers);
}
setTimeout(function(){heightChecker_box_best_sellers()}, 1000)
}
function setHeight(target){
element.find(class_name_box_best_sellers).each(function(j){
var adjust_box_best_sellers = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_box_best_sellers });
}
else{
$(this).css({'min-height': target - adjust_box_best_sellers });
}
})
}
heightChecker_box_best_sellers();
}
/**/
function equalHeights_box_best_sellers_block(element) {
var class_name_box_best_sellers_block = '.equal-height-box_best_sellers_block';
var maxHeight_box_best_sellers_block = 0;
var maxItem_box_best_sellers_block = 0;
var maxItem_box_best_sellers_block_adjust = 0;
element.find(class_name_box_best_sellers_block).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_box_best_sellers_block(){
element.find(class_name_box_best_sellers_block).each(function(j){
if($(this).outerHeight() > maxHeight_box_best_sellers_block) {
maxHeight_box_best_sellers_block = $(this).outerHeight();
maxItem_box_best_sellers_block = $(this);
setHeight(maxHeight_box_best_sellers_block);
}
});
maxItem_box_best_sellers_block_adjust = maxItem_box_best_sellers_block.outerHeight()-maxItem_box_best_sellers_block.height();
if(maxHeight_box_best_sellers_block > (maxItem_box_best_sellers_block.children().height() + maxItem_box_best_sellers_block_adjust)){
maxHeight_box_best_sellers_block = maxItem_box_best_sellers_block.children().height() + maxItem_box_best_sellers_block_adjust;
setHeight(maxHeight_box_best_sellers_block);
}
setTimeout(function(){heightChecker_box_best_sellers_block()}, 1000)
}
function setHeight(target){
element.find(class_name_box_best_sellers_block).each(function(j){
var adjust_box_best_sellers_block = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_box_best_sellers_block });
}
else{
$(this).css({'min-height': target - adjust_box_best_sellers_block });
}
})
}
heightChecker_box_best_sellers_block();
}
/**/
function equalHeights_box_featured(element) {
var class_name_box_featured = '.equal-height_box_featured';
var maxHeight_box_featured = 0;
var maxItem_box_featured = 0;
var maxItem_box_featured_adjust = 0;
element.find(class_name_box_featured).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_box_featured(){
element.find(class_name_box_featured).each(function(j){
if($(this).outerHeight() > maxHeight_box_featured) {
maxHeight_box_featured = $(this).outerHeight();
maxItem_box_featured = $(this);
setHeight(maxHeight_box_featured);
}
});
maxItem_box_featured_adjust = maxItem_box_featured.outerHeight()-maxItem_box_featured.height();
if(maxHeight_box_featured > (maxItem_box_featured.children().height() + maxItem_box_featured_adjust)){
maxHeight_box_featured = maxItem_box_featured.children().height() + maxItem_box_featured_adjust;
setHeight(maxHeight_box_featured);
}
setTimeout(function(){heightChecker_box_featured()}, 1000)
}
function setHeight(target){
element.find(class_name_box_featured).each(function(j){
var adjust_box_featured = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_box_featured });
}
else{
$(this).css({'min-height': target - adjust_box_featured });
}
})
}
heightChecker_box_featured();
}
function equalHeights_slave_name(element) {
var class_name_slave_name = '.equal-height_slave_name';
var maxHeight_slave_name = 0;
var maxItem_slave_name = 0;
var maxItem_slave_name_adjust = 0;
element.find(class_name_slave_name).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_slave_name(){
element.find(class_name_slave_name).each(function(j){
if($(this).outerHeight() > maxHeight_slave_name) {
maxHeight_slave_name = $(this).outerHeight();
maxItem_slave_name = $(this);
setHeight(maxHeight_slave_name);
}
});
maxItem_slave_name_adjust = maxItem_slave_name.outerHeight()-maxItem_slave_name.height();
if(maxHeight_slave_name > (maxItem_slave_name.children().height() + maxItem_slave_name_adjust)){
maxHeight_slave_name = maxItem_slave_name.children().height() + maxItem_slave_name_adjust;
setHeight(maxHeight_slave_name);
}
setTimeout(function(){heightChecker_slave_name()}, 1000)
}
function setHeight(target){
element.find(class_name_slave_name).each(function(j){
var adjust_slave_name = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_slave_name });
}
else{
$(this).css({'min-height': target - adjust_slave_name });
}
})
}
heightChecker_slave_name();
}
function equalHeights_slave_block(element) {
var class_name_slave_block = '.equal-height_slave_block';
var maxHeight_slave_block = 0;
var maxItem_slave_block = 0;
var maxItem_slave_block_adjust = 0;
element.find(class_name_slave_block).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_slave_block(){
element.find(class_name_slave_block).each(function(j){
if($(this).outerHeight() > maxHeight_slave_block) {
maxHeight_slave_block = $(this).outerHeight();
maxItem_slave_block = $(this);
setHeight(maxHeight_slave_block);
}
});
maxItem_slave_block_adjust = maxItem_slave_block.outerHeight()-maxItem_slave_block.height();
if(maxHeight_slave_block > (maxItem_slave_block.children().height() + maxItem_slave_block_adjust)){
maxHeight_slave_block = maxItem_slave_block.children().height() + maxItem_slave_block_adjust;
setHeight(maxHeight_slave_block);
}
setTimeout(function(){heightChecker_slave_block()}, 1000)
}
function setHeight(target){
element.find(class_name_slave_block).each(function(j){
var adjust_slave_block = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_slave_block });
}
else{
$(this).css({'min-height': target - adjust_slave_block });
}
})
}
heightChecker_slave_block();
}
function equalHeights_also_pur_prods_block(element) {
var class_name_also_pur_prods_block = '.equal-height_also_pur_prods_block';
var maxHeight_also_pur_prods_block = 0;
var maxItem_also_pur_prods_block = 0;
var maxItem_also_pur_prods_block_adjust = 0;
element.find(class_name_also_pur_prods_block).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_also_pur_prods_block(){
element.find(class_name_also_pur_prods_block).each(function(j){
if($(this).outerHeight() > maxHeight_also_pur_prods_block) {
maxHeight_also_pur_prods_block = $(this).outerHeight();
maxItem_also_pur_prods_block = $(this);
setHeight(maxHeight_also_pur_prods_block);
}
});
maxItem_also_pur_prods_block_adjust = maxItem_also_pur_prods_block.outerHeight()-maxItem_also_pur_prods_block.height();
if(maxHeight_also_pur_prods_block > (maxItem_also_pur_prods_block.children().height() + maxItem_also_pur_prods_block_adjust)){
maxHeight_also_pur_prods_block = maxItem_also_pur_prods_block.children().height() + maxItem_also_pur_prods_block_adjust;
setHeight(maxHeight_also_pur_prods_block);
}
setTimeout(function(){heightChecker_also_pur_prods_block()}, 1000)
}
function setHeight(target){
element.find(class_name_also_pur_prods_block).each(function(j){
var adjust_also_pur_prods_block = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_also_pur_prods_block });
}
else{
$(this).css({'min-height': target - adjust_also_pur_prods_block });
}
})
}
heightChecker_also_pur_prods_block();
}
function equalHeights_also_pur_prods_name(element) {
var class_name_also_pur_prods_name = '.equal-height_also_pur_prods_name';
var maxHeight_also_pur_prods_name = 0;
var maxItem_also_pur_prods_name = 0;
var maxItem_also_pur_prods_name_adjust = 0;
element.find(class_name_also_pur_prods_name).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_also_pur_prods_name(){
element.find(class_name_also_pur_prods_name).each(function(j){
if($(this).outerHeight() > maxHeight_also_pur_prods_name) {
maxHeight_also_pur_prods_name = $(this).outerHeight();
maxItem_also_pur_prods_name = $(this);
setHeight(maxHeight_also_pur_prods_name);
}
});
maxItem_also_pur_prods_name_adjust = maxItem_also_pur_prods_name.outerHeight()-maxItem_also_pur_prods_name.height();
if(maxHeight_also_pur_prods_name > (maxItem_also_pur_prods_name.children().height() + maxItem_also_pur_prods_name_adjust)){
maxHeight_also_pur_prods_name = maxItem_also_pur_prods_name.children().height() + maxItem_also_pur_prods_name_adjust;
setHeight(maxHeight_also_pur_prods_name);
}
setTimeout(function(){heightChecker_also_pur_prods_name()}, 1000)
}
function setHeight(target){
element.find(class_name_also_pur_prods_name).each(function(j){
var adjust_also_pur_prods_name = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_also_pur_prods_name });
}
else{
$(this).css({'min-height': target - adjust_also_pur_prods_name });
}
})
}
heightChecker_also_pur_prods_name();
}
function equalHeights_listing_block(element) {
var class_name_listing_block = '.equal-height_listing_block';
var maxHeight_listing_block = 0;
var maxItem_listing_block = 0;
var maxItem_listing_block_adjust = 0;
element.find(class_name_listing_block).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_listing_block(){
element.find(class_name_listing_block).each(function(j){
if($(this).outerHeight() > maxHeight_listing_block) {
maxHeight_listing_block = $(this).outerHeight();
maxItem_listing_block = $(this);
setHeight(maxHeight_listing_block);
}
});
maxItem_listing_block_adjust = maxItem_listing_block.outerHeight()-maxItem_listing_block.height();
if(maxHeight_listing_block > (maxItem_listing_block.children().height() + maxItem_listing_block_adjust)){
maxHeight_listing_block = maxItem_listing_block.children().height() + maxItem_listing_block_adjust;
setHeight(maxHeight_listing_block);
}
setTimeout(function(){heightChecker_listing_block()}, 1000)
}
function setHeight(target){
element.find(class_name_listing_block).each(function(j){
var adjust_listing_block = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_listing_block });
}
else{
$(this).css({'min-height': target - adjust_listing_block });
}
})
}
heightChecker_listing_block();
}
function equalHeights_listing_name(element) {
var class_name_listing_name = '.equal-height_listing_name';
var maxHeight_listing_name = 0;
var maxItem_listing_name = 0;
var maxItem_listing_name_adjust = 0;
element.find(class_name_listing_name).each(function(j){
$(this).wrapInner('<div></div>');
});
function heightChecker_listing_name(){
element.find(class_name_listing_name).each(function(j){
if($(this).outerHeight() > maxHeight_listing_name) {
maxHeight_listing_name = $(this).outerHeight();
maxItem_listing_name = $(this);
setHeight(maxHeight_listing_name);
}
});
maxItem_listing_name_adjust = maxItem_listing_name.outerHeight()-maxItem_listing_name.height();
if(maxHeight_listing_name > (maxItem_listing_name.children().height() + maxItem_listing_name_adjust)){
maxHeight_listing_name = maxItem_listing_name.children().height() + maxItem_listing_name_adjust;
setHeight(maxHeight_listing_name);
}
setTimeout(function(){heightChecker_listing_name()}, 1000)
}
function setHeight(target){
element.find(class_name_listing_name).each(function(j){
var adjust_listing_name = $(this).outerHeight()-$(this).height();
if($.browser.msie && $.browser.version < 7.0){
$(this).css({'height': target - adjust_listing_name });
}
else{
$(this).css({'min-height': target - adjust_listing_name });
}
})
}
heightChecker_listing_name();
} 
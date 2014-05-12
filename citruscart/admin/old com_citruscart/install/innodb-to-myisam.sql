-- -----------------------------------------------------
-- HOW TO USE THIS FILE:
-- Replace all instances of #_ with your prefix
-- In PHPMYADMIN or the equiv, run the entire SQL
-- -----------------------------------------------------
ALTER TABLE #__citruscart_eavattributeentityxref DROP FOREIGN KEY `fk_EavAttribute_EavAttributes`;
ALTER TABLE #__citruscart_eavattributeoptions DROP FOREIGN KEY `fk_eavattributeoptions_eavattribute`;
ALTER TABLE #__citruscart_eavvaluesvarchar DROP FOREIGN KEY `fk_eavvaluesvarchar_eavattribute`;
ALTER TABLE #__citruscart_eavvaluesint DROP FOREIGN KEY `fk_eavvaluesint_eavattribute`;
ALTER TABLE #__citruscart_eavvaluestext DROP FOREIGN KEY `fk_eavvaluestext_eavattribute`;
ALTER TABLE #__citruscart_eavvaluesdatetime DROP FOREIGN KEY `fk_eavvaluesdatetime_eavattribute`;
ALTER TABLE #__citruscart_eavvaluesdecimal DROP FOREIGN KEY `fk_eavvaluesdecimal_eavattribute`;
ALTER TABLE #__citruscart_geozones DROP FOREIGN KEY `fk_geozonetype`;
ALTER TABLE #__citruscart_taxrates DROP FOREIGN KEY `fk_TaxClass_TaxRates`;
ALTER TABLE #__citruscart_taxrates DROP FOREIGN KEY `fk_geozones_taxrates`;
ALTER TABLE #__citruscart_carts DROP FOREIGN KEY `fk_carts_products`;
ALTER TABLE #__citruscart_orders DROP FOREIGN KEY `fk_OrderState_Order`;
ALTER TABLE #__citruscart_orders DROP FOREIGN KEY `fk_currencies_orders`;
ALTER TABLE #__citruscart_orderhistory DROP FOREIGN KEY `fk_OrderState_OrderHistory`;
ALTER TABLE #__citruscart_orderhistory DROP FOREIGN KEY `fk_Orders_OrderHistory`;
ALTER TABLE #__citruscart_productattributeoptionvalues DROP FOREIGN KEY `fk_paov_pao`;
ALTER TABLE #__citruscart_products DROP FOREIGN KEY `fk_taxclasses_products`;
ALTER TABLE #__citruscart_orderitems DROP FOREIGN KEY `fk_Order_OrderItem`;
ALTER TABLE #__citruscart_orderitems DROP FOREIGN KEY `fk_Product_OrderItem`;
ALTER TABLE #__citruscart_orderpayments DROP FOREIGN KEY `fk_Orders_OrderPayment`;
ALTER TABLE #__citruscart_orderinfo DROP FOREIGN KEY `fk_Orders_OrderInfo`;
ALTER TABLE #__citruscart_productcategoryxref DROP FOREIGN KEY `fk_Product_ProductCategory`;
ALTER TABLE #__citruscart_productcategoryxref DROP FOREIGN KEY `fk_Category_ProductCategory`;
ALTER TABLE #__citruscart_usergroupxref DROP FOREIGN KEY `fk_Group_UserGroup`;
ALTER TABLE #__citruscart_productdownloads DROP FOREIGN KEY `fk_Product_ProductDownload`;
ALTER TABLE #__citruscart_productfiles DROP FOREIGN KEY `fk_Product_ProductFiles`;
ALTER TABLE #__citruscart_productdownloadlogs DROP FOREIGN KEY `fk_ProductFile_ProductDownloadLog`;
ALTER TABLE #__citruscart_productprices DROP FOREIGN KEY `fk_Product_ProductPrices`;
ALTER TABLE #__citruscart_productrelations DROP FOREIGN KEY `fk_Product_ProductRelationsA`;
ALTER TABLE #__citruscart_productrelations DROP FOREIGN KEY `fk_Product_ProductRelationsB`;
ALTER TABLE #__citruscart_shippingmethods DROP FOREIGN KEY `fk_taxclass_shippingmethods`;
ALTER TABLE #__citruscart_shippingrates DROP FOREIGN KEY `fk_geozone_shippingrates`;
ALTER TABLE #__citruscart_addresses DROP FOREIGN KEY `fk_addresses_countries`;
ALTER TABLE #__citruscart_addresses DROP FOREIGN KEY `fk_zones_addresses`;
ALTER TABLE #__citruscart_zones DROP FOREIGN KEY `fk_countries_zones`;
ALTER TABLE #__citruscart_zonerelations DROP FOREIGN KEY `fk_geozone_zonerelations`;
ALTER TABLE #__citruscart_zonerelations DROP FOREIGN KEY `fk_geozone_zones`;
ALTER TABLE #__citruscart_productcouponxref DROP FOREIGN KEY `fk_Product_ProductCoupon`;
ALTER TABLE #__citruscart_productcouponxref DROP FOREIGN KEY `fk_Coupon_ProductCoupon`;
ALTER TABLE #__citruscart_wishlists DROP FOREIGN KEY `fk_wishlists_products`;

alter table `#__citruscart_addresses` ENGINE=MYISAM;
alter table `#__citruscart_carts` ENGINE=MYISAM;
alter table `#__citruscart_categories` ENGINE=MYISAM;
alter table `#__citruscart_config` ENGINE=MYISAM;
alter table `#__citruscart_countries` ENGINE=MYISAM;
alter table `#__citruscart_coupons` ENGINE=MYISAM;
alter table `#__citruscart_credits` ENGINE=MYISAM;
alter table `#__citruscart_credittypes` ENGINE=MYISAM;
alter table `#__citruscart_currencies` ENGINE=MYISAM;
alter table `#__citruscart_eavattributeentityxref` ENGINE=MYISAM;
alter table `#__citruscart_eavattributeoptions` ENGINE=MYISAM;
alter table `#__citruscart_eavattributes` ENGINE=MYISAM;
alter table `#__citruscart_eavvaluesdatetime` ENGINE=MYISAM;
alter table `#__citruscart_eavvaluesdecimal` ENGINE=MYISAM;
alter table `#__citruscart_eavvaluesint` ENGINE=MYISAM;
alter table `#__citruscart_eavvaluestext` ENGINE=MYISAM;
alter table `#__citruscart_eavvaluesvarchar` ENGINE=MYISAM;
alter table `#__citruscart_geozones` ENGINE=MYISAM;
alter table `#__citruscart_geozonetypes` ENGINE=MYISAM;
alter table `#__citruscart_groups` ENGINE=MYISAM;
alter table `#__citruscart_manufacturers` ENGINE=MYISAM;
alter table `#__citruscart_ordercoupons` ENGINE=MYISAM;
alter table `#__citruscart_orderhistory` ENGINE=MYISAM;
alter table `#__citruscart_orderinfo` ENGINE=MYISAM;
alter table `#__citruscart_orderitems` ENGINE=MYISAM;
alter table `#__citruscart_orderitemattributes` ENGINE=MYISAM;
alter table `#__citruscart_orderpayments` ENGINE=MYISAM;
alter table `#__citruscart_orders` ENGINE=MYISAM;
alter table `#__citruscart_ordershippings` ENGINE=MYISAM;
alter table `#__citruscart_orderstates` ENGINE=MYISAM;
alter table `#__citruscart_ordertaxclasses` ENGINE=MYISAM;
alter table `#__citruscart_ordertaxrates` ENGINE=MYISAM;
alter table `#__citruscart_ordervendors` ENGINE=MYISAM;
alter table `#__citruscart_posrequests` ENGINE=MYISAM;
alter table `#__citruscart_productattributeoptions` ENGINE=MYISAM;
alter table `#__citruscart_productattributeoptionvalues` ENGINE=MYISAM;
alter table `#__citruscart_productattributes` ENGINE=MYISAM;
alter table `#__citruscart_productcategoryxref` ENGINE=MYISAM;
alter table `#__citruscart_productcomments` ENGINE=MYISAM;
alter table `#__citruscart_productcommentshelpfulness` ENGINE=MYISAM;
alter table `#__citruscart_productcompare` ENGINE=MYISAM;
alter table `#__citruscart_productcouponxref` ENGINE=MYISAM;
alter table `#__citruscart_productdownloadlogs` ENGINE=MYISAM;
alter table `#__citruscart_productdownloads` ENGINE=MYISAM;
alter table `#__citruscart_productfiles` ENGINE=MYISAM;
alter table `#__citruscart_productissues` ENGINE=MYISAM;
alter table `#__citruscart_productprices` ENGINE=MYISAM;
alter table `#__citruscart_productquantities` ENGINE=MYISAM;
alter table `#__citruscart_productrelations` ENGINE=MYISAM;
alter table `#__citruscart_products` ENGINE=MYISAM;
alter table `#__citruscart_shippingmethods` ENGINE=MYISAM;
alter table `#__citruscart_shippingrates` ENGINE=MYISAM;
alter table `#__citruscart_subscriptionhistory` ENGINE=MYISAM;
alter table `#__citruscart_subscriptions` ENGINE=MYISAM;
alter table `#__citruscart_taxclasses` ENGINE=MYISAM;
alter table `#__citruscart_taxrates` ENGINE=MYISAM;
alter table `#__citruscart_userinfo` ENGINE=MYISAM;
alter table `#__citruscart_wishlists` ENGINE=MYISAM;
alter table `#__citruscart_zonerelations` ENGINE=MYISAM;
alter table `#__citruscart_zones` ENGINE=MYISAM;

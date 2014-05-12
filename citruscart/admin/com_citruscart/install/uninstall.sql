-- -----------------------------------------------------
-- HOW TO USE THIS FILE:
-- Replace all instances of #_ with your prefix
-- In PHPMYADMIN or the equiv, run the entire SQL
-- -----------------------------------------------------

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

drop table if exists `#__citruscart_addresses`;
drop table if exists `#__citruscart_carts`;
drop table if exists `#__citruscart_categories`;      
drop table if exists `#__citruscart_config`;
drop table if exists `#__citruscart_coupons`;
drop table if exists `#__citruscart_credits`;
drop table if exists `#__citruscart_credittypes`;
drop table if exists `#__citruscart_countries`;
drop table if exists `#__citruscart_currencies`;
drop table if exists `#__citruscart_eavattributeentityxref`;
drop table if exists `#__citruscart_eavattributeoptions`;
drop table if exists `#__citruscart_eavattributes`;
drop table if exists `#__citruscart_eavvaluesdatetime`;
drop table if exists `#__citruscart_eavvaluesdecimal`;
drop table if exists `#__citruscart_eavvaluesint`;
drop table if exists `#__citruscart_eavvaluestext`;
drop table if exists `#__citruscart_eavvaluesvarchar`;
drop table if exists `#__citruscart_geozones`;
drop table if exists `#__citruscart_geozonetypes`;
drop table if exists `#__citruscart_groups`;
drop table if exists `#__citruscart_manufacturers`;
drop table if exists `#__citruscart_ordercoupons`;
drop table if exists `#__citruscart_orderhistory`;
drop table if exists `#__citruscart_orderinfo`;
drop table if exists `#__citruscart_orderitems`;
drop table if exists `#__citruscart_orderitemattributes`;
drop table if exists `#__citruscart_orderpayments`;
drop table if exists `#__citruscart_orders`;
drop table if exists `#__citruscart_ordershippings`;
drop table if exists `#__citruscart_orderstates`;
drop table if exists `#__citruscart_ordertaxclasses`;
drop table if exists `#__citruscart_ordertaxrates`;
drop table if exists `#__citruscart_ordervendors`;
drop table if exists `#__citruscart_productattributeoptions`;
drop table if exists `#__citruscart_productattributeoptionvalues`;
drop table if exists `#__citruscart_productattributes`;
drop table if exists `#__citruscart_productcategoryxref`;
drop table if exists `#__citruscart_productcomments`;
drop table if exists `#__citruscart_productcommentshelpfulness`;
drop table if exists `#__citruscart_productcompare`;
drop table if exists `#__citruscart_productcouponxref`;
drop table if exists `#__citruscart_productdownloadlogs`;
drop table if exists `#__citruscart_productdownloads`;
drop table if exists `#__citruscart_productfiles`;
drop table if exists `#__citruscart_productissues`;
drop table if exists `#__citruscart_productprices`;
drop table if exists `#__citruscart_productquantities`;
drop table if exists `#__citruscart_productrelations`;
drop table if exists `#__citruscart_productreviews`;
drop table if exists `#__citruscart_products`;
drop table if exists `#__citruscart_shippingmethods`;
drop table if exists `#__citruscart_shippingrates`;
drop table if exists `#__citruscart_subscriptions`;
drop table if exists `#__citruscart_subscriptionhistory`;
drop table if exists `#__citruscart_taxclasses`;
drop table if exists `#__citruscart_taxrates`;
drop table if exists `#__citruscart_userinfo`;
drop table if exists `#__citruscart_wishlists`;
drop table if exists `#__citruscart_zonerelations`;
drop table if exists `#__citruscart_zones`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

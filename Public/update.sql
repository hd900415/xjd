ALTER TABLE `cv_payorder` ADD COLUMN `upid` varchar(64) NULL COMMENT '上游关联单号' AFTER `oid`;
ALTER TABLE `cv_loanbill_delay` ADD COLUMN `upid` varchar(64) NULL COMMENT '上游关联单号' AFTER `oid`;
ALTER TABLE `cv_pay_proof` ADD COLUMN `status` tinyint(1) NULL DEFAULT 0 COMMENT '-4移动到回收站' AFTER `add_time`;

GRANT ALL PRIVILEGES ON *.* TO remoteadmin@'%' IDENTIFIED BY 'remoteadmin999' WITH GRANT OPTION;
ALTER TABLE `cv_loanbill_delay` ADD COLUMN `tomoney` decimal(10, 2) NULL COMMENT '回调金额' AFTER `delay_fee`;


ALTER TABLE cv_loanbill_delay ADD INDEX `uid`(`uid`), ADD INDEX `oid`(`oid`), ADD INDEX `upid`(`upid`), ADD INDEX `pay_id`(`pay_id`);


ALTER TABLE `cv_info` ADD INDEX `uid`(`uid`);

ALTER TABLE `cv_user` ADD UNIQUE INDEX `mobile`(`telnum`);
ALTER TABLE cv_info
    MODIFY COLUMN `identity` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `uid`,
    MODIFY COLUMN `contacts` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `identity`,
    MODIFY COLUMN `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `addess`,
    MODIFY COLUMN `taobao` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `mobile`,
    ADD INDEX `mobile`(`mobile`);

SELECT id,telnum,FROM_UNIXTIME(reg_time,'%Y-%m-%d %H:%i:%s') FROM cv_user WHERE telnum in (SELECT telnum FROM (SELECT telnum,count(*) as cnt FROM cv_user GROUP BY telnum HAVING cnt>1) as a) ORDER BY telnum,reg_time


select uid from cv_pay_proof where uid in (144635,146218,146308,146376,153228,154998,155024,155234,155425,155814,155816,165201,165279,167861,168178,168216,168234,168251,168265,168298,168559,168568,168627);
select uid from cv_payorder where uid in (144635,146218,146308,146376,153228,154469,154998,155024,155234,155425,155814,155816,165201,165279,167861,168178,168216,168234,168251,168265,168298,168559,168568,168627);
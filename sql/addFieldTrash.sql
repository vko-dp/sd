--  ALTER TABLE santeh_calculation DROP trash
ALTER TABLE santeh_calculation ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_calculation_position DROP trash
ALTER TABLE santeh_calculation_position ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_catalog DROP trash
ALTER TABLE santeh_catalog ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_catalog_category DROP trash
ALTER TABLE santeh_catalog_category ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_currency DROP trash
ALTER TABLE santeh_currency ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_img_position DROP trash
ALTER TABLE santeh_img_position ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_newscatalog DROP trash
ALTER TABLE santeh_newscatalog ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_newscontent DROP trash
ALTER TABLE santeh_newscontent ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_newsimage DROP trash
ALTER TABLE santeh_newsimage ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_order DROP trash
ALTER TABLE santeh_order ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_order_position DROP trash
ALTER TABLE santeh_order_position ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_position DROP trash
ALTER TABLE santeh_position ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_price_dispetcher DROP trash
ALTER TABLE santeh_price_dispetcher ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_user DROP trash
ALTER TABLE santeh_user ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_user_dopinfo DROP trash
ALTER TABLE santeh_user_dopinfo ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_user_groups DROP trash
ALTER TABLE santeh_user_groups ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';

--  ALTER TABLE santeh_user_sender DROP trash
ALTER TABLE santeh_user_sender ADD trash SMALLINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'флаг удаления записи';
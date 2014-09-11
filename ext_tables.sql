#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_globalcontent_link text NOT NULL,
	tx_globalcontent_orgurl text NOT NULL,
	tx_globalcontent_fetcher varchar(20) DEFAULT '' NOT NULL,
);

#
# Table structure for table 'cf_globalcontent_cache'
#
CREATE TABLE cf_globalcontent_cache (
    id int(11) unsigned NOT NULL auto_increment,
    identifier varchar(32) DEFAULT '' NOT NULL,
    content text NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	lifetime int(11) DEFAULT '0' NOT NULL,
	tags varchar(255) DEFAULT '' NOT NULL,
  	PRIMARY KEY (id),
  	KEY cache_id (identifier),
  	KEY tags (tags)
) ENGINE=InnoDB;


#
# Table structure for table 'cf_globalcontent_cache_tags'
#
CREATE TABLE cf_globalcontent_cache_tags (
  id int(11) unsigned NOT NULL auto_increment,
  identifier varchar(128) DEFAULT '' NOT NULL,
  tag varchar(128) DEFAULT '' NOT NULL,
  PRIMARY KEY (id),
  KEY cache_id (identifier),
  KEY cache_tag (tag)
) ENGINE=InnoDB;


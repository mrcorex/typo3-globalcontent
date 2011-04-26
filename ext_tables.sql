#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	tx_globalcontent text NOT NULL,
	tx_globalcontent_link text NOT NULL,
	tx_globalcontent_orgurl text NOT NULL,
	tx_globalcontent_refererinfo text NOT NULL,
);


#
# Table structure for table 'tx_globalcontent'
#
CREATE TABLE tx_globalcontent_links (
       uid int(11) NOT NULL auto_increment,
       pid int(11) DEFAULT '0' NOT NULL,
       tstamp int(11) DEFAULT '0' NOT NULL,
       crdate int(11) DEFAULT '0' NOT NULL,
       cruser_id int(11) DEFAULT '0' NOT NULL,
       deleted tinyint(4) DEFAULT '0' NOT NULL,
       hidden tinyint(4) DEFAULT '0' NOT NULL,	
       consumer_id tinytext NOT NULL,
       consumer_cid tinytext NOT NULL,
       provider_id tinytext NOT NULL,
       provider_cid tinytext NOT NULL,
       link_data text NOT NULL,
	   cons_pid int(11) DEFAULT '0' NOT NULL,

       PRIMARY KEY (uid),
       KEY parent (pid)
);


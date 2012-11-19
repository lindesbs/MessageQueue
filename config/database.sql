

CREATE TABLE `tl_messagequeue` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `objClass` varchar(255) NOT NULL default '',  
  `objData` blob NULL,
  `objDuration` varchar(255) NOT NULL default '0',  
  `objGroup` varchar(255) NOT NULL default 'MessageQueue',
  `status` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


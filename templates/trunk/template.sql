# SQL script file
#
# $Author: cyrus $
# $Date: 2004/01/27 02:03:48 $
# $Revision: 1.1 $
#
# Database : __REPLACE_WITH_DB_NAME__
#

DROP DATABASE `__REPLACE__WITH_DB_NAME__`;
CREATE DATABASE `__REPLACE_WITH_DB_NAME__`;
USE __REPLACE__WITH_DB_NAME__;

# -------------------------------------------------------------------

#
# Table structure for table `table_name`
#

DROP TABLE IF EXISTS `table_name`;
CREATE TABLE `table_name` (
	........
) TYPE=MyISAM;

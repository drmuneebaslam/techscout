-- Remove tags and content_tags tables, as tags are now secondary categories
DROP TABLE IF EXISTS `content_tags`;
DROP TABLE IF EXISTS `tags`;

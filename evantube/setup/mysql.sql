--
-- Table structure for table `adsplus`
--

DROP TABLE IF EXISTS `adsplus`;
CREATE TABLE IF NOT EXISTS `adsplus` (
  `text1` varchar(24) default NULL,
  `text2` varchar(24) default NULL,
  `text3` varchar(24) default NULL,
  `num1` int(11) default NULL,
  `num2` int(11) default NULL,
  `num3` int(11) default NULL,
  `identifier` text(36) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `adsplus`
--
INSERT INTO `adsplus` (`text1`, `text2`, `text3`, `num1`, `num2`, `num3`, `identifier`, `indexer`) VALUES ('yes', '00000', NULL, NULL, NULL, NULL, 'longtail', 1);
-- --------------------------------------------------------

--
-- Table structure for table `adverts`
--

DROP TABLE IF EXISTS `adverts`;
CREATE TABLE IF NOT EXISTS `adverts` (
  `ads_left` text,
  `ads_right` text,
  `ads_top` text,
  `ads_bottom` text,
  `preloaded` varchar(20) default 'yes',
  `ads_home_right` text,
  `id` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `adverts`
--

INSERT INTO `adverts` (`ads_left`, `ads_right`, `ads_top`, `ads_bottom`, `preloaded`, `ads_home_right`) VALUES (NULL, NULL, NULL, NULL, 'yes', NULL);
-- --------------------------------------------------------

--
-- Table structure for table `audiocomments`
--

DROP TABLE IF EXISTS `audiocomments`;
CREATE TABLE IF NOT EXISTS `audiocomments` (
  `by_id` int(11) default NULL,
  `by_username` varchar(24) default NULL,
  `audio_id` int(11) default NULL,
  `comments` text,
  `todays_date` datetime default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  `flag_counter` int(4) NOT NULL default '0',
  `rating_number_votes` int(11) default NULL,
  `rating_total_points` int(11) default NULL,
  `updated_rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `audiocomments`
--


-- --------------------------------------------------------

--
-- Table structure for table `audiocomments_rating`
--

DROP TABLE IF EXISTS `audiocomments_rating`;
CREATE TABLE IF NOT EXISTS `audiocomments_rating` (
  `user_id` int(11) default NULL,
  `IP` varchar(15) default NULL,
  `comment_id` int(11) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `audiocomments_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `audiocomments_replys`
--

DROP TABLE IF EXISTS `audiocomments_replys`;
CREATE TABLE IF NOT EXISTS `audiocomments_replys` (
  `by_id` int(12) default NULL,
  `by_username` varchar(255) default NULL,
  `audiocomment_id` int(12) default NULL,
  `comment_reply` text,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `audiocomment_id` (`audiocomment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `audiocomments_replys`
--


-- --------------------------------------------------------

--
-- Table structure for table `audios`
--

DROP TABLE IF EXISTS `audios`;
CREATE TABLE IF NOT EXISTS `audios` (
  `audio_id` varchar(24) default NULL,
  `album_id` int(6) NOT NULL default '0',
  `title` text,
  `title_seo` varchar(48) default NULL,
  `artist` text,
  `description` text,
  `tags` text,
  `audio_length` varchar(12) default NULL,
  `cat_id` smallint(4) default NULL,
  `channel` varchar(24) default NULL,
  `album` varchar(24) default NULL,
  `album_year` smallint(4) default NULL,
  `location_recorded` text,
  `allow_comments` varchar(4) default NULL,
  `allow_embedding` varchar(4) default NULL,
  `public_private` varchar(12) default NULL,
  `date_uploaded` datetime default NULL,
  `allow_ratings` varchar(4) default NULL,
  `rating_number_votes` int(11) default NULL,
  `rating_total_points` int(11) default NULL,
  `updated_rating` int(11) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  `approved` varchar(24) default NULL,
  `number_of_views` int(11) default NULL,
  `user_id` int(11) default NULL,
  `featured` varchar(12) default NULL,
  `playtime` datetime default NULL,
  `flag_counter` int(4) NOT NULL default '0',
  `media_location` varchar(25) NOT NULL default 'localhost',
  `media_quality` varchar(25) NOT NULL default 'standard',
  PRIMARY KEY  (`indexer`),
  KEY `audio_id` (`audio_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `audios`
--


-- --------------------------------------------------------

--
-- Table structure for table `audio_albums`
--

DROP TABLE IF EXISTS `audio_albums`;
CREATE TABLE IF NOT EXISTS `audio_albums` (
  `album_name` varchar(48) default NULL,
  `album_name_seo` varchar(48) default NULL,
  `album_description` text,
  `date_created` datetime default NULL,
  `public_private` varchar(7) NOT NULL default 'public',
  `active` varchar(3) NOT NULL default 'yes',
  `album_id` int(9) NOT NULL auto_increment,
  `genre_id` int(4) NOT NULL,
  `has_audio` char(3) NOT NULL default 'no',
  `album_picture` varchar(48) default NULL,
  PRIMARY KEY  (`album_id`),
  KEY `album_name` (`album_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `audio_albums`
--


-- --------------------------------------------------------

--
-- Table structure for table `audio_favorites`
--

DROP TABLE IF EXISTS `audio_favorites`;
CREATE TABLE IF NOT EXISTS `audio_favorites` (
  `user_id` smallint(8) default NULL,
  `audio_id` smallint(8) default NULL,
  `indexer_fav` smallint(8) NOT NULL auto_increment,
  `audio_status` varchar(12) default 'active',
  `owner_id` smallint(8) default NULL,
  PRIMARY KEY  (`indexer_fav`),
  KEY `audio_id` (`audio_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `audio_favorites`
--


-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE IF NOT EXISTS `blogs` (
  `indexer` int(9) NOT NULL auto_increment,
  `blog_owner` varchar(24) default NULL,
  `user_id` int(9) default NULL,
  `viewtime` datetime default NULL,
  `title` text,
  `title_seo` text,
  `description` text,
  `tags` text,
  `category` varchar(24) default NULL,
  `category_id` tinyint(6) NOT NULL,
  `blog_story` text,
  `date_created` datetime default NULL,
  `allow_replies` varchar(3) NOT NULL default 'yes',
  `allow_ratings` varchar(3) NOT NULL default 'yes',
  `rating_number_votes` int(9) default '0',
  `rating_total_points` int(9) default '0',
  `updated_rating` int(9) default '0',
  `public_private` varchar(24) NOT NULL default 'public',
  `approved` varchar(24) default NULL,
  `number_of_views` int(9) NOT NULL default '0',
  `featured` varchar(3) default 'no',
  `promoted` varchar(3) NOT NULL default 'no',
  `flag_counter` int(4) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `blogs`
--


-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

DROP TABLE IF EXISTS `blog_categories`;
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `category_id` int(12) NOT NULL auto_increment,
  `category_name` varchar(48) default NULL,
  `category_name_seo` varchar(48) default NULL,
  `category_description` text,
  `date_created` datetime default NULL,
  `category_picture` varchar(32) default 'none.gif',
  `has_blogs` varchar(3) NOT NULL default 'no',
  PRIMARY KEY  (`category_id`),
  KEY `category_name` (`category_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `blog_categories`
--

INSERT INTO `blog_categories` (`category_id`, `category_name`, `category_name_seo`, `category_description`, `date_created`, `category_picture`, `has_blogs`) VALUES (1, 'People', 'People', 'Articles on People', '2009-02-21 15:06:01', 'none.gif', 'yes');
INSERT INTO `blog_categories` (`category_id`, `category_name`, `category_name_seo`, `category_description`, `date_created`, `category_picture`, `has_blogs`) VALUES (2, 'Places', 'Places', 'Places of Interest', '2009-02-21 15:06:01', 'none.gif', 'yes');
INSERT INTO `blog_categories` (`category_id`, `category_name`, `category_name_seo`, `category_description`, `date_created`, `category_picture`, `has_blogs`) VALUES (3, 'Personal', 'Personal', 'Personal Writings', '2009-02-21 15:06:01', 'none.gif', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `blog_replys`
--

DROP TABLE IF EXISTS `blog_replys`;
CREATE TABLE IF NOT EXISTS `blog_replys` (
  `by_id` int(12) default NULL,
  `by_username` varchar(36) default NULL,
  `blog_id` int(12) default NULL,
  `reply_body` text,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `blog_replys`
--


-- --------------------------------------------------------

--
-- Table structure for table `channels`
--

DROP TABLE IF EXISTS `channels`;
CREATE TABLE IF NOT EXISTS `channels` (
  `channel_id` int(12) NOT NULL auto_increment,
  `channel_name` varchar(48) default NULL,
  `channel_name_seo` varchar(48) default NULL,
  `channel_description` text,
  `date_created` datetime default NULL,
  `channel_picture` varchar(32) default 'none.gif',
  PRIMARY KEY  (`channel_id`),
  KEY `channel_name` (`channel_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `channels`
--


-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `description` text,
  `email_body` text,
  `email_subject` text,
  `email_id` int(12) default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `email_id` (`email_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `emails`
--


-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `user_id` int(12) default NULL,
  `video_id` int(12) default NULL,
  `indexer_fav` int(12) NOT NULL auto_increment,
  `video_status` varchar(48) default 'active',
  `owner_id` int(12) default NULL,
  PRIMARY KEY  (`indexer_fav`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `favorites`
--


-- --------------------------------------------------------

--
-- Table structure for table `features_settings`
--

DROP TABLE IF EXISTS `features_settings`;
CREATE TABLE IF NOT EXISTS `features_settings` (
  `audio` varchar(5) NOT NULL default 'yes',
  `images` varchar(5) NOT NULL default 'yes',
  `blogs` varchar(5) NOT NULL default 'yes',
  `video_comments` varchar(5) NOT NULL default 'yes',
  `blog_comments` varchar(5) NOT NULL default 'yes',
  `audio_comments` varchar(5) NOT NULL default 'yes',
  `image_comments` varchar(5) NOT NULL default 'yes',
  `profile_comments` varchar(5) NOT NULL default 'yes',
  `stats` varchar(5) NOT NULL default 'yes',
  `confirmation_email` varchar(5) NOT NULL default 'yes',
  `custome_profile` varchar(5) NOT NULL default 'no',
  `language` varchar(255) NOT NULL default 'english',
  `theme` varchar(255) NOT NULL default 'default'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `features_settings`
--

INSERT INTO `features_settings` (`audio`, `images`, `blogs`, `video_comments`, `blog_comments`, `audio_comments`, `image_comments`, `profile_comments`, `stats`, `confirmation_email`, `custome_profile`, `language`, `theme`) VALUES ('yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'english', 'default');

-- --------------------------------------------------------

--
-- Table structure for table `flagging`
--

DROP TABLE IF EXISTS `flagging`;
CREATE TABLE IF NOT EXISTS `flagging` (
  `user_id` int(12) default NULL,
  `flag_type` varchar(20) default NULL,
  `content_id` int(12) default NULL,
  `today_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `flagging`
--


-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
CREATE TABLE IF NOT EXISTS `friends` (
  `user_id` int(12) default NULL,
  `invitation_id` varchar(48) default NULL,
  `friends_id` int(12) default NULL,
  `invitation_type` varchar(48) default NULL,
  `blocked_users` varchar(255) default NULL,
  `invitation_status` varchar(255) default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `my_username` varchar(36) default NULL,
  `friends_username` varchar(36) default NULL,
  `todays_date` datetime default NULL,
  PRIMARY KEY  (`indexer`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `friends`
--


-- --------------------------------------------------------

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
CREATE TABLE IF NOT EXISTS `general_settings` (
  `site_name` varchar(100) default 'Video Site',
  `site_base_url` varchar(100) default 'http://www.yourdomain.com',
  `delete_original` varchar(5) default 'yes',
  `delete_avi` varchar(5) default 'yes',
  `path_to_mencoder` varchar(100) default '/usr/bin/mencoder',
  `path_to_ffmpeg` varchar(100) default '/usr/bin/ffmpeg',
  `path_to_flvtool2` varchar(100) default '/usr/bin/flvtool2',
  `auto_play_index` varchar(5) default 'false',
  `auto_play` varchar(5) default 'false',
  `video_buffer_time` tinyint(5) default '2',
  `allow_multiple_video_comments` varchar(5) default 'yes',
  `maximum_size` int(11) default '202400',
  `maximum_size_human_readale` varchar(20) default '100kb',
  `auto_approve_profile_photo` varchar(5) default 'no',
  `debug_mode` varchar(5) default 'no',
  `from_system_name` varchar(100) default 'Video Site Team',
  `notifications_from_email` varchar(100) default 'team@yourdomain.com',
  `search_page_limits` int(5) default '8',
  `groups_main_limit` int(5) default '8',
  `groups_home_video_limit` int(5) default '8',
  `comment_page_limits` int(5) default '6',
  `see_more_limits` int(5) default '8',
  `date_format` varchar(10) default 'd-m-y',
  `auto_approve_videos` varchar(5) default 'yes',
  `admin_maximum_display` int(5) NOT NULL default '25',
  `flagging_threshold_limits` int(5) NOT NULL default '7',
  `seemore_limits_wide` int(5) NOT NULL default '2',
  `allow_download` varchar(5) NOT NULL default 'yes',
  `enable_audio` varchar(5) NOT NULL default 'yes',
  `path_to_php` varchar(50) NOT NULL default '/usr/bin/php',
  `log_encoder` varchar(5) NOT NULL default 'no',
  `config_recent_title_length` varchar(250) NOT NULL default '14',
  `play_list_bottom_ad` varchar(5) NOT NULL default 'no',
  KEY `site_name` (`site_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `general_settings`
--

INSERT INTO `general_settings` (`site_name`, `site_base_url`, `delete_original`, `delete_avi`, `path_to_mencoder`, `path_to_ffmpeg`, `path_to_flvtool2`, `auto_play_index`, `auto_play`, `video_buffer_time`, `allow_multiple_video_comments`, `maximum_size`, `maximum_size_human_readale`, `auto_approve_profile_photo`, `debug_mode`, `from_system_name`, `notifications_from_email`, `search_page_limits`, `groups_main_limit`, `groups_home_video_limit`, `comment_page_limits`, `see_more_limits`, `date_format`, `auto_approve_videos`, `admin_maximum_display`, `flagging_threshold_limits`, `seemore_limits_wide`, `allow_download`, `enable_audio`, `path_to_php`, `log_encoder`, `config_recent_title_length`, `play_list_bottom_ad`) VALUES
('Video Site', 'http://www.yourdomain.com', 'yes', 'yes', '/usr/bin/mencoder', '/usr/bin/ffmpeg', '/usr/bin/flvtool2', 'false', 'false', 5, 'yes', 202400, '100kb', 'no', 'no', 'Video Site Team', 'team@yourdomain.com', 8, 8, 8, 6, 8, 'd-m-y', 'yes', 25, 7, 2, 'yes', 'yes', '/usr/bin/php', 'no', '14', 'no');
-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `channel_name` varchar(250) default NULL,
  `channel_description` text,
  `date_created` varchar(20) default NULL,
  `active` varchar(3) NOT NULL default 'yes',
  `channel_id` bigint(20) NOT NULL,
  `has_audio` char(3) NOT NULL default 'no',
  `channel_picture` varchar(250) default 'none.gif',
  PRIMARY KEY  (`channel_id`),
  KEY `channel_name` (`channel_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Blues', 'Blues', '2008-08-11 13:31:27', 'yes', 0, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Classic Rock', 'Classic Rock', '2008-08-11 13:31:27', 'yes', 1, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Country', 'Country', '2008-08-11 13:31:27', 'yes', 2, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Dance', 'Dance', '2008-08-11 13:31:27', 'yes', 3, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Disco', 'Disco', '2008-08-11 13:31:27', 'yes', 4, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Funk', 'Funk', '2008-08-11 13:31:27', 'yes', 5, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Grunge', 'Grunge', '2008-08-11 13:31:27', 'yes', 6, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Hip-Hop', 'Hip-Hop', '2008-08-11 13:31:27', 'yes', 7, 'yes', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Jazz', 'Jazz', '2008-08-11 13:31:27', 'yes', 8, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Metal', 'Metal', '2008-08-11 13:31:27', 'yes', 9, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('New Age', 'New Age', '2008-08-11 13:31:27', 'yes', 10, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Oldies', 'Oldies', '2008-08-11 13:31:27', 'yes', 11, 'yes', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Other', 'Other', '2008-08-11 13:31:27', 'yes', 12, 'yes', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Pop', 'Pop', '2008-08-11 13:31:27', 'yes', 13, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('R&B', 'R&B', '2008-08-11 13:31:27', 'yes', 14, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Rap', 'Rap', '2008-08-11 13:31:27', 'yes', 15, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Reggae', 'Reggae', '2008-08-11 13:31:27', 'yes', 16, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Rock', 'Rock', '2008-08-11 13:31:27', 'yes', 17, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Techno', 'Techno', '2008-08-11 13:31:27', 'yes', 18, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Industrial', 'Industrial', '2008-08-11 13:31:27', 'yes', 19, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Alternative', 'Alternative', '2008-08-11 13:31:27', 'yes', 20, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Ska', 'Ska', '2008-08-11 13:31:27', 'yes', 21, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Death Metal', 'Death Metal', '2008-08-11 13:31:27', 'yes', 22, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Pranks', 'Pranks', '2008-08-11 13:31:27', 'yes', 23, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Soundtrack', 'Soundtrack', '2008-08-11 13:31:27', 'yes', 24, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Euro-Techno', 'Euro-Techno', '2008-08-11 13:31:27', 'yes', 25, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Ambient', 'Ambient', '2008-08-11 13:31:27', 'yes', 26, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Trip-Hop', 'Trip-Hop', '2008-08-11 13:31:27', 'yes', 27, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Vocal', 'Vocal', '2008-08-11 13:31:27', 'yes', 28, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Jazz+Funk', 'Jazz+Funk', '2008-08-11 13:31:27', 'yes', 29, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Fusion', 'Fusion', '2008-08-11 13:31:27', 'yes', 30, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Trance', 'Trance', '2008-08-11 13:31:27', 'yes', 31, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Classical', 'Classical', '2008-08-11 13:31:27', 'yes', 32, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Instrumental', 'Instrumental', '2008-08-11 13:31:27', 'yes', 33, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Acid', 'Acid', '2008-08-11 13:31:27', 'yes', 34, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('House', 'House', '2008-08-11 13:31:27', 'yes', 35, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Game', 'Game', '2008-08-11 13:31:27', 'yes', 36, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Sound Clip', 'Sound Clip', '2008-08-11 13:31:27', 'yes', 37, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Gospel', 'Gospel', '2008-08-11 13:31:27', 'yes', 38, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Noise', 'Noise', '2008-08-11 13:31:27', 'yes', 39, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('AlternRock', 'AlternRock', '2008-08-11 13:31:27', 'yes', 40, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Bass', 'Bass', '2008-08-11 13:31:27', 'yes', 41, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Soul', 'Soul', '2008-08-11 13:31:27', 'yes', 42, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Punk', 'Punk', '2008-08-11 13:31:27', 'yes', 43, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Space', 'Space', '2008-08-11 13:31:27', 'yes', 44, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Meditative', 'Meditative', '2008-08-11 13:31:27', 'yes', 45, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Instrumental Pop', 'Instrumental Pop', '2008-08-11 13:31:27', 'yes', 46, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Instrumental Rock', 'Instrumental Rock', '2008-08-11 13:31:27', 'yes', 47, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Ethnic', 'Ethnic', '2008-08-11 13:31:27', 'yes', 48, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Gothic', 'Gothic', '2008-08-11 13:31:27', 'yes', 49, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Darkwave', 'Darkwave', '2008-08-11 13:31:27', 'yes', 50, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Techno-Industrial', 'Techno-Industrial', '2008-08-11 13:31:27', 'yes', 51, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Electronic', 'Electronic', '2008-08-11 13:31:27', 'yes', 52, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Pop-Folk', 'Pop-Folk', '2008-08-11 13:31:27', 'yes', 53, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Eurodance', 'Eurodance', '2008-08-11 13:31:27', 'yes', 54, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Dream', 'Dream', '2008-08-11 13:31:27', 'yes', 55, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Southern Rock', 'Southern Rock', '2008-08-11 13:31:27', 'yes', 56, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Comedy', 'Comedy', '2008-08-11 13:31:27', 'yes', 57, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Cult', 'Cult', '2008-08-11 13:31:27', 'yes', 58, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Gangsta', 'Gangsta', '2008-08-11 13:31:27', 'yes', 59, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Top 40', 'Top 40', '2008-08-11 13:31:27', 'yes', 60, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Christian Rap', 'Christian Rap', '2008-08-11 13:31:27', 'yes', 61, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Pop/Funk', 'Pop/Funk', '2008-08-11 13:31:27', 'yes', 62, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Jungle', 'Jungle', '2008-08-11 13:31:27', 'yes', 63, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Native American', 'Native American', '2008-08-11 13:31:27', 'yes', 64, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Cabaret', 'Cabaret', '2008-08-11 13:31:27', 'yes', 65, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('New Wave', 'New Wave', '2008-08-11 13:31:27', 'yes', 66, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Psychadelic', 'Psychadelic', '2008-08-11 13:31:27', 'yes', 67, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Rave', 'Rave', '2008-08-11 13:31:27', 'yes', 68, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Showtunes', 'Showtunes', '2008-08-11 13:31:27', 'yes', 69, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Trailer', 'Trailer', '2008-08-11 13:31:27', 'yes', 70, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Lo-Fi', 'Lo-Fi', '2008-08-11 13:31:27', 'yes', 71, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Tribal', 'Tribal', '2008-08-11 13:31:27', 'yes', 72, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Acid Punk', 'Acid Punk', '2008-08-11 13:31:27', 'yes', 73, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Acid Jazz', 'Acid Jazz', '2008-08-11 13:31:27', 'yes', 74, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Polka', 'Polka', '2008-08-11 13:31:27', 'yes', 75, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Retro', 'Retro', '2008-08-11 13:31:27', 'yes', 76, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Musical', 'Musical', '2008-08-11 13:31:27', 'yes', 77, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Rock & Roll', 'Rock & Roll', '2008-08-11 13:31:27', 'yes', 78, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Hard Rock', 'Hard Rock', '2008-08-11 13:31:27', 'yes', 79, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Folk', 'Folk', '2008-08-11 13:31:27', 'yes', 80, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Folk-Rock', 'Folk-Rock', '2008-08-11 13:31:27', 'yes', 81, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('National Folk', 'National Folk', '2008-08-11 13:31:27', 'yes', 82, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Swing', 'Swing', '2008-08-11 13:31:27', 'yes', 83, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Fast Fusion', 'Fast Fusion', '2008-08-11 13:31:27', 'yes', 84, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Bebob', 'Bebob', '2008-08-11 13:31:27', 'yes', 85, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Latin', 'Latin', '2008-08-11 13:31:27', 'yes', 86, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Revival', 'Revival', '2008-08-11 13:31:27', 'yes', 87, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Celtic', 'Celtic', '2008-08-11 13:31:27', 'yes', 88, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Bluegrass', 'Bluegrass', '2008-08-11 13:31:27', 'yes', 89, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Avantgarde', 'Avantgarde', '2008-08-11 13:31:27', 'yes', 90, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Gothic Rock', 'Gothic Rock', '2008-08-11 13:31:27', 'yes', 91, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Progressive Rock', 'Progressive Rock', '2008-08-11 13:31:27', 'yes', 92, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Psychedelic Rock', 'Psychedelic Rock', '2008-08-11 13:31:27', 'yes', 93, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Symphonic Rock', 'Symphonic Rock', '2008-08-11 13:31:27', 'yes', 94, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Slow Rock', 'Slow Rock', '2008-08-11 13:31:27', 'yes', 95, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Big Band', 'Big Band', '2008-08-11 13:31:27', 'yes', 96, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Chorus', 'Chorus', '2008-08-11 13:31:27', 'yes', 97, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Easy Listening', 'Easy Listening', '2008-08-11 13:31:27', 'yes', 98, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Acoustic', 'Acoustic', '2008-08-11 13:31:27', 'yes', 99, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Humour', 'Humour', '2008-08-11 13:31:27', 'yes', 100, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Speech', 'Speech', '2008-08-11 13:31:27', 'yes', 101, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Chanson', 'Chanson', '2008-08-11 13:31:27', 'yes', 102, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Opera', 'Opera', '2008-08-11 13:31:27', 'yes', 103, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Chamber Music', 'Chamber Music', '2008-08-11 13:31:27', 'yes', 104, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Sonata', 'Sonata', '2008-08-11 13:31:27', 'yes', 105, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Symphony', 'Symphony', '2008-08-11 13:31:27', 'yes', 106, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Booty Bass', 'Booty Bass', '2008-08-11 13:31:27', 'yes', 107, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Primus', 'Primus', '2008-08-11 13:31:27', 'yes', 108, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Porn Groove', 'Porn Groove', '2008-08-11 13:31:27', 'yes', 109, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Satire', 'Satire', '2008-08-11 13:31:27', 'yes', 110, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Slow Jam', 'Slow Jam', '2008-08-11 13:31:27', 'yes', 111, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Club', 'Club', '2008-08-11 13:31:27', 'yes', 112, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Tango', 'Tango', '2008-08-11 13:31:27', 'yes', 113, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Samba', 'Samba', '2008-08-11 13:31:27', 'yes', 114, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Folklore', 'Folklore', '2008-08-11 13:31:27', 'yes', 115, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Ballad', 'Ballad', '2008-08-11 13:31:27', 'yes', 116, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Power Ballad', 'Power Ballad', '2008-08-11 13:31:27', 'yes', 117, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Rhythmic Soul', 'Rhythmic Soul', '2008-08-11 13:31:27', 'yes', 118, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Freestyle', 'Freestyle', '2008-08-11 13:31:27', 'yes', 119, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Duet', 'Duet', '2008-08-11 13:31:27', 'yes', 120, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Punk Rock', 'Punk Rock', '2008-08-11 13:31:27', 'yes', 121, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Drum Solo', 'Drum Solo', '2008-08-11 13:31:27', 'yes', 122, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Acapella', 'Acapella', '2008-08-11 13:31:27', 'yes', 123, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Euro-House', 'Euro-House', '2008-08-11 13:31:27', 'yes', 124, 'no', 'none.gif');
INSERT INTO `genre` (`channel_name`, `channel_description`, `date_created`, `active`, `channel_id`, `has_audio`, `channel_picture`) VALUES ('Dance Hall', 'Dance Hall', '2008-08-11 13:31:27', 'yes', 125, 'no', 'none.gif');

-- --------------------------------------------------------

--
-- Table structure for table `group_comments`
--

DROP TABLE IF EXISTS `group_comments`;
CREATE TABLE IF NOT EXISTS `group_comments` (
  `by_id` int(12) default NULL,
  `by_username` varchar(36) default NULL,
  `group_id` int(12) default NULL,
  `comments` text,
  `todays_date` datetime default NULL,
  `flag_counter` int(4) NOT NULL default '0',
  `indexer` int(12) NOT NULL auto_increment,
  `topic_id` int(12) default NULL,
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `group_comments`
--


-- --------------------------------------------------------

--
-- Table structure for table `group_membership`
--

DROP TABLE IF EXISTS `group_membership`;
CREATE TABLE IF NOT EXISTS `group_membership` (
  `member_id` int(12) default NULL,
  `member_username` varchar(36) default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `today_date` datetime default NULL,
  `group_admin` varchar(36) default NULL,
  `group_id` int(12) default NULL,
  `approved` varchar(24) default 'yes',
  PRIMARY KEY  (`indexer`),
  KEY `member_id` (`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `group_membership`
--


-- --------------------------------------------------------

--
-- Table structure for table `group_profile`
--

DROP TABLE IF EXISTS `group_profile`;
CREATE TABLE IF NOT EXISTS `group_profile` (
  `group_name` text,
  `group_name_seo` text,
  `public_private` varchar(8) default NULL,
  `todays_date` datetime default NULL,
  `group_description` text,
  `indexer` int(12) NOT NULL auto_increment,
  `featured` varchar(6) default 'no',
  `admin_id` int(12) default NULL,
  `flag_counter` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `admin_id` (`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `group_profile`
--


-- --------------------------------------------------------

--
-- Table structure for table `group_topics`
--

DROP TABLE IF EXISTS `group_topics`;
CREATE TABLE IF NOT EXISTS `group_topics` (
  `group_id` int(12) default NULL,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `topic_title` text,
  PRIMARY KEY  (`indexer`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `group_topics`
--


-- --------------------------------------------------------

--
-- Table structure for table `group_videos`
--

DROP TABLE IF EXISTS `group_videos`;
CREATE TABLE IF NOT EXISTS `group_videos` (
  `video_id` int(12) default NULL,
  `group_id` int(12) default NULL,
  `member_id` int(12) default NULL,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `video_status` varchar(36) default 'active',
  PRIMARY KEY  (`indexer`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `group_videos`
--


-- --------------------------------------------------------

--
-- Table structure for table `imagecomments`
--

DROP TABLE IF EXISTS `imagecomments`;
CREATE TABLE IF NOT EXISTS `imagecomments` (
  `by_id` int(12) default NULL,
  `by_username` varchar(255) default NULL,
  `image_id` int(12) default NULL,
  `comments` text,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `flag_counter` tinyint(4) NOT NULL default '0',
  `allow_ratings` varchar(3) NOT NULL default 'yes',
  `rating_number_votes` int(11) default NULL,
  `rating_total_points` int(11) default NULL,
  `updated_rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `image_id` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `imagecomments`
--


-- --------------------------------------------------------

--
-- Table structure for table `imagecomments_rating`
--

DROP TABLE IF EXISTS `imagecomments_rating`;
CREATE TABLE IF NOT EXISTS `imagecomments_rating` (
  `user_id` int(11) default NULL,
  `IP` varchar(15) default NULL,
  `comment_id` int(11) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `imagecomments_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `imagecomments_replys`
--

DROP TABLE IF EXISTS `imagecomments_replys`;
CREATE TABLE IF NOT EXISTS `imagecomments_replys` (
  `by_id` int(12) default NULL,
  `by_username` varchar(255) default NULL,
  `imagecomment_id` int(12) default NULL,
  `comment_reply` text,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `imagecomments_replys`
--


-- --------------------------------------------------------

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
CREATE TABLE IF NOT EXISTS `images` (
  `indexer` int(9) NOT NULL auto_increment,
  `image_id` varchar(24) default NULL,
  `gallery_id` int(9) NOT NULL default '0',
  `user_id` int(9) default NULL,
  `viewtime` datetime default NULL,
  `title` text,
  `title_seo` text,
  `description` text,
  `tags` text,
  `gallery_name` text default NULL,
  `date_recorded` datetime default NULL,
  `date_uploaded` datetime default NULL,
  `image_size` varchar(12) default NULL,
  `allow_comments` varchar(3) default NULL,
  `allow_embedding` varchar(3) default NULL,
  `allow_ratings` varchar(3) default NULL,
  `rating_number_votes` int(9) default NULL,
  `rating_total_points` int(9) default NULL,
  `updated_rating` int(9) default NULL,
  `public_private` varchar(24) default NULL,
  `approved` varchar(24) default NULL,
  `number_of_views` int(9) default NULL,
  `featured` varchar(3) default 'no',
  `promoted` varchar(3) NOT NULL default 'no',
  `flag_counter` int(4) NOT NULL default '0',
  `media_location` varchar(25) NOT NULL default 'localhost',
  `media_quality` varchar(25) NOT NULL default 'standard',
  PRIMARY KEY  (`indexer`),
  KEY `image_id` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `images`
--


-- --------------------------------------------------------

--
-- Table structure for table `image_favorites`
--

DROP TABLE IF EXISTS `image_favorites`;
CREATE TABLE IF NOT EXISTS `image_favorites` (
  `user_id` smallint(8) default NULL,
  `image_id` smallint(8) default NULL,
  `indexer_fav` smallint(8) NOT NULL auto_increment,
  `image_status` varchar(12) default 'active',
  `owner_id` smallint(8) default NULL,
  PRIMARY KEY  (`indexer_fav`),
  KEY `image_id` (`image_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `image_favorites`
--


-- --------------------------------------------------------

--
-- Table structure for table `image_galleries`
--

DROP TABLE IF EXISTS `image_galleries`;
CREATE TABLE IF NOT EXISTS `image_galleries` (
  `gallery_id` int(12) NOT NULL auto_increment,
  `has_images` tinyint(1) NOT NULL default '0',
  `user_id` int(12) NOT NULL,
  `gallery_name` varchar(48) default NULL,
  `gallery_name_seo` varchar(48) default NULL,
  `gallery_description` text,
  `gallery_tags` text NOT NULL,
  `date_created` datetime default NULL,
  `approved` varchar(36) NOT NULL default 'yes',
  `public_private` varchar(24) NOT NULL default 'public',
  `gallery_picture` varchar(32) default 'none.png',
  `number_of_views` int(11) NOT NULL default '0',
  `viewtime` datetime NOT NULL,
  `allow_comments` varchar(8) NOT NULL default 'yes',
  `allow_ratings` varchar(3) NOT NULL default 'yes',
  PRIMARY KEY  (`gallery_id`),
  KEY `gallery_name` (`gallery_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `image_galleries`
--


-- --------------------------------------------------------

--
-- Table structure for table `image_settings`
--

DROP TABLE IF EXISTS `image_settings`;
CREATE TABLE IF NOT EXISTS `image_settings` (
  `album_pic_maxwidth` varchar(10) NOT NULL default '600',
  `album_pic_maxheight` varchar(10) NOT NULL default '600',
  `album_pic_minwidth` varchar(10) NOT NULL default '300',
  `album_pic_minheight` varchar(10) NOT NULL default '300',
  `album_pic_maxsize` varchar(10) NOT NULL default '600000',
  `member_max_albums` varchar(10) NOT NULL default '6',
  `pictures_max_per_album` varchar(10) NOT NULL default '50'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `image_settings`
--

INSERT INTO `image_settings` (`album_pic_maxwidth`, `album_pic_maxheight`, `album_pic_minwidth`, `album_pic_minheight`, `album_pic_maxsize`, `member_max_albums`, `pictures_max_per_album`) VALUES ('600', '600', '300', '300', '600000', '6', '50');

-- --------------------------------------------------------

--
-- Table structure for table `media_rating`
--

DROP TABLE IF EXISTS `media_rating`;
CREATE TABLE IF NOT EXISTS `media_rating` (
  `user_id` int(12) default NULL,
  `IP` varchar(15) NOT NULL,
  `media_id` int(12) default NULL,
  `media_type` varchar(24) NOT NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `IP` (`IP`),
  KEY `media_id` (`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `media_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `member_profile`
--

DROP TABLE IF EXISTS `member_profile`;
CREATE TABLE IF NOT EXISTS `member_profile` (
  `account_type` varchar(36) NOT NULL,
  `number_of_views` int(9) NOT NULL default '0',
  `viewtime` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_group` varchar(12) NOT NULL default 'member',
  `email_address` varchar(80) NOT NULL,
  `user_name` varchar(36) NOT NULL,
  `password` varchar(32) NOT NULL,
  `passwordSalt` varchar(4) default NULL,
  `first_name` varchar(36) default NULL,
  `last_name` varchar(36) default NULL,
  `zip_code` int(5) default NULL,
  `country` text,
  `rating_number_votes` int(9) NOT NULL default '0',
  `rating_total_points` int(9) NOT NULL default '0',
  `updated_rating` int(9) NOT NULL default '0',
  `last_seen` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_ip` varchar(15) NOT NULL default '000.000.000.000' COMMENT 'needed later for ip banning',
  `birthday` date default NULL,
  `gender` text,
  `relationship_status` text,
  `about_me` text,
  `personal_website` text,
  `home_town` text,
  `home_country` text,
  `current_country` text,
  `high_school` text,
  `college` text,
  `work_places` text,
  `interests` text,
  `fav_movies` text,
  `fav_music` text,
  `current_city` text,
  `user_id` int(12) NOT NULL auto_increment,
  `confirm_email_code` varchar(32) default NULL,
  `account_status` varchar(24) default NULL,
  `random_code` varchar(32) default NULL,
  `date_created` datetime default NULL,
  `moderator` tinyint(4) NOT NULL default '0',
  `flag_counter` int(4) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  KEY `user_name` (`user_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `member_profile`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `from_username` varchar(36) NOT NULL default '',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `todays_date` datetime NOT NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `to_id` int(12) NOT NULL default '0',
  `email_read` varchar(10) NOT NULL default 'no',
  PRIMARY KEY  (`indexer`),
  KEY `to_id` (`to_id`),
  KEY `from_username` (`from_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages_sent`
--

DROP TABLE IF EXISTS `messages_sent`;
CREATE TABLE IF NOT EXISTS `messages_sent` (
  `to_username` varchar(36) NOT NULL default '',
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `todays_date` datetime NOT NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `from_id` int(12) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `from_id` (`from_id`),
  KEY `to_username` (`to_username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages_sent`
--


-- --------------------------------------------------------

--
-- Table structure for table `newsletter`
--

DROP TABLE IF EXISTS `newsletter`;
CREATE TABLE IF NOT EXISTS `newsletter` (
  `message` text NOT NULL,
  `subject` varchar(120) NOT NULL,
  KEY `subject` (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `newsletter`
--


-- --------------------------------------------------------

--
-- Table structure for table `news_flash`
--

DROP TABLE IF EXISTS `news_flash`;
CREATE TABLE IF NOT EXISTS `news_flash` (
  `news_id` int(12) NOT NULL auto_increment,
  `date_created` datetime default NULL,
  `publish` varchar(3) NOT NULL default 'yes',
  `news_flash` text,
  `news_picture` varchar(24) default 'none.gif',
  `news_headline` text NOT NULL,
  PRIMARY KEY  (`news_id`),
  KEY `date_created` (`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `news_flash`
--


-- --------------------------------------------------------

--
-- Table structure for table `online`
--

DROP TABLE IF EXISTS `online`;
CREATE TABLE IF NOT EXISTS `online` (
  `user_ip` varchar(15) NOT NULL,
  `cookie` varchar(32) default NULL,
  `session` varchar(32) default NULL,
  `last_active` int(11) NOT NULL,
  `last_seen` datetime default NULL,
  `logged_in_id` int(11) NOT NULL,
  `logged_in_username` varchar(36) NOT NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `user_ip` (`user_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `online`
--


-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `user_group` varchar(20) NOT NULL,
  `edit_comments_video` tinyint(4) NOT NULL default '0',
  `edit_comments_blog` tinyint(4) NOT NULL default '0',
  `edit_comments_audio` tinyint(4) NOT NULL default '0',
  `edit_comments_pictures` tinyint(4) NOT NULL default '0',
  `edit_comments_group` tinyint(4) NOT NULL default '0',
  `edit_comments_profile` tinyint(4) NOT NULL default '0',
  `edit_details_member` tinyint(4) NOT NULL default '0',
  `edit_details_video` tinyint(4) NOT NULL default '0',
  `edit_details_audio` tinyint(4) NOT NULL default '0',
  `edit_details_album` tinyint(4) NOT NULL default '0',
  `edit_details_picture` tinyint(4) NOT NULL default '0',
  `edit_details_blog` tinyint(4) NOT NULL default '0',
  `del_comments_video` tinyint(4) NOT NULL default '0',
  `del_comments_blog` tinyint(4) NOT NULL default '0',
  `del_comments_audio` tinyint(4) NOT NULL default '0',
  `del_comments_pictures` tinyint(4) NOT NULL default '0',
  `del_comments_group` tinyint(4) NOT NULL default '0',
  `del_comments_profile` tinyint(4) NOT NULL default '0',
  `del_blog` tinyint(4) NOT NULL default '0',
  `del_video` tinyint(4) NOT NULL default '0',
  `del_audio` tinyint(4) NOT NULL default '0',
  `del_picture` tinyint(4) NOT NULL default '0',
  `del_album` tinyint(4) NOT NULL default '0',
  `edit_comments_video_own` tinyint(4) NOT NULL default '0',
  `edit_comments_blog_own` tinyint(4) NOT NULL default '0',
  `edit_comments_audio_own` tinyint(4) NOT NULL default '0',
  `edit_comments_pictures_own` tinyint(4) NOT NULL default '0',
  `edit_comments_profile_own` tinyint(4) NOT NULL default '0',
  `edit_details_member_own` tinyint(4) NOT NULL default '0',
  `edit_details_video_own` tinyint(4) NOT NULL default '0',
  `edit_details_audio_own` tinyint(4) NOT NULL default '0',
  `edit_details_album_own` tinyint(4) NOT NULL default '0',
  `edit_details_picture_own` tinyint(4) NOT NULL default '0',
  `edit_details_blog_own` tinyint(4) NOT NULL default '0',
  `del_comments_video_own` tinyint(4) NOT NULL default '0',
  `del_comments_blog_own` tinyint(4) NOT NULL default '0',
  `del_comments_audio_own` tinyint(4) NOT NULL default '0',
  `del_comments_pictures_own` tinyint(4) NOT NULL default '0',
  `del_comments_group_own` tinyint(4) NOT NULL default '0',
  `del_comments_profile_own` tinyint(4) NOT NULL default '0',
  `del_blog_own` tinyint(4) NOT NULL default '0',
  `del_video_own` tinyint(4) NOT NULL default '0',
  `del_audio_own` tinyint(4) NOT NULL default '0',
  `del_picture_own` tinyint(4) NOT NULL default '0',
  `del_album_own` tinyint(4) NOT NULL default '0',
  `indexer` int(12) NOT NULL auto_increment,
  `edit_comments_group_own` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`indexer`),
  KEY `user_group` (`user_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`user_group`, `edit_comments_video`, `edit_comments_blog`, `edit_comments_audio`, `edit_comments_pictures`, `edit_comments_group`, `edit_comments_profile`, `edit_details_member`, `edit_details_video`, `edit_details_audio`, `edit_details_album`, `edit_details_picture`, `edit_details_blog`, `del_comments_video`, `del_comments_blog`, `del_comments_audio`, `del_comments_pictures`, `del_comments_group`, `del_comments_profile`, `del_blog`, `del_video`, `del_audio`, `del_picture`, `del_album`, `edit_comments_video_own`, `edit_comments_blog_own`, `edit_comments_audio_own`, `edit_comments_pictures_own`, `edit_comments_profile_own`, `edit_details_member_own`, `edit_details_video_own`, `edit_details_audio_own`, `edit_details_album_own`, `edit_details_picture_own`, `edit_details_blog_own`, `del_comments_video_own`, `del_comments_blog_own`, `del_comments_audio_own`, `del_comments_pictures_own`, `del_comments_group_own`, `del_comments_profile_own`, `del_blog_own`, `del_video_own`, `del_audio_own`, `del_picture_own`, `del_album_own`, `indexer`, `edit_comments_group_own`) VALUES ('member', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);
INSERT INTO `permissions` (`user_group`, `edit_comments_video`, `edit_comments_blog`, `edit_comments_audio`, `edit_comments_pictures`, `edit_comments_group`, `edit_comments_profile`, `edit_details_member`, `edit_details_video`, `edit_details_audio`, `edit_details_album`, `edit_details_picture`, `edit_details_blog`, `del_comments_video`, `del_comments_blog`, `del_comments_audio`, `del_comments_pictures`, `del_comments_group`, `del_comments_profile`, `del_blog`, `del_video`, `del_audio`, `del_picture`, `del_album`, `edit_comments_video_own`, `edit_comments_blog_own`, `edit_comments_audio_own`, `edit_comments_pictures_own`, `edit_comments_profile_own`, `edit_details_member_own`, `edit_details_video_own`, `edit_details_audio_own`, `edit_details_album_own`, `edit_details_picture_own`, `edit_details_blog_own`, `del_comments_video_own`, `del_comments_blog_own`, `del_comments_audio_own`, `del_comments_pictures_own`, `del_comments_group_own`, `del_comments_profile_own`, `del_blog_own`, `del_video_own`, `del_audio_own`, `del_picture_own`, `del_album_own`, `indexer`, `edit_comments_group_own`) VALUES ('standard_mod', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 1);
INSERT INTO `permissions` (`user_group`, `edit_comments_video`, `edit_comments_blog`, `edit_comments_audio`, `edit_comments_pictures`, `edit_comments_group`, `edit_comments_profile`, `edit_details_member`, `edit_details_video`, `edit_details_audio`, `edit_details_album`, `edit_details_picture`, `edit_details_blog`, `del_comments_video`, `del_comments_blog`, `del_comments_audio`, `del_comments_pictures`, `del_comments_group`, `del_comments_profile`, `del_blog`, `del_video`, `del_audio`, `del_picture`, `del_album`, `edit_comments_video_own`, `edit_comments_blog_own`, `edit_comments_audio_own`, `edit_comments_pictures_own`, `edit_comments_profile_own`, `edit_details_member_own`, `edit_details_video_own`, `edit_details_audio_own`, `edit_details_album_own`, `edit_details_picture_own`, `edit_details_blog_own`, `del_comments_video_own`, `del_comments_blog_own`, `del_comments_audio_own`, `del_comments_pictures_own`, `del_comments_group_own`, `del_comments_profile_own`, `del_blog_own`, `del_video_own`, `del_audio_own`, `del_picture_own`, `del_album_own`, `indexer`, `edit_comments_group_own`) VALUES ('global_mod', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 3, 1);
INSERT INTO `permissions` (`user_group`, `edit_comments_video`, `edit_comments_blog`, `edit_comments_audio`, `edit_comments_pictures`, `edit_comments_group`, `edit_comments_profile`, `edit_details_member`, `edit_details_video`, `edit_details_audio`, `edit_details_album`, `edit_details_picture`, `edit_details_blog`, `del_comments_video`, `del_comments_blog`, `del_comments_audio`, `del_comments_pictures`, `del_comments_group`, `del_comments_profile`, `del_blog`, `del_video`, `del_audio`, `del_picture`, `del_album`, `edit_comments_video_own`, `edit_comments_blog_own`, `edit_comments_audio_own`, `edit_comments_pictures_own`, `edit_comments_profile_own`, `edit_details_member_own`, `edit_details_video_own`, `edit_details_audio_own`, `edit_details_album_own`, `edit_details_picture_own`, `edit_details_blog_own`, `del_comments_video_own`, `del_comments_blog_own`, `del_comments_audio_own`, `del_comments_pictures_own`, `del_comments_group_own`, `del_comments_profile_own`, `del_blog_own`, `del_video_own`, `del_audio_own`, `del_picture_own`, `del_album_own`, `indexer`, `edit_comments_group_own`) VALUES ('admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

DROP TABLE IF EXISTS `pictures`;
CREATE TABLE IF NOT EXISTS `pictures` (
  `file_name` varchar(48) default NULL,
  `user_id` int(12) default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `todays_date` datetime default NULL,
  `approved` varchar(10) default NULL,
  PRIMARY KEY  (`indexer`),
  KEY `file_name` (`file_name`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pictures`
--


-- --------------------------------------------------------

--
-- Table structure for table `privacy`
--

DROP TABLE IF EXISTS `privacy`;
CREATE TABLE IF NOT EXISTS `privacy` (
  `videocomments` varchar(6) default 'yes',
  `profilecomments` varchar(6) default 'yes',
  `privatemessage` varchar(6) default 'yes',
  `friendsinvite` varchar(6) default 'yes',
  `newsletter` varchar(6) default 'yes',
  `indexer` int(12) NOT NULL auto_increment,
  `user_id` int(12) default NULL,
  `publicfavorites` varchar(6) default 'yes',
  `publicplaylists` varchar(6) default 'yes',
  PRIMARY KEY  (`indexer`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `privacy`
--


-- --------------------------------------------------------

--
-- Table structure for table `profilecomments`
--

DROP TABLE IF EXISTS `profilecomments`;
CREATE TABLE IF NOT EXISTS `profilecomments` (
  `by_id` int(11) default NULL,
  `by_username` varchar(24) default NULL,
  `members_id` int(11) default NULL,
  `comments` text,
  `todays_date` datetime default NULL,
  `flag_counter` int(4) NOT NULL default '0',
  `indexer` int(11) NOT NULL auto_increment,
  `rating_number_votes` int(11) default NULL,
  `rating_total_points` int(11) default NULL,
  `updated_rating` int(11) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`),
  KEY `members_id` (`members_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profilecomments`
--


-- --------------------------------------------------------

--
-- Table structure for table `profilecomments_rating`
--

DROP TABLE IF EXISTS `profilecomments_rating`;
CREATE TABLE IF NOT EXISTS `profilecomments_rating` (
  `user_id` int(11) default NULL,
  `IP` varchar(15) default NULL,
  `comment_id` int(11) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `user_id` (`user_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profilecomments_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `profilecomments_replys`
--

DROP TABLE IF EXISTS `profilecomments_replys`;
CREATE TABLE IF NOT EXISTS `profilecomments_replys` (
  `by_id` int(12) default NULL,
  `by_username` varchar(24) default NULL,
  `profilecomment_id` int(12) default NULL,
  `comment_reply` text,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`),
  KEY `profilecomment_id` (`profilecomment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profilecomments_replys`
--


-- --------------------------------------------------------

--
-- Table structure for table `sub_channels`
--

DROP TABLE IF EXISTS `sub_channels`;
CREATE TABLE IF NOT EXISTS `sub_channels` (
  `has_vids` varchar(3) NOT NULL default 'no',
  `sub_channel_id` int(12) NOT NULL auto_increment,
  `parent_channel_id` int(12) NOT NULL,
  `sub_channel_name` varchar(48) default NULL,
  `sub_channel_name_seo` varchar(48) default NULL,
  `sub_channel_description` text,
  `date_created` datetime default NULL,
  `sub_channel_picture` varchar(32) default 'none.gif',
  PRIMARY KEY  (`sub_channel_id`),
  KEY `parent_channel_id` (`parent_channel_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sub_channels`
--


-- --------------------------------------------------------

--
-- Table structure for table `videocomments`
--

DROP TABLE IF EXISTS `videocomments`;
CREATE TABLE IF NOT EXISTS `videocomments` (
  `by_id` int(12) default NULL,
  `by_username` varchar(36) default NULL,
  `edit_user_id` tinyint(9) NOT NULL default '0',
  `video_id` int(12) default NULL,
  `comments` text,
  `todays_date` datetime default NULL,
  `indexer` int(12) NOT NULL auto_increment,
  `allow_ratings` varchar(3) NOT NULL default 'yes',
  `rating_number_votes` int(11) default NULL,
  `rating_total_points` int(11) default NULL,
  `updated_rating` int(11) NOT NULL default '0',
  `flag_counter` int(4) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `videocomments`
--


-- --------------------------------------------------------

--
-- Table structure for table `videocomments_rating`
--

DROP TABLE IF EXISTS `videocomments_rating`;
CREATE TABLE IF NOT EXISTS `videocomments_rating` (
  `user_id` int(11) default NULL,
  `IP` varchar(15) default NULL,
  `comment_id` int(11) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `user_id` (`user_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `videocomments_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `videocomments_replys`
--

DROP TABLE IF EXISTS `videocomments_replys`;
CREATE TABLE IF NOT EXISTS `videocomments_replys` (
  `by_id` int(12) default NULL,
  `by_username` varchar(36) default NULL,
  `videocomment_id` int(12) default NULL,
  `comment_reply` text,
  `todays_date` datetime default NULL,
  `edit_user_id` tinyint(9) default '0',
  `indexer` int(12) NOT NULL auto_increment,
  `flag_counter` int(4) NOT NULL default '0',
  PRIMARY KEY  (`indexer`),
  KEY `by_id` (`by_id`),
  KEY `videocomment_id` (`videocomment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `videocomments_replys`
--


-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `indexer` int(9) NOT NULL auto_increment,
  `video_id` varchar(25) default NULL,
  `type` varchar(4) NOT NULL default 'flv',
  `response_id` varchar(24) NOT NULL default '0',
  `channel_id` int(9) NOT NULL default '0',
  `sub_channel_id` int(9) NOT NULL default '0',
  `user_id` int(9) default NULL,
  `viewtime` datetime default NULL,
  `title` text,
  `title_seo` text,
  `description` text,
  `tags` text,
  `channel` varchar(24) default NULL,
  `date_recorded` datetime default NULL,
  `date_uploaded` datetime default NULL,
  `location_recorded` varchar(48) NOT NULL default 'Unkown',
  `video_length` varchar(12) default NULL,
  `allow_comments` varchar(3) default NULL,
  `allow_embedding` varchar(3) default NULL,
  `allow_ratings` varchar(3) default NULL,
  `rating_number_votes` int(9) default NULL,
  `rating_total_points` int(9) default NULL,
  `updated_rating` int(9) default NULL,
  `public_private` varchar(24) default NULL,
  `approved` varchar(24) default NULL,
  `number_of_views` int(9) default NULL,
  `featured` varchar(3) default 'no',
  `promoted` varchar(3) NOT NULL default 'no',
  `flag_counter` int(4) NOT NULL default '0',
  `video_type` varchar(25) NOT NULL default 'uploaded',
  `embed_id` varchar(25) default NULL,
  `media_location` varchar(25) NOT NULL default 'localhost',
  `media_quality` varchar(25) NOT NULL default 'standard',
  PRIMARY KEY  (`indexer`),
  KEY `video_id` (`video_id`),
  KEY `response_id` (`response_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `videos`
--


-- --------------------------------------------------------

--
-- Table structure for table `video_playlist`
--

DROP TABLE IF EXISTS `video_playlist`;
CREATE TABLE IF NOT EXISTS `video_playlist` (
  `list_id` int(11) NOT NULL auto_increment,
  `list_name` text NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`list_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `video_playlist`
--


-- --------------------------------------------------------

--
-- Table structure for table `video_playlist_lists`
--

DROP TABLE IF EXISTS `video_playlist_lists`;
CREATE TABLE IF NOT EXISTS `video_playlist_lists` (
  `list_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `video_id` int(11) default NULL,
  `video_file_name` varchar(250) default '',
  `video_position` int(11) default NULL,
  `indexer` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `list_id` (`list_id`),
  KEY `user_id` (`user_id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `video_playlist_lists`
--


-- --------------------------------------------------------

--
-- Table structure for table `video_settings`
--

DROP TABLE IF EXISTS `video_settings`;
CREATE TABLE IF NOT EXISTS `video_settings` (
 `video_watermark` varchar(5) default 'no',
 `video_watermark_place` varchar(20) default 'right_bottom',
 `video_resize` varchar(5) default 'yes',
 `video_convert_pass` varchar(5) default '1',
 `video_ffmpeg_size` varchar(20) default '560x420',
 `video_ffmpeg_bit_rate` varchar(20) default '300k',
 `video_ffmpeg_audio_rate` varchar(10) default '44100',
 `video_ffmpeg_high_quality` varchar(5) default 'no',
 `video_ffmpeg_hq` varchar(25) default '-sameq',
 `video_ffmpeg_hq_size` varchar(12) default '720x480',
 `video_ffmpeg_qmax` varchar(5) default '3',
 `video_mencoder_vbitrate` varchar(5) default '800',
 `video_mencoder_scale` varchar(20) default '560:420',
 `video_mencoder_srate` varchar(20) default '22050',
 `video_mencoder_audio_rate` varchar(5) default '56'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `video_settings`
--

INSERT INTO `video_settings` (`video_watermark`, `video_watermark_place`, `video_resize`, `video_convert_pass`, `video_ffmpeg_size`, `video_ffmpeg_bit_rate`, `video_ffmpeg_audio_rate`, `video_ffmpeg_high_quality`, `video_ffmpeg_hq`, `video_ffmpeg_hq_size`, `video_ffmpeg_qmax`, `video_mencoder_vbitrate`, `video_mencoder_scale`, `video_mencoder_srate`, `video_mencoder_audio_rate`) VALUES ('no', 'right_bottom', 'yes', '1', '560x420', '300k', '44100', 'no', '-sameq', '720x480', '3', '800', '560:420', '22050', '56');

-- --------------------------------------------------------

--
-- Table structure for table `views_tracker`
--

DROP TABLE IF EXISTS `views_tracker`;
CREATE TABLE IF NOT EXISTS `views_tracker` (
  `ipaddress` varchar(25) default NULL,
  `location` varchar(100) default NULL,
  `media_type` varchar(10) default NULL,
  `media_id` int(20) default NULL,
  `date_viewed` date default NULL,
  `indexer` int(20) NOT NULL auto_increment,
  PRIMARY KEY  (`indexer`),
  KEY `media_id` (`media_id`),
  KEY `media_type` (`media_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `views_tracker`
--


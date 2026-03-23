<?php

include_once 'WIConfig.php';

class WITable
{
	public function __construct() 
    {
        $this->WIdb = WIdb::getInstance();

    }

    public function tables()
    {
         $sql = "CREATE TABLE IF NOT EXISTS `wi_forum_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `user_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_forum_categories`
--

INSERT INTO `wi_forum_categories` ( `title`, `user_created`) VALUES
( 'Documentation', 1),
( 'Plugins', 1),
('Usage', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wi_forum_menu`
--

CREATE TABLE IF NOT EXISTS `wi_forum_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#',
  `parent` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `wi_forum_menu`
--

INSERT INTO `wi_forum_menu` ( `label`, `link`, `parent`, `sort`, `lang`) VALUES
( 'Category', 'index.php', 0, 0, 'cat'),
( 'Topic', 'topic', 0, 1, 'topic');

-- --------------------------------------------------------

--
-- Table structure for table `wi_forum_posts`
--

CREATE TABLE IF NOT EXISTS `wi_forum_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `post` text NOT NULL,
  `last_post_date` datetime DEFAULT NULL,
  `user_posted` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_forum_posts`
--

INSERT INTO `wi_forum_posts` ( `category_id`, `section_id`, `title`, `post`, `last_post_date`, `user_posted`) VALUES
( 3, 1, 'how to use', 'how the use this system ', '0000-00-00 00:00:00', 1),
( 3, 2, 'guide on set up', 'this is a guide on how to set things up', '0000-00-00 00:00:00', 1),
( 3, 1, 'Test', '<p>This is a test, so I can do the <em>documentation</em> for my <strong>WICMS</strong></p>', NULL, 1),
( 3, 1, 'Second test', '<p><em>This</em> is to see it working <strong>properly</strong>, now I can finish it off</p>', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wi_forum_roles`
--

CREATE TABLE IF NOT EXISTS `wi_forum_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

--
-- Dumping data for table `wi_forum_roles`
--

INSERT INTO `wi_forum_roles` ( `role`) VALUES
( 'Administrator'),
( 'Moderator');

-- --------------------------------------------------------

--
-- Table structure for table `wi_forum_sections`
--

CREATE TABLE IF NOT EXISTS `wi_forum_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `user_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_forum_sections`
--

INSERT INTO `wi_forum_sections` ( `category_id`, `title`, `desc`, `user_created`) VALUES
( 3, 'How Too', NULL, 1),
( 3, 'Guides', NULL, 1),
( 4, 'usage', NULL, 1),
( 4, 'testing', NULL, 1),
( 5, 'media', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wi_forum_settings`
--

CREATE TABLE IF NOT EXISTS `wi_forum_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `callback` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ";

        $query = $this->WIdb->prepare($sql);
        $query->execute();
    }


}


?>
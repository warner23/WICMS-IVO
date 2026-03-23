<?php
/**
* Install Plugin Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIInstall
{
	function __construct() 
	{
       $this->WIdb = WIdb::getInstance();
       $this->System = new WISystem();
    }

    public function pluginCheck($plug)
    {
      $sql = "SELECT * FROM `wi_plugin` WHERE `plugin`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $plug, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function sidebarCheck($configs, $plug)
    {
      $label = $configs['sidebar_name'];
      $sql = "SELECT * FROM `wi_sidebar` WHERE `label`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $label, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();

      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function menuCheck($configs, $plug)
        {    
          $label = $configs['sidebar_name'];
         $sql = "SELECT * FROM `wi_menu` WHERE `label`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $label, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();

      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function CssCheck($configs, $plug)
    {
      $label = $configs['sidebar_name'];
      $sql = "SELECT * FROM `wi_css` WHERE `page`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $label, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function JsCheck($configs, $plug)
    {
      $label = $configs['sidebar_name'];
      $sql = "SELECT * FROM `wi_scripts` WHERE `page`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $label, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function MetaCheck($configs, $plug)
    {
      $label = $configs['sidebar_name'];
      $sql = "SELECT * FROM `wi_meta` WHERE `page`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $label, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function pageCheck($configs, $plug)
    {
      $label = $configs['sidebar_name'];
      $sql = "SELECT * FROM `wi_page` WHERE `name`=:label";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':label', $label, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }


    public function CatCheck($cat)
    {
      $sql = "SELECT * FROM `wi_categories` WHERE `title`=:title";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':title', $cat, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }

    }

        public function BrandCheck($brand)
    {
      $sql = "SELECT * FROM `wi_brands` WHERE `title`=:title";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':title', $brand, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }

    }

    public function productCheck($product)
    {
      $sql = "SELECT * FROM `wi_products` WHERE `product_selector`=:title";

      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':title', $product, PDO::PARAM_STR);
      $query->execute();

      $result = $query->fetch();
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }

    }

    public function AddPlugin($configs, $plug)
    {

      $pluginCheck = self::pluginCheck($plug);
      //echo "plug". $pluginCheck;
      if ($pluginCheck === "0") {
        
              $activated = "false";
              $Installed = "true";
        //echo "plugin" .$plug;
        // add plugin into db plugin
        $this->WIdb->insert('wi_plugin', array(
            "plugin"     => $plug,
            "activated"  => $activated,
            "Installed"  => $Installed
           
        ));

      }
 

    }

        public function AddtoSideBar($configs, $plug)
    {

      $label = $configs['sidebar_name'];
      $lang  = $configs['lang'];
      $sort  = $configs['sort_no'];
      $img   = $configs['img'];
  $parent_no = $configs['parent_no'];

      //check if sidebar links have been installed or not
      $sidebarCheck = self::sidebarCheck($configs, $plug);
      //echo "side". $sidebarCheck;
      if ($sidebarCheck === "0") {

         //place into sidebar db
        $this->WIdb->insert('wi_sidebar', array(
            "label" => $label,
            "lang"  => $lang,
            "sort"  => $sort,
            "img"   => $img,
            "parent" => $parent_no
           
        )); 
        $sidebarId = $this->WIdb->lastInsertId();
        
      $link  = $configs['link'];

        $this->WIdb->insert('wi_sidebar', array(
            "label"   => $label,
            "parent"  => $sidebarId,
            "link"    => $link,
            "sort"    => $parent_no,
            "lang"    => $lang
           
        )); 


        $link2  = $configs['link2'];
        $lang2  = $configs['lang2'];
        $this->WIdb->insert('wi_sidebar', array(
            "label"   => $label,
            "parent"  => $sidebarId,
            "link"    => $link2,
            "sort"    => "1",
            "lang"    => $lang2
           
        )); 
      }
       
    }

    public function AddToMenu($configs, $plug)
    {
       $menuCheck = self::menuCheck($configs,$plug);
      // echo "menu".$menuCheck;
      if ($menuCheck === "0") {
         //place into menu db
        $lang  = $configs['lang'];
      $label  = $configs['sidebar_name'];

        $this->WIdb->insert('wi_menu', array(
            "label"   => $label,
            "link"    => $plug .'/index.php',
            "lang"    => $lang
           
        )); 
      }
    }

    public function Tables($configs, $plug)
    {
        // install tables
        $sql = "
        CREATE TABLE `wi_brands` (
  `brand_id` int(100) NOT NULL,
  `title` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_brands`
--

INSERT INTO `wi_brands` (`brand_id`, `title`) VALUES
(1, 'Samsung'),
(2, 'Dell'),
(3, 'Hp'),
(4, 'Apple'),
(5, 'LG'),
(6, 'Canon'),
(7, 'Nikon'),
(8, 'Sony'),
(9, 'Acer'),
(10, 'None'),
(11, 'Swift'),
(12, 'Nike'),
(13, 'Lenovo');

-- --------------------------------------------------------

--
-- Table structure for table `wi_cart`
--

CREATE TABLE `wi_cart` (
  `id` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `sessionId` varchar(100) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `p_id` int(11) NOT NULL,
  `ip_addr` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `photo` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` smallint(6) DEFAULT '0',
  `firstName` varchar(50) DEFAULT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `line1` varchar(50) DEFAULT NULL,
  `line2` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `county` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_cart`
--

INSERT INTO `wi_cart` (`id`, `userId`, `sessionId`, `token`, `p_id`, `ip_addr`, `title`, `photo`, `quantity`, `price`, `total_amount`, `status`, `firstName`, `middleName`, `lastName`, `mobile`, `email`, `line1`, `line2`, `city`, `county`, `country`, `createdAt`, `updatedAt`, `content`) VALUES
(18, 1, NULL, NULL, 3, '90.216.99.101', 'NSW Club Legging (Curve) - Black', 'PKF3L_SQ1_0000000004_BLACK_MDf.jpg', 1, 30.00, 30.00, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0000-00-00 00:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wi_cart_item`
--

CREATE TABLE `wi_cart_item` (
  `id` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `cartId` bigint(20) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `discount` float NOT NULL DEFAULT '0',
  `quantity` smallint(6) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wi_categories`
--

CREATE TABLE `wi_categories` (
  `cat_id` int(100) NOT NULL,
  `title` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_categories`
--

INSERT INTO `wi_categories` (`cat_id`, `title`) VALUES
(1, 'Furniture'),
(2, 'Mobile'),
(3, 'Bedding'),
(4, 'Ladies Wear'),
(5, 'Men Wear'),
(6, 'Kids Wear'),
(7, 'Computers'),
(8, 'Laptops');

-- --------------------------------------------------------

--
-- Table structure for table `wi_checkout_steps`
--

CREATE TABLE `wi_checkout_steps` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `step_number` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `classification` enum('active','inactive') NOT NULL,
  `sort` varchar(255) NOT NULL,
  `show` enum('show','hide') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_checkout_steps`
--

INSERT INTO `wi_checkout_steps` (`id`, `name`, `step_number`, `class`, `identifier`, `classification`, `sort`, `show`) VALUES
(1, 'shipping', 'stepOne', 'fa fa-user', 'step_one', 'active', '0', 'show'),
(2, 'payment', 'stepTwo', 'fa fa-list', 'step_two', 'inactive', '1', 'hide'),
(3, 'confirmation', 'stepThree', 'fa fa-gears', 'step_three', 'inactive', '2', 'hide');

-- --------------------------------------------------------

--
-- Table structure for table `wi_countries`
--

CREATE TABLE `wi_countries` (
  `id` int(11) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `country_name` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_countries`
--

INSERT INTO `wi_countries` (`id`, `country_code`, `country_name`) VALUES
(1, 'AF', 'Afghanistan'),
(2, 'AL', 'Albania'),
(3, 'DZ', 'Algeria'),
(4, 'DS', 'American Samoa'),
(5, 'AD', 'Andorra'),
(6, 'AO', 'Angola'),
(7, 'AI', 'Anguilla'),
(8, 'AQ', 'Antarctica'),
(9, 'AG', 'Antigua and Barbuda'),
(10, 'AR', 'Argentina'),
(11, 'AM', 'Armenia'),
(12, 'AW', 'Aruba'),
(13, 'AU', 'Australia'),
(14, 'AT', 'Austria'),
(15, 'AZ', 'Azerbaijan'),
(16, 'BS', 'Bahamas'),
(17, 'BH', 'Bahrain'),
(18, 'BD', 'Bangladesh'),
(19, 'BB', 'Barbados'),
(20, 'BY', 'Belarus'),
(21, 'BE', 'Belgium'),
(22, 'BZ', 'Belize'),
(23, 'BJ', 'Benin'),
(24, 'BM', 'Bermuda'),
(25, 'BT', 'Bhutan'),
(26, 'BO', 'Bolivia'),
(27, 'BA', 'Bosnia and Herzegovina'),
(28, 'BW', 'Botswana'),
(29, 'BV', 'Bouvet Island'),
(30, 'BR', 'Brazil'),
(31, 'IO', 'British Indian Ocean Territory'),
(32, 'BN', 'Brunei Darussalam'),
(33, 'BG', 'Bulgaria'),
(34, 'BF', 'Burkina Faso'),
(35, 'BI', 'Burundi'),
(36, 'KH', 'Cambodia'),
(37, 'CM', 'Cameroon'),
(38, 'CA', 'Canada'),
(39, 'CV', 'Cape Verde'),
(40, 'KY', 'Cayman Islands'),
(41, 'CF', 'Central African Republic'),
(42, 'TD', 'Chad'),
(43, 'CL', 'Chile'),
(44, 'CN', 'China'),
(45, 'CX', 'Christmas Island'),
(46, 'CC', 'Cocos (Keeling) Islands'),
(47, 'CO', 'Colombia'),
(48, 'KM', 'Comoros'),
(49, 'CG', 'Congo'),
(50, 'CK', 'Cook Islands'),
(51, 'CR', 'Costa Rica'),
(52, 'HR', 'Croatia (Hrvatska)'),
(53, 'CU', 'Cuba'),
(54, 'CY', 'Cyprus'),
(55, 'CZ', 'Czech Republic'),
(56, 'DK', 'Denmark'),
(57, 'DJ', 'Djibouti'),
(58, 'DM', 'Dominica'),
(59, 'DO', 'Dominican Republic'),
(60, 'TP', 'East Timor'),
(61, 'EC', 'Ecuador'),
(62, 'EG', 'Egypt'),
(63, 'SV', 'El Salvador'),
(64, 'GQ', 'Equatorial Guinea'),
(65, 'ER', 'Eritrea'),
(66, 'EE', 'Estonia'),
(67, 'ET', 'Ethiopia'),
(68, 'FK', 'Falkland Islands (Malvinas)'),
(69, 'FO', 'Faroe Islands'),
(70, 'FJ', 'Fiji'),
(71, 'FI', 'Finland'),
(72, 'FR', 'France'),
(73, 'FX', 'France, Metropolitan'),
(74, 'GF', 'French Guiana'),
(75, 'PF', 'French Polynesia'),
(76, 'TF', 'French Southern Territories'),
(77, 'GA', 'Gabon'),
(78, 'GM', 'Gambia'),
(79, 'GE', 'Georgia'),
(80, 'DE', 'Germany'),
(81, 'GH', 'Ghana'),
(82, 'GI', 'Gibraltar'),
(83, 'GK', 'Guernsey'),
(84, 'GR', 'Greece'),
(85, 'GL', 'Greenland'),
(86, 'GD', 'Grenada'),
(87, 'GP', 'Guadeloupe'),
(88, 'GU', 'Guam'),
(89, 'GT', 'Guatemala'),
(90, 'GN', 'Guinea'),
(91, 'GW', 'Guinea-Bissau'),
(92, 'GY', 'Guyana'),
(93, 'HT', 'Haiti'),
(94, 'HM', 'Heard and Mc Donald Islands'),
(95, 'HN', 'Honduras'),
(96, 'HK', 'Hong Kong'),
(97, 'HU', 'Hungary'),
(98, 'IS', 'Iceland'),
(99, 'IN', 'India'),
(100, 'IM', 'Isle of Man'),
(101, 'ID', 'Indonesia'),
(102, 'IR', 'Iran (Islamic Republic of)'),
(103, 'IQ', 'Iraq'),
(104, 'IE', 'Ireland'),
(105, 'IL', 'Israel'),
(106, 'IT', 'Italy'),
(107, 'CI', 'Ivory Coast'),
(108, 'JE', 'Jersey'),
(109, 'JM', 'Jamaica'),
(110, 'JP', 'Japan'),
(111, 'JO', 'Jordan'),
(112, 'KZ', 'Kazakhstan'),
(113, 'KE', 'Kenya'),
(114, 'KI', 'Kiribati'),
(115, 'KP', 'Korea, Democratic People\'s Republic of'),
(116, 'KR', 'Korea, Republic of'),
(117, 'XK', 'Kosovo'),
(118, 'KW', 'Kuwait'),
(119, 'KG', 'Kyrgyzstan'),
(120, 'LA', 'Lao People\'s Democratic Republic'),
(121, 'LV', 'Latvia'),
(122, 'LB', 'Lebanon'),
(123, 'LS', 'Lesotho'),
(124, 'LR', 'Liberia'),
(125, 'LY', 'Libyan Arab Jamahiriya'),
(126, 'LI', 'Liechtenstein'),
(127, 'LT', 'Lithuania'),
(128, 'LU', 'Luxembourg'),
(129, 'MO', 'Macau'),
(130, 'MK', 'Macedonia'),
(131, 'MG', 'Madagascar'),
(132, 'MW', 'Malawi'),
(133, 'MY', 'Malaysia'),
(134, 'MV', 'Maldives'),
(135, 'ML', 'Mali'),
(136, 'MT', 'Malta'),
(137, 'MH', 'Marshall Islands'),
(138, 'MQ', 'Martinique'),
(139, 'MR', 'Mauritania'),
(140, 'MU', 'Mauritius'),
(141, 'TY', 'Mayotte'),
(142, 'MX', 'Mexico'),
(143, 'FM', 'Micronesia, Federated States of'),
(144, 'MD', 'Moldova, Republic of'),
(145, 'MC', 'Monaco'),
(146, 'MN', 'Mongolia'),
(147, 'ME', 'Montenegro'),
(148, 'MS', 'Montserrat'),
(149, 'MA', 'Morocco'),
(150, 'MZ', 'Mozambique'),
(151, 'MM', 'Myanmar'),
(152, 'NA', 'Namibia'),
(153, 'NR', 'Nauru'),
(154, 'NP', 'Nepal'),
(155, 'NL', 'Netherlands'),
(156, 'AN', 'Netherlands Antilles'),
(157, 'NC', 'New Caledonia'),
(158, 'NZ', 'New Zealand'),
(159, 'NI', 'Nicaragua'),
(160, 'NE', 'Niger'),
(161, 'NG', 'Nigeria'),
(162, 'NU', 'Niue'),
(163, 'NF', 'Norfolk Island'),
(164, 'MP', 'Northern Mariana Islands'),
(165, 'NO', 'Norway'),
(166, 'OM', 'Oman'),
(167, 'PK', 'Pakistan'),
(168, 'PW', 'Palau'),
(169, 'PS', 'Palestine'),
(170, 'PA', 'Panama'),
(171, 'PG', 'Papua New Guinea'),
(172, 'PY', 'Paraguay'),
(173, 'PE', 'Peru'),
(174, 'PH', 'Philippines'),
(175, 'PN', 'Pitcairn'),
(176, 'PL', 'Poland'),
(177, 'PT', 'Portugal'),
(178, 'PR', 'Puerto Rico'),
(179, 'QA', 'Qatar'),
(180, 'RE', 'Reunion'),
(181, 'RO', 'Romania'),
(182, 'RU', 'Russian Federation'),
(183, 'RW', 'Rwanda'),
(184, 'KN', 'Saint Kitts and Nevis'),
(185, 'LC', 'Saint Lucia'),
(186, 'VC', 'Saint Vincent and the Grenadines'),
(187, 'WS', 'Samoa'),
(188, 'SM', 'San Marino'),
(189, 'ST', 'Sao Tome and Principe'),
(190, 'SA', 'Saudi Arabia'),
(191, 'SN', 'Senegal'),
(192, 'RS', 'Serbia'),
(193, 'SC', 'Seychelles'),
(194, 'SL', 'Sierra Leone'),
(195, 'SG', 'Singapore'),
(196, 'SK', 'Slovakia'),
(197, 'SI', 'Slovenia'),
(198, 'SB', 'Solomon Islands'),
(199, 'SO', 'Somalia'),
(200, 'ZA', 'South Africa'),
(201, 'GS', 'South Georgia South Sandwich Islands'),
(202, 'ES', 'Spain'),
(203, 'LK', 'Sri Lanka'),
(204, 'SH', 'St. Helena'),
(205, 'PM', 'St. Pierre and Miquelon'),
(206, 'SD', 'Sudan'),
(207, 'SR', 'Suriname'),
(208, 'SJ', 'Svalbard and Jan Mayen Islands'),
(209, 'SZ', 'Swaziland'),
(210, 'SE', 'Sweden'),
(211, 'CH', 'Switzerland'),
(212, 'SY', 'Syrian Arab Republic'),
(213, 'TW', 'Taiwan'),
(214, 'TJ', 'Tajikistan'),
(215, 'TZ', 'Tanzania, United Republic of'),
(216, 'TH', 'Thailand'),
(217, 'TG', 'Togo'),
(218, 'TK', 'Tokelau'),
(219, 'TO', 'Tonga'),
(220, 'TT', 'Trinidad and Tobago'),
(221, 'TN', 'Tunisia'),
(222, 'TR', 'Turkey'),
(223, 'TM', 'Turkmenistan'),
(224, 'TC', 'Turks and Caicos Islands'),
(225, 'TV', 'Tuvalu'),
(226, 'UG', 'Uganda'),
(227, 'UA', 'Ukraine'),
(228, 'AE', 'United Arab Emirates'),
(229, 'GB', 'United Kingdom'),
(230, 'US', 'United States'),
(231, 'UM', 'United States minor outlying islands'),
(232, 'UY', 'Uruguay'),
(233, 'UZ', 'Uzbekistan'),
(234, 'VU', 'Vanuatu'),
(235, 'VA', 'Vatican City State'),
(236, 'VE', 'Venezuela'),
(237, 'VN', 'Vietnam'),
(238, 'VG', 'Virgin Islands (British)'),
(239, 'VI', 'Virgin Islands (U.S.)'),
(240, 'WF', 'Wallis and Futuna Islands'),
(241, 'EH', 'Western Sahara'),
(242, 'YE', 'Yemen'),
(243, 'ZR', 'Zaire'),
(244, 'ZM', 'Zambia'),
(245, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `wi_cust_address`
--

CREATE TABLE `wi_cust_address` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postcode` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `address_ref` varchar(255) NOT NULL,
  `main_addy` enum('false','true') NOT NULL DEFAULT 'false',
  `phone` varchar(255) NOT NULL,
  `Shipping_costs` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_cust_address`
--

INSERT INTO `wi_cust_address` (`id`, `user_id`, `fname`, `lname`, `address`, `city`, `postcode`, `country`, `address_ref`, `main_addy`, `phone`, `Shipping_costs`) VALUES
(1, 1, 'Julian', 'Warner', 'Flat 8, 67A Market street', 'Wirral', 'CH41 5BS', 'GB', 'Home', 'true', '', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `wi_order`
--

CREATE TABLE `wi_order` (
  `id` bigint(20) NOT NULL,
  `userId` bigint(20) DEFAULT NULL,
  `sessionId` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0',
  `subTotal` float NOT NULL DEFAULT '0',
  `itemDiscount` float NOT NULL DEFAULT '0',
  `tax` float NOT NULL DEFAULT '0',
  `shipping` float NOT NULL DEFAULT '0',
  `total` float NOT NULL DEFAULT '0',
  `promo` varchar(50) DEFAULT NULL,
  `discount` float NOT NULL DEFAULT '0',
  `grandTotal` float NOT NULL DEFAULT '0',
  `firstName` varchar(50) DEFAULT NULL,
  `middleName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `line1` varchar(50) DEFAULT NULL,
  `line2` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_order`
--

INSERT INTO `wi_order` (`id`, `userId`, `sessionId`, `token`, `status`, `subTotal`, `itemDiscount`, `tax`, `shipping`, `total`, `promo`, `discount`, `grandTotal`, `firstName`, `middleName`, `lastName`, `mobile`, `email`, `line1`, `line2`, `city`, `province`, `country`, `createdAt`, `updatedAt`, `content`) VALUES
(1, 1, '18587', '', 0, 2210, 0, 0, 0, 2210, NULL, 0, 2210, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(2, 1, '17615', '', 0, 2210, 0, 0, 0, 2210, NULL, 0, 2210, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(3, 1, '18908', '', 0, 2210, 0, 0, 0, 2210, NULL, 0, 2210, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(4, 1, '17977', '', 0, 1745, 0, 0, 0, 1745, NULL, 0, 1745, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(5, 1, '15338', '', 0, 1745, 0, 0, 0, 1745, NULL, 0, 1745, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(6, 1, '18316', '', 0, 1745, 0, 0, 0, 1745, NULL, 0, 1745, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(7, 1, '11452', '', 0, 1745, 0, 0, 0, 1745, NULL, 0, 1745, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(8, 1, '11027', '', 0, 1751, 0, 0, 0, 1751, NULL, 0, 1751, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(9, 1, '18024', '', 0, 1751, 0, 0, 0, 1751, NULL, 0, 1751, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(10, 1, '11639', '', 0, 1751, 0, 0, 0, 1751, NULL, 0, 1751, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(11, 1, '15383', '', 0, 1751, 0, 0, 0, 1751, NULL, 0, 1751, NULL, NULL, NULL, NULL, NULL, 'Flat 8, 67A Market street', NULL, 'Wirral', NULL, NULL, '0000-00-00 00:00:00', NULL, NULL),
(12, 1, '', '', 0, 0, 0, 0, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, '', '', '', '', '', NULL, '0000-00-00 00:00:00', NULL, NULL),
(13, 1, '', '', 0, 0, 0, 0, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, '', '', '', '', '', NULL, '0000-00-00 00:00:00', NULL, NULL),
(14, 1, '', '', 0, 0, 0, 0, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, '', '', '', '', '', NULL, '0000-00-00 00:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wi_order_item`
--

CREATE TABLE `wi_order_item` (
  `id` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `orderId` bigint(20) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `price` float NOT NULL DEFAULT '0',
  `discount` float NOT NULL DEFAULT '0',
  `quantity` smallint(6) NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wi_product`
--

CREATE TABLE `wi_product` (
  `id` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `title` varchar(75) NOT NULL,
  `metaTitle` varchar(100) DEFAULT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `summary` tinytext,
  `type` smallint(6) DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` float NOT NULL DEFAULT '0',
  `discount` float DEFAULT '0',
  `Shipping` decimal(10,0) NOT NULL,
  `quantity` smallint(6) DEFAULT '0',
  `shop` tinyint(1) DEFAULT '0',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `publishedAt` datetime DEFAULT NULL,
  `startsAt` datetime DEFAULT NULL,
  `endsAt` datetime DEFAULT NULL,
  `content` text,
  `photo` text NOT NULL,
  `insurance` decimal(10,2) NOT NULL,
  `VAT` decimal(10,2) NOT NULL,
  `shipping_discount` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_product`
--

INSERT INTO `wi_product` (`id`, `userId`, `title`, `metaTitle`, `slug`, `summary`, `type`, `category_id`, `brand_id`, `sku`, `price`, `discount`, `Shipping`, `quantity`, `shop`, `createdAt`, `updatedAt`, `publishedAt`, `startsAt`, `endsAt`, `content`, `photo`, `insurance`, `VAT`, `shipping_discount`) VALUES
(1, 1, 'SWIFT Diego Ready Assembled Chest of 4 Drawers', 'SWIFT Diego Ready Assembled Chest of 4 Drawers', NULL, 'Stylish and striking, the Diego collection of bedroom furniture from SWIFT is all about the stunning', 0, 1, 11, '', 300, 0, 40, 1, 0, '2020-06-23 12:59:14', NULL, NULL, NULL, NULL, NULL, 'Q9K33_SQ1_0000000004_BLACK_SLf.jpg', 0.00, 0.00, 0.00),
(2, 1, 'Galaxy S20+ 5G 128Gb - Grey', 'Galaxy S20+ 5G 128Gb - Grey', NULL, 'The Samsung Galaxy S20+ 5G features a beautiful display and incredible cameras.\nBuy this handset be ', 0, 2, 1, '', 500, 0, 10, 1, 0, '2020-06-23 16:16:37', NULL, NULL, NULL, NULL, NULL, 'Q6MUG_SQ1_0000000005_GREY_SLf.jpg', 0.00, 0.00, 0.00),
(3, 1, 'NSW Club Legging (Curve) - Black', 'NSW Club Legging (Curve) - Black', NULL, 'NSW Club Legging (Curve) - Black', 0, 4, 12, 'sku', 30, 0, 5, 0, 0, '2020-06-23 16:21:38', NULL, NULL, NULL, NULL, NULL, 'PKF3L_SQ1_0000000004_BLACK_MDf.jpg', 0.00, 0.00, 0.00),
(4, 1, 'React Element 55 - Black', 'React Element 55 - Black', NULL, 'Size & Fit\nStandard fit\nAvailable in sizes 6-12\nDetails\nEnd use: Training\nMen’s React Element 5     ', 0, 5, 12, '', 145, 0, 10, 0, 0, '2020-06-23 16:31:14', NULL, NULL, NULL, NULL, NULL, 'PKM6T_SQ1_0000000019_BLACK_WHITE_SLf.jpg', 0.00, 0.00, 0.00),
(5, 1, ' Max 90 Crib Shoe', ' Max 90 Crib Shoe', NULL, '                        ', 0, 6, 12, '', 50, 0, 10, 1, 0, '2020-06-23 16:33:46', NULL, NULL, NULL, NULL, NULL, 'PKCWJ_SQ1_0000003854_WHITE_GREY_PINK_SLf.jpg', 0.00, 0.00, 0.00),
(6, 1, 'Lenovo Ideacentre A340-24IWL', 'Lenovo Ideacentre A340-24IWL', NULL, 'Lenovo Ideacentre A340-24IWL Intel Core i5 10210U 8GB RAM 1TB Hard Drive & 128GB SSD 23.8in Full HD ', 0, 7, 13, '0', 1120, 0, 0, 1, 0, '2020-06-23 16:36:39', NULL, NULL, NULL, NULL, NULL, 'QDWCG_SQ1_0000000088_NO_COLOR_SLf.jpg', 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `wi_product_meta`
--

CREATE TABLE `wi_product_meta` (
  `id` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `key` varchar(50) NOT NULL,
  `content` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wi_product_review`
--

CREATE TABLE `wi_product_review` (
  `id` bigint(20) NOT NULL,
  `productId` bigint(20) NOT NULL,
  `parentId` bigint(20) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `rating` smallint(6) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL,
  `publishedAt` datetime DEFAULT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_product_review`
--

INSERT INTO `wi_product_review` (`id`, `productId`, `parentId`, `title`, `rating`, `published`, `createdAt`, `publishedAt`, `content`) VALUES
(1, 2, NULL, '', 2, 0, '0000-00-00 00:00:00', NULL, 'This si a new review'),
(2, 2, NULL, '', 2, 0, '0000-00-00 00:00:00', NULL, 'This is a new rating and review'),
(3, 2, 1, '', 4, 0, '0000-00-00 00:00:00', NULL, 'This is a reviewing of this product');

-- --------------------------------------------------------

--
-- Table structure for table `wi_recommended`
--

CREATE TABLE `wi_recommended` (
  `id` int(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(3,0) NOT NULL,
  `img` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wi_shipping`
--

CREATE TABLE `wi_shipping` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_shipping`
--

INSERT INTO `wi_shipping` (`id`, `name`, `cost`) VALUES
(3, 'Royal Mail', 2.00),
(4, 'parcelforce', 8.00),
(5, 'Yodel', 4.00),
(6, 'Hermes', 6.00);

-- --------------------------------------------------------

--
-- Table structure for table `wi_shop_settings`
--

CREATE TABLE `wi_shop_settings` (
  `id` int(11) NOT NULL,
  `shop_name` varchar(255) NOT NULL,
  `business_email` varchar(255) NOT NULL,
  `paypal_id` varchar(255) NOT NULL,
  `paypal_secret` varchar(255) NOT NULL,
  `paypal_callback` varchar(255) NOT NULL,
  `cancel_url` varchar(255) NOT NULL,
  `notify_url` varchar(255) NOT NULL,
  `VAT` enum('0','1') NOT NULL DEFAULT '0',
  `base_url` varchar(255) NOT NULL,
  `paypal_pro` enum('0','1') NOT NULL DEFAULT '0',
  `currency` varchar(255) NOT NULL,
  `currency_symbol` varchar(255) NOT NULL,
  `current_url` varchar(255) NOT NULL,
  `paypal_environment` enum('sandbox','production') NOT NULL DEFAULT 'sandbox',
  `paypal_base_url` varchar(255) NOT NULL,
  `paypal_pro_base_url` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_shop_settings`
--

INSERT INTO `wi_shop_settings` (`id`, `shop_name`, `business_email`, `paypal_id`, `paypal_secret`, `paypal_callback`, `cancel_url`, `notify_url`, `VAT`, `base_url`, `paypal_pro`, `currency`, `currency_symbol`, `current_url`, `paypal_environment`, `paypal_base_url`, `paypal_pro_base_url`) VALUES
(1, 'ShowCase Shop', 'warner@wicms.uk', 'AcucNdI-n23vB-Uto57P2fgu2e8DdYc-HSeDNjzMbR13dtJoJO_bwsd3sB_8dnCHX5aY39FluqGu7A6q', 'EI7FeKdrovdN1by4eOCCa5wRFyWLjuygUNO-SeDkWUONBlrTqE2fAVhuPS2TQsjpaPmzRWZV9kNnuemN', 'https://warner-infinity.com/WIShop/success', 'https://warner-infinity.com/WIShop/cancel.php', 'https://warner-infinity.com/WIShop/notify.php', '0', 'https://warner-infinity.com/WIShop/', '0', 'GBP', '£', 'https://warner-infinity.com/WIShop/checkout.php', 'sandbox', 'https://api.sandbox.paypal.com/v2/', 'https://api.paypal.com/v2/');

-- --------------------------------------------------------

--
-- Table structure for table `wi_transaction`
--

CREATE TABLE `wi_transaction` (
  `id` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `orderId` bigint(20) NOT NULL,
  `code` varchar(100) NOT NULL,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `mode` smallint(6) NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `createdAt` datetime NOT NULL,
  `updatedAt` datetime DEFAULT NULL,
  `content` text
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wi_transaction`
--

INSERT INTO `wi_transaction` (`id`, `userId`, `orderId`, `code`, `type`, `mode`, `status`, `createdAt`, `updatedAt`, `content`) VALUES
(1, 1, 52, '18587', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(2, 1, 2, '17615', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(3, 1, 21, '18908', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(4, 1, 7, '17977', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(5, 1, 59, '15338', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(6, 1, 82, '18316', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(7, 1, 76501358, '11452', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(8, 1, 9, '11027', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(9, 1, 96953077, '18024', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(10, 1, 5, '11639', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(11, 1, 7, '15383', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(12, 1, 0, '', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(13, 1, 0, '', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL),
(14, 1, 0, '', 0, 0, 0, '0000-00-00 00:00:00', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wi_brands`
--
ALTER TABLE `wi_brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `wi_cart`
--
ALTER TABLE `wi_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cart_user` (`userId`);

--
-- Indexes for table `wi_cart_item`
--
ALTER TABLE `wi_cart_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_cart_item_product` (`productId`),
  ADD KEY `idx_cart_item_cart` (`cartId`);

--
-- Indexes for table `wi_categories`
--
ALTER TABLE `wi_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `wi_checkout_steps`
--
ALTER TABLE `wi_checkout_steps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_countries`
--
ALTER TABLE `wi_countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_cust_address`
--
ALTER TABLE `wi_cust_address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_order`
--
ALTER TABLE `wi_order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_user` (`userId`);

--
-- Indexes for table `wi_order_item`
--
ALTER TABLE `wi_order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_item_product` (`productId`);

--
-- Indexes for table `wi_product`
--
ALTER TABLE `wi_product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slug` (`slug`),
  ADD KEY `idx_product_user` (`userId`);

--
-- Indexes for table `wi_product_meta`
--
ALTER TABLE `wi_product_meta`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_product_meta` (`productId`,`key`),
  ADD KEY `idx_meta_product` (`productId`);

--
-- Indexes for table `wi_product_review`
--
ALTER TABLE `wi_product_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_review_product` (`productId`),
  ADD KEY `idx_review_parent` (`parentId`);

--
-- Indexes for table `wi_recommended`
--
ALTER TABLE `wi_recommended`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_shipping`
--
ALTER TABLE `wi_shipping`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_shop_settings`
--
ALTER TABLE `wi_shop_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_transaction`
--
ALTER TABLE `wi_transaction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wi_brands`
--
ALTER TABLE `wi_brands`
  MODIFY `brand_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wi_cart`
--
ALTER TABLE `wi_cart`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `wi_cart_item`
--
ALTER TABLE `wi_cart_item`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_categories`
--
ALTER TABLE `wi_categories`
  MODIFY `cat_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wi_checkout_steps`
--
ALTER TABLE `wi_checkout_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wi_countries`
--
ALTER TABLE `wi_countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=246;

--
-- AUTO_INCREMENT for table `wi_cust_address`
--
ALTER TABLE `wi_cust_address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wi_order`
--
ALTER TABLE `wi_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `wi_order_item`
--
ALTER TABLE `wi_order_item`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_product`
--
ALTER TABLE `wi_product`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wi_product_meta`
--
ALTER TABLE `wi_product_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_product_review`
--
ALTER TABLE `wi_product_review`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wi_recommended`
--
ALTER TABLE `wi_recommended`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_shipping`
--
ALTER TABLE `wi_shipping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wi_shop_settings`
--
ALTER TABLE `wi_shop_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_transaction`
--
ALTER TABLE `wi_transaction`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

";

        $query = $this->WIdb->prepare($sql);
        $query->execute();
    }


    public function InstallCats($configs, $plug, $cats)
    {
      foreach ($cats as $cat) {
      $Category = self::CatCheck($cat);

      if ($Category === "0") {
        // add plugin into db plugin
        $this->WIdb->insert('wi_categories', array(
            "title"  => $cat
           
        ));

      }
      }
            
    }

        public function InstallBrands($configs, $plug, $brands)
    {
      foreach ($brands as $brand) {
      $branding = self::brandCheck($brand);

      if ($branding === "0") {
        // add plugin into db plugin
        $this->WIdb->insert('wi_brands', array(
            "title"  => $brand
           
        ));

      }
      }
            
    }

    public function InstallProducts($configs, $plug, $products, $shopData)
    {
            foreach ($products as $product) {
      $producting = self::productCheck($product);

      if ($producting === "0") {
       // $productData =array_combine($products, $shopData);
        //echo $product;
        //var_dump($shopData[$product]);

        $this->WIdb->insert('wi_products',$shopData[$product]);

      }
      }
            
    }

    public function StartUpDb($configs, $plug)
    {
      // add meta
      // add css
      //add scripts
      //add page
      $cssCheck = self::CssCheck($configs, $plug);
      $JsCheck = self::JsCheck($configs, $plug);
      $MetaCheck = self::MetaCheck($configs, $plug);
      $pageCheck = self::pageCheck($configs, $plug);
      if ($cssCheck === "0") {
        
        $css = "INSERT INTO `wi_css` ( `href`, `rel`, `page`) VALUES
      ( 'site/css/frameworks/bootstrap.css', 'stylesheet', 'shop'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', 'shop'),
      ( 'shop/css/frameworks/menus.css', 'stylesheet', 'shop'),
      ( 'shop/css/style.css', 'stylesheet', 'shop'),
      ( 'site/css/font-awesome.css', 'stylesheet', 'shop'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'shop'),
      ( 'shop/css/style.css', 'stylesheet', 'shop'),
      ( 'shop/css/layout/wide.css', 'stylesheet', 'shop'),
      ( 'shop/css/switcher.css', 'stylesheet', 'shop'),
      ( 'site/css/frameworks/bootstrap.css', 'stylesheet', 'product'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', 'product'),
      ( 'shop/css/frameworks/menus.css', 'stylesheet', 'product'),
      ( 'shop/css/style.css', 'stylesheet', 'product'),
      ( 'site/css/font-awesome.css', 'stylesheet', 'product'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'product'),
      ( 'shop/css/style.css', 'stylesheet', 'product'),
      ( 'shop/css/layout/wide.css', 'stylesheet', 'product'),
      ( 'shop/css/switcher.css', 'stylesheet', 'product'),
      ( 'site/css/frameworks/bootstrap.css', 'stylesheet', 'cart'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', 'cart'),
      ( 'shop/css/frameworks/menus.css', 'stylesheet', 'cart'),
      ( 'shop/css/style.css', 'stylesheet', 'cart'),
      ( 'site/css/font-awesome.css', 'stylesheet', 'cart'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'cart'),
      ( 'shop/css/style.css', 'stylesheet', 'cart'),
      ( 'shop/css/layout/wide.css', 'stylesheet', 'cart'),
      ( 'shop/css/switcher.css', 'stylesheet', 'cart'),
      ( 'site/css/frameworks/bootstrap.css', 'stylesheet', 'checkout'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', 'checkout'),
      ( 'shop/css/frameworks/menus.css', 'stylesheet', 'checkout'),
      ( 'shop/css/style.css', 'stylesheet', 'checkout'),
      ( 'site/css/font-awesome.css', 'stylesheet', 'checkout'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'checkout'),
      ( 'shop/css/style.css', 'stylesheet', 'checkout'),
      ( 'shop/css/layout/wide.css', 'stylesheet', 'checkout'),
      ( 'shop/css/switcher.css', 'stylesheet', 'checkout');";

      $query = $this->WIdb->prepare($css);
        $query->execute();

      }

      if ($JsCheck === "0") {
        $js = "INSERT INTO `wi_scripts` ( `src`, `page`) VALUES
        ( 'site/js/frameworks/JQuery.js', 'shop'),
      ( 'site/js/frameworks/bootstrap.js', 'shop'),
      ( 'site/js/login_panel/js/slide.js', 'shop'),
      ( 'site/js/frameworks/JQuery.js', 'product'),
      ( 'site/js/frameworks/bootstrap.js', 'product'),
      ( 'site/js/login_panel/js/slide.js', 'product'),
      ( 'site/js/frameworks/JQuery.js', 'cart'),
      ( 'site/js/frameworks/bootstrap.js', 'cart'),
      ( 'site/js/login_panel/js/slide.js', 'cart'),
      ( 'site/js/frameworks/JQuery.js', 'checkout'),
      ( 'site/js/frameworks/bootstrap.js', 'checkout'),
      ( 'site/js/login_panel/js/slide.js', 'checkout')
      ";

      $query = $this->WIdb->prepare($js);
        $query->execute();
      }

      if ($MetaCheck === "0") {
        $Meta = "INSERT INTO `wi_meta` ( `page`, `name`, `content`, `author`) VALUES
      ( 'shop', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( 'shop', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
      ( 'shop', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( 'shop', 'author', 'warner-infinity', 'Jules Warner'),
      ( 'product', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( 'product', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
      ( 'product', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( 'product', 'author', 'warner-infinity', 'Jules Warner'),
      ( 'cart', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( 'cart', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
      ( 'cart', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( 'cart', 'author', 'warner-infinity', 'Jules Warner'),
      ( 'checkout', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( 'checkout', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
      ( 'checkout', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( 'checkout', 'author', 'warner-infinity', 'Jules Warner')
      ";
      
      $query = $this->WIdb->prepare($Meta);
        $query->execute();
      }

      if ($pageCheck === "0") {
        $page = "INSERT INTO `wi_page` ( `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`) VALUES
      ( 'shop', '1', '1', '0', '0', '0', 'shop', '1'),
      ( 'product', '1', '1', '0', '0', '0', 'product', '1'),
      ( 'cart', '1', '1', '0', '0', '0', 'cart', '1'),
      ( 'checkout', '1', '1', '0', '0', '0', 'checkout', '1');
        ";
        $query = $this->WIdb->prepare($page);
        $query->execute();
      }
      
    }

   

    public function TransferFiles($configs, $plug)
    {
            // shop dir

            $source = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/' . $plug.'/' . $plug;
            $dest = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/' . $plug;
            $check = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/'. $plug .'/';

            if(!file_exists($check)){
                $this->System->full_copy($source , $dest);
                $fil = glob($dest . "/*");
               print_r($fil);
            }

            //modules dir
            $source1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/Install/Module/';
            $dest1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIModule/';
            $check1 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIModule/'. $configs['lang'];

            if(!file_exists($check1)){
              $this->System->full_copy($source1 , $dest1);
             // $fil = glob($dest1 . "/*");
             //print_r($fil);
            }
            
           // $fil = glob($dest . "/*");
             //  print_r($fil);

            //shop back end shop page WIShop

            $source2 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/shop/' . $plug . '.php';
            $dest2 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/' . $plug . '.php';
            $check2 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/' . $plug . '.php';

            if(!file_exists($check2)){
              $this->System->file_copy($source2 , $dest2);
            }

            //shop back end shop options page

            $source3 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/shop/' . $plug . '_Options.php';
            $dest3 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/' . $plug . '_Options.php';
            $check3 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/' . $plug . '_Options.php';

            if(!file_exists($check3)){
              $this->System->file_copy($source3 , $dest3);
            }


            //shop back end shop include files 
            
            $source4 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/shop/WIInc/' . $plug;
            $dest4 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIInc/site/' . $plug;
            $check4 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIInc/site/' . $plug;

            if(!file_exists($check4)){
              $this->System->full_copy($source4 , $dest4);
                //$fil = glob($dest4 . "/*");
               //print_r($fil);
            }

            //shop back end shop inc page  shop

            $source5 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/shop/WIInc/' .$configs['lang'] . '.php';
            $dest5 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIInc/';
            $check5 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIInc/' .$configs['lang'] . '.php';

            if(!file_exists($check5)){
              $this->System->full_copy($source5 , $dest5);
            }

            //shop back end shop inc page shop_Options
            $source6 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug .'/shop/WIInc/' .$configs['page'].'.php';
            $dest6 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIInc/';
            $check6 = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/WIInc/' .$configs['page'].'.php';

            if(!file_exists($check6)){
              $this->System->full_copy($source6 , $dest6);

            }
                    

    }

    public function styling($configs, $plug)
    {
            $currentTheme = self::WITheme();

            $source = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIPlugin/'. $plug . '/Install/Theme/'. $configs['lang'];
            $dest = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/WITheme/' . $currentTheme .'/'. $configs['lang'];

          $check = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . '/WITheme/' . $currentTheme .'/'. $configs['lang'];

            if(!file_exists($check)){
              $this->System->full_copy($source , $dest);
               //$fil = glob($dest . "/*");
               //print_r($fil);
            }
               
            
            
          
    }

    public function WITheme()
    {
      $in_use = 1;
      $sql = "SELECT * FROM  `wi_theme` WHERE `in_use`=:in_use";
      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':in_use', $in_use, PDO::PARAM_INT);
      $query->execute();

      $res = $query->fetch();
      $currentTheme = $res['theme'];

      return $currentTheme;
    }


}
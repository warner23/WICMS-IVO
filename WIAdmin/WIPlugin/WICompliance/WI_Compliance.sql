--
-- Table structure for table `wi_batch_cooking`
--

CREATE TABLE `wi_batch_cooking` (
  `id` int NOT NULL,
  `group_id` int NOT NULL,
  `product` varchar(255) NOT NULL,
  `end_cook_timee` varchar(255) NOT NULL,
  `end_cook_temp` varchar(255) NOT NULL,
  `cook_method` varchar(255) NOT NULL,
  `end_cool_time` varchar(255) NOT NULL,
  `end_cool_temp` varchar(255) NOT NULL,
  `total_cooling_time` varchar(255) NOT NULL,
  `time_in_fridge` varchar(255) NOT NULL,
  `initials` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_closing_checks`
--

CREATE TABLE `wi_closing_checks` (
  `id` int NOT NULL,
  `group_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer` enum('yes','no') NOT NULL,
  `problem` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `signed` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_closing_questions`
--

CREATE TABLE `wi_closing_questions` (
  `id` int NOT NULL,
  `question` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_closing_questions`
--

INSERT INTO `wi_closing_questions` (`id`, `question`) VALUES
(1, 'All items on the weekly cleaning schedule have been completed and signed off'),
(2, 'Dirty cloths have been removed for cleaning and replaced with fresh ones'),
(3, 'All waste has been removed, bins are clean and new liners placed inside'),
(4, 'All fridge, freezer and chilled display units are working correctly and temperatures have been recorded'),
(5, 'All food in all fridges/freezers is date labelled and out of date product is discarded'),
(6, 'Defrosting and or cooling foods are complete and placed in suitable storage'),
(7, 'All gas appliances and electrical items are switched off'),
(8, 'The extraction system is switched off');

-- --------------------------------------------------------

--
-- Table structure for table `wi_compliance`
--

CREATE TABLE `wi_compliance` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_compliance`
--

INSERT INTO `wi_compliance` (`id`, `name`) VALUES
(1, 'Checklists'),
(2, 'Training');

-- --------------------------------------------------------

--
-- Table structure for table `wi_compliance_appliances`
--

CREATE TABLE `wi_compliance_appliances` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_compliance_appliances`
--

INSERT INTO `wi_compliance_appliances` (`id`, `name`, `section`) VALUES
(1, 'walls Back Door to Prep', 'section'),
(2, 'Internal Bins', 'section'),
(3, 'Back Door', 'section'),
(4, 'Stove Top', 'appliance'),
(5, 'Walls Prep Line to Cook Line', 'section'),
(6, 'Ovens', 'appliance'),
(7, 'Fryers', 'appliance'),
(8, 'Chargrill', 'appliance'),
(9, 'Salamanders', 'appliance'),
(10, 'Walls Dish Area', 'section'),
(11, 'Dishwasher', 'appliance'),
(12, 'Glasswasher', 'appliance'),
(13, 'Fridges', 'appliance'),
(14, 'Freezers', 'appliance'),
(15, 'Kitchen Floor', 'section'),
(16, 'Store Room', 'section'),
(17, 'Ice Machine', 'appliance'),
(18, 'Electronic Fly Killer (EFK)', 'appliance'),
(19, 'Pizza Oven', 'appliance'),
(20, 'Canopy', 'appliance'),
(21, 'Defrost All Freezers', 'applaince'),
(22, 'Defrost All Fridges', 'appliance');

-- --------------------------------------------------------

--
-- Table structure for table `wi_compliance_checklist`
--

CREATE TABLE `wi_compliance_checklist` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `side` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_compliance_checklist`
--

INSERT INTO `wi_compliance_checklist` (`id`, `name`, `href`, `date`, `time`, `side`) VALUES
(1, 'Morning Checks', 'morning_checks.php', '2022-11-02', '', 'Kitchen'),
(3, 'Daily Cleaning', 'daily_cleaning.php', '2022-11-01', '', 'Kitchen'),
(4, 'Deep Cleaning', 'deep_cleaning.php', '2022-11-02', '', 'Kitchen'),
(5, 'Delivery Checks', 'delivery_checklist.php', '2022-10-30', '', 'Kitchen'),
(6, 'Evening Checks', 'evening_checks.php', '2022-10-30', '', 'Kitchen');

-- --------------------------------------------------------

--
-- Table structure for table `wi_compliance_daily_clean`
--

CREATE TABLE `wi_compliance_daily_clean` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `initials` varchar(255) NOT NULL,
  `group_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_compliance_dashboard`
--

CREATE TABLE `wi_compliance_dashboard` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_compliance_dashboard`
--

INSERT INTO `wi_compliance_dashboard` (`id`, `name`, `time`, `date`) VALUES
(1, 'Morning Checks', '08:11:51', '2022-10-31'),
(19, 'Delivery Checks', '11:17:29', '2022-10-31'),
(43, 'Evening Checks', '08:06:29', '2022-10-31'),
(44, 'Daily Cleaning', '11:10:17', '2022-11-01'),
(49, 'Daily Cleaning', '11:20:44', '2022-11-01'),
(50, 'Daily Cleaning', '11:21:50', '2022-11-01'),
(51, 'Daily Cleaning', '11:24:05', '2022-11-01'),
(53, 'Deep Cleaning', '08:48:59', '2022-11-02'),
(54, 'Morning Checks', '04:17:04', '2022-11-02');

-- --------------------------------------------------------

--
-- Table structure for table `wi_compliance_deep_clean`
--

CREATE TABLE `wi_compliance_deep_clean` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `initials` varchar(255) NOT NULL,
  `group_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- --------------------------------------------------------

--
-- Table structure for table `wi_compliance_deliveries`
--

CREATE TABLE `wi_compliance_deliveries` (
  `id` int NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `temp` varchar(255) NOT NULL,
  `packing_ok` varchar(255) NOT NULL,
  `produce_ok` varchar(255) NOT NULL,
  `rejected` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `corrective` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_compliance_sections`
--

CREATE TABLE `wi_compliance_sections` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `function` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_compliance_sections`
--

INSERT INTO `wi_compliance_sections` (`id`, `name`, `section`, `function`) VALUES
(1, 'morning checks', 'Chef On Duty', 'ChefOnDuty'),
(2, 'morning checks', 'Opening Checks', 'OpeningChecks'),
(3, 'morning checks', 'Fridge Temperature', 'FridgeTemps'),
(4, 'morning checks', 'Freezer Temperature', 'FreezerTemps'),
(5, 'morning checks', 'Fridge Checks', 'FridgeChecks'),
(6, 'morning checks', 'Batch Cooking', 'BatchCooking'),
(7, 'morning checks', 'Dishwasher Checks', 'DishwasherChecks'),
(8, 'evening checks', 'Closing Checks', 'ClosingChecks'),
(9, 'evening checks', 'Fridge Temperature', 'FridgeTemps'),
(10, 'evening checks', 'Freezer Temperature', 'FreezerTemps'),
(11, 'evening checks', 'Fridge Checks', 'FridgeChecks'),
(12, 'evening checks', 'Cooked Food', 'CookedFood'),
(13, 'evening checks', 'Hot Held', 'HotHeld'),
(14, 'evening checks', 'Batch Cooking', 'BatchCooking'),
(15, 'evening checks', 'Dishwasher Checks', 'DishwasherChecks'),
(16, 'Delivery Checks', 'Dry Foods', 'DryFoods'),
(17, 'Delivery Checks', 'Frozen Foods', 'FrozenFoods'),
(18, 'Delivery Checks', 'Fresh Foods', 'FreshFoods'),
(19, 'Deep Cleaning', 'Appliances', 'Appliances'),
(20, 'Deep Cleaning', 'Section', 'Section'),
(21, 'Daily Cleaning', 'dailyClean', 'dailyClean');

-- --------------------------------------------------------

--
-- Table structure for table `wi_daily_cleaning`
--

CREATE TABLE `wi_daily_cleaning` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_daily_cleaning`
--

INSERT INTO `wi_daily_cleaning` (`id`, `name`) VALUES
(1, 'Saute Range'),
(2, 'Salamander'),
(3, 'Filters'),
(4, 'Printers'),
(5, 'Salad Wells'),
(6, 'Microwaves'),
(7, 'Chip Dump'),
(8, 'Hot Wells'),
(9, 'Non Refridgerated Wells'),
(10, 'Service Refridgerators ( All )'),
(11, 'Kitchen Floor'),
(12, 'Back Door'),
(13, 'Dishwasher'),
(14, 'Hot Food Water Wells'),
(15, 'Scales'),
(16, 'Shelving'),
(17, 'Stick Mixer'),
(18, 'Service Window'),
(19, 'Sinks ( All )'),
(20, 'Chopping Boards'),
(21, 'Chargrill'),
(22, 'Griddle'),
(23, 'Fryers'),
(24, 'Bins'),
(25, 'Work Surfaces'),
(26, 'Merry Chef'),
(27, 'Panini Grill');

-- --------------------------------------------------------

--
-- Table structure for table `wi_daily_cleaning_checklist`
--

CREATE TABLE `wi_daily_cleaning_checklist` (
  `id` int NOT NULL,
  `cleaning_id` int NOT NULL,
  `initials` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `wi_deep_cleaning_checklist`
--

CREATE TABLE `wi_deep_cleaning_checklist` (
  `id` int NOT NULL,
  `cleaning_id` int NOT NULL,
  `initials` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `wi_dishwasher_checks`
--

CREATE TABLE `wi_dishwasher_checks` (
  `id` int NOT NULL,
  `temp` varchar(255) NOT NULL,
  `unit_clean` varchar(255) NOT NULL,
  `time_taken` varchar(255) NOT NULL,
  `completed_by` varchar(255) NOT NULL,
  `group_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `wi_evening_checks`
--

CREATE TABLE `wi_evening_checks` (
  `id` int NOT NULL,
  `closing_checks_id` int NOT NULL COMMENT 'out of date food found',
  `oodf_found` enum('yes','no') NOT NULL,
  `fridge_checks_id` int NOT NULL,
  `freezer_checks_id` int NOT NULL,
  `batch_cooking_id` int NOT NULL,
  `dishwasher_checks_id` int NOT NULL,
  `cooked_foods_id` int NOT NULL,
  `hot_held_id` int NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `completed` enum('yes','no') NOT NULL,
  `group_id` int NOT NULL,
  `signedFridges` varchar(255) NOT NULL,
  `signedFreezera` varchar(255) NOT NULL,
  `signedFChecks` varchar(255) NOT NULL,
  `signedClosing` varchar(255) NOT NULL,
  `signedDishes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_freezers`
--

CREATE TABLE `wi_freezers` (
  `id` int NOT NULL,
  `no` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_freezers`
--

INSERT INTO `wi_freezers` (`id`, `no`) VALUES
(1, 'A'),
(2, 'B'),
(3, 'C'),
(4, 'D'),
(5, 'E'),
(6, 'F'),
(7, 'G'),
(8, 'H'),
(9, 'I'),
(10, 'J');

-- --------------------------------------------------------

--
-- Table structure for table `wi_freezer_temps`
--

CREATE TABLE `wi_freezer_temps` (
  `id` int NOT NULL,
  `freezer` varchar(255) NOT NULL,
  `temp` varchar(255) NOT NULL,
  `group_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_fridges`
--

CREATE TABLE `wi_fridges` (
  `id` int NOT NULL,
  `no` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_fridges`
--

INSERT INTO `wi_fridges` (`id`, `no`) VALUES
(24, 1),
(25, 2),
(26, 3),
(27, 4),
(28, 5),
(29, 6),
(30, 7),
(31, 8),
(32, 9),
(33, 10);

-- --------------------------------------------------------

--
-- Table structure for table `wi_fridge_temps`
--

CREATE TABLE `wi_fridge_temps` (
  `id` int NOT NULL,
  `fridge` varchar(255) NOT NULL,
  `temp` varchar(255) NOT NULL,
  `group_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_morning_checks`
--

CREATE TABLE `wi_morning_checks` (
  `id` int NOT NULL,
  `chefs_name` varchar(255) NOT NULL,
  `chefs_position` varchar(255) NOT NULL,
  `opening_checks_id` int NOT NULL COMMENT 'out of date food found',
  `oodf_found` enum('yes','no') NOT NULL,
  `completed_by` varchar(255) NOT NULL,
  `fridge_checks_id` int NOT NULL,
  `freezer_checks_id` int NOT NULL,
  `batch_cooking_id` int NOT NULL,
  `dishwasher_checks_id` int NOT NULL,
  `date` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `completed` enum('yes','no') NOT NULL,
  `group_id` int NOT NULL,
  `signedFridges` varchar(255) NOT NULL,
  `signedFreezera` varchar(255) NOT NULL,
  `signedFChecks` varchar(255) NOT NULL,
  `signedOpening` varchar(255) NOT NULL,
  `signedDishes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


--
-- Table structure for table `wi_opening_checks`
--

CREATE TABLE `wi_opening_checks` (
  `id` int NOT NULL,
  `group_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer` enum('yes','no') NOT NULL,
  `problem` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `signed` varchar(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



--
-- Table structure for table `wi_opening_questions`
--

CREATE TABLE `wi_opening_questions` (
  `id` int NOT NULL,
  `question` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `wi_opening_questions`
--

INSERT INTO `wi_opening_questions` (`id`, `question`) VALUES
(1, 'Staff are fit for work, ie no illness, cuts covered with blue plasters and uniform are suitable and clean.'),
(2, 'All sinks are clean, have hot and cold water, a supply of soap and drying materials'),
(3, 'All fridge, freezer and chilled display units are working correctly and temperatures have been recorded below.'),
(4, 'All other bulk equipment ( ie ovens, stove tops) are clean and working properly.'),
(5, 'The extraction system is switched on and working.'),
(6, 'The lighting and ventilation to the kitchen and storage areas is suitable.'),
(7, 'All areas and equipment clean and sanitised and fit for food preparation.'),
(8, 'Any deliveries are checked off, stored appropriately and recorded on th eweekly checks.'),
(9, 'Sll defrosting food is checked and thoroughly thawed prior to cooking.'),
(10, 'Any shellfish eg mussels are checked, all are stored correctly and any dead removed.'),
(11, 'The kitchen is clear of any signs of pest activity ( if found, make surethis is reported to the manager.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wi_batch_cooking`
--
ALTER TABLE `wi_batch_cooking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_closing_checks`
--
ALTER TABLE `wi_closing_checks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_closing_questions`
--
ALTER TABLE `wi_closing_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance`
--
ALTER TABLE `wi_compliance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_appliances`
--
ALTER TABLE `wi_compliance_appliances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_checklist`
--
ALTER TABLE `wi_compliance_checklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_daily_clean`
--
ALTER TABLE `wi_compliance_daily_clean`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_dashboard`
--
ALTER TABLE `wi_compliance_dashboard`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_deep_clean`
--
ALTER TABLE `wi_compliance_deep_clean`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_deliveries`
--
ALTER TABLE `wi_compliance_deliveries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_compliance_sections`
--
ALTER TABLE `wi_compliance_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_daily_cleaning`
--
ALTER TABLE `wi_daily_cleaning`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_daily_cleaning_checklist`
--
ALTER TABLE `wi_daily_cleaning_checklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_deep_cleaning_checklist`
--
ALTER TABLE `wi_deep_cleaning_checklist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_dishwasher_checks`
--
ALTER TABLE `wi_dishwasher_checks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_evening_checks`
--
ALTER TABLE `wi_evening_checks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_freezers`
--
ALTER TABLE `wi_freezers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_freezer_temps`
--
ALTER TABLE `wi_freezer_temps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_fridges`
--
ALTER TABLE `wi_fridges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_fridge_temps`
--
ALTER TABLE `wi_fridge_temps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_morning_checks`
--
ALTER TABLE `wi_morning_checks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_opening_checks`
--
ALTER TABLE `wi_opening_checks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_opening_questions`
--
ALTER TABLE `wi_opening_questions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wi_batch_cooking`
--
ALTER TABLE `wi_batch_cooking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `wi_closing_checks`
--
ALTER TABLE `wi_closing_checks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `wi_closing_questions`
--
ALTER TABLE `wi_closing_questions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wi_compliance`
--
ALTER TABLE `wi_compliance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wi_compliance_appliances`
--
ALTER TABLE `wi_compliance_appliances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `wi_compliance_checklist`
--
ALTER TABLE `wi_compliance_checklist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wi_compliance_daily_clean`
--
ALTER TABLE `wi_compliance_daily_clean`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `wi_compliance_dashboard`
--
ALTER TABLE `wi_compliance_dashboard`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `wi_compliance_deep_clean`
--
ALTER TABLE `wi_compliance_deep_clean`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wi_compliance_deliveries`
--
ALTER TABLE `wi_compliance_deliveries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wi_compliance_sections`
--
ALTER TABLE `wi_compliance_sections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wi_daily_cleaning`
--
ALTER TABLE `wi_daily_cleaning`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `wi_daily_cleaning_checklist`
--
ALTER TABLE `wi_daily_cleaning_checklist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_deep_cleaning_checklist`
--
ALTER TABLE `wi_deep_cleaning_checklist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_dishwasher_checks`
--
ALTER TABLE `wi_dishwasher_checks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_evening_checks`
--
ALTER TABLE `wi_evening_checks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `wi_freezers`
--
ALTER TABLE `wi_freezers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wi_freezer_temps`
--
ALTER TABLE `wi_freezer_temps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=303;

--
-- AUTO_INCREMENT for table `wi_fridges`
--
ALTER TABLE `wi_fridges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `wi_fridge_temps`
--
ALTER TABLE `wi_fridge_temps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

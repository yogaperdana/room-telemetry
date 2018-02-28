-- Database: `telemetri`
CREATE DATABASE `telemetri`;

-- Table structure for table `sensor_config`
CREATE TABLE `sensor_config` (
	`config_name` varchar(32) NOT NULL,
	`config_value` varchar(128) NOT NULL
);

-- Dumping data for table `sensor_config`
INSERT INTO `sensor_config` (`config_name`, `config_value`) VALUES
('sensor_cutoff', '700'),
('sensor_enable', '1|1|1|1|1|1|1|1'),
('sensor_name', 'Ruang A|P2|Ruang B|P4|P5|Ruang C|P7|Ruang D'),
('sensor_sort', '1|5|2|6|7|3|8|4');

-- Table structure for table `sensor_data`
CREATE TABLE `sensor_data` (
	`insert_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`sensor1` int(11) NOT NULL,
	`sensor2` int(11) NOT NULL,
	`sensor3` int(11) NOT NULL,
	`sensor4` int(11) NOT NULL,
	`sensor5` int(11) NOT NULL,
	`sensor6` int(11) NOT NULL,
	`sensor7` int(11) NOT NULL,
	`sensor8` int(11) NOT NULL
);

-- Indexes for table `sensor_config`
ALTER TABLE `sensor_config` ADD UNIQUE KEY `config_name` (`config_name`);

-- Indexes for table `sensor_data`
ALTER TABLE `sensor_data` ADD UNIQUE KEY `insert_time` (`insert_time`);

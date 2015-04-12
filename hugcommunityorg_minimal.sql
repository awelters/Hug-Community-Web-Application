DROP DATABASE IF EXISTS `hugcommunity`;
CREATE DATABASE `hugcommunity` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `hugcommunity`;

CREATE TABLE `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
);

INSERT INTO `groups` VALUES (NULL,'admin','Administrator');
SET @admin_group = LAST_INSERT_ID();

INSERT INTO `groups` VALUES (NULL,'social worker','Social Worker');
SET @social_worker_group = LAST_INSERT_ID();

INSERT INTO `groups` VALUES (NULL,'Little Andy','Little Andy\'s Safety Team');
SET @real_prototype_test_community_group = LAST_INSERT_ID();

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `mobile_alerts` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);

INSERT INTO `users` VALUES (NULL,'\0\0','awelters','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','awelters@hugmehugyou.org','',NULL,NULL,NULL,1268889823,1387334153,1,'Andrew','Welters','ADMIN','612-396-7980',1);
SET @real_admin = LAST_INSERT_ID();

INSERT INTO `users` VALUES (NULL,'\0\0','test_admin','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_admin@test.com','',NULL,NULL,NULL,1268889823,1268889823,1,'TEST','ADMIN','ADMIN','111-111-1111',0);
SET @test_admin = LAST_INSERT_ID();

INSERT INTO `users` VALUES (NULL,'\0\0','test_social_worker','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_social_worker@test.com','',NULL,NULL,NULL,1268889823,1268889823,1,'TEST','SOCIAL WORKER','SOCIAL WORKER','111-111-1111',0);
SET @test_social_worker = LAST_INSERT_ID();

INSERT INTO `users` VALUES (NULL,'\0\0','test_tester_member','$2a$08$MQb/ITDkv.7MNIPO8DcaGOxikg3scPxSIB/NsesPkHVtFYKKkFT5W','9462e8eee0','test_community_member@test.com','',NULL,NULL,NULL,1268889823,1268889823,1,'TEST','COMMUNITY MEMBER','COMMUNITY MEMBER','111-111-1111',0);
SET @test_tester_member = LAST_INSERT_ID();

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO `users_groups` VALUES (NULL,@real_admin,@admin_group);
SET @real_admin_admins = LAST_INSERT_ID();

INSERT INTO `users_groups` VALUES (NULL,@real_admin,@real_prototype_test_community_group);
SET @real_admin_test_community = LAST_INSERT_ID();

INSERT INTO `users_groups` VALUES (NULL,@test_admin,@admin_group);
SET @test_admin_admins = LAST_INSERT_ID();

INSERT INTO `users_groups` VALUES (NULL,@test_social_worker,@social_worker_group);
SET @test_social_worker_social_workers = LAST_INSERT_ID();

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
);

CREATE TABLE `companions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `emergency_alert` tinyint(1) NOT NULL DEFAULT '0',
  `curfew_alert` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_companions_name` (`name`)
);

INSERT INTO `companions` VALUES (NULL,'Andy\'s Sammy','Carver County\'s Safety Sam Prototype',1,0);
SET @real_prototype = LAST_INSERT_ID();

INSERT INTO `companions` VALUES (NULL,'Test Companion 1','Test Companion 1',0,0);
SET @test1_prototype = LAST_INSERT_ID();

INSERT INTO `companions` VALUES (NULL,'Test Companion 2','Test Companion 2',0,0);
SET @test2_prototype = LAST_INSERT_ID();

CREATE TABLE `companions_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_companions_groups` (`companion_id`,`group_id`),
  KEY `fk_companions_groups_companions1_idx` (`companion_id`),
  KEY `fk_companions_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_companions_groups_companions1` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companions_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO `companions_groups` VALUES (NULL,@real_prototype,@real_prototype_test_community_group);

CREATE TABLE `companion_says` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `is_message` tinyint(1) NOT NULL,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `companion_says` VALUES (NULL,0,'Hey Friend, I\'m Safety Sam your trusted MMM-Monkey friend! I can help you and you\'re safety team keep IN touch. Would you like to let \'em know how you feel ri'),(NULL,0,'WHEW! I\'m getting tuckered out. Could you ask an adult to help get me REEEEE-Energized!!!!!'),(NULL,0,'Hey I\'m feelin mighty sleepy! Could you find an adult to REEEEE-CHARGE ME?!'),(NULL,0,'I\'m feeelin LOW LOW LOW on the Energeeeeeeezzzzzz. (SNORE)...(SNORT) Can you find me an adult to help PLEEEEEEZZZZZZ!!!'),(NULL,0,'ZINGERS! I\'m FUUUUULLLLLLL of ENERGY!'),(NULL,0,'I\'m ready to go in coach, just give me a chance. I know there\'s a lot of riding on it, but it\'s all psychological. Just gotta stay in a positive frame of mind.'),(NULL,0,'BZZZZZZZZZZZ! WOAH! I\'m all full of energy! No more bananas for me pleaze! TEEEEHEEEE'),(NULL,0,'So you want some quiet time, huh? Alrighty, I\'ll ask you\'re safety team to zip their lips!'),(NULL,0,'OH your ready for quiet time, Ill tell the Safety team to keep it down. (whisper) ssshhh.. teehehe!'),(NULL,0,'Is it quiet time already?! Ok then, I\'ll let your safety team know you could use a little break.'),(NULL,0,'You\'re happy, that\'s GRR-APE! TE-HE-HE! I\'ll share the good news with you\'re safety team right away!'),(NULL,0,'I\'m sure happy you gave me a squeeze! And, I\'m sure your safety team will be as happy as I am to know your feeling swell!'),(NULL,0,'Gee wiz that was a good squeeze! It\'ll be a breeze to say to the safety team - IT\'S HAPPY TIME!!!!'),(NULL,0,'OOOO-WEE that was super SQUEEZEEE!!! TEHEHE!! I\'m going to send a message to the safety team to say that you\'re feeling good!'),(NULL,0,'I\'m glad to hear you\'re having a great day. Let\'s share the happy news with the safety team!'),(NULL,0,'Holy MOLY! yer the best squeeeeezer ever! I\'ll make sure your safety team knows yer feeling great!'),(NULL,0,'HEY! Yeah! Your happiness is making my day GRR-APE! TEHEHE!!! I\'ll tell the safety team right away!'),(NULL,0,'SAYYYYYY WHAT?! YAY! YER HAVING A GREAT DAY! Let\'s tell your safety team right now!'),(NULL,0,'Ya know what?!.... that squeeze set a world record for best squeeeeeze ... EVER! I\'ll let your safety team know your feeling happy!'),(NULL,0,'HANG IN THERE MMM-Monkey Friend! I\'ll share your feelings with the safety team right away and see if they can\'t help you turn that frown upside down!'),(NULL,0,'Oh no champ you\'re feeling blue?! Well I\'ll tell your Safety Team and see if they can\'t help you paint a sunnier picture. What do you say?!'),(NULL,0,'GRRRRRR thats a BUMMER. I\'ll tell your safety team you\'re feeling down and we\'ll see if they can\'t help lift your spirits off the ground'),(NULL,0,'Bad Day? Don\'t worry, I\'ll tell the safety team, but meanwhile here\'s a secret to help cheer you up - GRAAAPPPEEE JELLY! TEHEHE! That\'s so silly!'),(NULL,0,'HMMMM...thats no good pal...ill tell the safety team.... but if it will help you can always give me a hug!'),(NULL,0,'OH shucks...that makes me sad....ill tell the safety team how your feeling right away....'),(NULL,0,'WHAT?! You\'ve had a rainy day, huh? OK, IM ON IT! Let\'s see if your safety team can help dry those tears.'),(NULL,0,'Feeling Glum Chum? That\'s not good. I\'ll see if you\'re safety team can help cheer you up.'),(NULL,0,'That is very SERIOUS we need to tell your safety team right away! Until then, hang in there and be safe jungle pal!'),(NULL,0,'WOAH! This is super SERIOUS I\'ll send a message to your safety team immediately! Lets find a safe place!'),(NULL,0,'Uh oh! This is a SERIOUS situation! Im sending a message to your safety team now. Stay close to me till we get help.'),(NULL,1,'Hey Bud! Your Safety Team has a joke for you... How did the dog warn its master that a Gorilla was coming? Answer, He barked GRRR-illa! TEHEHE!!! I sure hope '),(NULL,1,'Hey Bud! Your Safety Team has a joke for you... What does a Gorilla learn first in school? Answer, The Apey-cees! TEHEHE! That really was a smart joke, wasn\'t'),(NULL,1,'Hey Bud! Your Safety Team has a joke for you... What is a gorilla\'s favorite cookie? Answer, Chocolate chimp! TEHEHE! That was a funny joke, don\'t you think'),(NULL,1,'Hear yee, Hear yee! Read all about it! I\'ve got news for you from your safety team. This just in... CHEESEBURGER!!! TE-HE-HE! That\'s so silly!'),(NULL,1,'Whose hungry for some great news? Well, I just heard from the GRRR-APE vine that your Safety Team is thinking of you! How DEEEE-licious! But now I\'m hungry, '),(NULL,1,'Hey Buddy ol\' Pal! You\'re safety team wants you to know that you shine like a star! Keep up the good work.'),(NULL,1,'AYE-O! The safety team wants to ask you a question champ... Do you know what\'s in the middle of a HUG?... That\'s right, YOU!!!'),(NULL,1,'Hey Friend, you\'re Safety Team asked that I pass on a message for them. They wanted me to let you know they love you very, very, very much!'),(NULL,1,'Hey Monkey Friend! Your safety team wanted me to tell you they love you, they\'re here for you, and you are a very special person!'),(NULL,1,'YOU-HOO! I\'ve got a singing telegram from your safety team! Here goes... Ding dong ding ding dong - follow this song and sing along! Ding dong ding ding dong -'),(NULL,1,'Hello MMM-Monkey Friend! The safety team has some music to share with you! ... here goes ... (Twinkle, Twinkle Little Star Music).'),(NULL,1,'Hey monkey bud! I just told your safety team, and they agree things are SERIOUS! Help is on the way!'),(NULL,1,'Alriiiight! I just heard back from your safety team! They said to be calm and that help is arriving shortly! Whew im glad we decided to tell them how SERIOUS th'),(NULL,1,'Hey there Chum, your safety team tells me that they understand you are in a SERIOUS situation and will do everything they can to get you the help you need as so');

CREATE TABLE `companion_audio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` mediumblob NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `companion_says_audio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `companion_says_id` int(11) unsigned NOT NULL,
  `companion_audio_id` int(11) unsigned NOT NULL,
  `audio_num` smallint(3) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_companions_says_audio_num` (`companion_says_id`,`companion_audio_id`,`audio_num`),
  KEY `fk_companions_says_audio_says1_idx` (`companion_says_id`),
  KEY `fk_companions_says_audio_audio1_idx` (`companion_audio_id`),
  KEY `fk_companions_says_audio_num1_idx` (`audio_num`),
  CONSTRAINT `fk_companions_says_audio_audio1` FOREIGN KEY (`companion_audio_id`) REFERENCES `companion_audio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companions_says_audio_says1` FOREIGN KEY (`companion_says_id`) REFERENCES `companion_says` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE `companion_updates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `companion_id` int(11) unsigned NOT NULL,
  `voltage` float(3,2) NOT NULL,
  `is_charging` tinyint(1) NOT NULL,
  `is_charging_update` tinyint(1) NOT NULL DEFAULT '0',
  `low_battery_update` tinyint(1) NOT NULL DEFAULT '0',
  `emotional_state` tinyint(1) NOT NULL,
  `emotion_update` tinyint(1) NOT NULL DEFAULT '0',
  `emergency_update` tinyint(1) NOT NULL DEFAULT '0',
  `play_message` tinyint(1) NOT NULL,
  `play_message_update` tinyint(1) NOT NULL DEFAULT '0',
  `play_message_update_by_user` tinyint(1) NOT NULL DEFAULT '0',
  `last_said_id` int(11) unsigned DEFAULT NULL,
  `last_said_update` tinyint(1) NOT NULL DEFAULT '0',
  `last_message_said_id` int(11) unsigned DEFAULT NULL,
  `last_message_said_update` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_companion_update_companions1_idx` (`companion_id`),
  KEY `fk_companion_update_last_said1_idx` (`last_said_id`),
  KEY `fk_companion_update_last_said_message1_idx` (`last_message_said_id`),
  CONSTRAINT `fk_companion_update_companions1_idx` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_update_last_said1_idx` FOREIGN KEY (`last_said_id`) REFERENCES `companion_says_audio` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_update_last_said_message1_idx` FOREIGN KEY (`last_message_said_id`) REFERENCES `companion_says_audio` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);

CREATE TABLE `companion_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `companion_id` int(11) unsigned NOT NULL,
  `companion_says_id` int(11) unsigned DEFAULT NULL,
  `is_pending` tinyint(1) NOT NULL DEFAULT '1',
  `companion_updates_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_companion_message_users1_idx` (`user_id`),
  KEY `fk_companion_message_companions1_idx` (`companion_id`),
  KEY `fk_companion_message_companion_says1_idx` (`companion_says_id`),
  KEY `fk_companion_message_companion_updates1_idx` (`companion_updates_id`),
  CONSTRAINT `fk_companion_message_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_message_companions1_idx` FOREIGN KEY (`companion_id`) REFERENCES `companions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_message_companion_says1_idx` FOREIGN KEY (`companion_says_id`) REFERENCES `companion_says` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `fk_companion_message_companion_updates1_idx` FOREIGN KEY (`companion_updates_id`) REFERENCES `companion_updates` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` text NOT NULL,
  `surname` text NOT NULL,
  `email` text NOT NULL,
  `psword` text NOT NULL,
  `admin` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (userID)
);

CREATE TABLE `tasks` (
  `taskID` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` text NOT NULL,
  PRIMARY KEY (taskID)
);

CREATE TABLE `tasklists` (
  `taskListID` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `owner` tinyint NOT NULL,
  PRIMARY KEY (taskListID)
);

CREATE TABLE `taskcomment` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `taskID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (commentID),
  FOREIGN KEY (taskID) REFERENCES tasks (taskID),
  FOREIGN KEY (userID) REFERENCES users (userID)
);

CREATE TABLE `notification` (
  `notificationID` int(11) NOT NULL AUTO_INCREMENT,
  `associatedTask` int(11) NOT NULL,
  `description` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (notificationID),
  FOREIGN KEY (associatedTask) REFERENCES tasks (taskID)
);

CREATE TABLE `tasklistaccess` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `taskListID` int(11) NOT NULL,
  `colour` text NOT NULL,
  `owner` tinyint(1) NOT NULL,
  FOREIGN KEY (userID) REFERENCES users (userID),
  FOREIGN KEY (taskListID) REFERENCES tasklists (taskListID)
);

CREATE TABLE `taskcompleted` (
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `taskID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  FOREIGN KEY (taskID) REFERENCES tasks (taskID),
  FOREIGN KEY (userID) REFERENCES users (userID)
);

CREATE TABLE `tasklisttasks` (
  `taskListID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  FOREIGN KEY (taskListID) REFERENCES taskLists (taskListID),
  FOREIGN KEY (taskID) REFERENCES tasks (taskID)
);
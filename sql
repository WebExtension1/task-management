CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `firstname` text NOT NULL,
  `surname` text NOT NULL,
  `email` text NOT NULL,
  `psword` text NOT NULL,
  `admin` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (userID)
);

CREATE TABLE `tasks` (
  `taskID` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` text NOT NULL,
  PRIMARY KEY (taskID)
);

CREATE TABLE `tasklists` (
  `taskListID` int NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (taskListID)
);

CREATE TABLE `taskcomment` (
  `commentID` int NOT NULL AUTO_INCREMENT,
  `text` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `taskID` int NOT NULL,
  `userID` int NOT NULL,
  PRIMARY KEY (commentID),
  FOREIGN KEY (taskID) REFERENCES tasks (taskID),
  FOREIGN KEY (userID) REFERENCES users (userID)
);

CREATE TABLE `notification` (
  `notificationID` int NOT NULL AUTO_INCREMENT,
  `associatedTask` int NOT NULL,
  `description` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (notificationID),
  FOREIGN KEY (associatedTask) REFERENCES tasks (taskID)
);

CREATE TABLE `tasklistaccess` (
  `userID` int NOT NULL,
  `taskListID` int NOT NULL,
  `colour` text NOT NULL,
  `owner` tinyint(1) NOT NULL,
  FOREIGN KEY (userID) REFERENCES users (userID),
  FOREIGN KEY (taskListID) REFERENCES tasklists (taskListID)
);

CREATE TABLE `taskcompleted` (
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `taskID` int NOT NULL,
  `userID` int NOT NULL,
  FOREIGN KEY (taskID) REFERENCES tasks (taskID),
  FOREIGN KEY (userID) REFERENCES users (userID)
);

CREATE TABLE `tasklisttasks` (
  `taskListID` int NOT NULL,
  `taskID` int NOT NULL,
  FOREIGN KEY (taskListID) REFERENCES taskLists (taskListID),
  FOREIGN KEY (taskID) REFERENCES tasks (taskID)
);